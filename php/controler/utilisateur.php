<?php
require_once('../utils/dbconnect.php');
require_once('../modele/Utilisateur.php');

//$calledFunction = $_POST["function"];
//$data = $_POST["data"];

$calledFunction = "getUser";
$data = array(
    $conn,
    10
);

call_user_func_array($calledFunction, $data);

function signIn($conn, $mail, $mdp)
{
    echo json_enconde(Utilisateur::signInUser($conn, $mail, $mdp));
}

function register($conn, $prenom, $nom, $mail, $date_naissance, $mdp)
{
    echo json_encode(Utilisateur::signUpUser($conn, $prenom, $nom, $mail, $date_naissance, $mdp));
}

function showProfile($conn, $id)
{
    echo json_encode(Utilisateur::showProfile($conn, $id));
}

function editProfile($conn, $id, $data, $userTag, $wishTag)
{
    echo json_encode(Utilisateur::editProfile($conn, $id, $data, $userTag, $wishTag));
}

function getUser($conn, $id)
{
    echo "<pre>";
    echo json_encode(Utilisateur::getUser($conn, $id));
    echo "</pre>";
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



