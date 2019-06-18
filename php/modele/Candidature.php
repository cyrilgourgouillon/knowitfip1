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
    * Recupere les informations des candidatures d'un post
    * 
    * @param - $idPost : id du post
    * @return Feedback
    */
    static function getCandidatureByPost($conn, $idPost) {
        $stmt = $conn->prepare("SELECT c.id as idCandidature, c.candidat as idCandidat, u.pseudo, u.date_naissance, u.avatar, u.description FROM
                                candidature c, utilisateur u
                                WHERE u.id = c.candidat
                                AND c.post =". $idPost);
        $stmt->execute();
        $count = $stmt->rowCount();
        
        if($count != 0) {
            
            $utilisateurDetail = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $cpt = 0;
            
            foreach ($utilisateurDetail as $row) {
                $idUser = $utilisateurDetail[$cpt]['idCandidat'];
                
                $age = NULL;
                
                if($utilisateurDetail[$cpt]['date_naissance'] != NULL)
                @$age = @date("Y-m-d") - @$utilisateurDetail[$cpt]['date_naissance'];
                
                unset($utilisateurDetail[$cpt]['date_naissance']);
                
                $utilisateurDetail[$cpt]['age'] = $age;
                $utilisateurDetail[$cpt]['tag'] = CompetenceUtilisateur::getTagUser($conn, $idUser);
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
        $stmt = $conn->prepare("SELECT p.id AS idPost, utilisateur, pseudo, titre, p.description, p.tmp_estime, p.date, p.type, etat
                                FROM post p, utilisateur u, candidature c
                                WHERE p.utilisateur = $idUser
                                AND p.utilisateur = c.candidat
                                AND p.id = c.post
                                AND u.id = p.utilisateur");
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
        
        return new Feedback(NULL, true, "Candidature de $idUser pour $idPost effectuée !");
    }

    /**
     * Renvoie le message et la réponse correspondant
     * à la candidature sélectionnée
     *
     * @param $conn, la connexion à la BDD
     * @param $idCand, l'identifiant de la candidature
     * @return un objet Feedback contenant les données
     */
    static function commentsOnCandidacy($conn, $idCand) {
        $stmt = $conn->prepare("SELECT message, reponse FROM candidature WHERE id = ?");
        $stmt->execute(array($idCand));
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return new Feedback($data, true, "Commentaires récupérés avec succès !");
    }

    /**
     * Permet de valider une candidature
     *
     * @param $conn, la connexion à la BDD
     * @param $idCand, l'identifiant de la candidature
     * @return Feedback, un objet indiquant le succès de la fonction
     */
    static function acceptCandidacy($conn, $idCand) {
        $stmt = $conn->prepare("UPDATE candidature SET etat = 'ACCEPTED' WHERE id = ?");
        $stmt->execute(array($idCand));

        return new Feedback(NULL, true, "Candidature acceptée avec succès !");
    }
}