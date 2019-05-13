<?php

class Utilisateur implements JsonSerializable{

    protected $id;
    protected $nom;
    protected $prenom;
    protected $pseudo;
    protected $avatar;
    protected $mail;
    protected $date_naissance;
    protected $credit;
    protected $date_inscription;
    protected $description;

    function __construct() {

    }

    static function getUser($conn, $id){
        $stmt = $conn->prepare("SELECT * FROM utilisateur WHERE id = ?");
        $stmt->execute(array($id));
        return $stmt->fetchObject(__CLASS__);
    }

    function JsonSerialize(){
        echo json_encode(
            [
                'nom' => $this->nom,
                'pseudo' => $this->pseudo
            ],
            JSON_PRETTY_PRINT
        );
    }
};
