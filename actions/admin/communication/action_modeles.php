<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/admin/ecoles/action_edition.php *****************/
/* Edition d'une école *************************************/
/* *********************************************************/
/* Dernière modification : le 16/02/15 *********************/
/* *********************************************************/

if (isset($_POST['connected']))
	die;

if (!empty($id_modele) &&
	!empty($_POST['save']) &&
	!empty($_POST['nom']) && (
		!empty($_POST['type']) && isset($_POST['sms']) ||
		empty($_POST['type']) && isset($_POST['titre']) && isset($_POST['email']))) {

	$exists = $pdo->query('SELECT '.
			'm.id '.
		'FROM modeles AS m '.
		'WHERE '.
			'm._etat = "active" AND '.
			'm.id = '.(int) $id_modele)
		or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
	$exists = $exists->fetch(PDO::FETCH_ASSOC);

	if (!empty($exists)) {
		$isSms = !empty($_POST['type']);
		$ref = pdoRevision('modeles', $id_modele);
		$pdo->exec('UPDATE modeles SET '.
				'_auteur = '.(int) $_SESSION['user']['id'].', '.
				'_ref = '.(int) $ref.', '.
				'_date = NOW(), '.
				'_message = "Modification du modele", '.
				//---------------//
				'type = "'.($isSms ? 'sms' : 'email').'", '.
				'nom = "'.secure($_POST['nom']).'", '.
				'titre = "'.secure($isSms ? '' : $_POST['titre']).'", '.
				'modele = "'.secure($isSms ? $_POST['sms'] : $_POST['email']).'" '.
			'WHERE id = '.(int) $id_modele);

		unset($id_modele); 
		$modify = true;
	} else 
		$modify = false;

}


if (!isset($_POST['type'])) $_POST['type'] = array();

//Ajout d'un admin
if (isset($_POST['add']) &&
	!empty($_POST['nom'][0])) {
	
	$pdo->exec('INSERT INTO modeles SET '.
		'_auteur = '.(int) $_SESSION['user']['id'].', '.
		'_date = NOW(), '.
		'_message = "Ajout du modèle", '.
		'_etat = "active", '.
		//---------------//
		'nom = "'.secure($_POST['nom'][0]).'", '.
		'type = "'.(in_array('0', $_POST['type']) ? 'sms' : 'email').'"');
	$add = true;

} else if (isset($_POST['add']))
	$add = false;


//On récupère l'indice du champ concerné
if (!empty($_POST['delete']) &&
	isset($_POST['id']) &&
	is_array($_POST['id']))
	$i = array_search($_POST['delete'], $_POST['id']);

//On supprime un admin
if (!empty($i) &&
	!empty($_POST['delete']) &&
	!empty($_POST['id'][$i]) &&
	intval($_POST['id'][$i])) {

	$exists = $pdo->query('SELECT '.
			'm.id '.
		'FROM modeles AS m '.
		'WHERE '.
			'm._etat = "active" AND '.
			'm.id = '.(int) $_POST['id'][$i])
		or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
	$exists = $exists->fetch(PDO::FETCH_ASSOC);

	if (!empty($exists)) {
		$ref = pdoRevision('modeles', $_POST['id'][$i]);
		$pdo->exec('UPDATE modeles SET '.
				'_auteur = '.(int) $_SESSION['user']['id'].', '.
				'_ref = '.(int) $ref.', '.
				'_date = NOW(), '.
				'_etat = "desactive", '.
				'_message = "Suppression du modele" '.
			'WHERE id = '.(int) $_POST['id'][$i]);

		$delete = true;
	} else
		$delete = false;
}



$modeles = $pdo->query('SELECT '.
		'm.id, m.type, m.nom, m.titre, m.modele '.
	'FROM modeles AS m '.
	'WHERE m._etat = "active" '.
	'ORDER BY m.type ASC, m.nom ASC')
	->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);


if (!empty($id_modele) &&
	empty($modeles[$id_modele]))
	die(require DIR.'templates/_error.php');

else if (!empty($id_modele)) {
	define('WYSIWYG', true);
	$modele = $modeles[$id_modele];
}

require DIR.'templates/admin/communication/modeles.php';