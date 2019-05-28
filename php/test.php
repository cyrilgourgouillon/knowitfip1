<?php
header('Content-Type: application/json');

ini_set('display_errors',1);

require_once('utils/dbconnect.php');
require_once('utils/Feedback.php');
require_once('modele/utilisateur.php');
require_once('modele/post.php');
//Utilisateur::signUpUser($conn, "Marie", "Pledosky", "testo@test.test","20/02/1997","12345");
//Utilisateur::signInUser($conn, "test@test.test", "12345");
/*
$data = array(
    'pseudo' => 'Macha',
    'nom' => 'Gourgouillon'
);

$userTag = array(
    2,
    3,
);

//Utilisateur::editUser($conn, 10, $data, $userTag);
CompetenceUtilisateur::editUserTag($conn, 10, $userTag);*/


$postData = array (
    "id" => 10,
    "type" => "Request",
    "titre" => "Cours complet SAP ABAP",
    "description" => "Salut, je suis une jeune eleve qui veut apprendre lABAP.
    Je voudrai avoir des connaissances abondantes avec vous. Les entreprises raffolent de ce genre dexperience. Lorem ipsum dolor et tout.",
    "tmp_estime" => 20,
    "date" => "05/05/2012"
);

$postDataM = array (
    "id" => 10,
    "titre" => "Cours de base SAP ABAP",
    "description" => "je suis une jeune eleve qui veut apprendre lABAP.
    Je voudrai avoir des connaissances abondantes avec vous. Les entreprises raffolent de ce genre dexperience. Lorem ipsum dolor et tout.",
    "tmp_estime" => 20,
);

$postTag = array (
    1,
    NULL
);

//Post::createPost($conn, 10, $postData, $postTag);
Post::editPost($conn, 10, $postDataM, $postTag);

//$user->JsonSerialize();