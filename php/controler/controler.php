<?php

function accueil() {

}

function signIn()
{
    require_once('../utils/dbconnect.php');
    if (isset($_SESSION['user'])) {
        $id = $_SESSION['user'];
        echo json_encode(Utilisateur::getUser($conn,$id),JSON_PRETTY_PRINT);
    }
    if (count($_POST) == 0) {
        //TODO
    }
    Utilisateur::signInUser($conn,$_POST['mail'],$_POST['mdp']);
}

function register()
{
    require_once('../utils/dbconnect.php');
    if (count($_POST) == 0) {
        //TODO
    }

    Utilisateur::signUpUser($conn, $_POST['prenom'], $_POST['nom'], $_POST['mail'], $_POST['date_naisssance'], $_POST['mdp']);
}

function deconnect()
{
    session_destroy();
}



