<?php
header('Content-Type: application/json');

require_once('../utils/dbconnect.php');
require_once('../modele/Competence.php');
require_once("../utils/Feedback.php");

$calledFunction = $_POST["function"];
$data = $_POST["data"];

// Push the $conn to the data
array_unshift($data, $conn);


//Call the function
call_user_func_array($calledFunction, $data);

function getAllCompetence($conn){
     echo json_encode(new Feedback(Competence::getAllCompetence($conn),true, ''));
}

