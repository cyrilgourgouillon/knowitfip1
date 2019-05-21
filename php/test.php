<?php
header('Content-Type: application/json');

ini_set('display_errors',1);

require_once('utils/dbconnect.php');
require_once('modele/utilisateur.php');

Utilisateur::signUpUser($conn, "Marie", "Pledosky", "test@test.test","20/02/1997","1234");
//$user->JsonSerialize();