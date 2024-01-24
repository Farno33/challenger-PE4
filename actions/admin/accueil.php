<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/admin/accueil.php *******************************/
/* Accueil de l'administration *****************************/
/* *********************************************************/
/* Dernière modification : le 20/11/14 *********************/
/* *********************************************************/

if (empty($_SESSION['user']) ||
	empty($_SESSION['user']['privileges'])) 
	die(header('location:'.url('accueil', false, false)));

//Inclusion du bon fichier de template
require DIR.'templates/admin/accueil.php';