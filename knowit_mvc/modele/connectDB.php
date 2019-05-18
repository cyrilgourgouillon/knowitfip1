<?php
function connect_pdo(){
	$hostnameDB = 'localhost';
	$usernameDB = 'root';
	$passwordDB = '';
	$nameDB = 'knowit';
	try
	{
		return $pdo = new PDO("mysql:host=$hostnameDB;dbname=$nameDB", $usernameDB, $passwordDB);
	}
	catch(Exception $e)
	{
		die("Echec : " . $e->getMessage());
	}
}
?>
