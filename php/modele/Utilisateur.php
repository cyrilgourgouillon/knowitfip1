<?php
session_start();

class Utilisateur
{

    protected $id;
    protected $nom;
    protected $prenom;
    protected $pseudo;
    protected $avatar;
    protected $mail;
    protected $date_naissance;
    protected $credit;
    protected $description;
    protected $admin_level;
    protected $reseau;

    function __construct()
    {

    }

    /**
     * STATIC
     * Check si utilisateur est connecté
     *
     * Param - $id
     */
    static function isConnected()
    {
        if (isset($_SESSION['user'])) {
            return $_SESSION['user'];
        } else {
            return false;
        }
    }

    /**
     * Gets the basic user information.
     *
     * @param PDO $conn The connection
     * @param number $id The identifier
     *
     * @return     boolean  The basic user information.
     */
    static function getBasicUserInfo($conn, $id)
    {
        $stmt = $conn->prepare("SELECT pseudo,avatar FROM utilisateur WHERE id = ?");
        if ($stmt->execute(array($id))) {
            $row = $stmt->fetch();
            return [
                'id' => $id,
                'pseudo' => $row['pseudo'],
                'avatar' => $row['avatar']
            ];
        } else {
            return false;
        }
    }

    /**
     * STATIC
     * Fonction permettant de recevoir les information basiques d'un utilisateur
     * en fonction de son id
     *
     * Param - $conn : PDO connection
     *       - $id  : User ID
     * Return - Feedback
     */
    static function getUser($conn, $id)
    {
        $stmt = $conn->prepare("SELECT id, nom, prenom, pseudo, avatar, mail, date_naissance, credit, description, reseau FROM utilisateur WHERE id = ?");
        $stmt->execute(array($id));
        $count = $stmt->rowCount();

        if ($count != 0) {

            $userDetail = $stmt->fetchObject();
            $userTag = CompetenceUtilisateur::getTagUser($conn, $id);
            $wishTag = CompetenceUtilisateur::getWishTagUser($conn, $id);

            if($userTag == NULL)
                $userTag = array();

            if($wishTag == NULL)
                $wishTag = array();

            return new Feedback(
                [
                    "user" => $userDetail,
                    "tag" => $userTag,
                    "wishTag" => $wishTag
                ]
                , true, "");
        } else {
            return new Feedback(NULL, false, "Utilisateur inconnu.");
        }
    }

    /**
     * STATIC
     * Inscrit un nouvel utilisateur Knowit, sauf s'il existe déjà
     *
     * Param - $conn : PDO connection
     * Return Feedback
     */
    static function signUpUser($conn, $nom, $prenom, $mail, $mdp)
    {
        //Si ce n'est pas vide
        if (preg_match("#^[a-zA-Z]{1,50}$#", $prenom) &&
            preg_match("#^[a-zA-Z]{1,50}$#", $nom) &&
            preg_match("#^[^\W][a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\@[a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\.[a-zA-Z]{2,4}$#", $mail) &&
            preg_match("#^[a-zA-Z0-9]{5,100}$#", $mdp)) {

            //Check if $mail already exist
            $stmt = $conn->prepare("SELECT id FROM utilisateur WHERE mail=:mail");
            $stmt->bindParam("mail", $mail, PDO::PARAM_STR);
            $stmt->execute();
            $count = $stmt->rowCount();

            if ($count) {
                //echo "Cette adresse mail est déjà utilisée !";
                return new Feedback(1, false, "Cette adresse mail est déjà utilisée !");
            }

            $mdp = password_hash($mdp, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO utilisateur (nom, prenom, mail, mdp, admin_level,credit) VALUES (:nom, :prenom, :mail, :mdp, 0, 0)");
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':prenom', $prenom);
            $stmt->bindParam(':mail', $mail);
            $stmt->bindParam(':mdp', $mdp);
            if ($stmt->execute()) {
                return new Feedback($conn->lastInsertId(), true, "");
            } else {
                return new Feedback(null, false, "Erreur d'insertion d'un nouvel utilisateur");
            }
        } else {
            //echo "Vérifiez les champs inscrits";
            return new Feedback(2, false, "Vérifiez les champs inscrits");
        }
    }

    /**
     * STATIC
     * Connecte un utilisateur
     *
     * Check si le compte $mail existe et si son mot de passe est valide.
     * Param - $conn : PDO connection
     * Return un object Feedback
     */
    static function signInUser($conn, $mail, $mdp)
    {

        if (preg_match("#^[^\W][a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\@[a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\.[a-zA-Z]{2,4}$#", $mail) &&
            preg_match("#^[a-zA-Z0-9]{5,100}$#", $mdp)) {

            $stmt = $conn->prepare("SELECT * FROM utilisateur WHERE mail=:mail");
            $stmt->bindParam("mail", $mail, PDO::PARAM_STR);
            $stmt->execute();
            $count = $stmt->rowCount();
            $data = $stmt->fetch(PDO::FETCH_OBJ);


            if ($count && password_verify($mdp, $data->mdp)) {
                $_SESSION["user"] = $data->id;
                return new Feedback($data->id, true, "Connexion réussie");
            } else {
                return new Feedback(3, false, "Adresse mail inconnue ou mot de passe erroné");
            }

        } else {
            return new Feedback(2, false, "Vérifiez les champs inscrits");
        }
    }

