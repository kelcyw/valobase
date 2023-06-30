<html>
<head>
    <title>CPSC 304 Group 9 Project - Valobase</title>
</head>

<style>
    .inv {
        display: none;
    }

    .divInv {
        display: none;
    }

    .divVis {
        display: inline-block;
    }

    .textInput {
        resize: none;
    }

    .playerName {
        resize: none;
        margin: 3px 0px;
    }

    .skinSelector {
        margin: 3px 0px;
    }

    .gunSelector {
        margin: 3px 0px;
    }

</style>

<body>
<h1>Insert New</h1>

<label for="insertOptions">Select an entity to insert: </label>
<select id="insertOptions">
    <option value="playerOption">Player</option>
    <option value="teamOption">Team</option>
    <option value="matchOption">Match</option>
</select>

<div id="playerOption" class="vis">
    <h2>Insert a Player</h2>
    <form method="POST" action="insert.php"> <!--refresh page when submitted-->
        <!-- i think we need to only do player -->
        <input type="hidden" id="insertPlayerRequest" name="insertPlayerRequest">
        Player: <input type="text" name="username"> <br /><br />
        Rank: <input type="text" name="rank"> <br /><br />
        Level: <input type="text" name="level"> <br /><br />

        <input type="submit" value="Insert" name="insertSubmit"></p>
    </form>

    <hr />

    <br />
    <br />

</div>
<div id="teamOption" class="inv">

    <h2>Insert a Team</h2>
    <form method="POST" action="insert.php"> <!--refresh page when submitted-->
        <!-- i think we need to only do team -->
        <input type="hidden" id="insertTeamRequest" name="insertTeamRequest">
        Team Name: <input type="text" name="teamName"> <br /><br />
        Number of Players: <input type="text" name="numOfPlayers"> <br /><br />
        Player Names:<br /><br />
        <input type="text" name="player1" required="true"> <br />
        <input type="text" name="player2"> <br />
        <input type="text" name="player3"> <br />
        <input type="text" name="player4"> <br />
        <input type="text" name="player5"> <br /><br />


        <input type="submit" value="Insert" name="insertSubmit"></p>
    </form>

    <hr />

</div>
<div id="matchOption" class="inv">


    <h2>Insert a Match</h2>
    <form method="POST" action="insert.php"> <!--refresh page when submitted-->
        <!-- i think we need to only do player -->
        <input type="hidden" id="insertMatchRequest" name="insertMatchRequest">
        Match ID: <input type="text" name="id">  <br /><br />
        Match Score: <input type="text" name="score"><br /><br />
        GameMode: <input type="text" name="gamemode">   <br /><br />
        Map: <input type="text" name="map">   <br /><br />
        Duration: <input type="text" name="duration">   <br /><br />
        <input type="submit" value="Insert" name="insertSubmit"></p>
    </form>

    <hr />
</div>

<form method="POST" action="index.php">
    <input type="hidden">
    <p><input type="submit" value="Return to Home" name="home"></p>
</form>

<script>

    document
        .getElementById('insertOptions')
        .addEventListener('change', function () {
            'use strict';
            var vis = document.querySelector('.vis'),
                insertOptions = document.getElementById(this.value);
            if (vis !== null) {
                vis.className = 'inv';
            }
            if (insertOptions !== null ) {
                insertOptions.className = 'vis';
            }
        });

    document
        .getElementById('playerRankOptions')
        .addEventListener('change', function () {
            'use strict';
            var divVis = document.querySelector('.divVis'),
                playerRankOptions = document.getElementById(this.value);
            if (divVis !== null) {
                divVis.className = 'divInv';
            }
            if (playerRankOptions !== null ) {
                playerRankOptions.className = 'divVis';
            }
        });

    const checkbox = document.getElementById('onTeam');

    const box = document.getElementById('inputTeamName');

    checkbox.addEventListener('click', function handleClick() {
        if (checkbox.checked) {
            box.style.display = 'inline-block';
        } else {
            box.style.display = 'none';
        }
    });
</script>
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
        echo ", ";
        echo $row[3];
        echo ", ";
        echo $row[4];
        echo ")";
        echo "<br> ";
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

