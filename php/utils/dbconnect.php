<?php

/**
 * Variable de connexion à la base de donnée.
 * PDO.
 */
$user = 'root';
$pass = 'root';
$charset = 'utf8';
$conn = null;

try {
    $conn = new PDO("mysql:host=localhost;dbname=knowitbd;charset=$charset", $user, $pass);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e){
    echo "Connection failed: " . $e->getMessage();
}