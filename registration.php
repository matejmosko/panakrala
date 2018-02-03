<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("db.php");

$newGuest = $_POST;
dbNewGuest($newGuest);
//addGuest($newGuest);

function addGuest($newGuest)
{
    saveFiles($newGuest);
}

function dbNewGuest($newGuest)
{
  $conn = setupDB();
  $project = $newGuest['project'];
  $event = $newGuest['event'];
  $name = $newGuest['name'];
  $email = $newGuest['email'];
  $message = $newGuest['message'];

  $sql = "INSERT INTO events_guests (project, event, name, email, message) VALUES ('$project', '$event', '$name', '$email', '$message')";

  if (mysqli_query($conn, $sql)) {
      echo "Úspešne ste zaregistrovali tím ".$name." na ".$event;
  } else {
      echo "Error: " . $sql . "<br>" . mysqli_error($conn);
  }

  mysqli_close($conn);
}

function saveFiles($newGuest)
{
    $guestList = [];
    $dir = 'data/projects/'.$_POST["project"].'/events/'.$_POST["event"];
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
    if (!file_exists($dir.'/registration.json')) {
        $data = "";
    } else {
        $data = file_get_contents($dir.'/registration.json');
    }
    if ($data != "") {
        $guestList = json_decode($data);
        $guestCount = count($guestList);
        $guestList[$guestCount] = $newGuest;
    } else {
        $guestList[0] = $newGuest;
    }

    file_put_contents($dir.'/registration.json', json_encode($guestList));
    print_r($guestList);
}
