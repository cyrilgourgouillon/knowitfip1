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
    static function getIdByName($conn, $name) {
        if($name == NULL)
            return -1;

        $stmt = $conn->prepare("SELECT id FROM competence WHERE libelle=:libelle"); 
        $stmt->bindParam("libelle", $name,PDO::PARAM_STR);
        $stmt->execute();
        $data=$stmt->fetch();

        if($data["id"] == NULL)
            return -1;

        return $data["id"];
    }
}