<?php

class CompetencePost {

    private $post;
    private $competence;

    function __construct() {

    }

    /**
     * 
     */
    static function editTagPost($conn, $id, $data) {
        $sqlUserTag = "INSERT INTO competence_post VALUES ";
		foreach($userTag as $tag) {

			$idCompetence = Competence::getIdByName($conn, $tag);

			//si il n'existe pas deja cette compétence chez l'utilisateur
			if($idCompetence != -1) {
			    $sqlUserTag .= "($id, $idCompetence),";
			}
		}
		$sqlUserTag = rtrim($sqlUserTag, ',');

        $stmt = $conn->prepare($sqlUserTag);

        CompetencePost::deleteAllCompetencePost($conn, $id);

		$stmt->execute();
    }

    /**
     * 
     */
    static function deleteAllCompetencePost($conn, $id) {
        $sqlUserTag = "DELETE FROM competence_post WHERE post = $id";
		$stmt = $conn->prepare($sqlUserTag); 
		$stmt->execute();
    }
}