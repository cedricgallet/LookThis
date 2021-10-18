<?php
session_start();
require_once(dirname(__FILE__).'/../../models/Comment.php');//Models
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
$errorsArray = array(); //ou $errorsArray = []; //déclaration d'un tableau vide

// Tableau des sujets disponible //
$arraySubject = ['commenter un article','soummettre une idée','signaler un bug sur le site','signaler un lien mort','supprimer mon compte'];

// Tableau des catégories disponibles //
$arrayCategories = ['autre','web','réseau','humaine','applicative'];

$title = 'Modification d\'un commentaire en cours ...';

// Nettoyage de l'id passé en GET dans l'url
$id = intval(trim(filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT)));

// Nettoyage du status 
$state = intval(trim(filter_input(INPUT_POST, 'state', FILTER_SANITIZE_NUMBER_INT)));

//On ne controle que s'il y a des données envoyées 
if($_SERVER['REQUEST_METHOD'] == 'POST') // On controle le type(post) que si il y a des données d'envoyées 
{ 
    
     // ===========================Sujet=================
    $subject = trim($_POST['subject']);

    // ===========================Categories===========================

    $categories = trim($_POST['categories']);

    // ===========================Commentaire=================

    $comment = trim($_POST['comment']);

    // Si il n'y a pas d'erreurs, on met à jour l'article.
    if(empty($errorsArray))
    {

        $commentInfo = new Comment($subject, $categories, $comment, $state);//On instancie/On récupére les infos 

        $result = $commentInfo->updateComment($id);//On met a jour et on ajoute en bdd        

        if($result===true){//Si l ajout s'est bien passé = 1
            
            header('location: /../../admin/controllers/list-comment-ctrl.php?msgCode=6');//On redirige av mess succés
            die;

        } else {
            // Si l'enregistrement s'est mal passé, on réaffiche le formulaire av un mess d'erreur.
            $msgCode = $result;
        } 
    
    }

}else{
    $commentInfo = Comment::getComment($id);
    // Si le commentaire n'existe pas, on redirige vers la liste complète avec un code erreur
    if($commentInfo)
    {
        $id = $commentInfo->id;
        $subject = $commentInfo->subject;
        $categories = $commentInfo->categories;
        $comment = $commentInfo->comment;
        $state = $commentInfo->state;

    } else {
        header('location: /../../admin/controllers/list-comment-ctrl.php?msgCode=8');
        die;
    }
}

// +++++++++++++++++++++++++++++++++++VUES+++++++++++++++++++++++++++++++
require_once dirname(__FILE__) . '/../../views/templates/header.php';
require_once dirname(__FILE__) . '/../../admin/views/edit-comment.php';
