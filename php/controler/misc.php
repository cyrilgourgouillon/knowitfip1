<?php
header('Content-Type: application/json');

ini_set("display_errors", 1);

require_once("../utils/dbconnect.php");
require_once('../utils/Feedback.php');

require_once("../modele/CompetencePost.php");
require_once("../modele/Misc.php");

$calledFunction = $_POST["function"];
$data = $_POST["data"];

// Push the $conn to the data
array_unshift($data, $conn);

//Call the function
call_user_func_array($calledFunction, $data);

/**
 * STATIC
 * 
 * Retourne tous les post (request ou knowledge)
 * avec la chaine $string dans le titre, description ou pseudo
 * 
 */
function rechercher($conn, $string) {
    echo json_encode(Misc::rechercher($conn, $string), JSON_PRETTY_PRINT);
}