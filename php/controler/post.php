<?php
header('Content-Type: application/json');
ini_set('display_errors',1);

require_once("../modele/Post.php");
require_once("../utils/dbconnect.php");
require_once("../utils/Feedback.php");

//$calledFunction = $_POST["function"];
//$data = $_POST["data"];

$calledFunction = "viewPost";
$data = array(
    $conn,
    10
);

call_user_func_array($calledFunction, $data);

function createPost() {
    echo json_encode(Post::getPost($conn, $idPost));
}

function editPost() {
    echo json_encode(Post::getPost($conn, $idPost));
}

function viewPost($conn, $idPost) {
    echo json_encode(Post::getPost($conn, $idPost));
}

function deletePost() {

}