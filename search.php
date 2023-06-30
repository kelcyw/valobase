<html>
<head>
    <title>CPSC 304 Group 9 Project - Valobase</title>
</head>

<style>
    .textInput {
        resize: none;
    }
    .inv {
        display: none;
    }

    .divInv {
        display: none;
    }

    .divVis {
        display: inline-block;
    }
</style>

<body>
<h1>Search Existing</h1>
<hr/>

</body>


<div class = "selectPart">
    <!-- SELECT PART will display all player attributes, (as long as player satisfies conditions) -->
    <p> Select from: </p>
    <p>
    <form method="POST" action="search.php">

        <select id="selectOptions" name="selectOptions">
            <option value="player">Player</option>
            <option value="team">Team</option>
            <option value="match">Match</option>
            <option value="gun">Gun</option>
            <option value="skin">Skin</option>
            <option value="agent">Agent</option>
            <option value="ability">Ability</option>
            <option value="passive">Passive</option>
            <option value="active">Active</option>
            <option value="owns">Owns</option>
            <option value="for">For</option>
            <option value="plays">Plays</option>
            <option value="isOn">IsOn</option>
        </select>
        </p>

        <p> Where: </p>
        <input type="hidden" id="selectRequest" name="selectRequest">
        <input type="text" name="att1" required='true'> <input type="text" name="att1val" required='true'><br /><br />
        <input type="text" name="att2"> <input type="text" name="att2val"><br /><br />
        <input type="text" name="att3"> <input type="text" name="att3val"><br /><br />
        <input type="text" name="att4"> <input type="text" name="att4val"><br /><br />
        <input type="text" name="att5"> <input type="text" name="att5val"><br /><br />
        <input type="submit" value="Submit" name="selectSubmit"></p>
    </form>
</div>

<hr >
<div class="projectPart">
    <!-- PROJECT PART will display all players, with some attributes -->
    <p> Project: </p>
    <form method="POST" action="search.php">
        <p>
            From:
            <select id="projectOptions" name="projectOptions">
                <option value="player">Player</option>
                <option value="team">Team</option>
                <option value="match">Match</option>
                <option value="gun">Gun</option>
                <option value="skin">Skin</option>
                <option value="agent">Agent</option>
                <option value="ability">Ability</option>
                <option value="passive">Passive</option>
                <option value="active">Active</option>
                <option value="owns">Owns</option>
                <option value="for">For</option>
                <option value="plays">Plays</option>
                <option value="isOn">IsOn</option>
            </select>
        </p>


        <p>Select attributes: </p>
        <input type="hidden" id="projectRequest" name="projectRequest">
        <input type="text" name="att1"> <br /><br />
        <input type="text" name="att2"> <br /><br />
        <input type="text" name="att3"> <br /><br />
        <input type="text" name="att4"> <br /><br />
        <input type="text" name="att5"> <br /><br />
        <input type="submit" value="Submit" name="projectSubmit"></p>


    </form>

</div>
<hr />
<h2>Default Searches</h2>
<div class="joinPart">


    <form method="POST" action="search.php">

        <p>
            Find all players who have played a match on:
            <select id="mapOptions" name="joinOptions">
                <option value="Ascent">Ascent</option>
                <option value="Bind">Bind</option>
                <option value="Breeze">Breeze</option>
                <option value="Fracture">Fracture</option>
                <option value="Haven">Haven</option>
                <option value="Icebox">Icebox</option>
                <option value="Lotus">Lotus</option>
                <option value="Pearl">Pearl</option>
                <option value="Split">Split</option>
            </select>
        </p>
        <input type="hidden" id="joinRequest" name="joinRequest">
        <input type="submit" value="Submit" name="joinSubmit"></p>
    </form>
</div>

<div class="aggGroupPart">
    <p>
    <form method="POST" action="search.php">
        <input type="hidden" id="aggGroupRequest" name="aggGroupRequest">
        Count the number of matches played on the map:
        <select id="aggGroupOptions" name='aggGroupOptions'>
            <option>Ascent</option>
            <option>Bind</option>
            <option>Breeze</option>
            <option>Fracture</option>
            <option>Haven</option>
            <option>Icebox</option>
            <option>Lotus</option>
            <option>Pearl</option>
            <option>Split</option>
        </select>
        (grouped by gamemode)
        </p>


        <input type="submit" value="Submit" name="aggGroupSubmit"></p>
    </form>
