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
<h1>Update New</h1>

<label for="insertOptions">Select an entity to update: </label>
<select id="insertOptions">
    <option value="playerOption">Player</option>
    <option value="teamOption">Team</option>
    <option value="matchOption">Match</option>
</select>

<div id="playerOption" class="vis">
    <h2>Update a Player</h2>
    <form method="POST" action="update.php"> <!--refresh page when submitted-->
        <!-- i think we need to only do player -->
        <input type="hidden" id="updatePlayerRequest" name="updatePlayerRequest">
        Old Username: <input type="text" name="username"> <br /><br />
        New Username: <input type="text" name="newusername"> <br /><br />
        New Rank: <input type="text" name="rank"> <br /><br />
        New Account Level: <input type="text" name="acctlevel"> <br /><br />

        <input type="submit" value="Update" name="updateSubmit"></p>
    </form>

    <hr />

    <br />
    <br />

</div>
<div id="teamOption" class="inv">

    <h2>Update Team</h2>
    <form method="POST" action="update.php"> <!--refresh page when submitted-->
        <!-- i think we need to only do team -->
        <input type="hidden" id="updateTeamRequest" name="updateTeamRequest">
        Old Team Name: <input type="text" name="oldTeamName"> <br /><br />
        New Team Name: <input type="text" name="newTeamName"> <br /><br />
        Number of Members: <input type="text" name="numOfMembers"> <br /><br />
        Player Names: <br />
        (if you want a player to stay on this team, you must include them in one of the fields below.) <br /><br />
        <input type="text" name="player1" required="true"> <br />
        <input type="text" name="player2"> <br />
        <input type="text" name="player3"> <br />
        <input type="text" name="player4"> <br />
        <input type="text" name="player5"> <br /><br />


        <input type="submit" value="Update" name="updateSubmit"></p>
    </form>

    <hr />

</div>
<div id="matchOption" class="inv">


    <h2>Update Match</h2>
    <form method="POST" action="update.php"> <!--refresh page when submitted-->
        <input type="hidden" id="updateMatchRequest" name="updateMatchRequest">
        Old Match ID: <input type="text" name="oldID">  <br /><br />
        New Match ID: <input type="text" name="newID">  <br /><br />
        Match Score: <input type="text" name="score"><br /><br />
        GameMode: <input type="text" name="gamemode">   <br /><br />
        Map Name: <input type="text" name="mapname"> <br /><br />
        Duration: <input type="text" name="duration"> <br /><br />
        <input type="submit" value="Update" name="updateSubmit"></p>
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

function handleUpdateMatch() {
    global $db_conn;

    $old_id = $_POST['oldID'];
    $new_id = $_POST['newID'];
    $new_score = $_POST['score'];
    $new_gamemode = $_POST['gamemode'];
    $new_map = $_POST['mapname'];
    $new_duration = $_POST['duration'];

    if ($new_score) {
        executePlainSQL("UPDATE match SET score='" . $new_score . "' WHERE match_ID='" . $old_id . "'");
    }
    if ($new_gamemode) {
        executePlainSQL("UPDATE match SET gamemode_name='" . $new_gamemode . "' WHERE match_ID='" . $old_id . "'");
    }
    if ($new_map) {
        executePlainSQL("UPDATE match SET map_name='" . $new_map . "' WHERE match_ID='" . $old_id . "'");
    }
    if ($new_duration) {
        executePlainSQL("UPDATE match SET duration='" . $new_duration . "' WHERE match_ID='" . $old_id . "'");
    }
    if ($new_id) {
        $matches_str = "SELECT username, agent_name FROM plays WHERE match_ID='" . $old_id ."'";
        $matches = oci_parse($db_conn, $matches_str);
        oci_execute($matches);

        executePlainSQL("DELETE FROM plays WHERE match_ID='" . $old_id . "'");
        executePlainSQL("UPDATE match SET match_ID='" . $new_id . "' WHERE match_ID='" . $old_id . "'");

        while (oci_fetch($matches)) {
            executePlainSQL("INSERT INTO plays VALUES('" . oci_result($matches, 'USERNAME') . "', '" . oci_result($matches, 'AGENT_NAME') . "', '" . $new_id . "')");
        }

    } else {
        $new_id = $old_id;
    }

    oci_commit($db_conn);

    echo "<br> Updated Match: " . $old_id . " to: " ;
    printMatchResult(executePlainSQL("SELECT * FROM match WHERE match_ID='" . $new_id . "'"));

}

