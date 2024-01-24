<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* includes/_challenger.php ********************************/
/* Actions relatives à l'application du Challenger *********/
/* *********************************************************/
/* Dernière modification : le 16/02/15 *********************/
/* *********************************************************/


//Inclusion des fonctions spéciales pour l'appli
require DIR . 'includes/SimpleHtmlDom/simple_html_dom.php';
require DIR . 'includes/_challenger_functions.php';


//Chargement des constantes définies dans la base de donnée
$constantes = $pdo->query('SELECT ' .
		'flag, ' .
		'valeur ' .
	'FROM configurations ' .
	'WHERE _etat = "active"')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$constantes = $constantes->fetchAll(PDO::FETCH_ASSOC);


foreach ($constantes as $constante) {
	if (!defined($constante['flag']))
		define($constante['flag'], makeValue($constante['valeur']));
}

if (!defined('APP_SPEED_ERROR')) 		define('APP_SPEED_ERROR',		1500);
if (!defined('APP_URL_CHALLENGE')) 		define('APP_URL_CHALLENGE',		'https://challenge-centrale-lyon.fr');
if (!defined('APP_URL_FACEBOOK')) 		define('APP_URL_FACEBOOK',		'https://www.facebook.com/ChallengeCentraleLyon');
if (!defined('APP_EMAIL_CHALLENGE')) 	define('APP_EMAIL_CHALLENGE',	'contact@challenge-centrale-lyon.fr');
if (!defined('APP_MAX_TRY_AUTH')) 		define('APP_MAX_TRY_AUTH',		5);
if (!defined('APP_WAIT_AUTH')) 			define('APP_WAIT_AUTH',			60);
if (!defined('APP_SESSION_MAX_TIME')) 	define('APP_SESSION_MAX_TIME',	900); //15 minutes
if (!defined('APP_SAVE_CONSTS')) 		define('APP_SAVE_CONSTS',		false);
if (!defined('APP_MESSAGE_LOGIN')) 		define('APP_MESSAGE_LOGIN',		'');
if (!defined('APP_ACTIVE_MESSAGE')) 	define('APP_ACTIVE_MESSAGE',	false);
if (!defined('APP_POINTS_1ER')) 		define('APP_POINTS_1ER',		100);
if (!defined('APP_POINTS_2E')) 			define('APP_POINTS_2E',			60);
if (!defined('APP_POINTS_3E')) 			define('APP_POINTS_3E',			40);
if (!defined('APP_DAYS_BETWEEN_MAILS')) define('APP_DAYS_BETWEEN_MAILS', 3);
if (!defined('APP_MAX_EMAILS')) 		define('APP_MAX_EMAILS',		5);
if (!defined('APP_FIN_PHASE1')) 		define('APP_FIN_PHASE1',		'2023-01-23 23:59:59'); //Début phase 2 (fin phase 1)
if (!defined('APP_DATE_MALUS')) 		define('APP_DATE_MALUS',		'2023-02-05 23:59:59'); //Début malus (fin phase 2)
if (!defined('APP_FIN_MALUS')) 			define('APP_FIN_MALUS',			'2023-03-05 23:59:59'); //Fin malus et donc des inscriptions
if (!defined('APP_FIN_INSCRIP')) 		define('APP_FIN_INSCRIP',		'2023-03-12 23:59:59'); //Fin des modifications
if (!defined('APP_FIN_CENTRALIENS')) 	define('APP_FIN_CENTRALIENS',	'2023-03-02 23:59:59'); //Fin des inscriptions des centraliens
if (!defined('APP_CENTRALIENS_PRIX_SOIREE')) 	define('APP_CENTRALIENS_PRIX_SOIREE',  '15'); //Prix de la soirée pour les centraliens
if (!defined('APP_CENTRALIENS_PRIX_PFOOD')) 	define('APP_CENTRALIENS_PRIX_PFOOD',	'7'); //Prix des pack~~food~~full pour les centraliens sportifs
if (!defined('APP_CENTRALIENS_NB_PFOOD')) 		define('APP_CENTRALIENS_NB_PFOOD',	  '100'); //Nombre de pack**full** possible
if (!defined('APP_CENTRALIENS_PRIX_GOURDE')) 	define('APP_CENTRALIENS_PRIX_GOURDE',	'3'); //Prix de la gourde pour les centraliens
if (!defined('APP_CENTRALIENS_NB_GOURDE')) 		define('APP_CENTRALIENS_NB_GOURDE',	  '300'); //Nombre de gourde restant
if (!defined('APP_CENTRALIENS_PRIX_TSHIRT')) 	define('APP_CENTRALIENS_PRIX_TSHIRT',	'4'); //Prix du tshirt pour les centraliens
if (!defined('APP_CENTRALIENS_NB_TSHIRT')) 		define('APP_CENTRALIENS_NB_TSHIRT',	  '200'); //Nombre de tshirt restant



