<?php
function accueil()
{
    require_once('vue/user/accueil.tpl');
}

function ident()
{
    if (isset($_SESSION['profil'])) {
        header('Location: index.php?controle=user&action=erreur&numErr=3');
        return;
    }
    if (count($_POST) == 0) {
        require_once('vue/user/ident.tpl');
        return;
    }
    $login = $_POST['login'];
    $pass = $_POST['pass'];
    require_once('modele/userDB.php');
    $res = verif_ident($login, $pass, $profil);
    if (!$res) require_once('vue/user/ident.tpl');
    else {
        $_SESSION['profil'] = $profil;
        header('Location: index.php?controle=user&action=accueil');
    }
}

function register()
{
    if (count($_POST) == 0) {
        require_once('vue/user/register.tpl');
        return;
    }
    $data = array();
    foreach ($_POST as $cle => $valeur) {
        $data[$cle] = $valeur;
    }
    require_once('modele/userDB.php');
    addUser($data);
}

function erreur()
{
    if (!isset($_GET['numErr']))
        $msgErr = 'Une erreur s\'est produite ...';
    else {
        switch ($_GET['numErr']) {
            case '1':
                $msgErr = 'La page n\'existe pas ...';
                break;
            case '2':
                $msgErr = 'Ce service est indisponible pour le moment ...';
                break;
            case '3':
                $msgErr = 'Vous êtes déjà connecté ...';
                break;
            default:
                $msgErr = 'Une erreur s\'est produite ...';
        }
    }
    require_once('vue/user/erreur.tpl');
}

function deconnect()
{
    session_destroy();
    header('Location: index.php?controle=user&action=accueil');
}

?>
