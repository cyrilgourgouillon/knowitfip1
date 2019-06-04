<?php
header('Content-Type: application/json');

require_once("../modele/Candidature.php");
require_once("../utils/dbconnect.php");

$calledFunction = $_POST["function"];
$data = $_POST["data"];

call_user_func_array($calledFunction, $data);

/**
 * STATIC
 * Recupere les informations des candidatures d'un post
 * 
 * @param - $idPost : id du post
 * @return Feedback
 */
function getCandidatureByPost($conn, $idPost) {
    echo json_encode(Candidature::getCandidatureByPost($conn, $idPost));
}

/**
 * STATIC
 * Recupere les informations des candidatures d'un utilisateur
 * 
 * @param - $idUser : id du user
 * @return Feedback
 */
function getCandidatureByUser($conn, $idUser) {
    echo json_encode(Candidature::getCandidatureByUser($conn, $idUser));
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
    echo json_encode(Candidature::candidaterPost($conn, $idUser, $idPost, $data));
}
