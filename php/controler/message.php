<?php
header('Content-Type: application/json');

require_once("../utils/dbconnect.php");
require_once('../utils/Feedback.php');
require_once("../modele/Message.php");

$calledFunction = $_POST["function"];
$data = $_POST["data"];

// Push the $conn to the data
array_unshift($data, $conn);

//Call the function
call_user_func_array($calledFunction, $data);

/**
 * { function_description }
 *
 * @param      <type>  $conn          The connection
 * @param      <type>  $message       The message
 * @param      <type>  $auteur        The auteur
 * @param      <type>  $destinataire  The destinataire
 */
function add($conn, $message, $auteur, $destinataire){
    echo json_encode(Message::add($conn, $message, $auteur, $destinataire));
}

/**
 * Gets the conversation.
 *
 * @param      <type>  $conn   The connection
 * @param      <type>  $u1     The u 1
 * @param      <type>  $u2     The u 2
 */
function getConversation($conn, $u1, $u2){
    echo json_encode(Message::getConversation($conn, $u1, $u2));
}

/**
 * Gets the new conversation message.
 *
 * @param      <type>  $conn   The connection
 * @param      <type>  $u1     The u 1
 * @param      <type>  $u2     The u 2
 */
function getNewConversationMessage($conn, $u1, $u2){
    echo json_encode(Message::getNewConversationMessage($conn, $u1, $u2));
}

/**
 * Sets the message read.
 *
 * @param      <type>  $conn   The connection
 * @param      <type>  $id     The identifier
 */
function setMessageRead($conn,$id){
    echo json_encode(Message::setMessageRead($conn, $id));
}

/**
 * Sets the conversation read.
 *
 * @param      <type>  $conn            The connection
 * @param      <type>  $idAuteur        The identifier auteur
 * @param      <type>  $idDestinataire  The identifier destinataire
 */
function setConversationRead($conn, $idAuteur, $idDestinataire){
    echo json_encode(Message::setConversationRead($conn, $idAuteur, $idDestinataire));
}

function getLastUsersMessage($conn, $recepteur){
    echo json_encode(Message::getLastUsersMessage($conn, $recepteur));
}