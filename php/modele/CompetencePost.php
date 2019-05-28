<?php

class CompetencePost {

    private $post;
    private $competence;

    function __construct() {

    }

    /**
     * STATIC
     * Modifie les tags d'un post
     * 
     * Param - $conn : Connexion PDO
     *       - $idPost : id du Post
     *       - $postId : tableau d'id de competences
     */
    static function editTagPost($conn, $idPost, $postId) {
        $sqlUserTag = "INSERT INTO competence_post VALUES ";
		foreach($postId as $tag) {

			//si il n'existe pas deja cette compétence chez l'utilisateur
			if($tag != NULL || $tag != '') {
			    $sqlUserTag .= "($idPost, $tag),";
			}
		}
		$sqlUserTag = rtrim($sqlUserTag, ',');

        $stmt = $conn->prepare($sqlUserTag);

        CompetencePost::deleteAllCompetencePost($conn, $idPost);

		$stmt->execute();
    }

    /**
     * STATIC
     * 
     * Supprime les compétences concernant le post $id
     * 
     * Param - $conn : connexion PDO
     *       - $id : id du post
     */
    static function deleteAllCompetencePost($conn, $id) {
        $sqlUserTag = "DELETE FROM competence_post WHERE post = $id";
		$stmt = $conn->prepare($sqlUserTag); 
		$stmt->execute();
    }
}