<?php
header('Content-Type: application/json');

ini_set('display_errors',1);

require_once("../modele/Post.php");
require_once("../utils/dbconnect.php");
require_once("../utils/Feedback.php");

//$calledFunction =  $_POST['function'];
//$data = $_POST['data'];

 
//JEU DE TEST :

$calledFunction = "getPostByUser";
$data = array($conn, 10, "Request");

call_user_func_array($calledFunction, $data);

/**
 * STATIC
 * Recupere les informations et les tags d'un post
 * 
 * Param - $idPost : id du post
 * Return un object Feedback
 */
function getPost($conn, $idPost) {
    echo json_encode(Post::getPost($conn, $idPost), JSON_PRETTY_PRINT);
}

/**
 * STATIC
 * Recupere les informations des posts d'un user
 * 
 * Param - $idUser : id du user
 *       - $type : request ou knowledge
 * Return un object Feedback
 */
function getPostByUser($conn, $idUser, $type) {
    echo json_encode(Post::getPostByUser($conn, $idUser, $type), JSON_PRETTY_PRINT);
}

/**
 * STATIC
 * Publier un Post
 * 
 * Param - $conn : PDO connection
 *       - $id : id du Post
 *       - $data : Tableau associatif de données associe à un post
 *       - $postTag: Tableau de competence associe au post
 * Return Feedback
 */
function createPost($conn, $idUtil, $data, $postTag) {
    echo json_encode(Post::createPost($conn, $idUtil, $data, $postTag), JSON_PRETTY_PRINT);
}

/**
 * STATIC
 * Editer un post
 * 
 * Param - $conn : connexion PDO
 *       - $idPost : id du post visé
 *       - $data : tableau associatif de données du post modifié
 *              - ATTENTION : KEY = CHAMPS BD
 *       - $postTag : tableau associatif de tags du post
 */
function editPost($conn, $idPost, $data, $postTag) {
    echo json_encode(Post::editPost($conn, $idPost, $data, $postTag), JSON_PRETTY_PRINT);
}

/**
 * STATIC
 * Recupere les informations d'un post
 * 
 * Param - $idPost : id du post
 * Return un object Feedback
 */
function deletePost($conn, $id) {
    echo json_encode(Post::deletePost($conn, $id), JSON_PRETTY_PRINT);
}