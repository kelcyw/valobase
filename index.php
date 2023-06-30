<html>
<head>
    <title>CPSC 304 Group 9 Project - Valobase</title>
</head>
<body>
<h1>Welcome to Valobase!</h1>

<form method="POST" action="index.php">
    <!-- if you want another page to load after the button is clicked, you have to specify that page in the action parameter -->
    <input type="hidden" id="loadTablesRequest" name="loadTablesRequest">
    <p><input type="submit" value="Load Tables" name="submit"></p>
</form>

<form method="GET" action="index.php">
    <input type="hidden" id="printDataRequest" name="printDataRequest">
    <p><input type="submit" value="Show Current Data" name="submit"></p>
</form>

<form method="POST" action="insert.php">
    <input type="hidden">
    <p><input type="submit" value="Insert new (player, team, or match)" name="submit"></p>
</form>

<form method="POST" action="update.php">
    <input type="hidden">
    <p><input type="submit" value="Update existing (player, team, or match)" name="submit"></p>
</form>

<form method="POST" action="delete.php">
    <input type="hidden">
    <p><input type="submit" value="Delete existing (player, team, or match)" name="submit"></p>
</form>

<form method="POST" action="search.php">
    <input type="hidden">
    <p><input type="submit" value="Search existing (player, team, gamemode, etc.)" name="submit"></p>
</form>

<form method="POST" action="index.php">
    <input type="hidden" id="resetTablesRequest" name="resetTablesRequest">
    <p><input type="submit" value="Reset" name="submit"></p>
</form>

<?php


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
    echo "<table>";

    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        echo $row[0]; //or just use "echo $row[0]"
        echo ", ";
    }

    echo "</table>";
}

function printPlayerResult($result) { //prints results from a select statement for player table
    echo "<table>";

    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        echo $row[0]; //or just use "echo $row[0]"
        echo "(";
        echo $row[1];
        echo ", ";
        echo $row[2];
        echo ")";
        echo ", ";
    }

    echo "</table>";
}

function printTeamResult($result) { //prints results from a select statement for team table
    echo "<table>";

    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        echo $row[0]; //or just use "echo $row[0]"
        echo "(Member count: ";
        echo $row[1];
        echo ")";
        echo ", ";
    }

    echo "</table>";
}

function printOwnsResult($result) { //prints results from a select statement for owns table
    echo "<table>";

    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        echo $row[0]; //or just use "echo $row[0]"
        echo ": ";
        echo $row[1];
        echo ", ";
    }

    echo "</table>";
}

