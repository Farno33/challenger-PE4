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

function makeFlag($str) {
	return preg_replace('/[^\p{L}\p{N}_]+/i', '', $str);
}

//Ajout d'une constante
if (defined('APP_SAVE_CONSTS') &&
	APP_SAVE_CONSTS &&
	isset($_POST['add']) &&
	!empty(makeFlag($_POST['flag'][0])) &&
	!empty($_POST['nom'][0]) &&
	!empty($_POST['value'][0])) {

	$count = $pdo->query('SELECT '.
		'COUNT(flag) AS cflag '.
		'FROM configurations '.
		'WHERE '.
			'_etat = "active" AND '.
			'flag = "'.makeFlag($_POST['flag'][0]).'"')
		or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
	$count = $count->fetch(PDO::FETCH_ASSOC);


	if (empty($count['cflag']))
		$pdo->exec('INSERT INTO configurations SET '.
			'_auteur = '.(int) $_SESSION['user']['id'].', '.
			'_date = NOW(), '.
			'_message = "Ajout de la constante", '.
			//-----------//
			'flag = "'.makeFlag($_POST['flag'][0]).'", '.
			'nom = "'.secure($_POST['nom'][0]).'", '.
			'valeur = "'.secure($_POST['value'][0]).'"');

	$add = empty($count['cflag']);
}

//On récupère l'indice du champ concerné
if ((!empty($_POST['delete']) || 
	!empty($_POST['edit'])) &&
	isset($_POST['last_flag']) &&
	is_array($_POST['last_flag'])) {
	$i = array_search(empty($_POST['delete']) ?
		$_POST['edit'] :
		$_POST['delete'],
		$_POST['last_flag']);

	$count = $pdo->query('SELECT '.
		'id '.
		'FROM configurations '.
		'WHERE '.
			'_etat = "active" AND '.
			'flag = "'.makeFlag($_POST['flag'][$i]).'"')
		or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
	$count = $count->fetch(PDO::FETCH_ASSOC);
}


//On edite une constante
if (isset($i) &&
	empty($_POST['delete']) &&
	!empty($_POST['nom'][$i]) &&
	!empty(makeFlag($_POST['flag'][$i])) &&
	!empty($_POST['value'][$i]) &&
	!empty($_POST['last_flag'][$i])) {

	if (!empty($count['id'])) {
		$ref = pdoRevision('configurations', $count['id']);
		$pdo->exec('UPDATE configurations SET '.
				'_auteur = '.(int) $_SESSION['user']['id'].', '.
				'_ref = '.(int) $ref.', '.
				'_date = NOW(), '.
				'_message = "Modification de la constante", '.
				//------------------//
				'nom = "'.secure($_POST['nom'][$i]).'", '.
				'flag = "'.makeFlag($_POST['flag'][$i]).'", '.
				'valeur = "'.secure($_POST['value'][$i]).'" '.
			'WHERE id = '.(int) $count['id']);
	}
	
	$modify = !empty($count['id']);
}


//On supprime une constante
else if (defined('APP_SAVE_CONSTS') &&
	APP_SAVE_CONSTS &&
	isset($i) &&
	!empty($_POST['delete']) &&
	!empty($_POST['last_flag'])) {

	if (!empty($count['id'])) {
		$ref = pdoRevision('configurations', $count['id']);
		$pdo->exec('UPDATE configurations SET '.
				'_auteur = '.(int) $_SESSION['user']['id'].', '.
				'_ref = '.(int) $ref.', '.
				'_date = NOW(), '.
				'_message = "Suppression de la constante", '.
				'_etat = "desactive" '.
			'WHERE id = '.(int) $count['id']);
	}

	$delete = !empty($count['id']);
}


$constantes = $pdo->query('SELECT '.
		'flag, '.
		'nom, '.
		'valeur '.
	'FROM configurations '.
	'WHERE '.
		'_etat = "active" '.
	'ORDER BY '.
		'flag ASC')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$constantes = $constantes->fetchAll(PDO::FETCH_ASSOC);


//Inclusion du bon fichier de template
require DIR.'templates/admin/configurations/liste.php';
