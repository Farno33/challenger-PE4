<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/admin/logement/action_chambres.php **************/
/* Liste des batiments *************************************/
/* *********************************************************/
/* Dernière modification : le 16/02/15 *********************/
/* *********************************************************/


$ecoles = $pdo->query('SELECT '.
		'e.id, '.
		'e.nom, '.
		'(SELECT COUNT(p.id) '.
			'FROM participants AS p '.
			'JOIN tarifs_ecoles AS te ON '.
				'te.id = p.id_tarif_ecole AND '.
				'te._etat = "active" '.
			'JOIN tarifs AS t ON '.
				't.id = te.id_tarif AND '.
				't._etat = "active" '.
			'LEFT JOIN chambres_participants AS cp ON '.
				'cp.id_participant = p.id AND '.
				'cp._etat = "active" '.
			'WHERE '.
				'p._etat = "active" AND '.
				'p.id_ecole = e.id AND '.
				't.logement = 1 AND '.
				'p.sexe = "h" AND '.
				'cp.id IS NULL) AS nb_need '.
	'FROM ecoles AS e '.
	'WHERE '.
		'e._etat = "active" '.
	'ORDER BY '.
		'e.nom ASC')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$ecoles = $ecoles->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);



$zones = $pdo->query('SELECT '.
		'z.id, '.
		'zone, '.
		'path, '.
		'color, '.
		'MAX(t.numero) AS max, '.
		'(SELECT COUNT(t1.id) '.
			'FROM tentes AS t1 '.
			'WHERE '.
				't1.id_zone = z.id AND '.
				't1._etat = "active") AS nb_tentes, '.
		(!empty($_GET['ecole']) && in_array($_GET['ecole'], array_keys($ecoles)) ? 
		'(SELECT COUNT(t2.id) '.
			'FROM tentes AS t2 '.
			'WHERE '.
				't2.id_ecole = '.(int) $_GET['ecole'].' AND '.
				't2.id_zone = z.id AND '.
				't2._etat = "active") AS nb_attrib_ecole, ' : '').
		'(SELECT COUNT(t3.id) '.
			'FROM tentes AS t3 '.
			'WHERE '.
				't3.id_ecole IS NOT NULL AND '.
				't3.id_zone = z.id AND '.
				't3._etat = "active") AS nb_attrib '.
	'FROM zones AS z '.
	'LEFT JOIN tentes AS t ON '.
		't._etat = "active" AND '.
		't.id_zone = z.id '.
	'WHERE '.
		'z._etat = "active" '.
	'GROUP BY z.id') 
	->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);



if (isset($_GET['add']) &&
	!empty($_POST['zone']) &&
	!empty($_POST['color']) &&
	!empty($_POST['path']) &&
	is_array($_POST['path']) &&
	count($_POST['path']) >= 3) {

	foreach ($_POST['path'] as $bound) {
		if (count($bound) != 2 ||
			!isset($bound['lat']) ||
			!isset($bound['lng']) ||
			!is_numeric($bound['lat']) ||
			!is_numeric($bound['lng']) ||
			$bound['lat'] < -90 || $bound['lat'] > 90 ||
			$bound['lng'] < -180 || $bound['lng'] > 180) {
			die;
		}
	}

	$pdo->exec('INSERT INTO zones SET '.
		'_auteur = '.(int) $_SESSION['user']['id'].', '.
		'_date = NOW(), '.
		'_message = "Ajout d\'une zone", '.
		//------------//
		'zone = "'.secure($_POST['zone']).'", '.
		'path = "'.secure(json_encode($_POST['path'])).'", '.
		'color = "'.secure($_POST['color']).'"');

	die;
}


if (isset($_GET['addTente']) &&
	!empty($_POST['zone']) &&
	in_array($_POST['zone'], array_keys($zones)) &&
	!empty($_POST['zone']) &&
	!empty($_POST['numero']) &&
	is_numeric($_POST['numero']) &&
	$_POST['numero'] > 0 &&
	isset($_POST['lat']) &&
	isset($_POST['lng']) && 
	is_numeric($_POST['lat']) &&
	is_numeric($_POST['lng']) &&
	$_POST['lat'] >= -90 && $_POST['lat'] <= 90 &&
	$_POST['lng'] >= -180 && $_POST['lng'] <= 180) {

	$numero = max((int) $zones[$_POST['zone']]['max'], max(0, (int) $_POST['numero']));
	if ($numero > 999)
		die;

	$pdo->exec('INSERT INTO tentes SET '.
		'_auteur = '.(int) $_SESSION['user']['id'].', '.
		'_date = NOW(), '.
		'_message = "Ajout d\'une tente", '.
		//------------//
		'id_zone = '.(int) $_POST['zone'].', '.
		'numero = "'.secure(sprintf("%03d", $numero)).'", '.
		'latitude = '.(float) $_POST['lat'].', '.
		'longitude = '.(float) $_POST['lng']);

	die(json_encode(['numero' => $numero, 'id' => $pdo->lastInsertId()]));
}


