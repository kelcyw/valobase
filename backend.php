<html lang="en">
<head>
    <title>CPSC 304 PHP/Oracle Demonstration</title>
</head>

<body>
<h2>Reset</h2>
<p>If you wish to reset the table press on the reset button. If this is the first time you're running this page, you MUST use reset</p>

<form method="POST" action="backend.php">
    <!-- if you want another page to load after the button is clicked, you have to specify that page in the action parameter -->
    <input type="hidden" id="resetTablesRequest" name="resetTablesRequest">
    <p><input type="submit" value="Reset" name="reset"></p>
</form>

<hr />

<h2>Insert a Team</h2>
<form method="POST" action="backend.php"> <!--refresh page when submitted-->
    <input type="hidden" id="insertQueryRequest" name="insertQueryRequest">
    Team Name: <input type="text" name="insNo"> <br /><br />
    Number of Players: <input type="text" name="insName"> <br /><br />

    <input type="submit" value="Insert" name="insertSubmit"></p>
</form>

<hr />

<h2>Update Name in DemoTable</h2>
<p>The values are case sensitive and if you enter in the wrong case, the update statement will not do anything.</p>

<form method="POST" action="backend.php"> <!--refresh page when submitted-->
    <input type="hidden" id="updateQueryRequest" name="updateQueryRequest">
    Old Name: <input type="text" name="oldName"> <br /><br />
    New Name: <input type="text" name="newName"> <br /><br />

    <input type="submit" value="Update" name="updateSubmit"></p>
</form>

<hr />

<h2>Count the Tuples in DemoTable</h2>
<form method="GET" action="backend.php"> <!--refresh page when submitted-->
    <input type="hidden" id="countTupleRequest" name="countTupleRequest">
    <input type="submit" name="countTuples"></p>
</form>

 <?php
// tells the system it is parsing PHP instead of HTML for example

$success = true;
$db_conn = NULL;
$show_debug_alert_messages = false;

function debugAlertMessage($message) {
    global $show_debug_alert_messages;

    if ($show_debug_alert_messages) {
        echo "<script type='text/javascript'>alert('" . $message . "');</script>";
    }
}

function executePlainSQL($cmdstr) { //takes a plain (no bound variables) SQL command and executes it
    //echo "<br>running ".$cmdstr."<br>";
    global $db_conn, $success;

    $statement = oci_parse($db_conn, $cmdstr);
    //There are a set of comments at the end of the file that describe some of the OCI specific functions and how they work

    if (!$statement) {
        echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
        $e = OCI_Error($db_conn); // For OCIParse errors pass the connection handle
        echo htmlentities($e['message']);
        $success = False;
    }

    $r = oci_execute($statement, OCI_DEFAULT);
    if (!$r) {
        echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
        $e = oci_error($statement); // For OCIExecute errors pass the statement-handle
        echo htmlentities($e['message']);
        $success = False;
    }

    return $statement;
}

function executeBoundSQL($cmdstr, $list) {
    /* Sometimes the same statement will be executed several times with different values for the variables involved in the query.
In this case you don't need to create the statement several times. Bound variables cause a statement to only be
parsed once and you can reuse the statement. This is also very useful in protecting against SQL injection.
See the sample code below for how this function is used */

    global $db_conn, $success;
    $statement = oci_parse($db_conn, $cmdstr);

    if (!$statement) {
        echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
        $e = OCI_Error($db_conn);
        echo htmlentities($e['message']);
        $success = False;
    }

    foreach ($list as $tuple) {
        foreach ($tuple as $bind => $val) {
            //echo $val;
            //echo "<br>".$bind."<br>";
            oci_bind_by_name($statement, $bind, $val);
            unset ($val); //make sure you do not remove this. Otherwise $val will remain in an array object wrapper which will not be recognized by Oracle as a proper datatype
        }

        $r = oci_execute($statement, OCI_DEFAULT);
        if (!$r) {
            echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
            $e = OCI_Error($statement); // For OCIExecute errors, pass the statement-handle
            echo htmlentities($e['message']);
            echo "<br>";
            $success = False;
        }
    }
}

function printResult($result) { //prints results from a select statement
    echo "<br>Retrieved data from table demoTable:<br>";
    echo "<table>";
    echo "<tr><th>ID</th><th>Name</th></tr>";

    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        echo "<tr><td>" . $row["ID"] . "</td><td>" . $row["NAME"] . "</td></tr>"; //or just use "echo $row[0]"
    }

    echo "</table>";
}

