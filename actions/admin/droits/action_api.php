<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/admin/droits/action_liste.php *******************/
/* Edition des organisateurs *******************************/
/* *********************************************************/
/* Dernière modification : le 24/11/14 *********************/
/* *********************************************************/


//Ajout d'un admin
if (isset($_POST['add']) &&
	!empty($_POST['login'][0])) {

	$count = $pdo->query('SELECT '.
			'u.id, '.
			'u.private_token, '.
			'u.public_token '.
		'FROM utilisateurs AS u '.
		'WHERE '.
			'u._etat = "active" AND '.
			'u.responsable = 1 AND '.
			'u.login = "'.secure($_POST['login'][0]).'"')
		or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
	$count = $count->fetch(PDO::FETCH_ASSOC);

	$add = false;

	if (!empty($count['id']) && empty($count['private_token'])) {
		$add = true;
		$ref = pdoRevision('utilisateurs', $count['id']);
		$pdo->exec('UPDATE utilisateurs SET '.
				'_auteur = '.(int) $_SESSION['user']['id'].', '.
				'_date = NOW(), '.
				'_message = "Création des tokens pour l\'accès API", '.
				'_ref = '.(int) $ref.', '.
				//---------------//
				'public_token = "'.sha1(uniqid().'public').'", '.
				'private_token = "'.sha1(uniqid().'private').'" '.
			'WHERE '.
				'id = '.(int) $count['id']);
	}
}


//On récupère l'indice du champ concerné
if (!empty($_POST['delete']) &&
	isset($_POST['login']) &&
	is_array($_POST['login'])) {
	$i = array_search($_POST['delete'], $_POST['login']);

	$count = $pdo->query('SELECT '.
			'u.id, '.
			'u.private_token, '.
			'u.public_token '.
		'FROM utilisateurs AS u '.
		'WHERE '.
			'u._etat = "active" AND '.
			'u.login = "'.secure($_POST['login'][$i]).'"')
		or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
	$count = $count->fetch(PDO::FETCH_ASSOC);
}


//On supprime un admin
if (!empty($i) &&
	isset($count) &&
	!empty($_POST['delete']) &&
	!empty($_POST['login'][$i])) {

	$delete = true;

	if (!empty($count['id']) && 
		!empty($count['private_token']) &&
		!empty($count['public_token'])) {
		$ref = pdoRevision('utilisateurs', $count['id']);
		$pdo->exec('UPDATE utilisateurs SET '.
				'_auteur = '.(int) $_SESSION['user']['id'].', '.
				'_ref = '.(int) $ref.', '.
				'_date = NOW(), '.
				'_message = "Suppression des tokens d\'accès à l\'API", '.
				//---------------//
				'public_token = NULL, '.
				'private_token = NULL '.
			'WHERE id = '.(int) $count['id']);
	}
}

$admins = $pdo->query('SELECT '.
		'id, '.
		'login, '.
		'nom, '.
		'prenom, '.
		'private_token, '.
		'public_token '.
	'FROM utilisateurs '.
	'WHERE '.
		'_etat = "active" AND '.
		'responsable = 1 '.
	'ORDER BY '.
		'nom ASC, prenom ASC')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$admins = $admins->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);



//Inclusion du bon fichier de template
require DIR.'templates/admin/droits/api.php';
