<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/admin/configurations/action_liste.php ***********/
/* Edition des constantes **********************************/
/* *********************************************************/
/* Dernière modification : le 21/11/14 *********************/
/* *********************************************************/


//Ajout d'une tache
if (isset($_POST['add']) &&
	!empty($_POST['nom'][0]) &&
	!empty($_POST['periodicite'][0]) &&
	isValidCronTab($_POST['periodicite'][0]) && 
	!empty($_POST['script'][0])) {

	$active = !empty($_POST['active'][0]);

	$pdo->exec('INSERT INTO taches SET '.
		'_auteur = '.(int) $_SESSION['user']['id'].', '.
		'_date = NOW(), '.
		'_message = "Ajout de la tâche", '.
		//-----------//
		'nom = "'.secure($_POST['nom'][0]).'", '.
		'active = '.(!empty($active) ? 1 : 0).', '.
		'periodicite = "'.secure($_POST['periodicite'][0]).'", '.
		'script = "'.secure($_POST['script'][0]).'"');

	$add = true;
}

//On récupère l'indice du champ concerné
if ((!empty($_POST['delete']) || 
	!empty($_POST['edit'])) &&
	isset($_POST['id']) &&
	is_array($_POST['id'])) {
	$i = array_search(empty($_POST['delete']) ?
		$_POST['edit'] :
		$_POST['delete'],
		$_POST['id']);

	$exists = $pdo->query('SELECT id FROM taches WHERE '.
		'id = '.(int) $_POST['id'][$i].' AND '.
		'periodicite <> "test" AND '.
		'_etat = "active"')
		->fetch(PDO::FETCH_ASSOC);
}


//On edite une tache
if (isset($i) &&
	empty($_POST['delete']) &&
	!empty($_POST['nom'][$i]) &&
	!empty($_POST['periodicite'][$i]) &&
	isValidCronTab($_POST['periodicite'][$i]) &&
	!empty($_POST['script'][$i]) &&
	!empty($_POST['id'][$i])) {
	$active = in_array($_POST['id'][$i], $_POST['active']);

	if (!empty($exists)) {
		$ref = pdoRevision('taches', $_POST['id'][$i]);
		$pdo->exec('UPDATE taches SET '.
				'_auteur = '.(int) $_SESSION['user']['id'].', '.
				'_ref = '.(int) $ref.', '.
				'_date = NOW(), '.
				'_message = "Modification de la tâche", '.
				//------------------//
				'nom = "'.secure($_POST['nom'][$i]).'", '.
				'active = '.(!empty($active) ? 1 : 0).', '.
				'periodicite = "'.secure($_POST['periodicite'][$i]).'", '.
				'script = "'.secure($_POST['script'][$i]).'" '.
			'WHERE id = '.(int) $_POST['id'][$i]);
	}
	
	$modify = !empty($exists);
}


//On supprime une tache
else if (isset($i) &&
	!empty($_POST['delete']) &&
	!empty($_POST['id'][$i])) {

	if (!empty($exists)) {
		$ref = pdoRevision('taches', $_POST['id'][$i]);
		$pdo->exec('UPDATE taches SET '.
				'_auteur = '.(int) $_SESSION['user']['id'].', '.
				'_ref = '.(int) $ref.', '.
				'_date = NOW(), '.
				'_message = "Suppression de la taches", '.
				'_etat = "desactive" '.
			'WHERE id = '.(int) $_POST['id'][$i]);
	}

	$delete = !empty($exists);
}


$taches = $pdo->query('SELECT '.
		'id, '.
		'nom, '.
		'periodicite, '.
		'script, '.
		'active, '.
		'execution '.
	'FROM taches WHERE '.
		'_etat = "active" '.
	'ORDER BY nom ASC, id ASC')
	->fetchAll(PDO::FETCH_ASSOC);


$serviceOk = false;
$now = new DateTime();

foreach ($taches as $k => $tache) {
	if ($tache['periodicite'] == 'test') {
		if (!empty($tache['execution'])) {
			$date = new DateTime($tache['execution']);
			$date->add(new DateInterval('PT2M'));

			if ($date > $now)
				$serviceOk = true;
		}

		unset($taches[$k]);
		break;
	}
}

//Inclusion du bon fichier de template
require DIR.'templates/admin/configurations/taches.php';
