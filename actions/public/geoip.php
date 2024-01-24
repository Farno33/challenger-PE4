<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/public/login_ecoles.php *************************/
/* Gére la connexion pour les écoles ***********************/
/* *********************************************************/
/* Dernière modification : le 20/11/14 *********************/
/* *********************************************************/

if (empty($_SESSION['user']))
	die(header('location:'.url('accueil', false, false)));


$ip = getClientIp();
$ip = explode(',', $ip);
$ip = empty($ip[0]) ? 'UNKNOWN' : $ip[0];

if (empty($ip) ||
	$ip == 'UNKNOWN' ||
	isset($_SESSION['user']['geoip']) &&
	$_SESSION['user']['geoip'] >= 0)
	die();

$geoip = isConnected() ? http_get('http://freegeoip.net/json/'.$ip) : json_encode([]);

if (json_decode($geoip) === null) {
	$_SESSION['user']['geoip'] = isset($_SESSION['user']['geoip']) ? -$_SESSION['user']['geoip'] : 1;
	$_SESSION['user']['geoip']++;

	if ($_SESSION['user']['geoip'] > 2)
		$_SESSION['user']['geoip'] = 0;

	die();
} 

$_SESSION['user']['geoip'] = 0;
$pdo->exec('UPDATE connexions SET '.
		'geoip = "'. addslashes(htmlentities($geoip, ENT_NOQUOTES)).'" '.
	'WHERE '.
		'id = '.(int) $_SESSION['user']['active'].' AND '.
		'id_utilisateur = '.(int) $_SESSION['user']['id']);

die();