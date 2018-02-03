<?php
require_once(__DIR__."/kamosko-config.php");

function setupDB()
{
    // Create connection
    $conn = new mysqli($GLOBALS['servername'], $GLOBALS['username'], $GLOBALS['password'], $GLOBALS['dbname']);

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

function eventGetGuests($eventId)
{
    $conn = setupDB();
    $sql = "SELECT * FROM events_guests WHERE event='$eventId'";
    $result = $conn->query($sql);
    $output = "<table>";
    $output .= "<thead><tr><th></th><th>Registrované tímy</th></tr></thead>";
    $output .= "<tbody>";

    if (mysqli_num_rows($result) > 0) {
        // output data of each row
        $i = 1;
        while ($row = mysqli_fetch_assoc($result)) {
            $output .= "<tr><td>".$i."</td><td>" . $row["name"]. "</td></tr>";
            $i++;
        }
    }
    $output .= "</tbody>";
    $output .= "</table>";
    return $output;
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
            $output .= "<tr><td>".$i."</td><td>".date($row['timestamp'])."</td><td><strong>" . $row["name"]. "</strong></td><td>" . $row["email"]. "</td><td>".$row["message"]."</td><td><a href='' class='delete'>X</a></td></tr>";
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
