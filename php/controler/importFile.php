<?php
header('Content-Type: application/json');

ini_set('display_errors', 1);

require_once('../utils/dbconnect.php');
require_once('../modele/Utilisateur.php');
require_once('../modele/Competence.php');
require_once('../modele/CompetenceUtilisateur.php');
require_once("../utils/Feedback.php");

print_r($_FILES);
print_r($_POST);

//addAvatar($conn, )

/**
 * Permet d'uploader l'avatar choisi par l'utilisateur
 * sur le serveur et envoie le chemin de l'image à la
 * fonction modèle.
 *
 * @param $conn, la connexion à la BDD
 * @param $id, l'identifiant de l'utilisateur
 */
function addAvatar($conn, $id) {
    $path = null;
    if ( 0 < $_FILES['file']['error'] ) {
        echo 'Error: ' . $_FILES['file']['error'] . '<br>';
    }
    else {
        $path = '../../img/'. $id . '_' . $_FILES['file']['name'];
        move_uploaded_file($_FILES['file']['tmp_name'], $path);
    }

    echo json_encode(Utilisateur::addAvatar($conn, $id, $path), JSON_PRETTY_PRINT);
}