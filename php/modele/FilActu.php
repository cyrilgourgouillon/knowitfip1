<?php
class FilActu {

    function __construct() {
        
    }

    static function afficherFilActu($conn, $idUser) {
        $sql = "
        SELECT p.id, p.type,  u.avatar, u.pseudo, p.titre, p.description
        FROM post p , competence_post cp, utilisateur u
        WHERE p.type = 'Request'
        AND p.id = cp.post
        AND p.utilisateur = u.id
        AND p.utilisateur <> $idUser
        AND cp.competence IN (SELECT cu.competence FROM competence_utilisateur cu WHERE cu.utilisateur = $idUser)

        UNION

        SELECT p.id, p.type, u.avatar, u.pseudo, p.titre, p.description
        FROM post p , competence_post cp, utilisateur u
        WHERE p.type = 'Knowledge'
        AND p.id = cp.post
        AND p.utilisateur = u.id
        AND p.utilisateur <> $idUser
        AND cp.competence IN (SELECT csu.competence FROM competence_souhaitee_utilisateur csu WHERE csu.utilisateur = $idUser)
        ";
    }
}