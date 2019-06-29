<?php
header('Content-Type: application/json');

require_once("../utils/dbconnect.php");
require_once('../utils/Feedback.php');
require_once("../modele/Candidature.php");
require_once('../modele/CompetenceUtilisateur.php');
require_once('../modele/CompetencePost.php');

$calledFunction = $_POST["function"];
$data = $_POST["data"];
//
// Push the $conn to the data
rray_unshift($data, $conn);

//Call the function
call_user_func_array($calledFunction, $data);


function addExperience($conn, $idSession, $note) {
    echo json_encode(CompetenceUtilisateur::addExperience($conn, $idSession, $note), JSON_PRETTY_PRINT);
}

function getExperience($conn, $idSession){
    echo json_encode(CompetenceUtilisateur::getExperience($conn, $idSession), JSON_PRETTY_PRINT);
}