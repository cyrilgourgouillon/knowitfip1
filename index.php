<?php
session_start();
require_once('php/controler/controler.php');
$action = $_GET['action'];
if(!isset($action)) {
	accueil();
	return;
}
else{
	$action();
}