<?php
class Reseau {

    function __construct() {

    }

    static function getReseauOf($conn, $id){
        $stmt = $conn->prepare("SELECT id, pseudo FROM utilisateur WHERE id != ?");
        if($stmt->execute(array($id))){
            return new Feedback($stmt->fetchAll(PDO::FETCH_ASSOC), true, "");
        }else{
           return new Feedback(NULL, false, "");
       }
   }
}