if (isset($_GET['editTente']) &&
	!empty($_POST['tente']) &&
	isset($_POST['lat']) &&
	isset($_POST['lng']) && 
	is_numeric($_POST['lat']) &&
	is_numeric($_POST['lng']) &&
	$_POST['lat'] >= -90 && $_POST['lat'] <= 90 &&
	$_POST['lng'] >= -180 && $_POST['lng'] <= 180) {

	$existe = $pdo->query('SELECT '.
			'id '.
		'FROM tentes '.
		'WHERE '.
			'_etat = "active" AND '.
			'id = '.(int) $_POST['tente'])
		->fetch(PDO::FETCH_ASSOC);

	if (empty($existe))
		die;


	$ref = pdoRevision('tentes', $existe['id']);
	$pdo->exec('UPDATE tentes SET '.
			'_auteur = '.(int) $_SESSION['user']['id'].', '.
			'_date = NOW(), '.
			'_ref = '.(int) $ref.', '.
			'_message = "Déplacement de la tente", '.
			//------------//
			'latitude = '.(float) $_POST['lat'].', '.
			'longitude = '.(float) $_POST['lng'].' '.
		'WHERE '.
			'id = '.$existe['id']);

	die;
}


if (isset($_GET['edit']) &&
	!empty($_POST['zone']) &&
	intval($_POST['zone']) &&
	!empty($_POST['path']) &&
	is_array($_POST['path']) &&
	count($_POST['path']) >= 3) {

	foreach ($_POST['path'] as $bound) {
		if (count($bound) != 2 ||
			!isset($bound['lat']) ||
			!isset($bound['lng']) ||
			!is_numeric($bound['lat']) ||
			!is_numeric($bound['lng']) ||
			$bound['lat'] < -90 || $bound['lat'] > 90 ||
			$bound['lng'] < -180 || $bound['lng'] > 180) {
			die('shit');
		}
	}

	$ref = pdoRevision('zones', (int) $_POST['zone']);
	$pdo->exec('UPDATE zones SET '.
			'_auteur = '.(int) $_SESSION['user']['id'].', '.
			'_date = NOW(), '.
			'_ref = '.(int) $ref.', '.
			'_message = "Modification du path de la zone", '.
			//------------//
			'path = "'.secure(json_encode($_POST['path'])).'" '.
		'WHERE '.
			'id = '.(int) $_POST['zone']) or die(print_r($pdo->errorInfo()));

	die;
}

if (!empty($_GET['zone']) &&
	empty($zones[$_GET['zone']]))
		unset($_GET['zone']);


if (isset($_GET['get'])) {
	$return = [];

	if (!empty($_POST['ecole']) && 
		in_array($_POST['ecole'], array_keys($ecoles)))
		$tentes = $pdo->query('SELECT '.
			't.id_zone, '.
			't.id, '.
			't.latitude, '.
			't.longitude, '.
			't.numero, '.
			't.places, '.
			't.id_ecole AS ecole '.
		'FROM tentes AS t '.
		'WHERE '.
			't._etat = "active"')
		->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_GROUP);


	foreach ($zones as $id => $zone) {
		if (!empty($_POST['zone']) && 
			$_POST['zone'] != $id)
			continue;

		$return[] = [
			'id' => $id,
			'zone' => $zone['zone'],
			'color' => $zone['color'],
			'path' => json_decode(unsecure($zone['path'])),
			'tentes' => !empty($tentes[$id]) ? $tentes[$id] : []];


	}

	die(json_encode($return));
}


if (isset($_GET['getTentes']) && 
	!empty($_POST['zone']) &&
	in_array($_POST['zone'], array_keys($zones))) {
	$return = [];

	$tentes = $pdo->query('SELECT '.
			't.id, '.
			't.latitude, '.
			't.longitude, '.
			't.numero, '.
			't.places, '.
			't.id_ecole AS ecole '.
		'FROM tentes AS t '.
		'WHERE '.
			't.id_zone = '.(int) $_POST['zone'].' AND '.
			't._etat = "active"')
		->fetchAll(PDO::FETCH_ASSOC);

	die(json_encode($tentes));
}



if (isset($_GET['set']) && 
	!empty($_POST['tente']) &&
	!empty($_POST['ecole']) &&
	in_array($_POST['ecole'], array_keys($ecoles))) {

	$existe = $pdo->query('SELECT '.
			't.id, '.
			't.id_ecole AS ecole '.
		'FROM tentes AS t '.
		'WHERE '.
			't.id = '.(int) $_POST['tente'].' AND '.
			'(t.id_ecole IS NULL OR 
				t.id_ecole = '.(int) $_POST['ecole'].') AND '.
			't._etat = "active"')
		->fetch(PDO::FETCH_ASSOC);

	if (empty($existe))
		die;

	$ref = pdoRevision('tentes', (int) $existe['id']);
	$pdo->exec('UPDATE tentes SET '.
			'id_ecole = '.(!empty($existe['ecole']) ? 'NULL' : (int) $_POST['ecole']).', '.
			//-------------//
			'_auteur = '.(int) $_SESSION['user']['id'].', '.
			'_date = NOW(), '.
			'_ref = '.(int) $ref.', '.
			'_message = "'.(!empty($existe['ecole']) ? 'Suppression de l\'attribution de la tente' : 
				'Ajout de l\'attribution de la tente').'" '.
			//------------//
		'WHERE '.
			'id = '.$existe['id']) or die(print_r($pdo->errorInfo()));

	die;
}




//Inclusion du bon fichier de template
require DIR.'templates/admin/logement/tentes.php';