function connectToDB() {
    global $db_conn;

    // Your username is ora_(CWL_ID) and the password is a(student number). For example,
    // ora_platypus is the username and a12345678 is the password.
    $db_conn = oci_connect("ora_sahibrao", "a43038967", "dbhost.students.cs.ubc.ca:1522/stu");

    if ($db_conn) {
        debugAlertMessage("Database is Connected");
        return true;
    } else {
        debugAlertMessage("Cannot connect to Database");
        $e = OCI_Error(); // For OCILogon errors pass no handle
        echo htmlentities($e['message']);
        return false;
    }
}

function disconnectFromDB() {
    global $db_conn;

    debugAlertMessage("Disconnect from Database");
    oci_close($db_conn);
}

// TODO Update
function handleUpdateRequest() {
    global $db_conn;

    $old_name = $_POST['oldName'];
    $new_name = $_POST['newName'];

    // you need the wrap the old name and new name values with single quotations
    executePlainSQL("UPDATE demoTable SET name='" . $new_name . "' WHERE name='" . $old_name . "'");
    oci_commit($db_conn);
}

// TODO creating tables (start up?)
function startUp() {
    global $db_conn;

    // drop all tables
    executeBoundSQL("BEGIN FOR t IN (SELECT 'drop table ' || table_name || ' cascade constraints;' as drop_statement
             FROM user_tables) LOOP EXECUTE IMMEDIATE t.drop_statement; END LOOP; END;", );

    // player
    executePlainSQL("CREATE TABLE player (username char(20) NOT NULL PRIMARY KEY , rank char(20), acct_level int)");
    // team
    executePlainSQL("CREATE TABLE team (team_name char(20) NOT NULL PRIMARY KEY , num_of_members char(20) NOT NULL)");
//    // skin
//    executePlainSQL("CREATE TABLE skin (skin_name char(20) NOT NULL PRIMARY KEY , price int)");
//    // gun
//    executePlainSQL("CREATE TABLE gun (gun_name char(20) NOT NULL PRIMARY KEY , type char(20), c_cost int)");
//    // gameMode
//    executePlainSQL("CREATE TABLE gamemode (gamemode_name char(20) NOT NULL PRIMARY KEY , num_of_players int)");
//    // map
//    executePlainSQL("CREATE TABLE map (map_name char(20) NOT NULL PRIMARY KEY , numOfSites int)");
//    // agent
//    executePlainSQL("CREATE TABLE team (agent_name char(20) NOT NULL PRIMARY KEY , role char(20))");
//    // match
//    executePlainSQL("CREATE TABLE match (match_ID int NOT NULL PRIMARY KEY,
//                                                gamemode_name char(20) NOT NULL,
//                                                map_name char(20) NOT NULL ,
//                                                CONSTRAINT fk_gamemode FOREIGN KEY (gamemode_name) REFERENCES gamemode(gamemode_name) ON DELETE CASCADE ON UPDATE CASCADE,
//                                                CONSTRAINT fk_map FOREIGN KEY (map_name) REFERENCES map(map_name) ON DELETE CASCADE ON UPDATE CASCADE)");
//    // ability
//    executePlainSQL("CREATE TABLE ability (ability_name char(20) NOT NULL, agent_name char(20) NOT NULL,
//                                                    CONSTRAINT pk_ability PRIMARY KEY (ability_name, agent_name),
//                                                    CONSTRAINT fk_ability FOREIGN KEY (agent_name) REFERENCES agent(agent_name) ON DELETE CASCADE ON UPDATE CASCADE )");
//    // passive
//    executePlainSQL("CREATE TABLE passive (ability_name char(20) NOT NULL, agent_name char(20) NOT NULL,
//                                                    CONSTRAINT pk_passive PRIMARY KEY (ability_name, agent_name),
//                                                    CONSTRAINT fk_agent FOREIGN KEY (agent_name) REFERENCES agent(agent_name) ON DELETE CASCADE ON UPDATE CASCADE,
//                                                    CONSTRAINT fk_ability FOREIGN KEY (ability_name) REFERENCES ability(ability_name) ON DELETE CASCADE ON UPDATE CASCADE)");
//    // active
//    executePlainSQL("CREATE TABLE active (ability_name char(20) NOT NULL, agent_name char(20) NOT NULL, c_cost int,
//                                                    CONSTRAINT pk_active PRIMARY KEY (ability_name, agent_name),
//                                                    CONSTRAINT fk_agent FOREIGN KEY (agent_name) REFERENCES agent(agent_name) ON DELETE CASCADE ON UPDATE CASCADE,
//                                                    CONSTRAINT fk_ability FOREIGN KEY (ability_name) REFERENCES ability(ability_name) ON DELETE CASCADE ON UPDATE CASCADE)");
//    // owns
//    executePlainSQL("CREATE TABLE owns (username char(20) NOT NULL, gun_name char(20) NOT NULL,
//                                                    CONSTRAINT pk_owns PRIMARY KEY (username, gun_name),
//                                                    CONSTRAINT fk_username FOREIGN KEY (username) REFERENCES username(username) ON DELETE CASCADE ON UPDATE CASCADE,
//                                                    CONSTRAINT fk_gun FOREIGN KEY (gun_name) REFERENCES gun(gun_name) ON DELETE CASCADE ON UPDATE CASCADE)");
//    // for
//    executePlainSQL("CREATE TABLE for (skin_name char(20) NOT NULL, gun_name char(20) NOT NULL,
//                                                    CONSTRAINT pk_for PRIMARY KEY (skin_name, gun_name),
//                                                    CONSTRAINT fk_username FOREIGN KEY (username) REFERENCES username(username) ON DELETE CASCADE ON UPDATE CASCADE,
//                                                    CONSTRAINT fk_gun FOREIGN KEY (gun_name) REFERENCES gun(gun_name) ON DELETE CASCADE ON UPDATE CASCADE)");
//    // plays
//    executePlainSQL("CREATE TABLE plays (username char(20) NOT NULL, agent_name char(20) NOT NULL, match_ID int NOT NULL,
//                                                    CONSTRAINT pk_plays PRIMARY KEY (username, agent_name, match_ID),
//                                                    CONSTRAINT fk_username FOREIGN KEY (username) REFERENCES username(username) ON DELETE CASCADE ON UPDATE CASCADE,
//                                                    CONSTRAINT fk_agent FOREIGN KEY (agent_name) REFERENCES agent(agent_name) ON DELETE CASCADE ON UPDATE CASCADE
//                                                    CONSTRAINT fk_match FOREIGN KEY (match_ID) REFERENCES match(match_ID) ON DELETE CASCADE ON UPDATE CASCADE)");
    // isOn
    executePlainSQL("CREATE TABLE isOn (username char(20) NOT NULL, team_name char(20) NOT NULL,
                                                    PRIMARY KEY (username,team_name),
                                                FOREIGN KEY (username) REFERENCES player(username) ON DELETE CASCADE ON UPDATE CASCADE,
                                                 FOREIGN KEY (team_name) REFERENCES team(team_name) ON DELETE CASCADE ON UPDATE CASCADE)");

    oci_commit($db_conn);
}