function printMatchResult($result) { //prints results from a select statement for match table
    echo "<table>";

    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        echo $row[0]; //or just use "echo $row[0]"
        echo "( ";
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

function handleInsertMatch() {
    global $db_conn;

    //Getting the values from user and insert data into the table
    $tuple = array (
        ":bind1" => $_POST['id'],
        ":bind2" => $_POST['score'],
        ":bind3" => $_POST['gamemode'],
        ":bind4" => $_POST['map'],
        ":bind5" => $_POST['duration']
    );

    $allTuples = array (
        $tuple
    );

    // adding the match
    executeBoundSQL("insert into match values (:bind1, :bind2, :bind3, :bind4, :bind5)", $allTuples);

    oci_commit($db_conn);

    echo "Added new match. Current matches in database: ";
    printMatchResult(executePlainSQL("SELECT * FROM match"));
}

function handleInsertTeam() {
    global $db_conn;

    // Getting the values from user and insert data into the table
    $tuple = array (
        ":bind1" => $_POST['teamName'],
        ":bind2" => $_POST['numOfPlayers'],
        ":bind3" => $_POST['player1'],
        ":bind4" => $_POST['player2'],
        ":bind5" => $_POST['player3'],
        ":bind6" => $_POST['player4'],
        ":bind7" => $_POST['player5']
    );

    $allTuples = array (
        $tuple
    );
    
    $p1 = $_POST['player1'];
    $p2 = $_POST['player2'];
    $p3 = $_POST['player3'];
    $p4 = $_POST['player4'];
    $p5 = $_POST['player5'];

    // adding the team
    executeBoundSQL("insert into team values (:bind1, :bind2)", $allTuples);

    // adding isOn
    if ($p1) {
        executeBoundSQL("insert into isOn values (:bind3, :bind1)", $allTuples);
    }
    if ($p2) {
        executeBoundSQL("insert into isOn values (:bind4, :bind1)", $allTuples);
    }
    if ($p3) {
        executeBoundSQL("insert into isOn values (:bind5, :bind1)", $allTuples);
    }
    if ($p4) {
        executeBoundSQL("insert into isOn values (:bind6, :bind1)", $allTuples);
    }
    if ($p5) {
        executeBoundSQL("insert into isOn values (:bind7, :bind1)", $allTuples);
    }

    oci_commit($db_conn);

    echo "Added new team. Current teams in database: ";
    printTeamResult(executePlainSQL("SELECT * FROM team"));
}

function handleInsertPlayer() {
    global $db_conn;

    //Getting the values from user and insert data into the table
    $tuple = array (
        ":bind1" => $_POST['username'],
        ":bind2" => $_POST['rank'],
        ":bind3" => $_POST['level']
    );

    $allTuples = array (
        $tuple
    );

    // adding the player
    executeBoundSQL("insert into player values (:bind1, :bind2, :bind3)", $allTuples);

    oci_commit($db_conn);

    echo "Added new player. Current players in database: ";
    printPlayerResult(executePlainSQL("SELECT * FROM player"));

}


// Count
function handleCountRequest() {
    global $db_conn;

    $result = executePlainSQL("SELECT Count(*) FROM player");

    if (($row = oci_fetch_row($result)) != false) {
        echo "<br> The number of tuples in demoTable: " . $row[0] . "<br>";
    }
}

// HANDLE ALL POST ROUTES
// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
function handlePOSTRequest() {
    if (connectToDB()) {
        if (array_key_exists('insertQueryRequest', $_POST)) {
            handleInsertRequest();
        } else if (array_key_exists('insertPlayerRequest', $_POST)) {
            handleInsertPlayer();
        } else if (array_key_exists('insertTeamRequest', $_POST)) {
            handleInsertTeam();
        } else if (array_key_exists('insertMatchRequest', $_POST)) {
            handleInsertMatch();
        }

        disconnectFromDB();
    }
}

if (isset($_POST['reset']) || isset($_POST['updateSubmit']) || isset($_POST['insertSubmit'])) {
    handlePOSTRequest();
}
?>

</body>
</html>