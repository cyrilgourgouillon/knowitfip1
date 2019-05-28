<?php

//header('Content-Type: application/json');
require_once('../utils/dbconnect.php');
require_once('../modele/utilisateur.php');
require_once('../utils/Feedback.php');

session_start();
var_dump(Utilisateur::signUpUser($conn, "Myce", "Obone", "myce@hotmail.fr", "20/12/1992", "12345"));
var_dump(Utilisateur::signInUser($conn, "myce@hotmail.fr", "12345"));
showProfile();

function signIn()
{
    echo json_enconde(Utilisateur::signInUser($conn, $_POST['mail'], $_POST['mdp']), JSON_PRETTY_PRINT);
}

function register()
{
    $prenom = $_POST['prenom'];
    $nom = $_POST['nom'];
    $mail = $_POST['mail'];
    $date_naissance = $_POST['date_naisssance'];
    $mdp = $_POST['mdp'];

    echo json_encode(Utilisateur::signUpUser($conn, $prenom, $nom, $mail, $date_naissance, $mdp), JSON_PRETTY_PRINT);
}

function showProfile()
{
    $id = $_SESSION['user'];

    echo json_encode(Utilisateur::showProfile($conn, $id), JSON_PRETTY_PRINT);
}

function editProfile()
{
    $id = $_SESSION['id'];
    $data = $_POST['data'];
    $userTag = $_POST['userTag'];
    $wishTag = $_POST['wishTag'];

    echo json_encode(Utilisateur::editProfile($conn, $id, $data, $userTag, $wishTag), JSON_PRETTY_PRINT);
}

function createPost()
{

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



