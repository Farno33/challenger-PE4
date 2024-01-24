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
	!empty($_POST['login'][0]) &&
	!empty($_POST['poste'][0]) &&
	isset($_POST['photo'][0])) {

	$count = $pdo->query('SELECT '.
			'u.id AS uid, '.
			'c.id AS cid '.
		'FROM utilisateurs AS u '.
		'LEFT JOIN contacts AS c ON '.
			'c.id_utilisateur = u.id AND '.
			'c._etat = "active" '.
		'WHERE '.
			'u._etat = "active" AND '.
			'u.responsable = 1 AND '.			
			'u.login = "'.secure($_POST['login'][0]).'"')
		or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
	$count = $count->fetch(PDO::FETCH_ASSOC);

	$add = false;

	if (!empty($count['uid']) && empty($count['cid'])) {
		$add = true;
		$pdo->exec('INSERT INTO contacts SET '.
			'_auteur = '.(int) $_SESSION['user']['id'].', '.
			'_date = NOW(), '.
			'_message = "Ajout de l\'utilisateur dans les contacts", '.
			'_etat = "active", '.
			//---------------//
			'id_utilisateur = '.$count['uid'].', '.
			'poste = "'.secure($_POST['poste'][0]).'", '.
			'photo = "'.secure($_POST['photo'][0]).'"');
	}
}


//On récupère l'indice du champ concerné
if ((!empty($_POST['delete']) || 
	!empty($_POST['edit'])) &&
	isset($_POST['login']) &&
	is_array($_POST['login'])) {
	$i = array_search(empty($_POST['delete']) ?
		$_POST['edit'] :
		$_POST['delete'],
		$_POST['login']);

	$count = $pdo->query('SELECT '.
			'u.id AS uid, '.
			'c.id AS cid '.
		'FROM utilisateurs AS u '.
		'LEFT JOIN contacts AS c ON '.
			'c.id_utilisateur = u.id AND '.
			'c._etat = "active" '.
		'WHERE '.
			'u._etat = "active" AND '.
			'u.login = "'.secure($_POST['login'][$i]).'"')
		or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
	$count = $count->fetch(PDO::FETCH_ASSOC);
}


//On edite un admin
if (!empty($i) &&
	isset($count) &&
	empty($_POST['delete']) &&
	!empty($_POST['poste'][$i]) &&
	isset($_POST['photo'][$i]) &&
	!empty($_POST['login'][$i])) {
	$modify = false;

	if (!empty($count['uid']) && 
		!empty($count['cid'])) {
		$modify = true;
		$ref = pdoRevision('contacts', $count['cid']);
		$pdo->exec('set FOREIGN_KEY_CHECKS = 0');
		$pdo->exec('UPDATE contacts SET '.
				'_auteur = '.(int) $_SESSION['user']['id'].', '.
				'_ref = '.(int) $ref.', '.
				'_date = NOW(), '.
				'_message = "Modification des données de contact", '.
				//---------------//
				'poste = "'.secure($_POST['poste'][$i]).'", '.
				'photo = "'.secure($_POST['photo'][$i]).'" '.
			'WHERE '.
				'_etat = "active" AND '.
				'id = '.(int) $count['cid']);
		$pdo->exec('set FOREIGN_KEY_CHECKS = 1');
	}
}


//On supprime un admin
else if (!empty($i) &&
	isset($count) &&
	!empty($_POST['delete']) &&
	!empty($_POST['login'][$i])) {

	$delete = true;

	// die(print_r(secure($_POST['login'][$i])));

	if (!empty($count['uid']) && 
		!empty($count['cid'])) {
		$ref = pdoRevision('contacts', $count['cid']);
		$pdo->exec('set FOREIGN_KEY_CHECKS = 0');
		$pdo->exec('UPDATE contacts SET '.
				'_auteur = '.(int) $_SESSION['user']['id'].', '.
				'_ref = '.(int) $ref.', '.
				'_date = NOW(), '.
				'_etat = "desactive", '.
				'_message = "Suppression du contact" '.
			'WHERE id = '.(int) $count['cid']) or die(print_r($pdo->errorInfo()));
		$pdo->exec('set FOREIGN_KEY_CHECKS = 1');
	}
}

$admins = $pdo->query('SELECT '.
		'id, '.
		'login, '.
		'nom, '.
		'prenom '.
	'FROM utilisateurs '.
	'WHERE '.
		'_etat = "active" AND '.
		'responsable = 1 '.
	'ORDER BY '.
		'nom ASC, prenom ASC')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$admins = $admins->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);


$contacts = $pdo->query('SELECT '.
		'id_utilisateur, '.
		'poste, '.
		'photo '.
	'FROM contacts '.
	'WHERE '.
		'_etat = "active"')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$contacts = $contacts->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);

//Inclusion du bon fichier de template
require DIR.'templates/admin/droits/contacts.php';
