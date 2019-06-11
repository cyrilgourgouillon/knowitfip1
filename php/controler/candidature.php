<?php
header('Content-Type: application/json');

ini_set('display_errors',1);

require_once("../modele/Candidature.php");
require_once("../utils/dbconnect.php");
require_once("../utils/Feedback.php");

// $calledFunction = $_POST["function"];
// $data = $_POST["data"];

// // Push the $conn to the data
// array_unshift($data, $conn);

$calledFunction = "getCandidatureByUser";
$data = array(
    $conn,
    10
);

//Call the function
call_user_func_array($calledFunction, $data);


/**
 * STATIC
 * Recupere les informations des candidatures d'un post
 * 
 * @param - $idPost : id du post
 * @return Feedback
 */
function getCandidatureByPost($conn, $idPost) {
    echo json_encode(Candidature::getCandidatureByPost($conn, $idPost),JSON_PRETTY_PRINT);
}

/**
 * STATIC
 * Recupere les informations des candidatures d'un utilisateur
 * 
 * @param - $idUser : id du user
 * @return Feedback
 */
function getCandidatureByUser($conn, $idUser) {
    echo json_encode(Candidature::getCandidatureByUser($conn, $idUser),JSON_PRETTY_PRINT);
}

/**
 * STATIC
 * Candidater à un post
 * 
 * @param - $conn : Connexion PDO
 *        - $idUser
 *        - $idPost
 *        - $data : Tableau assoc nomChampsBD => value
 * @return Feedback
 */
function candidaterPost($conn, $idUser, $idPost, $data) {
    echo json_encode(Candidature::candidaterPost($conn, $idUser, $idPost, $data),JSON_PRETTY_PRINT);
}
