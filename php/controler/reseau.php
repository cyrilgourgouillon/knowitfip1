<?php
header('Content-Type: application/json');

require_once("../utils/dbconnect.php");
require_once('../utils/Feedback.php');
require_once("../modele/Reseau.php");


$calledFunction = $_POST["function"];
$data = $_POST["data"];

// Push the $conn to the data
array_unshift($data, $conn);

//Call the function
call_user_func_array($calledFunction, $data);

/**
 * Temporary - Gets the reseau of : Return all the user 
 *
 * @param      <type>  $conn   The connection
 * @param      <type>  $id     The identifier
 */
function getReseauOf($conn, $id){
    echo json_encode(Reseau::getReseauOf($conn, $id));
}