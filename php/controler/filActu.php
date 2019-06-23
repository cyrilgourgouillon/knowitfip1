<?php
header('Content-Type: application/json');

ini_set('display_errors', 1);

require_once("../utils/dbconnect.php");
require_once('../utils/Feedback.php');

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
function afficherFilActu($conn, $idUser) {
    echo json_encode(FilActu::afficherFilActu($conn, $idUser), JSON_PRETTY_PRINT);
}