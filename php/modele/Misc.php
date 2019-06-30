<?php
class Misc {
    
    function __construct() {
        
    }

    static function rechercher($conn, $string) {
        $stmt = $conn->prepare("SELECT p.id, utilisateur, pseudo, titre, p.description, tmp_estime, date(date) as date, type
                                FROM post p, utilisateur u
                                WHERE u.id = p.utilisateur
                                AND titre LIKE '%$string%'
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
			return new Feedback(NULL, false, "Aucun post, ne correspond à votre recherche.");
    }
}