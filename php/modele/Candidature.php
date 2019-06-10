<?php
    require_once('../utils/Feedback.php');
    require_once('CompetenceUtilisateur.php');
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
            $stmt = $conn->prepare("SELECT u.id, u.avatar, u.pseudo, u.description, u.date_naissance
                                    FROM candidature c, utilisateur u
                                    WHERE post = $idPost
                                    AND c.candidat = u.id");
            $stmt->execute();
            $count = $stmt->rowCount();

            if($count != 0) {
           
                $utilisateurDetail = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $cpt = 0;
    
                foreach ($utilisateurDetail as $row) {
                    $idUser = $utilisateurDetail[$cpt]['id'];

                    $age = NULL;
                    
                    if($utilisateurDetail[$cpt]['date_naissance'] != NULL)
                        @$age = @date("Y-m-d") - @$utilisateurDetail[$cpt]['date_naissance'];
                    
                    unset($utilisateurDetail[$cpt]['date_naissance']);

                    $stmt = $conn->prepare("SELECT c.id FROM post p, candidature c WHERE p.id = c.post AND p.id = $idPost");
                    $stmt->execute();
                    $count = $stmt->rowCount();
    
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
                
            if ($data != null) {
                $sqlPost = "INSERT INTO candidature VALUES (DEFAULT, :message, :tmp_estime, DEFAULT, :candidat, :post)";
                $stmt = $conn->prepare($sqlPost);
                $stmt->bindParam("message", $data['message'], PDO::PARAM_STR);
                $stmt->bindParam("tmp_estime", $data['tmp_estime']); //nullable
                $stmt->bindParam("candidat", $idUser);
                $stmt->bindParam("post", $idPost);

                $stmt->execute();
            } else {
                return new Feedback(NULL, false, "Erreur Data, no contents found");
            }
        
            return new Feedback(NULL, true, "Candidature de $idUser pour $idPost effectuée !");
        }
    }