function printMatchResult($result) { //prints results from a select statement for match table
    echo "<table>";

    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        echo $row[0]; //or just use "echo $row[0]"
        echo "(";
        echo $row[1];
        echo ", ";
        echo $row[2];
        echo ", ";
        echo $row[3];
        echo ", ";
        echo $row[4];
        echo ")";
        echo ", ";
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

// drop all the tables currently
function dropTables() {
    global $db_conn;
    executePlainSQL("DROP TABLE passive");
    executePlainSQL("DROP TABLE active");
    executePlainSQL("DROP TABLE ability");
    executePlainSQL("DROP TABLE owns");
    executePlainSQL("DROP TABLE for_gun");
    executePlainSQL("DROP TABLE isOn");
    executePlainSQL("DROP TABLE skin");
    executePlainSQL("DROP TABLE gun");
    executePlainSQL("DROP TABLE plays");
    executePlainSQL("DROP TABLE agent");
    executePlainSQL("DROP TABLE player");
    executePlainSQL("DROP TABLE team");
    executePlainSQL("DROP TABLE match");
    executePlainSQL("DROP TABLE gamemode");
    executePlainSQL("DROP TABLE map");
    oci_commit($db_conn);
    echo "<br>Cleared all tables. Click on 'Load Tables' to recreate the tables. <br>";
}

function startUp() {
    global $db_conn;

    // player
    executePlainSQL("CREATE TABLE player (username char(20) NOT NULL PRIMARY KEY , rank char(20), acct_level int)");
    // team
    executePlainSQL("CREATE TABLE team (team_name char(20) NOT NULL PRIMARY KEY , num_of_members char(20) NOT NULL)");
    // skin
    executePlainSQL("CREATE TABLE skin (skin_name char(20) NOT NULL PRIMARY KEY , price int)");
    // gun
    executePlainSQL("CREATE TABLE gun (gun_name char(20) NOT NULL PRIMARY KEY , gun_type char(20), c_cost int)");
    // gameMode
    executePlainSQL("CREATE TABLE gamemode (gamemode_name char(20) NOT NULL PRIMARY KEY , num_of_players int)");
    // map
    executePlainSQL("CREATE TABLE map (map_name char(20) NOT NULL PRIMARY KEY , numOfSites int)");
    // agent
    executePlainSQL("CREATE TABLE agent (agent_name char(20) NOT NULL PRIMARY KEY , role char(20))");
    // match
    executePlainSQL("CREATE TABLE match (match_ID int NOT NULL PRIMARY KEY,
                                                score char(5) NOT NULL, 
                                                gamemode_name char(20) NOT NULL,
                                                map_name char(20) NOT NULL ,
                                                duration int NOT NULL,
                                                FOREIGN KEY (gamemode_name) REFERENCES gamemode(gamemode_name) ON DELETE CASCADE,
                                                FOREIGN KEY (map_name) REFERENCES map(map_name) ON DELETE CASCADE)");
    // ability
    executePlainSQL("CREATE TABLE ability (ability_name char(20) NOT NULL, agent_name char(20) NOT NULL,
                                                    PRIMARY KEY (ability_name, agent_name),
                                                    FOREIGN KEY (agent_name) REFERENCES agent(agent_name) ON DELETE CASCADE)");
    // passive
    executePlainSQL("CREATE TABLE passive (ability_name char(20) NOT NULL, agent_name char(20) NOT NULL,
                                                    PRIMARY KEY (ability_name, agent_name),
                                                    FOREIGN KEY (ability_name, agent_name) REFERENCES ability(ability_name, agent_name) ON DELETE CASCADE)");
    // active
    executePlainSQL("CREATE TABLE active (ability_name char(20) NOT NULL, agent_name char(20) NOT NULL, c_cost int,
                                                    PRIMARY KEY (ability_name, agent_name),
                                                    FOREIGN KEY (ability_name, agent_name) REFERENCES ability(ability_name, agent_name) ON DELETE CASCADE)");
    // owns
    executePlainSQL("CREATE TABLE owns (username char(20) NOT NULL, skin_name char(20) NOT NULL,
                                                    PRIMARY KEY (username, skin_name),
                                                    FOREIGN KEY (username) REFERENCES player(username) ON DELETE CASCADE,
                                                    FOREIGN KEY (skin_name) REFERENCES skin(skin_name) ON DELETE CASCADE)");
    // for
    executePlainSQL("CREATE TABLE for_gun (skin_name char(20) NOT NULL, gun_name char(20) NOT NULL,
                                                    PRIMARY KEY (skin_name, gun_name),
                                                    FOREIGN KEY (gun_name) REFERENCES gun(gun_name) ON DELETE CASCADE)");
    // plays
    executePlainSQL("CREATE TABLE plays (username char(20) NOT NULL, agent_name char(20) NOT NULL, match_ID int NOT NULL,
                                                    PRIMARY KEY (username, agent_name, match_ID),
                                                    FOREIGN KEY (username) REFERENCES player(username) ON DELETE CASCADE,
                                                    FOREIGN KEY (agent_name) REFERENCES agent(agent_name) ON DELETE CASCADE,
                                                    FOREIGN KEY (match_ID) REFERENCES match(match_ID) ON DELETE CASCADE)");
    // isOn
    executePlainSQL("CREATE TABLE isOn (username char(20) NOT NULL, team_name char(20) NOT NULL,
                                                    PRIMARY KEY (username, team_name),
                                                    FOREIGN KEY (username) REFERENCES player(username) ON DELETE CASCADE,
                                                    FOREIGN KEY (team_name) REFERENCES team(team_name) ON DELETE CASCADE)");

    oci_commit($db_conn);
    echo "<br>Created Tables. <br>";
    insertPreData();
}

// inserts some example players, teams, and matches as well as gamemodes, maps, etc.
// SHOULD ALWAYS RUN AFTER startUp()
function insertPreData() {
    global $db_conn;

    // players
    executePlainSQL("BEGIN
                        INSERT INTO player VALUES ('namoraeh#rae', 'Silver_II', 254);
                        INSERT INTO player VALUES ('mango#tango', 'Platinum_I', 356);
                        INSERT INTO player VALUES ('blueberry#xiao', 'Silver_II', 280);
                        INSERT INTO player VALUES ('strawberry#3864', 'Gold_III', 86);
                        INSERT INTO player VALUES ('spaghetti#6969', 'Iron_III', 45);
                        INSERT INTO player VALUES ('spaghett#6969', 'Iron_III', 45);
                        INSERT INTO player VALUES ('fruittart#1111', 'Silver_II', 45);
                        INSERT INTO player VALUES ('cake#1111', 'Silver_II', 45);
                        INSERT INTO player VALUES ('creampufft#1111', 'Silver_II', 45);
                    END;");

    // teams
    executePlainSQL("BEGIN
                        INSERT INTO team (team_name, num_of_members) VALUES ('Fruits', 3);
                        INSERT INTO team (team_name, num_of_members) VALUES ('Team Name Pending', 2);
                        INSERT INTO team (team_name, num_of_members) VALUES ('Sweets', 3);
                    END;");

    // isOn
    executePlainSQL("BEGIN
                        INSERT INTO isOn VALUES ('mango#tango', 'Fruits');
                        INSERT INTO isOn VALUES ('strawberry#3864', 'Fruits');
                        INSERT INTO isOn VALUES ('blueberry#xiao', 'Fruits');
                        INSERT INTO isOn VALUES ('namoraeh#rae', 'Team Name Pending');
                        INSERT INTO isOn VALUES ('spaghetti#6969', 'Team Name Pending');
                        INSERT INTO isOn VALUES ('fruittart#1111', 'Sweets');
                        INSERT INTO isOn VALUES ('cake#1111', 'Sweets');
                        INSERT INTO isOn VALUES ('creampufft#1111', 'Sweets');
                    END;");

    // map
    executePlainSQL("BEGIN
                        INSERT INTO map VALUES ('Haven', 3);
                        INSERT INTO map VALUES ('Ascent', 2);
                        INSERT INTO map VALUES ('Bind', 2);
                    END;");

    // gamemode
    executePlainSQL("BEGIN
                        INSERT INTO gamemode VALUES ('Unrated', 10);
                        INSERT INTO gamemode VALUES ('Competitive', 10);
                        INSERT INTO gamemode VALUES ('Deathmatch', 14);
                    END;");

    // match
    executePlainSQL("BEGIN
                        INSERT INTO match VALUES (12973, '13-06', 'Unrated', 'Bind', 45);
                        INSERT INTO match VALUES (12974, '10-13', 'Competitive', 'Haven', 60);
                        INSERT INTO match VALUES (12975, '22-40', 'Deathmatch', 'Ascent', 10);
                    END;");

    // gun
    executePlainSQL("BEGIN
                        INSERT INTO gun VALUES ('Vandal', 'Rifle', 2900);
                        INSERT INTO gun VALUES ('Phantom', 'Rifle', 2900);
                        INSERT INTO gun VALUES ('Guardian', 'Rifle', 2250);
                        INSERT INTO gun VALUES ('Spectre', 'SMG', 1600);
                        INSERT INTO gun VALUES ('Stinger', 'SMG', 1100);
                        INSERT INTO gun VALUES ('Frenzy', 'Pistol', 450);
                        INSERT INTO gun VALUES ('Sheriff', 'Pistol', 800);
                        INSERT INTO gun VALUES ('Classic', 'Pistol', 0);
                        INSERT INTO gun VALUES ('Bucky', 'Shotgun', 850);
                        INSERT INTO gun VALUES ('Judge', 'Shotgun', 1850);
                    END;");

    // skin
    executePlainSQL("BEGIN
                        INSERT INTO skin VALUES ('Silvanus', 1275);
                        INSERT INTO skin VALUES ('Prime', 1775);
                        INSERT INTO skin VALUES ('Smite', 875);
                    END;");

    // owns
    executePlainSQL("BEGIN
                        INSERT INTO owns VALUES ('namoraeh#rae', 'Prime');
                        INSERT INTO owns VALUES ('strawberry#3864', 'Prime');
                        INSERT INTO owns VALUES ('blueberry#xiao', 'Smite');
                    END;");

    // for
    executePlainSQL("BEGIN
                        INSERT INTO for_gun VALUES ('Prime', 'Vandal');
                        INSERT INTO for_gun VALUES ('Prime', 'Frenzy');
                        INSERT INTO for_gun VALUES ('Smite', 'Spectre');
                    END;");

    // agent
    executePlainSQL("BEGIN
                        INSERT INTO agent VALUES ('Phoenix', 'Duelist');
                        INSERT INTO agent VALUES ('Breach', 'Initiator');
                        INSERT INTO agent VALUES ('Gekko', 'Initiator');
                    END;");

    // ability
    executePlainSQL("BEGIN
                        INSERT INTO ability VALUES ('Flashpoint', 'Breach');
                        INSERT INTO ability VALUES ('Hot Hands', 'Phoenix');
                        INSERT INTO ability VALUES ('Buddy Orb', 'Gekko');
                    END;");

    // active
    executePlainSQL("BEGIN
                        INSERT INTO active VALUES ('Flashpoint', 'Breach', 250);
                        INSERT INTO active VALUES ('Hot Hands', 'Phoenix', 0);
                    END;");

    // passive
    executePlainSQL("BEGIN
                        INSERT INTO passive VALUES ('Buddy Orb', 'Gekko');
                    END;");
    
    // plays
    executePlainSQL("BEGIN
                        INSERT INTO plays VALUES ('namoraeh#rae', 'Breach', 12973);
                        INSERT INTO plays VALUES ('blueberry#xiao', 'Gekko', 12973);
                        INSERT INTO plays VALUES ('blueberry#xiao', 'Phoenix', 12974);
                        INSERT INTO plays VALUES ('blueberry#xiao', 'Gekko', 12975);
                    END;");


    oci_commit($db_conn);

    echo "<br>Added Data. <br>";
    printTables();
}

function printTables() {
    echo "<br> Players: <br>";
    printPlayerResult(executePlainSQL("SELECT * FROM player"));
    echo "<br> Teams: <br>";
    printTeamResult(executePlainSQL("SELECT * FROM team"));
    echo "<br> isOn: <br>";
    printOwnsResult(executePlainSQL("SELECT * FROM isOn"));
    echo "<br> Maps: <br>";
    printResult(executePlainSQL("SELECT * FROM map"));
    echo "<br> Gamemodes: <br>";
    printResult(executePlainSQL("SELECT * FROM gamemode"));
    echo "<br> Matches: <br>";
    printMatchResult(executePlainSQL("SELECT * FROM match"));
    echo "<br> Guns: <br>";
    printPlayerResult(executePlainSQL("SELECT * FROM gun"));
    echo "<br> Skins: <br>";
    printResult(executePlainSQL("SELECT * FROM skin"));
    echo "<br> owns: <br>";
    printOwnsResult(executePlainSQL("SELECT * FROM owns"));
    echo "<br> for: <br>";
    printOwnsResult(executePlainSQL("SELECT * FROM for_gun"));
    echo "<br> Agents: <br>";
    printResult(executePlainSQL("SELECT * FROM agent"));
    echo "<br> Abilities: <br>";
    printResult(executePlainSQL("SELECT * FROM ability"));
    echo "<br> Actives: <br>";
    printResult(executePlainSQL("SELECT * FROM active"));
    echo "<br> Passives: <br>";
    printResult(executePlainSQL("SELECT * FROM passive"));
    echo "<br> plays: <br>";
    printPlayerResult(executePlainSQL("SELECT * FROM plays"));
}



// HANDLE ALL POST ROUTES
// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
function handlePOSTRequest() {
    if (connectToDB()) {
        if (array_key_exists('resetTablesRequest', $_POST)) {
            dropTables();
        } else if (array_key_exists('loadTablesRequest', $_POST)) {
            startUp();
        } else if (array_key_exists('insertQueryRequest', $_POST)) {
            handleInsertRequest();
        } else if (array_key_exists('insertPlayerRequest', $_POST)) {
            handleInsertPlayer();
        } else if (array_key_exists('insertTeamRequest', $_POST)) {
            handleInsertTeam();
        } else if (array_key_exists('insertMatchRequest', $_POST)) {
            handleInsertMatch();
        } else if (array_key_exists('printDataRequest', $_POST)) {
            printTables();
        }

        disconnectFromDB();
    }
}

// HANDLE ALL GET ROUTES
// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
function handleGETRequest() {
    if (connectToDB()) {
        if (array_key_exists('printDataRequest', $_GET)) {
            printTables();
        }

        disconnectFromDB();
    }
}

if (isset($_POST['submit'])) {
    handlePOSTRequest();
 } else if (isset($_GET['submit'])) {
    handleGETRequest();
}
?>
</body>
</html>