<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/admin/module.php ********************************/
/* Redirection suivant les modules de l'administration *****/
/* *********************************************************/
/* Dernière modification : le 20/11/14 *********************/
/* *********************************************************/

if (empty($_SESSION['user']) ||
	empty($_SESSION['user']['privileges'])) 
	die(header('location:'.url('accueil', false, false)));


//L'utilisateur peut-il accéder dans le module concerné
$module = $args[1][0];
if (empty($_SESSION['user']['privileges']) ||
	!in_array($module, $_SESSION['user']['privileges']))
	die(require DIR.'templates/_error.php');


//On insére le module concerné
require DIR.'actions/admin/module_'.$module.'.php';
