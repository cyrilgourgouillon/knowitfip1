<?php
header('Content-Type: application/json');

require_once('../utils/dbconnect.php');
require_once('../modele/Utilisateur.php');
require_once("../utils/Feedback.php");

$calledFunction = $_POST["function"];
$data = $_POST["data"];

// Push the $conn to the data
array_unshift($data, $conn);


//Call the function
call_user_func_array($calledFunction, $data);
/**
 * STATIC
 * Check si utilisateur est connecté
 * 
 * Param - $id
 */
function isConnected() {
    echo json_encode(new Feedback(null, Utilisateur::isConnected(), '') , JSON_PRETTY_PRINT);
}



/**
 * Gets the basic user information.
 *
 * @return     feedback  The basic user information.
 */
function getBasicUserInfo($conn){
     $id = Utilisateur::isConnected();
     if($id != false){
          echo json_encode(new Feedback(Utilisateur::getBasicUserInfo($conn, $id), true, ''), JSON_PRETTY_PRINT);
     }else{
           echo json_encode(new Feedback(null,false,''), JSON_PRETTY_PRINT);
     }
}

/**
 * STATIC
 * Fonction permettant de recevoir les information baasiques d'un utilisateur
 * en fonction de son id
 * 
 * Param - $conn : PDO connection
 *       - $id  : User ID
 * Return - un objet utilisateur à partir de $id
 */
function getUser($conn, $id)
{
    echo json_encode(Utilisateur::getUser($conn, $id), JSON_PRETTY_PRINT);
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
    echo json_encode(Utilisateur::signInUser($conn, $mail, $mdp), JSON_PRETTY_PRINT);
}

/**
 * STATIC
 * Inscrit un nouvel utilisateur Knowit, sauf s'il existe déjà
 * 
 * Param - $conn : PDO connection
 * Return Feedback
 */
function register($conn, $nom, $prenom, $mail, $mdp)
{
    echo json_encode(Utilisateur::signUpUser($conn, $nom, $prenom, $mail, $mdp), JSON_PRETTY_PRINT);
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
    echo json_encode(Utilisateur::showProfile($conn, $id), JSON_PRETTY_PRINT);
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
    echo json_encode(Utilisateur::editProfile($conn, $id, $data, $userTag, $wishTag), JSON_PRETTY_PRINT);
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



