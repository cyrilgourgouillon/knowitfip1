<?php
    session_start();

    class Utilisateur {

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

    function __construct() {

    }

    /**
     * STATIC
     * Check si utilisateur est connecté
     * 
     * Param - $id
     */
    static function isConnected() {
        if(isset($_SESSION['user'])) {
            return $_SESSION['user'];
        }else {
            return false;
        }
    }

    /**
     * Gets the basic user information.
     *
     * @param      PDO   $conn   The connection
     * @param      number   $id     The identifier
     *
     * @return     boolean  The basic user information.
     */
    static function getBasicUserInfo($conn, $id){
        $stmt = $conn->prepare("SELECT pseudo,avatar FROM utilisateur WHERE id = ?");
        if($stmt->execute(array($id))){
            $row = $stmt->fetch();
            return[
                'id' => $id,
                'pseudo' => $row['pseudo'],
                'avatar' => $row['avatar']
            ];
        }else{
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
    static function getUser($conn, $id){
        $stmt = $conn->prepare("SELECT id, nom, prenom, pseudo, avatar, mail, date_naissance, credit, description, reseau FROM utilisateur WHERE id = ?");
        $stmt->execute(array($id));
        $count = $stmt->rowCount();
        
        if($count != 0) {

            $userDetail = $stmt->fetchObject();
            $userTag = CompetenceUtilisateur::getTagUser($conn, $id);
            $wishTag = CompetenceUtilisateur::getWishTagUser($conn, $id);

            return new Feedback(
                [
                    "user" => $userDetail,
                    "tag" => $userTag,
                    "wishTag" => $wishTag
                ]
                , true, "");
        }
        else {
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
    static function signUpUser($conn, $nom, $prenom, $mail, $mdp) {
        //Si ce n'est pas vide
        if(preg_match("#^[a-zA-Z]{5,50}$#",$prenom) &&
            preg_match("#^[a-zA-Z]{5,50}$#",$nom) && 
            preg_match("#^[^\W][a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\@[a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\.[a-zA-Z]{2,4}$#", $mail) &&
            preg_match("#^[a-zA-Z0-9]{5,100}$#", $mdp)) {

            //Check if $mail already exist
            $stmt = $conn->prepare("SELECT id FROM utilisateur WHERE mail=:mail"); 
            $stmt->bindParam("mail", $mail,PDO::PARAM_STR);
            $stmt->execute();
            $count=$stmt->rowCount();

            if($count){
            //echo "Cette adresse mail est déjà utilisée !";
            return new Feedback(1, false, "Cette adresse mail est déjà utilisée !");
            }
            
            $mdp = password_hash($mdp, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO utilisateur (nom, prenom, mail, mdp, admin_level,credit) VALUES (:nom, :prenom, :mail, :mdp, 0, 0)");
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':prenom', $prenom);
            $stmt->bindParam(':mail', $mail);
            $stmt->bindParam(':mdp', $mdp);
            if($stmt->execute()){
                return new Feedback($conn->lastInsertId(), true, "");
            }else{
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
    static function signInUser($conn, $mail, $mdp) {

        if(preg_match("#^[^\W][a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\@[a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\.[a-zA-Z]{2,4}$#", $mail) &&
            preg_match("#^[a-zA-Z0-9]{5,100}$#", $mdp)) {
            
            $stmt = $conn->prepare("SELECT * FROM utilisateur WHERE mail=:mail"); 
            $stmt->bindParam("mail", $mail,PDO::PARAM_STR);
            $stmt->execute();
            $count=$stmt->rowCount();
            $data=$stmt->fetch(PDO::FETCH_OBJ);

            if($count && password_verify($mdp, $data->mdp)){
            $_SESSION["user"] = $data->id;
            return new Feedback($data->id, true, "Connexion réussie");
            }
            else{
            return new Feedback(3, false, "Adresse mail inconnue ou mot de passe erroné");
            } 

        }
        else {
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
    static function editUser($conn, $id, $data, $userTag = NULL, $wishTag = NULL) {

        //Update user values
        if ($data != null) {
            $sqlUser = "UPDATE utilisateur SET ";
            foreach($data as $key => $value) {
                $sqlUser .= "$key = '$value',";
            }
            $sqlUser = rtrim($sqlUser, ',');
            $sqlUser .= " WHERE id = $id;";
        } else {
            return new Feedback(4, false, "Erreur Data, no contents found");
        }

        $stmt = $conn->prepare($sqlUser); 
        $stmt->execute();

        //die(var_dump($userTag));
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
   * @param $conn, la connexion à la BDD
   * @param $id, l'id de l'Utilisateur
   * @return array formaté du profil utilsateur
   */
  static function showProfile($conn, $id)
  {
     $stmt = $conn->prepare("SELECT u.nom, u.prenom, u.avatar, u.credit, u.description, cu.niveau, cu.points_experience, c.libelle
                        FROM utilisateur u, competence c, competence_utilisateur cu WHERE u.id = ? and cu.utilisateur = u.id
                        and c.id = cu.competence");
     $stmt->execute(array($id));

     $res = $stmt->fetchAll();

     $json = array();
     $json['competences'] = array();

     $formated_array = array_reduce($res, function ($prev, $current) {
        $prev['prenom'] = $current['prenom'];
        $prev['nom'] = $current['nom'];
        $prev['credit'] = $current['credit'];
        $prev['$description'] = $current['description'];
        $comp['libelle'] = $current['libelle'];
        $comp['niveau'] = $current['niveau'];
        $comp['experience'] = $current['points_experience'];
        array_push($prev['competences'], $comp);

        return $prev;
     }, $json);

     return $formated_array;
  }

};
