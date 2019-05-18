<?php
session_start();

if(count($_GET) == 0 or !isset($_GET['controle'])
or !isset($_GET['action'])){
	header('Location: index.php?controle=user&action=accueil');
	return;
}
else{
	$controle = $_GET['controle'];
	$action = $_GET['action'];
}

try{
	if (!file_exists('controle/' . $controle . '.php')){
		throw new Exception();
	}
	require_once('controle/' . $controle . '.php');
	if (!function_exists($action)){
		throw new Exception();
	}
	$action();
}
catch(Exception $e)
{
	header('Location: index.php?controle=user&action=erreur&numErr=1');
	return;
}
?>