    /**
     * STATIC
     * Update les informations d'un utilisateur
     *
     * Param - $conn : PDO connection
     *       - $id : id d'un utilisateur
     *       - $data : tableau associatif de type 'nomChampsBD => valeur'
     *       - $userTag : [Optionnel] tableau de competence associe à un utilisateur
     * Return Feedback
     */
    static function editUser($conn, $id, $data, $userTag = NULL, $wishTag = NULL)
    {

        //Update user values
        if ($data != null) {
            $sqlUser = "UPDATE utilisateur SET ";
            foreach ($data as $key => $value) {
                if ($key == 'mdp')
                    $value = password_hash($value, PASSWORD_DEFAULT);
                $sqlUser .= "$key = " . $conn->quote($value) . ",";
            }
            $sqlUser = rtrim($sqlUser, ',');
            $sqlUser .= " WHERE id = $id;";
        } else {
            return new Feedback(4, false, "Erreur Data, no contents found");
        }

        $stmt = $conn->prepare($sqlUser);
        $stmt->execute();


        if ($userTag != NULL && $userTag != NULL) {
            CompetenceUtilisateur::editUserTag($conn, $id, $userTag, $wishTag);
        }

        return new Feedback(5, true, "Modification utilisateur reussie !");

    }

    /**
     * Envoie les données permettant
     * d'afficher un profil utilisateur.
     * Plus précisement, renvoie un tableau qui
     * associe à l'utilisateur concerné ses différentes
     * compétences.
     *
     * @param $conn , la connexion à la BDD
     * @param $id , l'id de l'Utilisateur
     * @return array formaté du profil utilsateur
     */
    static function showProfile($conn, $id)
    {
        $stmt = $conn->prepare("SELECT u.pseudo, u.avatar, u.credit, u.description, cu.niveau, cu.points_experience, c.libelle
                        FROM utilisateur u, competence c, competence_utilisateur cu WHERE u.id = ? and cu.utilisateur = u.id
                        and c.id = cu.competence");
        $stmt->execute(array($id));

        $res = $stmt->fetchAll();

        $json = array();
        $json['competences'] = array();

        $formated_array = array_reduce($res, function ($prev, $current) {
            $prev['pseudo'] = $current['pseudo'];
            $prev['credit'] = $current['credit'];
            $prev['description'] = $current['description'];
            $prev['avatar'] = $current['avatar'];
            $comp['libelle'] = $current['libelle'];
            $comp['niveau'] = $current['niveau'];
            $comp['experience'] = $current['points_experience'];
            array_push($prev['competences'], $comp);

            return $prev;
        }, $json);

        return $formated_array;
    }

    /**
     * Renvoie des statistiques sur l'activité de l'utilisateur
     * (nombre de knowledege, de request, d'amis dans le réseau...)
     *
     * @param $conn, la connexion à la BDD
     * @param $id, l'identifiant de l'utilisateur
     * @return Feedback contenant les données statistiques
     */
    static function showStats($conn, $id)
    {
        $stmtKDG = $conn->prepare("SELECT count(p.id) as knowledge_count
                            FROM utilisateur u, post p 
                            WHERE u.id = ? AND p.utilisateur = u.id AND p.type = 'Knowledge'");
        $stmtRQT = $conn->prepare("SELECT count(p.id) as request_count
                            FROM utilisateur u, post p 
                            WHERE u.id = ? AND p.utilisateur = u.id AND p.type = 'Request'");
        $stmtNWK = $conn->prepare("SELECT count(r.id) as network_size FROM reseau r, utilisateur u WHERE u.id = ? AND r.utilisateur = U.id");
        $stmtOLD = $conn->prepare("SELECT inscrit_depuis FROM utilisateur WHERE id = ?");

        $stmtKDG->execute(array($id));
        $stmtRQT->execute(array($id));
        $stmtNWK->execute(array($id));
        $stmtOLD->execute(array($id));

        $data = array();
        $data['data'] = array();
        array_push($data['data'], $stmtKDG->fetch(PDO::FETCH_ASSOC), $stmtRQT->fetch(PDO::FETCH_ASSOC),
            $stmtNWK->fetch(PDO::FETCH_ASSOC), $stmtOLD->fetch(PDO::FETCH_ASSOC));

        return new Feedback($data, true, "Statistiques récupérées avec succès !");
    }

    /**
     * Ajoute l'avatar de l'utilisateur
     *
     * @param $conn, la connexion à la BDD
     * @param $id, l'identifiant de l'utilisateur
     * @param $img, le chemin sur le serveur menant à l'avatar
     * @return un objet Feedback sans données indiquant le succès de la fonction
     */
    static function addAvatar($conn, $id, $img) {
        $stmt = $conn->prepare("UPDATE utilisateur SET avatar = ? WHERE id = ?");
        $stmt->execute(array($img, $id));

        return new Feedback(NULL, true, "Avatar uploadé avec succès !");
    }

}

;
