<?php
require_once(__DIR__ . '/db.php');

switch ($_GET['script']) {
  case 'eventGetGuests':
    $guests = eventGetGuests($_GET['projectId'], $_GET['eventId']);
    echo json_encode($guests);
    break;

  default:
    $arr = [];
    echo json_encode($arr);
    break;
}
