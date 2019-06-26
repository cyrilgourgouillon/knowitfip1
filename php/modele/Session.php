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
        $stmt = $conn->prepare("(SELECT s.id as idSession, c.candidat, p.id AS idPost, u.id AS idUser, pseudo, titre, p.description, p.tmp_estime, p.date, p.type, s.etat
                                FROM session s, post p, candidature c, utilisateur u
                                WHERE s.post = p.id
                                AND s.candidature = c.id
                                AND p.utilisateur = u.id
                                AND c.candidat = $idUser)
                                
                                UNION
                                
                                (SELECT s.id as idSession, c.candidat, p.id AS idPost,  u.id AS idUser, pseudo, titre, p.description, p.tmp_estime, p.date, p.type, s.etat
                                FROM session s, post p, candidature c, utilisateur u
                                WHERE s.post = p.id
                                AND s.candidature = c.id
                                AND p.utilisateur = u.id
                                AND p.utilisateur = $idUser)
                                ORDER BY date DESC");
        $stmt->execute();
        $count = $stmt->rowCount();
        
        if($count != 0) {
            
            $postDetail = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $cpt = 0;
            
            foreach ($postDetail as $row) {
                $idPost = $postDetail[$cpt]['idPost'];

                $postDetail[$cpt]['tag'] = CompetencePost::getTagPost($conn, $idPost);

                if ($postDetail[$cpt]['tag'] == NULL)
                    $postDetail[$cpt]['tag'] = array();
                
                $cpt++;
            }
            
            return new Feedback($postDetail, true, "");
        }
        else
        return new Feedback(NULL, false, "Aucune candidature.");
    }

    /**
     * STATIC
     *
     * Permet au candidat d'un knowledge ou à l'auteur d'une request
     * de noter la session qu'il a suivie. Clôt la session.
     *
     * @param $conn , la connexion à la BDD
     * @param $idSession , l'id de la session à noter
     * @param $note , la note attribuée
     * @return Feedback, l'objet indiquant le succès de la méthode
     */
    static function evaluateSession($conn, $idSession, $note) {
        $stmt = $conn->prepare("UPDATE session SET note = ?, etat = 'TERMINEE' WHERE id = ?");
        $stmt->execute(array($note, $idSession));

        return new Feedback(NULL, true, "Note affectée avec succès !");
    }

    //A partir de la session récupérer l'id du post et l'id de la candidature associés
}