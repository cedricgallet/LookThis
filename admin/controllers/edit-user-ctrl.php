<?php
session_start();
require_once(dirname(__FILE__).'/../../config/regex.php');
require_once(dirname(__FILE__).'/../../models/User.php');//Models
require_once(dirname(__FILE__).'/../../config/config.php');//Constante + gestion erreur

// *****************************************SECURISER ACCES PAGE******************************************
if (!isset($_SESSION['user'])) {
    header('Location: /../../controllers/signIn-ctrl.php?msgCode=30'); 
    die;
}

$passDefault =  password_verify(DEFAULT_PASS, $_SESSION['user']->password);//On check si le mdp par défault est le meme que le mdp en cours

if($_SESSION['user']->email != DEFAULT_EMAIL && $passDefault != DEFAULT_PASS) {
    header('Location: /../../controllers/signIn-ctrl.php?msgCode=30'); 
    die;
        
}
// *******************************************************************************************************

// Initialisation du tableau d'erreurs
$errorsArray = array();
$title = 'Modification d\'un utilisateur en cours ...';

// Nettoyage de l'id passé en GET dans l'url
$id = intval(trim(filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT)));

// Nettoyage du status 
$state = intval(trim(filter_input(INPUT_POST, 'state', FILTER_SANITIZE_NUMBER_INT)));

//On ne controle que s'il y a des données envoyées 
if($_SERVER['REQUEST_METHOD'] == 'POST') // On controle le type(post) que si il y a des données d'envoyées 
{ 
    
     // pseudo
    // On verifie l'existance et on nettoie
    $pseudo = trim(filter_input(INPUT_POST, 'pseudo', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES));

    //On test si le champ n'est pas vide
    if(!empty($pseudo)){
        // On test la valeur
        $testRegex = preg_match('/'.REGEX_PSEUDO.'/',$pseudo);

        if(!$testRegex){
            $errorsArray['pseudo'] = 'Merci de choisir un pseudo valide';
        }
    }else{
        $errorsArray['pseudo'] = 'Le champ est obligatoire';
    }

    // **********************Email******************************

    // On verifie l'existance et on nettoie
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));

    //On test si le champ n'est pas vide
    if(!empty($email)) 
    {
        // On test la valeur
        $testMail = filter_var($email, FILTER_VALIDATE_EMAIL);

        if(!$testMail){
            $errorsArray['email'] = 'Le mail n\'est pas valide';
        }

    }else{
        $errorsArray['email'] = 'Le champ est obligatoire';
    }
    // ***************Mot de passe******************
    $password = $_POST['password'];

    //On test les autre champs si seulement le mdp actuel est le bon 
    if(!empty($password))
    {

        $cost =['cost' => 12]; // On hash le mot de passe avec Bcrypt, via un coût de 12
        $password = password_hash($password, PASSWORD_DEFAULT,$cost);


    }

    // Si il n'y a pas d'erreurs, on met à jour l'utilisateur.
    if(empty($errorsArray))
    {

        $user = new User($pseudo, $email, $password, "", $state);//On instancie/On récupére les infos 

        $result = $user->update($id);//On met a jour        

        if($result===true){//Si l ajout s'est bien passé = 1
            
            header('location: /../../admin/controllers/list-user-ctrl.php?msgCode=2');//On redirige av mess succés

        } else {
            // Si l'enregistrement s'est mal passé, on réaffiche le formulaire av un mess d'erreur.
            $msgCode = $result;
        }
        
    
    }

}else{
    $user = User::get($id);
    // Si l'utilisateur n'existe pas, on redirige vers la liste complète avec un code erreur
    if($user){
        $id = $user->id;
        $pseudo = $user->pseudo;
        $email =$user->email;
        $state =$user->state;
    } else {
        header('location: /../../admin/controllers/list-user-ctrl.php?msgCode=3');
        die;
    }
}


/* ************* VUES **************************/
require_once dirname(__FILE__) . '/../../views/templates/header.php';
require_once dirname(__FILE__) . '/../../admin/views/edit-user.php';
