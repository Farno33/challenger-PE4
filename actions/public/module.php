<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/public/login.php ********************************/
/* Gére la connexion dans les différents modules ***********/
/* *********************************************************/
/* Dernière modification : le 20/11/14 *********************/
/* *********************************************************/


//S'il n'est pas connecté, on se connecte
if (empty($_SESSION['user']))
	die(header('location:'.url('login', false, false)));



$nomsEcoles = $pdo->query('SELECT '.
		'id, '.
		'nom, '.
		'etat_inscription '.
	'FROM ecoles '.
	'WHERE _etat = "active" '.
	'ORDER BY nom ASC')
	->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);


$admin_actif = !empty($_SESSION['user']['privileges']);
$communication_active = $admin_actif && in_array('communication', $_SESSION['user']['privileges']);
$competition_active = $admin_actif && in_array('competition', $_SESSION['user']['privileges']);
$ecoles_actives = $admin_actif &&
	in_array('ecoles', $_SESSION['user']['privileges']) ? array_keys($nomsEcoles) : 
	(!empty($_SESSION['user']['ecoles']) ? $_SESSION['user']['ecoles'] : []);

$count = ($admin_actif ? 1 : 0) + ($communication_active ? 1 : 0) + ($competition_active ? 1 : 0) + count($ecoles_actives);

//L'utilisateur n'a pas accès à une (ou plusieurs écoles), qu'à l'administration
if ($admin_actif &&
	$count == 1)
	die(header('location:'.url('admin', false, false)));


//L'utilisateur n'a accès qu'à une seule école mais pas à l'administration
if (!$admin_actif && 
	count($ecoles_actives) == 1) {

	if ($nomsEcoles[$_SESSION['user']['ecoles'][0]]['etat_inscription'] != 'fermee')
		die(header('location:'.url('ecoles/'.$_SESSION['user']['ecoles'][0].'/'.
			(empty($_SESSION['user']['first']) ? 'participants' : 'accueil'), false, false)));

	else
		die(header('location:'.url('login', false, false)));
}

//L'utilisateur n'a rien du tout, on va sur son profil
if (!$admin_actif &&
	!count($ecoles_actives)) {
		
	//Si c'est un centralien on va sur la page des options
	if (!empty($user) &&
		!empty($user['cas']))
		die(header('location:'.url('centralien', false, false)));

	else
		die(header('location:'.url('profil', false, false)));
}


//L'utilisateur sélectionne une école
if (!empty($_POST['ecole']) && 
	intval($_POST['ecole']) &&
	!empty($nomsEcoles[$_POST['ecole']]) && 
	in_array($_POST['ecole'], $ecoles_actives)) {

	if ($admin_actif ||
		$nomsEcoles[$_POST['ecole']]['etat_inscription'] != 'fermee')
		die(header('location:'.url('ecoles/'.$_POST['ecole'].'/'.
			(empty($_SESSION['user']['first']) ? 'participants' : 'accueil'), false, false)));

	else
		$fermee = $_POST['ecole'];
}

//On retourne les participants
if (!empty($_POST['filter'])) {
	$participants = $pdo->query('SELECT '.
			'p.id, '.
			'p.prenom, '.
			'p.nom, '.
			'e.nom AS enom, '.
			'e.id AS eid '.
		'FROM participants AS p '.
		'JOIN ecoles AS e ON '.
			'e.id = p.id_ecole AND '.
			'e._etat = "active" '.
		'WHERE '.
			'p._etat = "active" AND ('.
				'CONCAT(p.nom, " ", p.prenom) LIKE "%'.secure($_POST['filter']).'%" OR '.
				'CONCAT(p.prenom, " ", p.nom) LIKE "%'.secure($_POST['filter']).'%") '.
		'ORDER BY '.
			'p.nom ASC, '.
			'p.prenom ASC '.
		'LIMIT 20')
		->fetchAll(PDO::FETCH_ASSOC);

	foreach ($participants as $k => $participant) {
		$participants[$k]['url'] = url($participant['id'].'/'.substr(sha1(APP_SEED.$participant['eid'].'_'.$participant['id']), 0, 20), true, false);
	}

	die(json_encode($participants));
}

$unread = $pdo->query('SELECT COUNT(r.id) AS count FROM recus AS r WHERE r.ouvert = 0 AND '.
		'NOT (r.from = "'.getPhone(SMS_PHONE).'" AND '.
		'r.message LIKE "test%" OR '.
		'r.from LIKE "%'.EMAIL_MAIL.'%" AND '.
		'r.titre LIKE "test%" AND '.
		'r.message LIKE "test%")')->fetch(PDO::FETCH_ASSOC);
$unread = $unread['count'];


//Inclusion du bon fichier de template
require DIR.'templates/public/module.php';