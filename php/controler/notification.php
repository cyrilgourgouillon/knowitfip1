<?php
header('Content-Type: application/json');

ini_set('display_errors', 1);

require_once("../utils/dbconnect.php");
require_once('../utils/Feedback.php');

require_once("../modele/Notification.php");

$calledFunction = $_POST["function"];
$data = $_POST["data"];

// Push the $conn to the data
array_unshift($data, $conn);

//Call the function
call_user_func_array($calledFunction, $data);

function getNotificationByUser($conn, $idUser) {
    echo json_encode(Notification::getNotificationByUser($conn, $idUser), JSON_PRETTY_PRINT);
}

function seeNotification($conn, $idUser) {
    echo json_encode(Notification::seeNotification($conn, $idUser), JSON_PRETTY_PRINT);
}
