<?php
header('Content-Type: application/json');

ini_set('display_errors',1);

require_once('utils/dbconnect.php');
require_once('utils/Feedback.php');
require_once('modele/utilisateur.php');

//Utilisateur::signUpUser($conn, "Marie", "Pledosky", "testo@test.test","20/02/1997","12345");
//Utilisateur::signInUser($conn, "test@test.test", "12345");

$data = array(
    'pseudo' => 'Macha',
    'nom' => 'Gourgouillon'
);

$userTag = array(
    "PHP",
    "Java",
    "Plantation",
    "Skateboard",
    NULL
 );

Utilisateur::editUser($conn, 10, $data, $userTag);

//$user->JsonSerialize();