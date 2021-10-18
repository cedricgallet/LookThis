<?php
session_start();
require_once dirname(__FILE__) . '/../../models/Article.php';//Models
require_once(dirname(__FILE__).'/../../config/config.php');//Constante + gestion erreur


$title = 'Consultation d\'un article en cours ...';


// Nettoyage de l'id passé en GET dans l'url
$id = intval(trim(filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT)));

// Appel à la méthode statique permettant de récupérer tous les infos d'un seul article
$articleInfo = Article::getArticle($id);

// Si l'article n'existe pas, on redirige vers la liste complète avec un code erreur
if(!$articleInfo){
    header('location: /../admin/controllers/list-article-ctrl.php?msgCode=23');
}

/* ************* AFFICHAGE DES VUES **************************/

require_once dirname(__FILE__) . '/../../views/templates/header.php';
require_once dirname(__FILE__) . '/../../admin/views/display-article.php';
