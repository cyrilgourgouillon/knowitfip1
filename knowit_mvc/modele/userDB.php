<?php

require_once('modele/connectDB.php');

function verif_ident($login, $pass, &$profil){
	$pdo = connect_pdo();
	$profil = null;
	$sql = 'SELECT *
			FROM utilisateur
			WHERE pseudo = ? AND mdp = ?';
	$sth = $pdo->prepare($sql);
	$sth->execute(array($login, hash('sha512', $pass)));
	while ($row = $sth->fetch()) {
		$profil = $row;
	}
	return isset($profil);
}

function addUser($data_params) {
	$pdo = connect_pdo();
	$sql = 'INSERT INTO utilisateur VALUES (NULL,?,?,?,?,?,?,0,?,0,NULL,?)';
	$sth = $pdo->prepare($sql);
	$sth->execute(array_values($data_params));
}

?>
