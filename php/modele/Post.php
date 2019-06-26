<?php
require_once('../utils/Feedback.php');
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
     * Return un object feedback
     */
    static function getPost($conn, $idPost) {
        $stmt = $conn->prepare("SELECT utilisateur, pseudo, titre, p.description, tmp_estime, date, type FROM post p, utilisateur u WHERE p.id = $idPost AND u.id = p.utilisateur");
        $stmt->execute();
        $count = $stmt->rowCount();

		if($count != 0) {
            $postDetail = $stmt->fetchObject();
            $postTag = CompetencePost::getTagPost($conn, $idPost);
            
			return new Feedback(
                [
                    'post' => $postDetail,
                    'tag' => $postTag
                ]
                , true, "");
        }
		else
			return new Feedback(NULL, false, "Post inconnu.");
    }

    /**
     * STATIC
     * Recupere les informations des posts d'un user
     * 
     * Param - $idUser : id du user
     *       - $type : request ou knowledge
     * Return un object feedback
     */
    static function getPostByUser($conn, $idUser, $type) {
        $stmt = $conn->prepare("SELECT p.id, utilisateur, pseudo, titre, p.description, tmp_estime, date(date) as date, type
                                FROM post p, utilisateur u
                                WHERE p.utilisateur = $idUser
                                AND u.id = p.utilisateur
                                AND type = '$type'
                                ORDER BY p.date DESC");
        $stmt->execute();
        $count = $stmt->rowCount();

		if($count != 0) {
           
            $postDetail = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $cpt = 0;

            foreach ($postDetail as $row) {
                $idPost = $postDetail[$cpt]['id'];
                $stmt = $conn->prepare("SELECT c.id FROM post p, candidature c WHERE p.id = c.post AND p.id = $idPost");
                $stmt->execute();
                $count = $stmt->rowCount();

                $postDetail[$cpt]['nbCandidat'] = $count;
                $postDetail[$cpt]['tag'] = CompetencePost::getTagPost($conn, $idPost);
                $cpt++;
            }
            
			return new Feedback($postDetail, true, "");
        }
		else
			return new Feedback(NULL, false, "Post inconnu.");
    }

    /**
	 * STATIC
	 * Publier un Post
	 * 
	 * Param - $conn : PDO connection
	 *       - $id : id du Post
	 *       - $data : Tableau associatif de données associe à un utilisateur
	 *       - $postTag: Tableau de competence associe au post
	 * Return Feedback
	 */
	static function createPost($conn, $idUtil, $data, $postTag) {

		$stmt = $conn->prepare("INSERT INTO post VALUES (DEFAULT, :titre, :description, :tmp_estime, DEFAULT, :type, :utilisateur)");
		$stmt->bindParam(':titre', $data["titre"]);
		$stmt->bindParam(':description', $data["description"]);
		$stmt->bindParam(':tmp_estime', $data["tmp_estime"]);
		$stmt->bindParam(':type', $data["type"]);
        $stmt->bindParam(':utilisateur', $idUtil);

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
     * Param - $conn : connexion PDO
     *       - $idPost : id du post visé
     *       - $data : tableau associatif de données du post modifié
     *              - ATTENTION : KEY = CHAMPS BD
     *       - $postTag : tableau associatif de tags du post
     */
    static function editPost($conn, $idPost, $data, $postTag = null) {
        $stmt = $conn->prepare("SELECT id FROM post WHERE id = $idPost");
        $stmt->execute();
        $count = $stmt->rowCount();

		if($count == 0)
			return new Feedback(NULL, false, "Post inexistant");

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

    /**
     * STATIC
     * Recupere les informations d'un post
     * 
     * Param - $idPost : id du post
     * Return un object Feedback
     */
    static function deletePost($conn, $id) {
        CompetencePost::deleteAllCompetencePost($conn, $id);

        $sqlUserTag = "DELETE FROM post WHERE id = $id";
		$stmt = $conn->prepare($sqlUserTag); 
        $stmt->execute();
        
        return new Feedback(NULL, true, "");
    }
}