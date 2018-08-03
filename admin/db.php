<?php

require_once(__DIR__."/kamosko-config.php");

function createDB()
{
    $conn = new mysqli($GLOBALS['db']['servername'], $GLOBALS['db']['username'], $GLOBALS['db']['password']);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Create database
    $dbname = $GLOBALS['db']['dbname'];
    $sql = "CREATE DATABASE '$dbname'";
    if ($conn->query($sql) === true) {
        echo "Database created successfully";
    } else {
        echo "Error creating database: " . $conn->error;
    }

    $conn->close();
    createTables();
}

function createTables()
{
    $conn = setupDB();
    $sql = "CREATE TABLE events_guests (
      id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      event VARCHAR(30) NOT NULL,
      project VARCHAR(30) NOT NULL,
      email VARCHAR(50),
      name VARCHAR(50),
      message VARCHAR(255),
      regtime TIMESTAMP
    )";

    if ($conn->query($sql) === true) {
        echo "Table MyGuests created successfully";
    } else {
        echo "Error creating table: " . $conn->error;
    }

    $conn->close();
}

function setupDB()
{
    // Create connection
    $conn = new mysqli($GLOBALS['db']['servername'], $GLOBALS['db']['username'], $GLOBALS['db']['password'], $GLOBALS['db']['dbname']);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

function eventGuestCount($eventId)
{
    $conn = setupDB();
    $sql = "SELECT COUNT(*) FROM events_guests WHERE event='$eventId'";
    $result = $conn->query($sql);

    return $result->fetch_array()[0];
}

function eventGetGuests($projectId, $eventId)
{
    $conn = setupDB();
    $sql = "SELECT regtime, name FROM events_guests WHERE event='$eventId' AND project='$projectId'";
    $result = $conn->query($sql);
    $rows = [];
    while ($row = mysqli_fetch_array($result)) {
        $rows[] = $row;
    }
    return $rows;
}

function eventSendEmail()
{
}

function adminEventGetGuests($eventId)
{
    $conn = setupDB();
    $sql = "SELECT * FROM events_guests WHERE event='$eventId'";
    $result = $conn->query($sql);
    $output = "<table>";
    $output .= "<thead><tr><th>#</th><th>Time</th><th>Name</th><th>email</th><th>Message</th><th>Tools</th></tr></thead>";
    $output .= "<tbody>";

    if (mysqli_num_rows($result) > 0) {
        // output data of each row
        $i = 1;
        while ($row = mysqli_fetch_assoc($result)) {
            $output .= "<tr><td>".$i."</td><td>".date($row['regtime'])."</td><td><strong>" . $row["name"]. "</strong></td><td>" . $row["email"]. "</td><td>".$row["message"]."</td><td><a href='' class='delete'>X</a></td></tr>";
            $i++;
        }
    }
    $output .= "</tbody>";
    $output .= "</table>";
    return $output;
}

function adminEventRemoveGuest($guestId)
{
}
