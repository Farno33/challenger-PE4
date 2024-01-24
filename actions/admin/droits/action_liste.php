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
	isset($_POST['nom'][0]) &&
	isset($_POST['prenom'][0]) &&
	isset($_POST['email'][0]) &&
	isset($_POST['telephone'][0])) {

	if (!isset($_POST['cas'])) $_POST['cas'] = array();
	if (!isset($_POST['responsable'])) $_POST['responsable'] = array();
	$cas = in_array('0', $_POST['cas']);
	$responsable = in_array('0', $_POST['responsable']);

	$add = false;
	
	/*if ($cas) {
		$count = $pdo->query('SELECT '.
			'COUNT(id) AS cid '.
			'FROM utilisateurs '.
			'WHERE '.
				'_etat = "active" AND '.
				'cas = 1 AND '.
				'login = "'.secure($_POST['login'][0]).'"')
			or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
		$count = $count->fetch(PDO::FETCH_ASSOC);
		
		if (empty($count['cid'])) {
			$people = api(['type' => 'soap', 'login' => secure($_POST['login'][0])]);

			if (!empty($people->login) &&
				empty($people->error)) {
				$_POST['nom'][0] = ucname($people->nom);
				$_POST['prenom'][0] = ucname($people->prenom);
				$_POST['email'][0] = $people->emailPro;
				$add = true;
			}

			if (empty($add)) {
				$people = api(['type' => 'ldap', 'login' => secure($_POST['login'][0])]);

				if (!empty($people->uid) &&
					empty($people->error)) {
					$_POST['nom'][0] = ucname($people->sn);
					$_POST['prenom'][0] = ucname($people->givenname);
					$_POST['email'][0] = $people->mail;
					$add = true;
				}
			}
		}
	}

	else */if (//!$cas && 
		!empty($_POST['nom'][0]) &&
		!empty($_POST['prenom'][0]) &&
		!empty($_POST['email'][0])) {
		$add = true;
	} 


	if ($add) {
		$pdo->exec('INSERT INTO utilisateurs SET '.
			'_auteur = '.(int) $_SESSION['user']['id'].', '.
			'_date = NOW(), '.
			'_message = "Ajout de l\'utilisateur", '.
			'_etat = "active", '.
			//---------------//
			(!$cas ? 'pass = "'.hashPass($_POST['login'][0]).'", ' : '').
			'cas = '.(int) ($cas ? 1 : 0).', '.
			'responsable = '.(int) ($responsable ? 1 : 0).', '.
			'nom = "'.secure($_POST['nom'][0]).'", '.
			'prenom = "'.secure($_POST['prenom'][0]).'", '.
			'email = "'.secure($_POST['email'][0]).'", '.
			'telephone = "'.secure($_POST['telephone'][0]).'", '.
			'login = "'.secure($_POST['login'][0]).'"');
	}
}


//On récupère l'indice du champ concerné
if ((!empty($_POST['delete']) || 
	!empty($_POST['edit'])) &&
	isset($_POST['id']) &&
	is_array($_POST['id']))
	$i = array_search(empty($_POST['delete']) ?
		$_POST['edit'] :
		$_POST['delete'],
		$_POST['id']);


