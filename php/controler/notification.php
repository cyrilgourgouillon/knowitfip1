<?php
header('Content-Type: application/json');

require_once("../utils/dbconnect.php");
require_once('../utils/Feedback.php');

require_once("../modele/Notification.php");

$calledFunction = $_POST["function"];
$data = $_POST["data"];

// Push the $conn to the data
array_unshift($data, $conn);

//Call the function
call_user_func_array($calledFunction, $data);

function countNotification($conn, $idUser) {
    echo json_encode(Notification::countNotification($conn, $idUser), JSON_PRETTY_PRINT);
}

function getNotificationByUser($conn, $idUser) {
    echo json_encode(Notification::getNotificationByUser($conn, $idUser), JSON_PRETTY_PRINT);
}

function seeNotification($conn, $idsNotifs) {
    echo json_encode(Notification::seeNotification($conn, $idsNotifs), JSON_PRETTY_PRINT);
}
