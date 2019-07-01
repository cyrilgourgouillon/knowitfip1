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
    
    static function getUser($conn, $idCand) {
        $stmt = $conn->prepare("SELECT candidat FROM candidature WHERE id = $idCand");
        $stmt->execute();
        $candidatAccepte = $stmt->fetch();
        return $candidatAccepte[0];
    }
    /**
    * STATIC
    * Recupere les informations des candidatures d'un post
    * 
    * @param - $idPost : id du post
    * @return Feedback
    */
    static function getCandidatureByPost($conn, $idPost) {
        $stmt = $conn->prepare("SELECT c.id as idCandidature, c.candidat as idCandidat, c.etat, u.pseudo, u.date_naissance, u.avatar, u.description FROM
                                candidature c, utilisateur u
                                WHERE u.id = c.candidat
                                AND c.post = $idPost
                                ORDER BY c.date DESC");
        $stmt->execute();
        $count = $stmt->rowCount();
        
        if($count != 0) {
            
            $utilisateurDetail = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $cpt = 0;
            
            foreach ($utilisateurDetail as $row) {

                $idUser = $utilisateurDetail[$cpt]['idCandidat'];

                $stmtKDG = $conn->prepare("SELECT count(p.id) as knowledge_count
                                            FROM utilisateur u, post p 
                                            WHERE u.id = ? AND p.utilisateur = u.id AND p.type = 'Knowledge'");
                $stmtRQT = $conn->prepare("SELECT count(p.id) as request_count
                                            FROM utilisateur u, post p 
                                            WHERE u.id = ? AND p.utilisateur = u.id AND p.type = 'Request'");

                $stmtKDG->execute(array($idUser));
                $stmtRQT->execute(array($idUser));
                $knowledges = $stmtKDG->fetch();
                $requests = $stmtRQT->fetch(PDO::FETCH_ASSOC);

                $utilisateurDetail[$cpt]['knowledge_count'] = $knowledges['knowledge_count'];
                $utilisateurDetail[$cpt]['request_count'] = $requests['request_count'];

                
                $age = NULL;
                
                if($utilisateurDetail[$cpt]['date_naissance'] != NULL)
                    @$age = @date("Y-m-d") - @$utilisateurDetail[$cpt]['date_naissance'];
                
                unset($utilisateurDetail[$cpt]['date_naissance']);
                
                $utilisateurDetail[$cpt]['age'] = $age;
                $utilisateurDetail[$cpt]['tag'] = CompetenceUtilisateur::getTagUser($conn, $idUser);

                if ($utilisateurDetail[$cpt]['tag'] == NULL)
                    $utilisateurDetail[$cpt]['tag'] = array();

                $cpt++;
            }
            
            return new Feedback($utilisateurDetail, true, "");
        }
        else
        return new Feedback(NULL, false, "Aucune candidature pour ce post.");
    }
    
    /**
    * STATIC
    * Recupere les informations des candidatures d'un utilisateur
    * 
    * @param - $idUser : id du user
    * @return Feedback
    */
    static function getCandidatureByUser($conn, $idUser) {
        $stmt = $conn->prepare("SELECT p.id AS idPost, pseudo, u.id AS idUser, c.id AS idCandid, titre, p.description, p.tmp_estime, p.date, p.type, etat
                                FROM post p, utilisateur u, candidature c
                                WHERE c.candidat = $idUser
                                AND p.utilisateur = u.id
                                AND p.id = c.post
                                ORDER BY c.date DESC");
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
     * Renvoie le message et la réponse correspondant
     * à la candidature sélectionnée
     *
     * @param $conn, la connexion à la BDD
     * @param $idCand, l'identifiant de la candidature
     * @return un objet Feedback contenant les données
     */
    static function getCommentCandidature($conn, $idCand) {
        $stmt = $conn->prepare("SELECT candidat, message, tmp_estime, reponse, post, date FROM candidature WHERE id = ?");
        $stmt->execute(array($idCand));
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return new Feedback($data, true, "Commentaires récupérés avec succès !");
    }
    
    /**
    * STATIC
    * Candidater à un post
    * Notif
    * 
    * @param - $conn : Connexion PDO
    *        - $idUser
    *        - $idPost
    *        - $data : Tableau assoc nomChampsBD => value
    * @return Feedback
    */
    static function candidaterPost($conn, $idUser, $idPost, $data) {
        $stmt = $conn->prepare("SELECT id FROM candidature WHERE candidat = $idUser AND post = $idPost");
        $stmt->execute();
        $count = $stmt->rowCount();
        
        if($count != 0)
        return new Feedback(NULL, false, "Candidature déjà effectuée sur ce post.");
        
        $message =  "En attente";
        if ($data != null) {
            $sqlPost = "INSERT INTO candidature VALUES (DEFAULT, :message, NULL, :tmp_estime, :etat, DEFAULT, :candidat, :post)";
            $stmt = $conn->prepare($sqlPost);
            $stmt->bindParam("message", $data['message'], PDO::PARAM_STR);
            $stmt->bindParam("tmp_estime", $data['tmp_estime']); //nullable
            $stmt->bindParam("etat", $message);
            $stmt->bindParam("candidat", $idUser);
            $stmt->bindParam("post", $idPost);
            
            $stmt->execute();
        } else {
            return new Feedback(NULL, false, "Erreur Data, no contents found");
        }
        
        //-------Partie notification-------
        $stmt = $conn->prepare("SELECT utilisateur FROM post WHERE id = $idPost");
        $stmt->execute();
        $auteurPost = $stmt->fetch();

        $user = Utilisateur::getBasicUserInfo($conn, $idUser);
        Notification::addNotification($conn, $auteurPost[0], $idPost, "Candidat", $user['pseudo'] . " a candidaté à votre post !");
        //-------fin de la partie notification-------

        return new Feedback(NULL, true, "Candidature de $idUser pour $idPost effectuée !");
    }

    /**
     * Permet de valider une candidature
     * Valider une candidature refuse toutes les autres
     * Notif
     *
     * @param $conn, la connexion à la BDD
     * @param $idCand, l'identifiant de la candidature
     * @return Feedback, un objet indiquant le succès de la fonction
     */
    static function accepterCandidature($conn, $idCand, $reponse) {
        $stmt = $conn->prepare("UPDATE candidature SET etat = 'Accepté', reponse = ? WHERE id = ?");
        $stmt->execute(array($reponse, $idCand));

        //-------Partie notification-------
        $cdd = Candidature::getUser($conn, $idCand);

        Notification::addNotification($conn, $cdd, $idCand, "Candidature", "Une de vos candidatures a été acceptée !");
        //-------fin de la partie notification-------

        return new Feedback(NULL, true, "Candidature acceptée avec succès !");
    }

    /**
     * Valide la candidature une fois que la session commence
     *
     * @param      PDO   $conn      The connection
     * @param      int  $isCandid   Id of the candid
     */
    static function valideCandidature($conn, $idCandid){
        $stmt = $conn->prepare("UPDATE candidature SET etat = 'Validé' WHERE id = ?");
        $stmt->execute(array($idCandid));

        //-------Partie notification-------
        $cdd = Post::getUser($conn, $idCandid);

        Notification::addNotification($conn, $cdd, $idCandid, "Candidat", "Un candidat a validé votre acceptation !");
        //-------fin de la partie notification-------

        return new Feedback(NULL, true, "Candidature validée avec succès !");
    }

    /**
     * Annule une candidature
     * Notif
     *
     * @param      PDO  $conn      The connection
     * @param      int  $idCandid  The identifier candid
     */     
    static function annuleCandidature($conn, $idCandid){
        $stmt = $conn->prepare("UPDATE candidature SET etat = 'Annulé' WHERE id = ?");
        $stmt->execute(array($idCandid));

        //-------Partie notification-------
        $cdd = Candidature::getUser($conn, $idCandid);

        Notification::addNotification($conn, $cdd, $idCandid, "Candidat", "Un candidat a annulé sa candidature");
        //-------fin de la partie notification-------
        
        return new Feedback(NULL, true, "Candidature annulée avec succès !");
    }

    /**
     * Permet de refuser une candidature
     * Notif
     *
     * @param $conn, la connexion à la BDD
     * @param $idCand, l'identifiant de la candidature
     * @return Feedback, un objet indiquant le succès de la fonction
     */
    static function refuserCandidature($conn, $idCandid) {
        $stmt = $conn->prepare("UPDATE candidature SET etat = 'Refusé' WHERE id = ?");
        $stmt->execute(array($idCandid));

        //-------Partie notification-------
        $cdd = Candidature::getUser($conn, $idCandid);

        Notification::addNotification($conn, $cdd, $idCandid, "Postulation", "Une de vos candidatures a été refusée.");
        //-------fin de la partie notification-------

        return new Feedback(NULL, true, "Candidature refusée avec succès !");
    }

    /**
     * Lance une nouvelle session
     * 
     * @param $conn, la connexion à la BDD
     * @param $idCand, l'identifiant de la candidature
     * @return Feedback, un objet indiquant le succès de la fonction
     */
    static function startSession($conn, $idCand) {
        $stmt = $conn->prepare("SELECT post FROM candidature WHERE id = ?");
        $stmt->execute(array($idCand));

        $idPost = $stmt->fetch();

        $stmt = $conn->prepare("INSERT INTO session (post, candidature, date_depart, date_fin, etat) VALUES (:post, :candidature, DEFAULT, null, 'En cours')");
        $stmt->bindParam("post", $idPost[0]);
        $stmt->bindParam("candidature", $idCand);
        $stmt->execute();
        $sessionID = $conn->lastInsertId();

        $stmt = $conn->prepare("UPDATE candidature SET etat = 'Refusé' WHERE post = $idPost[0] AND etat <> 'Accepté'");
        $stmt->bindParam("post", $idPost[0]);
        $stmt->bindParam("candidature", $idCand);
        $stmt->execute();

        //-------Partie notification-------
        $cdd = Post::getUser($conn, $idCand);

        Notification::addNotification($conn, $cdd, $sessionID, "Session", "Une session a démarrée !");
        //-------fin de la partie notification-------

        return new Feedback(NULL, true, "Session démarée avec succès !");
    }

    static function isCandidat($conn, $idPost, $idUser) {
        $stmt = $conn->prepare("SELECT c.candidat AS idUser, c.message,  c.date, c.tmp_estime,  p.id as idPost FROM candidature c, post p WHERE c.post = p.id AND c.candidat = ? AND p.id = ? ");
        $stmt->execute(array($idUser, $idPost));

        return new Feedback($stmt->fetch(PDO::FETCH_ASSOC), true, '');
    }

}