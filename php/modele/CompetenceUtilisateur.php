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
     * Retourne les tag d'un user
     * Param - $conn : connexion PDO
     *       - $id : id d'un user
     */
    static function getTagUser($conn, $id) {
        $stmt = $conn->prepare("SELECT competence, points_experience, niveau, nb_vote FROM competence_utilisateur WHERE utilisateur = $id");
		$stmt->execute();
		$count = $stmt->rowCount();
		
		if($count != 0)
			return $stmt->fetchAll();
		else
			return NULL;
    }

	/**
	 * STATIC
	 * Modifie les competences d'un utilisateur
	 * S'inscrit dans la fonction editUser()
	 * 
	 * Param - $conn : PDO connection
	 *       - $id : id d'un utilisateur
	 *       - $userTag : [Optionnel] tableau d'id de competences associe à un utilisateur
	 */
	static function editUserTag($conn, $id, $userTag, $wishTag = NULL) {
		//Update user tag
		
		$sqlUserTag = "INSERT INTO competence_utilisateur VALUES ";
		foreach($userTag as $tag) {

            $alreadyHaveIt = CompetenceUtilisateur::alreadyHaveIt($conn, $id, $tag);

			//si il n'existe pas deja cette compétence chez l'utilisateur
			if(!$alreadyHaveIt) {
			    $sqlUserTag .= "($id, $tag, 0, 0, 0),";
			}
		}
        $sqlUserTag = rtrim($sqlUserTag, ',');
        
        echo $sqlUserTag;

		$stmt = $conn->prepare($sqlUserTag); 
		$stmt->execute();
	}

    /**
     * STATIC
     * Check si un utilisateur possede deja la competence $tag
     * 
     * return - true si il la possede.
     *        - false sinon.
     */
    static function alreadyHaveIt($conn, $idUser, $tag) {
        if($tag == -1) {
            return true;
        }
        if($tag == NULL || $tag == '')
            return true;
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