<?php
require_once('Competence.php');
require_once('CompetenceUtilisateur.php');
ini_set('display_errors',1);

class Utilisateur implements JsonSerializable{

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
     * Return an utilisateur object thanks to his $id.
     */
    static function getUser($conn, $id){
        $stmt = $conn->prepare("SELECT * FROM utilisateur WHERE id = ?");
        $stmt->execute(array($id));
        return $stmt->fetchObject(__CLASS__);
    }

    /**
     * STATIC
     * Sign up an new user to Knowit.
     * Insert a line in database if $mail doesn't already exist.
     * Return int :
     *  0 - all good
     *  1 - already exist
     *  2 - problem with user input
     */
    static function signUpUser($conn, $prenom, $nom, $mail, $date_naissance, $mdp) {
        //Si ce n'est pas vide
        if(preg_match("#^[a-zA-Z]{5,50}$#",$prenom) &&
            preg_match("#^[a-zA-Z]{5,50}$#",$nom) && 
            preg_match("#^[^\W][a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\@[a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\.[a-zA-Z]{2,4}$#", $mail) &&
            preg_match("#^([0-2][0-9]|(3)[0-1])(\/)(((0)[0-9])|((1)[0-2]))(\/)\d{4}$#", $date_naissance) &&
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
            
            $date_naissance = date("Y-m-d", strtotime($date_naissance)); 
            
            $mdp = password_hash($mdp, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO utilisateur (nom, prenom, mail, date_naissance, mdp, admin_level,credit) VALUES (:nom, :prenom, :mail, :date_naissance, :mdp, 0, 0)");
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':prenom', $prenom);
            $stmt->bindParam(':mail', $mail);
            $stmt->bindParam(':date_naissance', $date_naissance);
            $stmt->bindParam(':mdp', $mdp);
            $stmt->execute();
        } else {
            //echo "Vérifiez les champs inscrits";
            return new Feedback(2, false, "Vérifiez les champs inscrits");
        }
    }

    /**
     * STATIC
     * Sign in a user
     * Check if $mail exist and if $mdp et matched with this account
     * Return : user object
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
                //echo "Tout est bon";
                $_SESSION["user"] = $data->id;
                return new Feedback($data->id, true, "Connexion réussie");
            }
            else{
                //echo "Adresse mail inconnue";
                return new Feedback(2, false, "Adresse mail inconnue ou mot de passe erroné");
            } 

        }
        else {
            //echo "Vérifiez les champs inscrits";
            return new Feedback(2, false, "Vérifiez les champs inscrits");
        }
    }

    /**
     * STATIC
     * Update user
     * $data is an associative array of user information
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
        }
        echo "\n".$sqlUser;

        $stmt = $conn->prepare($sqlUser); 
        $stmt->execute();

        //die(var_dump($userTag));
        if ($userTag != NULL) {
            Utilisateur::editUserTag($conn, $id, $userTag/*, $wishTag*/);
        }

    }

    /**
     * STATIC
     * IN : $id -> user ID, $userTag -> array of competence
     * Insert in database "competence_utilisateur"
     */
    static function editUserTag($conn, $id, $userTag, $wishTag = NULL) {
        //Update user tag
        
        $sqlUserTag = "INSERT INTO competence_utilisateur VALUES ";
        foreach($userTag as $tag) {

            $idCompetence = Competence::getIdByName($conn, $tag);
            $alreadyHaveIt = CompetenceUtilisateur::haveAlreadyIt($conn, $id, $idCompetence);

            //si il n'existe pas deja cette compétence chez l'utilisateur
            if(!$alreadyHaveIt && $idCompetence != -1) {
                $sqlUserTag .= "($id, $idCompetence, 0, 0, 0),";
            }
        }
        $sqlUserTag = rtrim($sqlUserTag, ',');

        $stmt = $conn->prepare($sqlUserTag); 
        $stmt->execute();
    }

    function JsonSerialize(){
        echo json_encode(
            [
                "id" => $this->id,
                "nom" => $this->nom,
                "prenom" => $this->prenom,
                "pseudo" => $this->pseudo,
                "avatar" => $this->avatar,
                "mail" => $this->mail,
                "date_naissance" => $this->date_naissance,
                "credit" => $this->credit,
                "description" => $this->description
            ],
            JSON_PRETTY_PRINT
        );
    }
};
