<?php

function accueil()
{

}

function signIn()
{
    require_once('../utils/dbconnect.php');
    if (isset($_SESSION['user'])) {
        $id = $_SESSION['user'];
        echo json_encode(Utilisateur::getUser($conn, $id), JSON_PRETTY_PRINT);
    }

    echo json_enconde(Utilisateur::signInUser($conn, $_POST['mail'], $_POST['mdp']), JSON_PRETTY_PRINT);
}

function register()
{
    require_once('../utils/dbconnect.php');

    $prenom = $_POST['prenom'];
    $nom = $_POST['nom'];
    $mail = $_POST['mail'];
    $date_naissance = $_POST['date_naisssance'];
    $mdp = $_POST['mdp'];

    echo json_encode(Utilisateur::signUpUser($conn, $prenom, $nom, $mail, $date_naissance, $mdp), JSON_PRETTY_PRINT);
}

function showProfile()
{

}

function editProfile() {

}

function createPost() {

}

function showPosts() {

}

function showCandidatesForPost() {

}

function searchPost() {

}

function evaluateLesson() {

}

function reward() {

}

function deconnect()
{
    session_destroy();

    accueil();
}



