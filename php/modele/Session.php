<?php
class Session {

    private $id;
    private $date_depart;
    private $date_fin;

    private $post_id;
    private $utilisateur_id;
    
    function __construct() {
        
    }

    /**
     * STATIC
     * 
     * Retourne toutes les sessions d'un utilisateur
     * 
     * @param $id = id utilisateur
     * @return feedback
     */
    static function getSessionByUser($conn, $idUser) {
        $stmt = $conn->prepare("SELECT p.id AS idPost, pseudo, titre, p.description, p.tmp_estime, p.date, p.type, etat
                                FROM post p, utilisateur u, candidature c
                                WHERE c.candidat = $idUser
                                AND p.utilisateur = u.id
                                AND p.id = c.post");
        $stmt->execute();
        $count = $stmt->rowCount();
        
        if($count != 0) {
            
            $postDetail = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $cpt = 0;
            
            foreach ($postDetail as $row) {
                $idPost = $postDetail[$cpt]['idPost'];

                $postDetail[$cpt]['tag'] = CompetencePost::getTagPost($conn, $idPost);
                $cpt++;
            }
            
            return new Feedback($postDetail, true, "");
        }
        else
        return new Feedback(NULL, false, "Aucune candidature.");
    }
}