//On edite un admin
if (!empty($i) &&
	empty($_POST['delete']) &&
	!empty($_POST['login'][$i]) &&
	isset($_POST['nom'][$i]) &&
	isset($_POST['prenom'][$i]) &&
	isset($_POST['email'][$i]) &&
	isset($_POST['telephone'][$i]) &&
	!empty($_POST['id'][$i]) &&
	intval($_POST['id'][$i])) {

	if (!isset($_POST['cas'])) $_POST['cas'] = array();
	if (!isset($_POST['responsable'])) $_POST['responsable'] = array();
	$cas = in_array($_POST['id'][$i], $_POST['cas']);
	$responsable = in_array($_POST['id'][$i], $_POST['responsable']);

	$exists = $pdo->query('SELECT login, cas '.
		'FROM utilisateurs '.
		'WHERE '.
			'_etat = "active" AND '.
			'id = '.(int) $_POST['id'][$i])
		or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
	$exists = $exists->fetch(PDO::FETCH_ASSOC);

	$modify = false;
	$changePass = false;

	if (empty($exists)) { }

	/*else if ($cas) {
		$count = $pdo->query('SELECT '.
			'COUNT(id) AS cid '.
			'FROM utilisateurs '.
			'WHERE '.
				'_etat = "active" AND '.
				'cas = 1 AND '.
				'login = "'.secure($_POST['login'][$i]).'" AND '.
				'id <> '.(int) $_POST['id'][$i])
			or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
		$count = $count->fetch(PDO::FETCH_ASSOC);

		if (empty($count['cid'])) {
			$people = api(['type' => 'soap', 'login' => secure($_POST['login'][$i])]);
			
			if (!empty($people->login) &&
				empty($people->error)) {
				$_POST['nom'][$i] = ucname($people->nom);
				$_POST['prenom'][$i] = ucname($people->prenom);
				$_POST['email'][$i] = $people->emailPro;
				$modify = true;
			}

			if (empty($modify)) {
				$people = api(['type' => 'ldap', 'login' => secure($_POST['login'][$i])]);

				if (!empty($people->uid) &&
					empty($people->error)) {
					$_POST['nom'][$i] = ucname($people->sn);
					$_POST['prenom'][$i] = ucname($people->givenname);
					$_POST['email'][$i] = $people->mail;
					$modify = true;
				}
			}
		}
	}
*/
	else if (//!$cas && 
		!empty($_POST['nom'][$i]) &&
		!empty($_POST['prenom'][$i]) &&
		!empty($_POST['email'][$i])) {
		$modify = true;
		$changePass = !$exists['cas'] && $_POST['login'][$i] != $exists['login'];
	} 


	if ($modify) {
		$ref = pdoRevision('utilisateurs', $_POST['id'][$i]);
		$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
		$pdo->exec('UPDATE utilisateurs SET '.
				'_auteur = '.(int) $_SESSION['user']['id'].', '.
				'_ref = '.(int) $ref.', '.
				'_date = NOW(), '.
				'_message = "Modification de l\'utilisateur", '.
				//---------------//
				($changePass ? 'pass = "'.hashPass($_POST['login'][$i]).'", ' : '').
				'cas = '.(int) ($cas ? 1 : 0).', '.
				'responsable = '.(int) ($responsable ? 1 : 0).', '.
				'nom = "'.secure($_POST['nom'][$i]).'", '.
				'prenom = "'.secure($_POST['prenom'][$i]).'", '.
				'email = "'.secure($_POST['email'][$i]).'", '.
				'telephone = "'.secure($_POST['telephone'][$i]).'", '.
				'login = "'.secure($_POST['login'][$i]).'" '.
			'WHERE '.
				'_etat = "active" AND '.
				'id = '.(int) $_POST['id'][$i]);
		$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
	}
}


//On supprime un admin
else if (!empty($i) &&
	!empty($_POST['delete']) &&
	!empty($_POST['id'][$i]) &&
	intval($_POST['id'][$i])) {

	$ref = pdoRevision('utilisateurs', $_POST['id'][$i]);
	$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
	$pdo->exec('UPDATE utilisateurs SET '.
			'_auteur = '.(int) $_SESSION['user']['id'].', '.
			'_ref = '.(int) $ref.', '.
			'_date = NOW(), '.
			'_etat = "desactive", '.
			'_message = "Suppression de l\'utilisateur" '.
		'WHERE id = '.(int) $_POST['id'][$i]);
	$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

	$delete = true;
}


$admins = $pdo->query('SELECT '.
		'id, '.
		'telephone, '.
		'login, '.
		'nom, '.
		'prenom, '.
		'email, '.
		'cas, '.
		'responsable '.
	'FROM utilisateurs '.
	'WHERE '.
		'_etat = "active" '.
	'ORDER BY '.
		'responsable DESC, cas ASC, login ASC')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$admins = $admins->fetchAll(PDO::FETCH_ASSOC);


//Inclusion du bon fichier de template
require DIR.'templates/admin/droits/liste.php';
