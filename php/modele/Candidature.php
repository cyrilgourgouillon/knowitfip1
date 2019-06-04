<?php
class Candidature {

    private $id;
    private $message;
    private $tmp_estime;
    private $date;

    private $utilisateur_id;
    private $post_id;
    
    function __construct() {
        
    }

    /**
     * STATIC
     * Candidater à un post
     * 
     * @param - $conn : Connexion PDO
     *        - $idUser
     *        - $idPost
     *        - $data : Tableau assoc nomChampsBD => value
     * @return Feedback
     */
    static function candidaterPost($conn, $idUser, $idPost, $data) {
        $stmt = $conn->prepare("SELECT id FROM candidature WHERE utilisateur = $idUser AND post = $idPost");
        $stmt->execute();
        $count = $stmt->rowCount();

		if($count == 0)
            return new Feedback(NULL, false, "Candidature déjà effectuée sur ce post.");
            
        if ($data != null) {
            $sqlPost = "INSERT INTO candidature VALUES (DEFAULT, :message, :tmp_estime, DEFAULT, :utilisateur, :post)";
            $stmt = $conn->prepare($sqlPost);
            $stmt->bindParam("message", $date['message'], PDO::PARAM_STR);
            $stmt->bindParam("tmp_estime", $date['tmp_estime']);
            $stmt->bindParam("utilisateur", $date['utilisateur']);
            $stmt->bindParam("post", $date['post']);

            $stmt->execute();
        } else {
            return new Feedback(NULL, false, "Erreur Data, no contents found");
        }
    
        return new Feedback(NULL, true, "Modification utilisateur reussie !");
    }
}