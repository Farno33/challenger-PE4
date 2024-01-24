<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* templates/admin/accueil.php *****************************/
/* Template de l'accueil de l'administration ***************/
/* *********************************************************/
/* Dernière modification : le 20/11/14 *********************/
/* *********************************************************/

if (!empty($_SESSION['user']['privileges']))
	require DIR.'templates/admin/_header_admin.php';

else
	require DIR.'templates/_header_nomenu.php';
