<?php
header('Content-Type: application/json');

require_once("../utils/dbconnect.php");
require_once('../utils/Feedback.php');
require_once("../modele/Post.php");
require_once("../modele/Utilisateur.php");
require_once("../modele/Notification.php");
require_once("../modele/Candidature.php");
require_once('../modele/CompetenceUtilisateur.php');
require_once('../modele/CompetencePost.php');

$calledFunction = $_POST["function"];
$data = $_POST["data"];

// Push the $conn to the data
array_unshift($data, $conn);

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
 * Vérifie si un utilisateur est déjà candidat d'un post
 * 
 * @param - $idPost : id du post
 * @return Feedback
 */
function isCandidat($conn, $idPost, $idUser) {
    echo json_encode(Candidature::isCandidat($conn, $idPost, $idUser),JSON_PRETTY_PRINT);
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

/**
 * Permet d'afficher les commentaires
 * sur la candidature qui a été sélectionné
 *
 * @param $conn, la connexion à la BDD
 * @param $idCand, l'identifiant de la candidature
 */
function getCommentCandidature($conn, $idCand) {
    echo json_encode(Candidature::getCommentCandidature($conn, $idCand), JSON_PRETTY_PRINT);
}


/**
 * Permet de reffuser une candidature
 *
 * @param $conn, la connexion à la BDD
 * @param $idCand, l'identifiant de la candidature
 */
function refuserCandidature($conn, $idCand) {
    echo json_encode(Candidature::refuserCandidature($conn, $idCand), JSON_PRETTY_PRINT);
}

/**
 * Permet de valider une candidature
 *
 * @param $conn, la connexion à la BDD
 * @param $idCand, l'identifiant de la candidature
 */
function accepterCandidature($conn, $idCand, $reponse) {
    echo json_encode(Candidature::accepterCandidature($conn, $idCand,  $reponse), JSON_PRETTY_PRINT);
}

/**
 * Lance une nouvelle session
 * 
 * @param $conn, la connexion à la BDD
 * @param $idCand, l'identifiant de la candidature
 * @return Feedback, un objet indiquant le succès de la fonction
 */
function startSession($conn, $idCand) {
     Candidature::valideCandidature($conn, $idCand);
     echo json_encode(Candidature::startSession($conn, $idCand), JSON_PRETTY_PRINT);
}

/**
 * Annule une candidature
 *
 * @param      PDO  $conn    The connection
 * @param      int  $idCand  The identifier candidature
 * @return     Feedback, un objet indiquant le succès de la fonction
 */
function annuleCandidature($conn, $idCand) {
     echo json_encode(Candidature::annuleCandidature($conn, $idCand), JSON_PRETTY_PRINT);
}