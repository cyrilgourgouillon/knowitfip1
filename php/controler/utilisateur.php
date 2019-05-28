<?php

require_once('../utils/dbconnect.php');
require_once('../modele/Utilisateur.php');

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

function showProfile($conn)
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



