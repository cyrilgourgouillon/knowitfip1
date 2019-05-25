<?php

require_once('../utils/dbconnect.php');
require_once('../modele/utilisateur.php');

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
f
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
}