// TODO RESET
function handleResetRequest() {
    global $db_conn;
    // Drop old table
    executePlainSQL("DROP TABLE demoTable");

    // Create new table
    echo "<br> creating new table <br>";
    executePlainSQL("CREATE TABLE demoTable (id int PRIMARY KEY, name char(30))");
    oci_commit($db_conn);
}

// TODO Insert

function handleInsertRequest() {
    global $db_conn;

    //Getting the values from user and insert data into the table
    $tuple = array (
        ":bind1" => $_POST['insNo'],
        ":bind2" => $_POST['insName']
    );

    $allTuples = array (
        $tuple
    );

    executeBoundSQL("insert into team values (:bind1, :bind2)", $allTuples);
    oci_commit($db_conn);
}

// Count
function handleCountRequest() {
    global $db_conn;

    $result = executePlainSQL("SELECT Count(*) FROM team");

    if (($row = oci_fetch_row($result)) != false) {
        echo "<br> The number of tuples in demoTable: " . $row[0] . "<br>";
    }
}

// HANDLE ALL POST ROUTES
// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
function handlePOSTRequest() {
    if (connectToDB()) {
        if (array_key_exists('resetTablesRequest', $_POST)) {
            startUp();
        } else if (array_key_exists('updateQueryRequest', $_POST)) {
            handleUpdateRequest();
        } else if (array_key_exists('insertQueryRequest', $_POST)) {
            handleInsertRequest();
        }

        disconnectFromDB();
    }
}

// HANDLE ALL GET ROUTES
// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
function handleGETRequest() {
    if (connectToDB()) {
        if (array_key_exists('countTuples', $_GET)) {
            handleCountRequest();
        }

        disconnectFromDB();
    }
}

if (isset($_POST['reset']) || isset($_POST['updateSubmit']) || isset($_POST['insertSubmit'])) {
    handlePOSTRequest();
} else if (isset($_GET['countTupleRequest'])) {
    handleGETRequest();
}
 ?>
</body>
</html>

