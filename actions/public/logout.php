<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/public/logout.php *******************************/
/* Gére la déconnexion dans les différents modules *********/
/* *********************************************************/
/* Dernière modification : le 20/11/14 *********************/
/* *********************************************************/


//On ferme tous les modules
$cas = isset($_SESSION['user']['cas']);
//session_destroy();
//session_regenerate_id(true);
$_SESSION = [];


//Suppression des cookies
setcookie('token', '', time());
setcookie('data', '', time());


if ($cas &&
	isset($_GET['cas']))
	phpCAS::logoutWithRedirectService(url('accueil', true, false));


//Redirection vers l'accueil
die(header('location:'.url('accueil', false, false)));
