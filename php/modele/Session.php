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
        $stmt = $conn->prepare("UPDATE session SET note = ?, etat = 'Terminé' WHERE id = ?");
        $stmt->execute(array($note, $idSession));

        //Mettre les points d'éxperience
        self::addExperienceSession($conn, $idSession, $note);

        //Mettre les crédits pour les deux
        self::addCreditSession($conn, $idSession);

        return new Feedback(NULL, true, "Note affectée avec succès !");
    }

    /**
     * Permet de récupérer l'id du post et de la candidature
     * associés à la session
     *
     * @param $conn, la connexion à la BDD
     * @param $idSession, l'id de la session
     * @return Feedback, l'objet encapsulant les données (id du post, id de la session)
     */
    static function getPostAndCandidacyFromSession($conn, $idSession) {
        $stmt = $conn->prepare("SELECT post, candidature FROM session WHERE id = ?");
        $stmt->execute(array($idSession));
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return new Feedback($data, true, "Informations de la session récupérées avec succès !");
    }

    /**
     * Permet d'ajouter ou de supprimer des crédits à l'issue d'une session
     * Celui qui a donné le cours reçoit des crédits
     * Celui qui a reçu le cours perd des crédits
     *
     * @param $conn, la connexion à la BDD
     * @param $idSession, l'id de la session
     * @param $idUser, l'id de l'utilisateur
     * @return Feedback, l'objet qui encapsule les données à afficher
     */
    static function addCreditSession($conn, $idSession) {
        $stmt = $conn->prepare("SELECT cd.tmp_estime, cd.candidat, p.type, p.utilisateur FROM candidature cd, session s, post p WHERE s.id = ? and s.candidature = cd.id
                                and s.post = p.id");
        $stmt->execute(array($idSession));
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $credit = intval($result["tmp_estime"])*5;

        $data = array();

        // if knowledge
        // On ajoute au post et on enleve au candidat
        // Sinon
        // On ajoute au candidat et on enleve au post
        
        if($result['type'] == "Knowledge"){
            Utilisateur::addCredit($conn, $result['utilisateur'] , $credit);
            Utilisateur::addCredit($conn, $result['candidat'], 0 - $credit);
        }else{
            Utilisateur::addCredit($conn, $result['candidat'], $credit);
            Utilisateur::addCredit($conn, $result['utilisateur'], 0 - $credit);
        }

        return new Feedback(NULL , true, "Crédits modifiés avec succès !");
    }

    /**
     * Gets the credit of a session without dbModification
     *
     * @param      <type>    $conn       The connection
     * @param      <type>    $idSession  The identifier session
     * @param      <type>    $idUser     The identifier user
     *
     * @return     Feedback  The credit.
     */
    static function getCreditSession($conn, $idSession, $idUser) {
        $stmt = $conn->prepare("SELECT cd.tmp_estime, cd.candidat, p.type, p.utilisateur FROM candidature cd, session s, post p WHERE s.id = ? and s.candidature = cd.id
                                and s.post = p.id");
        $stmt->execute(array($idSession));
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $credit = intval($result["tmp_estime"])*5;

        $data;

        if (($result['candidat'] == $idUser && $result['type'] == "request")
            || ($result['utilisateur'] == $idUser && $result['type'] == "knowledge")) {
            $data = ["credit"=>"+$credit"];
        }else if (($result['candidat'] == $idUser && $result['type'] == "knowledge")
            || ($result['utilisateur'] == $idUser && $result['type'] == "request")) {
            $data = ["credit"=>"-$credit"];
        }

        return new Feedback($data, true, "Crédits recupérés avec succès !");
    }

     static function addExperienceSession($conn, $idSession, $note) {
        $stmt_for_users_and_post = $conn->prepare("SELECT cd.candidat as candidat, p.utilisateur as auteur, p.id as post 
                                                    FROM session s, post p, candidature cd
                                                    WHERE s.id = ? and s.candidature = cd.id and cd.post = p.id");
        $stmt_for_users_and_post->execute(array($idSession));
        $users_and_post = $stmt_for_users_and_post->fetch(PDO::FETCH_ASSOC);

        $stmt_for_skills = $conn->prepare("SELECT cp.competence FROM competence_post cp, post p WHERE p.id = ? and cp.post = p.id");
        $stmt_for_skills->execute(array($users_and_post['post']));
        $skills = $stmt_for_skills->fetchAll(PDO::FETCH_COLUMN);

        $stmt_for_session_lenght = $conn->prepare("SELECT cd.tmp_estime FROM candidature cd, session s WHERE s.id = ?
                                                   and cd.id = s.candidature");
        $stmt_for_session_lenght->execute(array($idSession));
        $session_length = $stmt_for_session_lenght->fetch(PDO::FETCH_ASSOC);

        $exp = intval($session_length['tmp_estime'])*intval($note)/2;

        CompetenceUtilisateur::addMultiplesPointExp($conn, $users_and_post['candidat'], $skills, $exp);
        CompetenceUtilisateur::addMultiplesPointExp($conn, $users_and_post['auteur'], $skills, $exp);

        return new Feedback(NULL, true, "Points d'expérience ajoutés avec succès !");
    }


    static function getExperienceSession($conn, $idSession) {
        $stmt_for_note_and_post = $conn->prepare("SELECT s.note, p.id as post 
                                                    FROM session s, post p 
                                                    WHERE s.id = ? and s.post = p.id");
        $stmt_for_note_and_post->execute(array($idSession));
        $note_and_post = $stmt_for_note_and_post->fetch(PDO::FETCH_ASSOC);

        $stmt_for_skills = $conn->prepare("SELECT cp.competence, c.libelle FROM competence_post cp, post p, competence c  WHERE p.id = ? and cp.post = p.id AND cp.competence = c.id");
        $stmt_for_skills->execute(array($note_and_post['post']));
        $skills = $stmt_for_skills->fetchAll(PDO::FETCH_ASSOC);

        $stmt_for_session_lenght = $conn->prepare("SELECT cd.tmp_estime FROM candidature cd, session s WHERE s.id = ?
                                                   and cd.id = s.candidature");
        $stmt_for_session_lenght->execute(array($idSession));
        $session_length = $stmt_for_session_lenght->fetch(PDO::FETCH_ASSOC);

        $exp = intval($session_length['tmp_estime'])*intval($note_and_post['note'])/2;

        $data = array();
        for ($i = 0; $i < count($skills); ++$i) {
            $data[$i] = array("competence" => array_values($skills[$i])[1], "experience" => $exp);
        }

        return new Feedback($data, true, "Points d'expérience ajoutés avec succès !");
    }
}