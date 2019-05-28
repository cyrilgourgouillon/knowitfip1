<?php
require_once("CompetencePost.php");

class Post {

    private $id;
    private $utilisateur;
    private $titre;
    private $description;
    private $tmp_estime;
    private $date;

    function __construct() {
        
    }

    /**
     * STATIC
     * Recupere les informations d'un post
     * 
     * Param - $idPost : id du post
     * Return un object Post
     */
    static function getPost($conn, $idPost) {
        $stmt = $conn->prepare("SELECT utilisateur, titre, description, tmp_estime, date, type FROM post WHERE id = $idPost");
        $stmt->execute();
        $count = $stmt->rowCount();

		if($count != 0)
			return new Feedback($stmt->fetchObject(), true, "");
		else
			return new Feedback(0, false, "Post inconnu.");
    }

    /**
	 * STATIC
	 * Publier un Post
	 * 
	 * Param - $conn : PDO connection
	 *       - $id : id de l'utilisateur
	 *       - $data : 
	 *       - $postTag: Tableau de competence associe à un utilisateur
	 * Return Feedback
	 */
	static function createPost($conn, $idUtil, $data, $postTag) {
        
        $data["date"] = date("Y-m-d", strtotime($data["date"])); 

		$stmt = $conn->prepare("INSERT INTO post VALUES (DEFAULT, :titre, :description, :tmp_estime, :date, :type, :utilisateur)");
		$stmt->bindParam(':titre', $data["type"]);
		$stmt->bindParam(':description', $data["description"]);
		$stmt->bindParam(':tmp_estime', $data["tmp_estime"]);
		$stmt->bindParam(':date', $data["date"]);
		$stmt->bindParam(':type', $data["type"]);
        $stmt->bindParam(':utilisateur', $data["id"]);

        if($stmt->execute()){
            CompetencePost::editTagPost($conn, $conn->lastInsertId(), $postTag);
        } else {
            return new Feedback(6, false, "Erreur requête, données incorrectes");
        }

        return new Feedback($conn->lastInsertId(), true, "Post correctement publié.");
	}

    /**
     * STATIC
     * Editer un post
     * 
     * Param
     */
    static function editPost($conn, $idPost, $data, $postTag = null) {
        if ($data != null) {
			$sqlPost = "UPDATE post SET ";
			foreach($data as $key => $value) {
                //On accepte pas le changement de type de post, au cas où
                if($key == "type" || $key == "date")
                    continue;

			    $sqlPost .= "$key = '$value',";
			}
			$sqlPost = rtrim($sqlPost, ',');
			$sqlPost .= " WHERE id = $idPost;";
		} else {
			return new Feedback(4, false, "Erreur Data, no contents found");
		}

		$stmt = $conn->prepare($sqlPost); 
		$stmt->execute();

		//die(var_dump($userTag));
		if ($postTag != NULL) {
			CompetencePost::editTagPost($conn, $idPost, $postTag);
		}

		return new Feedback(NULL, true, "Modification utilisateur reussie !");
    }
}