</div>

<div class="aggHavePart">
    <form method="POST" action="search.php">
        <p>Find usernames of all players that have played the agent:
            <select id="aggHaveOptions" name="aggHaveOptions">
                <option>Astra</option>
                <option>Breach</option>
                <option>Brimstone</option>
                <option>Chamber</option>
                <option>Cypher</option>
                <option>Gekko</option>
                <option>Jett</option>
                <option>KAYO</option>
                <option>Killjoy</option>
                <option>Neon</option>
                <option>Omen</option>
                <option>Phoenix</option>
                <option>Raze</option>
                <option>Reyna</option>
                <option>Sage</option>
                <option>Skye</option>
                <option>Sova</option>
                <option>Viper</option>
                <option>Yoru</option>
            </select>
            more than once.
        </p>

        <input type="hidden" id="aggHaveRequest" name="aggHaveRequest">
        <input type="submit" value="Submit" name="aggHaveSubmit"></p>
    </form>
</div>

<div class="nestedAgg">
    <p>
    <form method="POST" action="search.php">
        <input type="hidden" id="aggNestedRequest" name="aggNestedRequest">
        Select the gun type(s) with the highest average credit cost:
        </p>

        <p>

            <input type="submit" value="Submit" name="aggNestedSubmit"></p>
    </form>
    </p>
</div>

<div class="Division">
    <p>Find the usernames of players who have played matches on ALL maps: </p>
    <form method="POST" action="search.php">
        <input type="hidden" id="divisionRequest" name="divisionRequest">
        <input type="submit" value="Submit" name="divisionSubmit"></p>
    </form>
</div>

<form method="POST" action="index.php">
    <input type="hidden">
    <p><input type="submit" value="Return to Home" name="home"></p>
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
        echo "(";
        echo $row[1];
        echo ", ";
        echo $row[2];
        echo ")";
        echo "<br>";
    }

    echo "</table>";
}

function printResultProject($result) { //prints results for projection
    echo "<table>";

    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        echo "[";
        echo $row[0]; //or just use "echo $row[0]"
        echo ", ";
        echo $row[1];
        echo ", ";
        echo $row[2];
        echo ", ";
        echo $row[3];
        echo ", ";
        echo $row[4];
        echo "]";
        echo ", ";
        echo "<br>";
    }

    echo "</table>";
}

