<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/admin/statistiques/action_ecoles.php ************/
/* Liste des Ecoles ****************************************/
/* *********************************************************/
/* Dernière modification : le 16/02/15 *********************/
/* *********************************************************/

//Ajout d'un admin
if (isset($_POST['add']) &&
	!empty($_POST['nom'][0]) &&
	isset($_POST['description'][0]) &&
	isset($_POST['latitude'][0]) && (
		empty($_POST['latitude'][0]) ||
		is_numeric($_POST['latitude'][0]) &&
		$_POST['latitude'][0] >= 0 &&
		$_POST['latitude'][0] <= 90) &&
	isset($_POST['longitude'][0]) && (
		empty($_POST['longitude'][0]) ||
		is_numeric($_POST['longitude'][0]) &&
		$_POST['longitude'][0] >= -180 &&
		$_POST['longitude'][0] <= +180)) {
	
	$pdo->exec('INSERT INTO sites SET '.
		'_auteur = '.(int) $_SESSION['user']['id'].', '.
		'_date = NOW(), '.
		'_message = "Ajout du site", '.
		'_etat = "active", '.
		//---------------//
		'nom = "'.secure($_POST['nom'][0]).'", '.
		'description = "'.secure($_POST['description'][0]).'", '.
		'latitude = '.(is_numeric($_POST['latitude'][0]) ? (float) $_POST['latitude'][0] : 'NULL').', '.
		'longitude = '.(is_numeric($_POST['longitude'][0]) ? (float) $_POST['longitude'][0] : 'NULL'));
	$add = true;

} else if (isset($_POST['add']))
	$add = false;


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
	!empty($_POST['nom'][$i]) &&
	isset($_POST['description'][$i]) &&
	isset($_POST['latitude'][$i]) && (
		empty($_POST['latitude'][$i]) ||
		is_numeric($_POST['latitude'][$i]) &&
		$_POST['latitude'][$i] >= 0 &&
		$_POST['latitude'][$i] <= 90) &&
	isset($_POST['longitude'][$i]) && (
		empty($_POST['longitude'][$i]) ||
		is_numeric($_POST['longitude'][$i]) &&
		$_POST['longitude'][$i] >= -180 &&
		$_POST['longitude'][$i] <= +180)) {

	$exists = $pdo->query('SELECT id '.
		'FROM sites '.
		'WHERE '.
			'_etat = "active" AND '.
			'id = '.(int) $_POST['id'][$i])
		or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
	$exists = $exists->fetch(PDO::FETCH_ASSOC);

	if (empty($exists))
		$modify = false;

	else {
		$ref = pdoRevision('sites', $_POST['id'][$i]);
		$pdo->exec('UPDATE sites SET '.
				'_auteur = '.(int) $_SESSION['user']['id'].', '.
				'_ref = '.(int) $ref.', '.
				'_date = NOW(), '.
				'_message = "Modification du site", '.
				//---------------//
				'nom = "'.secure($_POST['nom'][$i]).'", '.
				'description = "'.secure($_POST['description'][$i]).'", '.
				'latitude = '.(is_numeric($_POST['latitude'][$i]) ? (float) $_POST['latitude'][$i] : 'NULL').', '.
				'longitude = '.(is_numeric($_POST['longitude'][$i]) ? (float) $_POST['longitude'][$i] : 'NULL').' '.
			'WHERE '.
				'_etat = "active" AND '.
				'id = '.(int) $_POST['id'][$i]);
		$modify = true;
	}
}


//On supprime un admin
else if (!empty($i) &&
	!empty($_POST['delete']) &&
	!empty($_POST['id'][$i]) &&
	intval($_POST['id'][$i])) {

	$exists = $pdo->query('SELECT '.
			's.id, '.
			'(SELECT COUNT(m.id) FROM matchs AS m WHERE '.
				'm.id_site = s.id AND '.
				'm._etat = "active") AS nb_matchs '.
		'FROM sites AS s '.
		'WHERE '.
			's._etat = "active" AND '.
			's.id = '.(int) $_POST['id'][$i])
		or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
	$exists = $exists->fetch(PDO::FETCH_ASSOC);

	if (!empty($exists) &&
		empty($exists['nb_matchs'])) {
		$ref = pdoRevision('sites', $_POST['id'][$i]);
		$pdo->exec('UPDATE sites SET '.
				'_auteur = '.(int) $_SESSION['user']['id'].', '.
				'_ref = '.(int) $ref.', '.
				'_date = NOW(), '.
				'_etat = "desactive", '.
				'_message = "Suppression du site" '.
			'WHERE id = '.(int) $_POST['id'][$i]);

		$delete = true;
	} else
		$delete = false;
}



$sites = $pdo->query('SELECT '.
		's.id, '.
		's.nom, '.
		's.description, '.
		's.latitude, '.
		's.longitude, '.
		'(SELECT COUNT(m.id) FROM matchs AS m WHERE '.
				'm.id_site = s.id AND '.
				'm._etat = "active") AS nb_matchs '.
	'FROM sites AS s '.
	'WHERE s._etat = "active" '.
	'ORDER BY s.nom ASC') 
	->fetchAll(PDO::FETCH_ASSOC);



//Inclusion du bon fichier de template
require DIR.'templates/admin/tournois/sites.php';
