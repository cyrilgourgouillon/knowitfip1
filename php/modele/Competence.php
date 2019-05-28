<?php
class Competence {

    private $id;
    private $libelle;
    private $approuve;
    
    private $type_id;
    
    function __construct() {
        
    }

    /**
     * STATIC
     * Return l'id de la competence a partir de son nom
     * Return -1 sinon
     */
    static function getNameById($conn, $id) {
        if($id == NULL)
            return -1;

        $stmt = $conn->prepare("SELECT libelle FROM competence WHERE id=:id"); 
        $stmt->bindParam("id", $id,PDO::PARAM_STR);
        $stmt->execute();
        $data=$stmt->fetch();

        if($data["libelle"] == NULL)
            return "Inconnu";

        return $data["libelle"];
    }
}