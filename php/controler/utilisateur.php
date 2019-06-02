<?php
header('Content-Type: application/json');

require_once('../utils/dbconnect.php');
require_once('../modele/Utilisateur.php');

$calledFunction = $_POST["function"];
$data = $_POST["data"];

call_user_func_array($calledFunction, $data);

/**
 * STATIC
 * Fonction permettant de recevoir les information basiques d'un utilisateur
 * en fonction de son id
 * 
 * Param - $conn : PDO connection
 *       - $id  : User ID
 * Return - un objet utilisateur à partir de $id
 */
function getUser($conn, $id)
{
    echo json_encode(Utilisateur::getUser($conn, $id));
}

/**
 * STATIC
 * Connecte un utilisateur
 * 
 * Check si le compte $mail existe et si son mot de passe est valide.
 * Param - $conn : PDO connection
 * Return un object Feedback
 */
function signIn($conn, $mail, $mdp)
{
    echo json_enconde(Utilisateur::signInUser($conn, $mail, $mdp));
}

/**
 * STATIC
 * Inscrit un nouvel utilisateur Knowit, sauf s'il existe déjà
 * 
 * Param - $conn : PDO connection
 * Return Feedback
 */
function register($conn, $prenom, $nom, $mail, $date_naissance, $mdp)
{
    echo json_encode(Utilisateur::signUpUser($conn, $prenom, $nom, $mail, $date_naissance, $mdp));
}

/**
 * Envoie les données permettant
 * d'afficher un profil utilisateur.
 * Plus précisement, renvoie un tableau qui
 * associe à l'utilisateur concerné ses différentes
 * compétences.
 *
 * @param $conn, la connexion à la BDD
 * @param $id, l'id de l'Utilisateur
 * @return array formaté du profil utilsateur
 */
function showProfile($conn, $id)
{
    echo json_encode(Utilisateur::showProfile($conn, $id));
}

/**
 * STATIC
 * Update les informations d'un utilisateur
 * 
 * Param - $conn : PDO connection
 *       - $id : id d'un utilisateur
 *       - $data : tableau associatif de type 'nomChampsBD => valeur'
 *       - $userTag : [Optionnel] tableau de competence associe à un utilisateur
 * Return Feedback
 */
function editProfile($conn, $id, $data, $userTag, $wishTag)
{
    echo json_encode(Utilisateur::editProfile($conn, $id, $data, $userTag, $wishTag));
}

function showPosts()
{

}

function showCandidatesForPost()
{

}

function searchPost()
{

}

function evaluateLesson()
{

}

function reward()
{

}

function deconnect()
{
    session_destroy();
}



