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
	static function editUserTag($conn, $id, $userTag, $wishTag) {
        $sqlCurrentTag = "SELECT competence FROM competence_utilisateur WHERE utilisateur = :id";
        $stmt = $conn->prepare($sqlCurrentTag);
        $stmt->bindParam('id',$id);
        $stmt->execute();
        
        $currentTag = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $test = array_diff($currentTag, $userTag);

        if(count($test) != 0){
            CompetenceUtilisateur::deleteUserTag($conn, $id, $test);
        }

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
        try {
            $stmt->execute();
        }
        catch(Exception $e) {
            // :/!\: Merci de ne rien echo, cela casse le JSON de retour
            // Merci de ne pas commit ce genre de chose à l'avenir
            // Si je t'encule
            // fdp
            // Signé, ton tendre
            // Nathan
            //echo "Compétences déjà présentes";
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
		try {
            $stmt->execute();
        }
        catch(Exception $e) {
            // Je vais t'enculer
            // Putain d'autiste
            //echo "Compétences déjà présentes";
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
     * Supprime les compétences d'un utilisateur
     * 
     * Param - $conn : connexion PDO
     *       - $id : id de l'utilisateur
     *       - $tags : tags à supprimer
     */
    static function deleteUserTag($conn, $id, $tags) {
        $sqlUserTag = "DELETE FROM competence_utilisateur WHERE utilisateur = $id AND (competence = ";
        $test = implode(" OR competence = ", $tags);
        $sqlUserTag .= $test . ")";

		$stmt = $conn->prepare($sqlUserTag); 
		$stmt->execute();
    }

    /**
     * STATIC
     * 
     * Supprime les compétences utilisateur
     * 
     * Param - $conn : connexion PDO
     *       - $id : id de l'utilisateur
     */
    static function deleteAllWishTag($conn, $id) {
        $sqlUserTag = "DELETE FROM competence_souhaitee_utilisateur WHERE utilisateur = $id";
		$stmt = $conn->prepare($sqlUserTag); 
		$stmt->execute();
    }

    static function addPointExp($conn, $idUtilisateur, $competence, $exp){
        $stmtGetExp = $conn->prepare("SELECT points_experience FROM competence_utilisateur WHERE utilisateur = ? AND competence = ?");
        $stmtGetExp->execute(array($idUtilisateur, $competence)); 

        $oldExp = $stmtGetExp->fetchColumn();

        if($oldExp && intval($oldExp)+$exp <= 1000){
            $stmtUpdateExp = $conn->prepare("UPDATE competence_utilisateur SET points_experience = ? WHERE utilisateur = ? AND competence = ?");
            $stmtUpdateExp->execute(array(intval($oldExp)+$exp, $idUtilisateur, $competence)); 
            return true;
        }

        return false;
    }

    static function addMultiplesPointExp($conn, $idUtilisateur, $competences, $exp){
        foreach($competences as $competence){
            self::addPointExp($conn, $idUtilisateur, $competence, $exp);
        }
    }

}
