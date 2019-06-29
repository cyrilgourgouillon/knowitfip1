<?php
header('Content-Type: application/json');

require_once("../utils/dbconnect.php");
require_once('../utils/Feedback.php');
require_once('../modele/CompetencePost.php');
require_once('../modele/CompetenceUtilisateur.php');
require_once('../modele/Utilisateur.php');
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


/**
 * STATIC
 *
 * Permet au candidat d'un knowledge ou à l'auteur d'une request
 * de noter la session qu'il a suivie. Clôt la session.
 *
 * @param $conn, la connexion à la BDD
 * @param $idSession, l'id de la session à noter
 * @param $note, la note attribuée
 */
function evaluateSession($conn, $idSession, $note) {
    echo json_encode(Session::evaluateSession($conn, $idSession, $note), JSON_PRETTY_PRINT);
}

function getPostAndCandidacyFromSession($conn, $idSession) {
    echo json_encode(Session::getPostAndCandidacyFromSession($conn, $idSession), JSON_PRETTY_PRINT);
}