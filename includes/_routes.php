<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* includes/_routes.php ************************************/
/* Déclaration de toutes les routes de l'application *******/
/* *********************************************************/
/* Dernière modification : le 20/11/14 *********************/
/* *********************************************************/


define('GET', '/?(?:\?.*)?');

$routes = !defined('SITE_ENABLED') || !SITE_ENABLED ? array(
	'(?:.*)' 							=> 'public/disabled.php',
) : array(
	//SPECIAL
	'cron'								=> '../cron.php',
	'receive'							=> '../sms.php',
	'api'								=> '../api.php',
	'history'							=> '../history.php',
	'update'							=> '../update.php',
	'metrics'							=> '../metrics.php',

	//PUBLIC
	'(?:accueil)?'						=> 'public/module.php',
	'login'								=> 'public/login.php',
	'creation'                          => 'public/creation.php',
	'geoip'								=> 'public/geoip.php',
	'logout'							=> 'public/logout.php',
	'profil'							=> 'public/profil.php',
	'image/(\w+)'						=> 'public/image.php',
	'contact'							=> 'public/contact.php',
	'classement'						=> 'public/classement.php',
	'score/(\w+)(?:/(\w+))?'			=> 'public/score.php',
	'reglement(?:/([a-z]{2,10}))?'		=> 'public/reglement.php',
	'([1-9][0-9]*)/(\w+)'				=> 'public/signature.php',

	//CENTRALIEN
	'centralien(?:/(\w+)?(?:/((?:\w+/?)*))?)?'	=> 'centralien/centralien.php',

	//ECOLE
	'ecoles/(\d+)(?:/accueil)?'			=> 'ecoles/accueil.php',
	'ecoles/(\d+)/participants'			=> 'ecoles/participants.php',
	'ecoles/(\d+)/sportifs'				=> 'ecoles/sportifs.php',
	'ecoles/(\d+)/recapitulatif'		=> 'ecoles/recapitulatif.php',
	'ecoles/(\d+)/import'				=> 'import/import.php',
	'ecoles/(\d+)/import/ajax'			=> 'import/ajax.php',

	//ADMIN
	'admin(?:/accueil)?'				=> 'admin/accueil.php',
	'admin/module/(\w+)(?:/(\w+))?'		=> 'admin/module.php',
);


foreach ($routes as $route => $action) {
	$routes[$route . GET] = $action;
	unset($routes[$route]);
}
