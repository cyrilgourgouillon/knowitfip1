<?php
class Message {

    function __construct() {

    }
    /**
     * { function_description }
     *
     * @param      <type>    $conn          The connection
     * @param      <type>    $message       The message
     * @param      <type>    $auteur        The auteur
     * @param      <type>    $destinataire  The destinataire
     *
     * @return     Feedback  ( description_of_the_return_value )
     */
    static function add($conn, $message, $auteur, $destinataire){
        $stmt = $conn->prepare("INSERT INTO message (texte, has_been_read, emetteur, recepteur) VALUES (?,?,?,?)");
        $stmt->execute(array($message, 0, $auteur, $destinataire));

        return new Feedback(NULL, true, "");
    }

    /**
     * Gets the conversation.
     *
     * @param      <type>    $conn   The connection
     * @param      <type>    $u1     The u 1
     * @param      <type>    $u2     The u 2
     *
     * @return     Feedback  The conversation.
     */
    static function getConversation($conn, $u1, $u2){
        $stmt = $conn->prepare("SELECT * FROM message WHERE (emetteur = ? AND recepteur = ?) OR (recepteur = ? AND emetteur = ?)");
        if($stmt->execute(array($u1, $u2, $u1, $u2))){
            self::setConversationRead($conn, $u1, $u2);
            return new Feedback($stmt->fetchAll(PDO::FETCH_ASSOC), true, "");
        }else{
         return new Feedback(NULL, false, "");
     }
 }

   /**
    * Gets the new conversation message.
    *
    * @param      <type>    $conn   The connection
    * @param      <type>    $u1     The u 1
    * @param      <type>    $u2     The u 2
    *
    * @return     Feedback  The new conversation message.
    */
   static function getNewConversationMessage($conn, $u1, $u2){
    $stmt = $conn->prepare("SELECT * FROM message WHERE  (recepteur = ? AND emetteur = ?) AND has_been_read = ?");
    if($stmt->execute(array($u1, $u2, false))){
        self::setConversationRead($conn, $u1, $u2);
        return new Feedback($stmt->fetchAll(PDO::FETCH_ASSOC), true, "");
    }else{
     return new Feedback(NULL, false, "");
 }
}

/**
 * Sets the message read.
 *
 * @param      <type>    $conn   The connection
 * @param      <type>    $id     The identifier
 *
 * @return     Feedback  ( description_of_the_return_value )
 */
static function setMessageRead($conn, $id){
    $stmt = $conn->prepare("UPDATE message SET has_been_read = ? WHERE id = ?");
    $stmt->execute(array(true, $id));

    return new Feedback(NULL, true, "");
}

/**
 * Sets the conversation read.
 *
 * @param      <type>    $conn            The connection
 * @param      <type>    $idAuteur        The identifier auteur
 * @param      <type>    $idDestinataire  The identifier destinataire
 *
 * @return     Feedback  ( description_of_the_return_value )
 */
static function setConversationRead($conn, $idAuteur, $idDestinataire){
    $stmt = $conn->prepare("UPDATE message SET has_been_read = ? WHERE emetteur = ? AND recepteur=?");
    $stmt->execute(array(true, $idDestinataire,  $idAuteur));

    return new Feedback(NULL, true, "");
}

}