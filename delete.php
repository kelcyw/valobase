<html>
    <head>
        <title>CPSC 304 Group 9 Project - Valobase</title>
    </head>

    <style>
    </style>

    <body>
        <h1>Delete Existing</h1>

        <div class = "sectionOne">
            <p>
                Choose an entity to delete:
            </p>

            
        </div>

    </body>


<hr />

    <h2>Select a Player for Deletion:</h2>
    <form method="POST" action="delete.php"> <!--refresh page when submitted-->
        <input type="hidden" id="deletePlayerRequest" name="deletePlayerRequest">
        Player Username: <input type="text" name="username"> <br /><br />
        

        <input type="submit" value="Delete" name="deleteSubmit"></p>
    </form>

    <hr />
    <h2>Select a Team for Deletion:</h2>
    <form method="POST" action="delete.php"> <!--refresh page when submitted-->
        <input type="hidden" id="deleteTeamRequest" name="deleteTeamRequest">
        Team Name: <input type="text" name="teamname"> <br /><br />
        

        <input type="submit" value="Delete" name="deleteSubmit"></p>
    </form>

    <hr />
    <h2>Select a Match for Deletion:</h2>
    <form method="POST" action="delete.php"> <!--refresh page when submitted-->
        <input type="hidden" id="deleteMatchRequest" name="deleteMatchRequest">
        Match ID: <input type="text" name="match_ID"> <br /><br />
        

        <input type="submit" value="Delete" name="deleteSubmit"></p>
    </form>



    
    <hr />
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
    echo "<br>Deleted: <br>";
    echo "<table>";

    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        echo $row[0];
        echo ", ";
        echo $row[1];
        echo ", ";
        echo $row[2];
        echo $row[3];
        echo $row[4];

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


// CODE STARTS HERE

function handleDeletePlayer() {
    global $db_conn;

    //getting username input
    $username = $_POST['username'];
    printResult(executePlainSQL("SELECT * FROM player WHERE username='$username'"));
    executePlainSQL("DELETE FROM player WHERE username='$username'");
    

    oci_commit($db_conn);
}

function handleDeleteTeam() {
    global $db_conn;

    //getting team name input
    $teamname = $_POST['teamname'];
    printResult(executePlainSQL("SELECT * FROM team WHERE team_name='$teamname'"));
    executePlainSQL("DELETE FROM team WHERE team_name='$teamname'");
    

    oci_commit($db_conn);
}

function handleDeleteMatch() {
    global $db_conn;

    // getting match ID input
    $match_ID = $_POST['match_ID'];
    printResult(executePlainSQL("SELECT * FROM match WHERE match_ID='$match_ID'"));
    executePlainSQL("DELETE FROM match WHERE match_ID='$match_ID'");


    oci_commit($db_conn);
}


// HANDLE ALL POST ROUTES
// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
function handlePOSTRequest() {
    if (connectToDB()) {
        if (array_key_exists('deletePlayerRequest', $_POST)) {
            handleDeletePlayer();
        } else if (array_key_exists('deleteTeamRequest', $_POST)) {
            handleDeleteTeam();
        } else if (array_key_exists('deleteMatchRequest', $_POST)) {
            handleDeleteMatch();
        }

        disconnectFromDB();
    }
}

// HANDLE ALL GET ROUTES
// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.


if (isset($_POST['deleteSubmit'])) {
    handlePOSTRequest();
} 
?>

</html>