function handleUpdateTeam() {
    global $db_conn;

    $old_tname = $_POST['oldTeamName'];
    $new_tname = $_POST['newTeamName'];
    $new_nummembers = $_POST['numOfMembers'];
    $new_p1 = $_POST['player1'];
    $new_p2 = $_POST['player2'];
    $new_p3 = $_POST['player3'];
    $new_p4 = $_POST['player4'];
    $new_p5 = $_POST['player5'];

    if ($new_nummembers) {
        executePlainSQL("UPDATE team SET num_of_members='" . $new_nummembers . "' WHERE team_name='" . $old_tname . "'");
    }

    if (!$new_tname) {
        $new_tname = $old_tname;
    }

    executePlainSQL("DELETE FROM isOn WHERE team_name='" . $old_tname . "'");
    executePlainSQL("UPDATE team SET team_name='" . $new_tname . "' WHERE team_name='" . $old_tname . "'");

    $playerUpdate = false;

    // update isOn
    // recreate isOn tuples with new_tname
    // IMPORTANT: MUST INCLUDE OLD PLAYERS IF YOU WANT THEM TO STAY ON THE TEAM
    if ($new_p1) {
        executePlainSQL("INSERT INTO isOn(username, team_name) VALUES('" . $new_p1 . "', '" . $new_tname . "')");
        $playerUpdate = true;
    }
    if ($new_p2) {
        executePlainSQL("INSERT INTO isOn(username, team_name) VALUES('" . $new_p2 . "', '" . $new_tname . "')");
    }
    if ($new_p3) {
        executePlainSQL("INSERT INTO isOn(username, team_name) VALUES('" . $new_p3 . "', '" . $new_tname . "')");
    }
    if ($new_p4) {
        executePlainSQL("INSERT INTO isOn(username, team_name) VALUES('" . $new_p4 . "', '" . $new_tname . "')");
    }
    if ($new_p5) {
        executePlainSQL("INSERT INTO isOn(username, team_name) VALUES('" . $new_p5 . "', '" . $new_tname . "')");
    }
    if ($playerUpdate) {
        executePlainSQL("UPDATE isOn SET team_name='" . $new_tname . "' WHERE team_name='" . $old_tname . "'");
        echo "<br> Players have been updated: <br>";
        printResult(executePlainSQL("SELECT username FROM isON WHERE team_name='" . $new_tname . "'"));
    }

    oci_commit($db_conn);

    echo "<br> Team ". $old_tname ." has been updated to: ";
    printTeamResult(executePlainSQL("SELECT * FROM team WHERE team_name='" . $new_tname . "'"));

}

function handleUpdatePlayer() {
    global $db_conn;

    $old_pname = $_POST['username'];
    $new_pname = $_POST['newusername'];
    $new_rank = $_POST['rank'];
    $new_level = $_POST['acctlevel'];

    // you need the wrap the old name and new name values with single quotations
    if ($new_rank) {
        executePlainSQL("UPDATE player SET rank='" . $new_rank . "' WHERE username='" . $old_pname . "'");
    }
    if ($new_level) {
        executePlainSQL("UPDATE player SET acct_level='" . $new_level . "' WHERE username='" . $old_pname . "'");
    }
    if ($new_pname) {
        // updates isOn by deleting any tuples with old name, and re-adding new tuples with new name
        $team = executePlainSQL("SELECT team_name FROM isOn WHERE username='" . $old_pname ."'");
        $team_ = OCI_Fetch_Array($team, OCI_BOTH);

        $skins_str = "SELECT skin_name FROM owns WHERE username='" . $old_pname ."'";
        $skins = oci_parse($db_conn, $skins_str);
        oci_execute($skins);

        $matches_str = "SELECT agent_name, match_ID FROM plays WHERE username='" . $old_pname ."'";
        $matches = oci_parse($db_conn, $matches_str);
        oci_execute($matches);

        executePlainSQL("DELETE FROM isOn WHERE username='" . $old_pname . "'");
        executePlainSQL("DELETE FROM owns WHERE username='" . $old_pname . "'");
        executePlainSQL("DELETE FROM plays WHERE username='" . $old_pname . "'");

        executePlainSQL("UPDATE player SET username='" . $new_pname . "' WHERE username='" . $old_pname . "'");

        if ($team_[0]) {
            executePlainSQL("INSERT INTO isOn VALUES('" . $new_pname . "', '" . $team_[0] . "')");
        }

        
        while (oci_fetch($skins)) {
            executePlainSQL("INSERT INTO owns VALUES('" . $new_pname . "', '" . oci_result($skins, 'SKIN_NAME') . "')");
        }

        while (oci_fetch($matches)) {
            executePlainSQL("INSERT INTO plays VALUES('" . $new_pname . "', '" . oci_result($matches, 'AGENT_NAME') . "', '" . oci_result($matches, 'MATCH_ID') . "')");
        }

    } else {
        $new_pname = $old_pname;
    }

    oci_commit($db_conn);

    echo "<br> Updated Player: " . $old_pname . " to: " ;

    printResult(executePlainSQL("SELECT * FROM player WHERE username='" . $new_pname . "'"));
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
        if (array_key_exists('updatePlayerRequest', $_POST)) {
            handleUpdatePlayer();
        } else if (array_key_exists('updateTeamRequest', $_POST)) {
            handleUpdateTeam();
        } else if (array_key_exists('updateMatchRequest', $_POST)) {
            handleUpdateMatch();
        }

        disconnectFromDB();
    }
}


if (isset($_POST['updateSubmit'])) {
    handlePOSTRequest();
}
?>

</body>
</html>