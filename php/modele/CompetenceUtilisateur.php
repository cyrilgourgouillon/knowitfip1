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
        $stmt = $conn->prepare("SELECT libelle, competence, points_experience, niveau, nb_vote FROM competence_utilisateur, competence WHERE utilisateur = $id AND competence.id = competence_utilisateur.competence");
		$stmt->execute();
		$count = $stmt->rowCount();
		
		if($count != 0)
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		else
			return NULL;
    }

    /**
     * STATIC
     * Retourne les tag d'un user
     * Param - $conn : connexion PDO
     *       - $id : id d'un user
     */
    static function getWishTagUser($conn, $id) {
        $stmt = $conn->prepare("SELECT libelle, competence FROM competence_souhaitee_utilisateur, competence WHERE utilisateur = $id AND competence.id = competence");
		$stmt->execute();
		$count = $stmt->rowCount();
		
		if($count != 0)
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
	static function editUserTag($conn, $id, $userTag, $wishTag ) {
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

		$stmt = $conn->prepare($sqlUserTag); 
        if($stmt->execute() == false){
            echo "Compétences déjà présentes";
        }
        
        //Update user wish taf
        $sqlWishTag = "INSERT INTO competence_souhaitee_utilisateur VALUES ";
		foreach($wishTag as $tag) {

            //si il n'existe pas deja cette compétence chez l'utilisateur
			$sqlWishTag .= "($id, $tag),";

		}
        $sqlWishTag = rtrim($sqlWishTag, ',');

        $stmt = $conn->prepare($sqlWishTag);
        CompetenceUtilisateur::deleteAllWishTag($conn, $id); //Suppression
		if($stmt->execute() == false){
            echo "Compétences déjà présentes";
        }
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

    /**
     * STATIC
     * 
     * Supprime les compétences utilisateur
     * 
     * Param - $conn : connexion PDO
     *       - $id : id du post
     */
    static function deleteAllWishTag($conn, $id) {
        $sqlUserTag = "DELETE FROM competence_souhaitee_utilisateur WHERE utilisateur = $id";
		$stmt = $conn->prepare($sqlUserTag); 
		$stmt->execute();
    }
}