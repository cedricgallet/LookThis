<?php
session_start(); // Démarrage de la session  

if(!isset($_SESSION['user']))// si la session n'existe pas ou si l'utilisateur n'est pas connecté on redirige
{
    header('Location:/../views/user/sigIn.php?msgCode=30');
    die();
}

// +++++++++++++++++++++TEMPLATES ET VUE++++++++++++++++++++++++++++
require_once(dirname(__FILE__).'/../views/templates/header.php');
require_once(dirname(__FILE__).'/../views/form/solutions.php');
require_once(dirname(__FILE__).'/../views/templates/footer.php');
