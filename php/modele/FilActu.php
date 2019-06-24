<?php
class FilActu {

    function __construct() {
        
    }

    static function afficherFilActu($conn, $idUser) {
        $sql = "
        (SELECT p.id as idPost, p.type,  u.avatar, u.pseudo, p.titre, p.description, p.date
        FROM post p , competence_post cp, utilisateur u
        WHERE p.type = 'Request'
        AND p.id = cp.post
        AND p.utilisateur = u.id
        AND p.utilisateur <> $idUser
        AND cp.competence IN (SELECT cu.competence FROM competence_utilisateur cu WHERE cu.utilisateur = $idUser))

        UNION

        (SELECT p.id as idPost, p.type, u.avatar, u.pseudo, p.titre, p.description, p.date
        FROM post p , competence_post cp, utilisateur u
        WHERE p.type = 'Knowledge'
        AND p.id = cp.post
        AND p.utilisateur = u.id
        AND p.utilisateur <> $idUser
        AND cp.competence IN (SELECT csu.competence FROM competence_souhaitee_utilisateur csu WHERE csu.utilisateur = $idUser)
        ORDER BY date DESC)
        ";
        $stmt = $conn->prepare($sql);
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
        return new Feedback(NULL, false, "Il n'y a rien...");
    }
}