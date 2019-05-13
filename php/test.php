<?php

ini_set('display_errors',1);

require_once('utils/dbconnect.php');
require_once('modele/utilisateur.php');

$user = Utilisateur::getUser($conn, 1);
$user->JsonSerialize();