$modulesAdmin = [
	'competition' 	=> ['Compétition', 		'Donne accès à de multiples listings ainsi qu\'à l\'outil d\'extraction de données et à la gestion des sports'],
	'droits'		=> ['Droits', 			'Offre la possibilité de la gestion des utilisateurs, des accès aux modules et aux écoles ainsi qu\'à la page Contact'],
	'ecoles'		=> ['Ecoles', 			'Fournit la gestion des écoles et des données associées notamment les tarifs et les messages envoyés'],
	'logement'		=> ['Logement', 		'Procure la gestion des chambres et des tentes, le recensement des disponibilités et la répartition des participants'],
	'perms' 		=> ['Permanances', 		'Permet la gestion des permanences et extraction des données associées'],
	'statistiques' 	=> ['Statistiques', 	'Présente un récapitulatif des données et des inscriptions en particulier en ce qui concerne les sports et les paiements'],
	'configurations' => ['Configurations', 	'Liste les différentes variables de configurations permettant un ajustement du fonctionnement du Challenger'],
	'tournois'		=> ['Tournois', 		'Organisation des tournois pour chaque sport par le biais des phases de poules et phases finales'],
	'communication'	=> ['Communication', 	'Gestion des messages (emails, SMS) envoyés aux participants et utilisateurs']
];

$modulesCentralien = [
	'prestations' 	=> ['Prestations', 	'Donne accès à l\'ensemble des options sélectionnables (Soirée, Pack-Foods, ...) ainsi qu\'à la définition des sports'],
	'equipes'		=> ['Equipes', 		'Liste les différentes équipes dont tu fais partie avec tous les autres centraliens inscrits dans ces présentes équipes'],
	//'codebar'		=> ['CodeBar', 		'Site de rechargement de ta carte participant'], //Présente l\'ensemble de l\'historique des dépenses faites au cours du Challenge'],
	'vptournoi'		=> ['VP tournoi', 	'Permet de gérer le tournoi dont tu es le VP.'],
	'permanances'   => ['Permanences', 	'C\'est ici qu\'il faut s\'inscire pour les permanances bénévoles.'],
];

$languesEcole = [
	'fr' 			=> 'Français',
	'en'			=> 'Anglais'
];

$labelsEtatChambre = [
	'noncontacte'	=> 'Pas contacté',
	'enattente'	 	=> 'En attente',
	'amies'			=> 'Pour amies',
	'amiesplus'	 	=> 'Avec amies',
	'lachee'	 	=> 'Lachée',
	'refusee'	 	=> 'Refusée',
];

$colorsEtatChambre = [
	'noncontacte'	=> '#CCC',
	'enattente'	 	=> '#CCF',
	'amies'			=> '#FF9',
	'amiesplus'	 	=> '#FC9',
	'lachee'	 	=> '#AFA',
	'pleine'	 	=> '#6A6',
	'refusee'	 	=> '#666',
];

$labelsFormatChambre = [
	'nonrenseigne'	=> 'Non renseigné',
	'court'	 		=> 'Court',
	'long_soiree'	=> 'Long (soirée)',
	'long_petitdej'	=> 'Long (petit dej)',
];

$colorsFormatChambre = [
	'nonrenseigne'	=> '#CCC',
	'court'	 		=> '#CEF',
	'long_soiree'	=> '#FDA',
	'long_petitdej'	=> '#FEB',
];

$labelsEtatClef = [
	'nonrecue'		=> 'Pas reçue',
	'recue'	 		=> 'Recue',
	'donnee'		=> 'Donnée',
	'recuperee'		=> 'Récupérée',
	'rendue'	 	=> 'Rendue',
	'perdue'	 	=> 'Perdue',
];

$colorsEtatClef = [
	'nonrecue'		=> '#CCC',
	'recue'	 		=> '#FC9',
	'donnee'		=> '#FF9',
	'recuperee'		=> '#AFA',
	'rendue'	 	=> '#666',
	'perdue'	 	=> '#F99',
];

$colorsZone = [
	'red',
	'green',
	'blue',
	'yellow',
	'orange',
	'purple',
	'magenta',
	'cyan'
];

$labelsPhasesFinales = [
	'finale' 		=> 'Finale',
	'petite_finale' => 'Petite finale',
	'demie' 		=> 'Demie',
	'quart' 		=> 'Quart',
	'huitieme' 		=> 'Huitième',
	'seizieme' 		=> 'Seizième'
];


