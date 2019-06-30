<?php
class Notification {

    private $id;
    private $date;
    private $type;
    private $text;
    private $is_see;
    private $is_mail;

    private $utilisateur;
    
    function __construct() {
        
    }

    static function countNotification($conn, $idUser) {
        $sql = "SELECT * FROM notification WHERE utilisateur = :idUser AND has_been_seen = 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam("idUser", $idUser);
        $stmt->execute();
        $count = $stmt->rowCount();
        return [
            'count' => $count
        ];
    }

    static function addNotification($conn, $idUser, $idObjet, $type, $texte) {
        $sql = "INSERT INTO notification (idObjet, type, texte, utilisateur) VALUES (:idObjet, :type, :texte, :utilisateur)";
        $stmt = $conn->prepare($sql);
        
        $stmt->bindParam("idObjet", $idObjet);
        $stmt->bindParam("type", $type);
        $stmt->bindParam("texte", $texte);
        $stmt->bindParam("utilisateur", $idUser);

        if($stmt->execute()) {
            return new Feedback(NULL, true, "Notification ajoutée");
        } else {
            return new Feedback(NULL, false, "erreur lors de addNotification");
        }
    }

    static function getNotificationByUser($conn, $idUser) {
        $sql = "SELECT * FROM notification WHERE utilisateur = :idUser ORDER BY date DESC LIMIT 5";
        $stmt = $conn->prepare($sql);

        $stmt->bindParam("idUser", $idUser);
        $stmt->execute();

        $count = $stmt->rowCount();

		if($count != 0) {
            $notifUser = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return new Feedback($notifUser, true, "");
        } else {
            return new Feedback(NULL, false, "erreur lors de getNotificationByUser");
        }
    }

    static function seeNotification($conn, $idsNotifs) {
        $ok = true;
        foreach ($idsNotifs as $idNotif) {
            $sql = "UPDATE notification SET has_been_seen = 1 WHERE id = :idNotif";
            $stmt = $conn->prepare($sql);

            $stmt->bindParam("idNotif", $idNotif);

            if(!$stmt->execute()) {
                $ok = false;
            } 
        }
        if($ok){
            return new Feedback(NULL, true, "Notifications lues");
        }else{
            return new Feedback(NULL, false, "Error");
        }
    }
}