function printResultPlain($result) { //prints results from a statement without any additional formatting
    echo "<table>";

    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        echo $row[0]; //or just use "echo $row[0]"
        echo "<br>";
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


// ----------------------------------------------- MY FUNCTIONS START HERE


function handleSelectRequest() {
    $table = $_POST['selectOptions'];

    $att1 = $_POST['att1'];
    $att2 = $_POST['att2'];
    $att3 = $_POST['att3'];
    $att4 = $_POST['att4'];
    $att5 = $_POST['att5'];

    $att1val = $_POST['att1val'];
    $att2val = $_POST['att2val'];
    $att3val = $_POST['att3val'];
    $att4val = $_POST['att4val'];
    $att5val = $_POST['att5val'];

    $str_to_execute = "SELECT * FROM " .$table." WHERE " . $att1 ." = '" . $att1val . "'";
    // concatenate stuff onto str_to_execute

    if ($att2) {
        $str_to_execute = $str_to_execute . "AND " . $att2 . "= '" . $att2val . "'";
    }
    if ($att3) {
        $str_to_execute = $str_to_execute . "AND"  . $att2 . "= '" . $att3val . "'";
    }
    if ($att4) {
        $str_to_execute = $str_to_execute . "AND " . $att3 . "= '" . $att4val . "'";
    }
    if ($att5) {
        $str_to_execute = $str_to_execute . "AND " . $att4 . "= '" . $att5val . "'";
    }

    // echo $str_to_execute . "<br>";
    printResultProject(executePlainSQL($str_to_execute));
}

// Works for any number of attributes
function handleProjectRequest() {
    global $db_conn;

    $table = $_POST['projectOptions'];

    $att1 = $_POST['att1'];
    $att2 = $_POST['att2'];
    $att3 = $_POST['att3'];
    $att4 = $_POST['att4'];
    $att5 = $_POST['att5'];

    $str_to_execute = "SELECT " . $att1 ."";
    // concatenate stuff onto str_to_execute

    if ($att2) {
        $str_to_execute = $str_to_execute . ", " . $att2 . "";
    }
    if ($att3) {
        $str_to_execute = $str_to_execute . ", " . $att3 . "";
    }
    if ($att4) {
        $str_to_execute = $str_to_execute . ", " . $att4 . "";
    }
    if ($att5) {
        $str_to_execute = $str_to_execute . ", " . $att5 . "";
    }

    $str_to_execute = $str_to_execute . " FROM " . $table . "";

    echo $str_to_execute . "<br>";
    printResultProject(executePlainSQL($str_to_execute));

}


function handleJoinRequest() {
    global $db_conn;

    $map = $_POST['joinOptions'];
    echo $map;
    echo "<br> your answer: <br>";
    printResult(executePlainSQL("SELECT plays.username FROM plays
                                JOIN match ON plays.match_ID = match.match_ID
                                WHERE match.map_name= '". $map ."'"));

    echo "<br>";
    echo "matches:";
    printMatchResult((executePlainSQL("SELECT * FROM match")));
    echo "<br>";
    echo "plays:";
    echo "<br>";
    printResult((executePlainSQL("SELECT * FROM plays")));
}


function handleAggGroupRequest() {
    global $db_conn;

    $map = $_POST['aggGroupOptions'];
    echo $map;
    echo "<br>";

    printResult(executePlainSQL("SELECT COUNT(match_ID), gamemode_name FROM match WHERE map_name= '". $map ."' GROUP BY gamemode_name "));

}


function handleAggHaveRequest() {
    global $db_conn;

    $agent = $_POST['aggHaveOptions'];
    echo $agent;
    echo "<br>";

    printResult(executePlainSQL("SELECT DISTINCT p.username FROM plays p, match m WHERE p.match_ID=m.match_ID AND p.agent_name= '". $agent ."'"));

}

function handleAggNestedRequest() {
    global $db_conn;

    printResultPlain(executePlainSQL("SELECT g.gun_type
                                    FROM gun g
                                    GROUP BY g.gun_type
                                    HAVING AVG(g.c_cost) > (SELECT AVG(g2.c_cost)
                                                            FROM gun g2)"));

}

function handleDivisionRequest() {
    global $db_conn;

    printResult(executePlainSQL("SELECT player.username
                                    FROM player 
                                    WHERE NOT EXISTS
                                        (SELECT mp.map_name
                                            FROM map mp
                                            MINUS 
                                            (SELECT match.map_name
                                            FROM plays pl JOIN match ON pl.match_ID = match.match_ID
                                            WHERE pl.username = player.username))"));

}



function handlePOSTRequest() {
    if (connectToDB()) {
        if (array_key_exists('selectRequest', $_POST)) {
            handleSelectRequest();
        }
        if (array_key_exists('projectRequest', $_POST)) {
            echo "PROJECT result: <br>";
            handleProjectRequest();
        }
        if (array_key_exists('joinRequest', $_POST)) {
            echo "JOIN result: <br>";
            handleJoinRequest();
        }
        if (array_key_exists('aggGroupRequest', $_POST)) {
            echo "Aggregation with GROUP BY result: <br>";
            handleAggGroupRequest();

        }
        if (array_key_exists('aggHaveRequest', $_POST)) {
            echo "Aggregation with HAVING result: <br>";
            handleAggHaveRequest();

        }
        if (array_key_exists('aggNestedRequest', $_POST)) {
            echo "Nested Aggregation result:  <br>";
            handleAggNestedRequest();

        }
        if (array_key_exists('divisionRequest', $_POST)) {
            echo "Division result: <br>";
            handleDivisionRequest();

        }


        disconnectFromDB();
    }
}


if (isset($_POST['selectSubmit'])  ||
    isset($_POST['projectSubmit']) || isset($_POST['joinSubmit']) ||
    isset($_POST['aggGroupSubmit']) || isset($_POST['aggHaveSubmit']) ||
    isset($_POST['aggNestedSubmit']) || isset($_POST['divisionSubmit'])) {

    handlePOSTRequest();

}
?>


</html>