if (!defined('NO_LOGIN') ||
	!NO_LOGIN) {

	//Pour le test AJAX (cela ne concerne que les utilisateurs avec une connexion DB ou cookie)
	if (PATH == 'check')
		die(!empty($_SESSION['user']) && (!empty($_SESSION['user']['cas']) ||
			!empty($_SESSION['user']['remember']) ||
			!empty($_SESSION['user']['last']) &&
			time() - $_SESSION['user']['last'] < APP_SESSION_MAX_TIME) ? '1' : '0');


	//Temps de session dépassé (cele ne concerne pas les connexions par CAS)
	if (		!empty($_SESSION['user']) &&
		empty($_SESSION['user']['cas']) &&
		empty($_SESSION['user']['remember']) &&
		!empty($_SESSION['user']['last']) &&
		time() - $_SESSION['user']['last'] >= APP_SESSION_MAX_TIME) {

		//Si plus de APP_SESSION_MAX_TIME+1min alors le message n'est pas affiche
		if (time() - $_SESSION['user']['last'] < 60 + APP_SESSION_MAX_TIME)
			$expire = true;

		unset($_SESSION['user']);
		//session_destroy();
		session_regenerate_id(true);
		$_SESSION = [];

		if (!empty($expire))
			$_SESSION['expire'] = true;
	}


	if (!empty($_SESSION['user']['last']) &&
		!empty($_SESSION['user']['id']) &&
		!empty($_SESSION['user']['active'])) {

		$user = $pdo->query('SELECT ' .
				'login, ' .
				'nom, ' .
				'prenom, ' .
				'email, ' .
				'telephone, ' .
				'cas, ' .
				'pass, ' .
				'private_token, ' .
				'public_token ' .
			'FROM utilisateurs ' .
			'WHERE ' .
				'id = ' . (int) $_SESSION['user']['id'] . ' AND ' .
				'_etat = "active"')
			->fetch(PDO::FETCH_ASSOC);

		if (empty($user))
			unset($_SESSION['user']);

		else {
			$pdo->exec('UPDATE connexions SET ' .
					'dernier = NOW() ' .
				'WHERE ' .
					'id_utilisateur = ' . (int) $_SESSION['user']['id'] . ' AND ' .
					'id = ' . (int) $_SESSION['user']['active']);

			$privileges = $pdo->query('SELECT ' .
					'module ' .
				'FROM droits_admin WHERE ' .
					'id_utilisateur = "' . (int) $_SESSION['user']['id'] . '" AND ' .
					'_etat = "active"')
				or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
			$privileges = $privileges->fetchAll(PDO::FETCH_ASSOC);


			$accesEcoles = $pdo->query('SELECT ' .
					'id_ecole ' .
				'FROM droits_ecoles WHERE ' .
					'id_utilisateur = "' . (int) $_SESSION['user']['id'] . '" AND ' .
					'_etat = "active"')
				or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
			$accesEcoles = $accesEcoles->fetchAll(PDO::FETCH_ASSOC);


			$_SESSION['user']['last'] = time();
			$_SESSION['user']['privileges'] = [];
			$_SESSION['user']['ecoles'] = [];


			foreach ($privileges as $privilege)
				if (in_array($privilege['module'], array_keys($modulesAdmin)))
					$_SESSION['user']['privileges'][] = $privilege['module'];


			foreach ($accesEcoles as $acces)
				$_SESSION['user']['ecoles'][] = $acces['id_ecole'];
		}
	} else
		unset($_SESSION['user']);


	$posLogin = strpos(PATH, 'login');
	if ($posLogin === false &&
		strpos(PATH, 'accueil') === false &&
		strpos(PATH, 'image') === false &&
		empty($_SESSION['user']) ||
		$posLogin === 0 &&
		isset($_GET['url']))
		$_SESSION['path'] = $posLogin === 0 ? $_GET['url'] : PATH;


	//Connexion auto via cookies 
	if (empty($_SESSION['user']) &&
		!empty($_COOKIE['token']) &&
		!empty($_COOKIE['data'])) {
		$hash = md5($_COOKIE['token'] . $_SERVER['HTTP_USER_AGENT']);

		$remember = $pdo->query('SELECT ' .
				'r.id_utilisateur, ' .
				'r.iv, ' .
				'u.pass, ' .
				'u.login ' .
			'FROM remember AS r ' .
			'JOIN utilisateurs AS u ON ' .
				'u.id = r.id_utilisateur AND ' .
				'u._etat = "active" ' .
			'WHERE ' .
				'token = "' . secure($_COOKIE['token']) . '" AND ' .
				'expire > NOW() AND ' .
				'hash = "' . secure($hash) . '"')
			->fetch(PDO::FETCH_ASSOC);

		$error = false;
		if (!empty($remember)) {
			if (verify($hash, $_COOKIE['data'])) {
				$user = $pdo->query('SELECT ' .
					'id, ' .
					'(SELECT COUNT(connexion) FROM connexions WHERE id_utilisateur = id) AS connexions ' .
				'FROM utilisateurs ' .
				'WHERE ' . 'id = "' . $remember['id_utilisateur'] . '" AND ' .
						'_etat = "active"') or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
				$user = $user->fetch(PDO::FETCH_ASSOC);
				
				if (empty($user))
					$error = true;
			} else	$error = true;
		} else $error = true;

		if (!empty($error)) {
			setcookie('token', '', time());
			setcookie('data', '', time());
		} else {
			$_POST['db'] = true;	// Peut-êre enlevable avec un peu d'attention; gardé pour ne pas trop altérer l'ancien comportement
			$cookie = true;
			die(require DIR . 'actions/public/login.php');
		}
	}
}
