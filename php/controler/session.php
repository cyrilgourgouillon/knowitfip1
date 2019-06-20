<?php
header('Content-Type: application/json');

ini_set('display_errors', 1);

require_once("../utils/dbconnect.php");
require_once('../utils/Feedback.php');
require_once('../modele/CompetencePost.php');
require_once('../modele/Session.php');

$calledFunction = $_POST["function"];
$data = $_POST["data"];

// Push the $conn to the data
array_unshift($data, $conn);

//Call the function
call_user_func_array($calledFunction, $data);

/**
 * STATIC
 * 
 * Retourne toutes les sessions d'un utilisateur
 * 
 * @param $id = id utilisateur
 * @return feedback
 */
function getSessionByUser($conn, $idUser) {
    echo json_encode(Session::getSessionByUser($conn, $idUser), JSON_PRETTY_PRINT);
}