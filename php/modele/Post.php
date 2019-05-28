<?php
require_once("CompetencePost.php");

/*abstract */class Post {

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
			return $stmt->fetchObject(__CLASS__);
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
        var_dump($postTag);
        
        $data["date"] = date("Y-m-d", strtotime($data["date"])); 

		$stmt = $conn->prepare("INSERT INTO post VALUES (DEFAULT, :titre, :description, :tmp_estime, :date, :type, :utilisateur)");
		$stmt->bindParam(':titre', $data["type"]);
		$stmt->bindParam(':description', $data["description"]);
		$stmt->bindParam(':tmp_estime', $data["tmp_estime"]);
		$stmt->bindParam(':date', $data["date"]);
		$stmt->bindParam(':type', $data["type"]);
        $stmt->bindParam(':utilisateur', $data["id"]);

        $stmt->execute();

		CompetencePost::editTagPost($conn, $conn->lastInsertId(), $postTag);
	}

    /**
     * 
     */
    static function editPost() {

    }
}