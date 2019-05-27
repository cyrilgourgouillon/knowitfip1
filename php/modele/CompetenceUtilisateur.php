<?php
class CompetenceUtilisateur {

    private $id;
    private $niveau;
    private $exp;
    private $nb_vote;

    private $competenceId;
    private $utilisateurId;
    
    function __construct() {
        
    }

    /**
     * STATIC
     * Check si un utilisateur possede deja la competence $tag
     * 
     * return - true si il la possede.
     *        - false sinon.
     */
    static function haveAlreadyIt($conn, $idUser, $tag) {
        if($tag == -1) {
            return true;
        }
        $stmt = $conn->prepare("SELECT * FROM competence_utilisateur WHERE utilisateur=$idUser AND competence=$tag"); 
        $stmt->execute();
        $count=$stmt->rowCount();

        if($count == 0){
            return false;
        } else {
            return true;
        }
    }
}