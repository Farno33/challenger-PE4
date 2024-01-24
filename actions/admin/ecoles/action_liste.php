<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/admin/ecoles/action_liste.php *******************/
/* Liste des Ecoles ****************************************/
/* *********************************************************/
/* Dernière modification : le 21/11/14 *********************/
/* *********************************************************/


if (!empty($_POST['del_ecole']) &&
	!empty($_POST['id']) &&
	intval($_POST['id'])) {
	/*$pdo->exec('DELETE FROM ecoles_sports WHERE id_ecole = '.(int) $_POST['id']);
	$pdo->exec('DELETE FROM equipes WHERE id_ecole = '.(int) $_POST['id']);
	$pdo->exec('DELETE FROM sportifs WHERE id_ecole = '.(int) $_POST['id']);
	$pdo->exec('DELETE FROM paiements WHERE id_ecole = '.(int) $_POST['id']);
	$pdo->exec('DELETE FROM participants WHERE id_ecole = '.(int) $_POST['id']);
	$pdo->exec('DELETE FROM ecoles WHERE id = '.(int) $_POST['id']);

	$delete = true;*/
}

if (!empty($_POST['empty_ecole']) &&
	!empty($_POST['id']) &&
	intval($_POST['id'])) {
	/*$pdo->exec('DELETE FROM ecoles_sports WHERE id_ecole = '.(int) $_POST['id']);
	$pdo->exec('DELETE FROM equipes WHERE id_ecole = '.(int) $_POST['id']);
	$pdo->exec('DELETE FROM sportifs WHERE id_ecole = '.(int) $_POST['id']);
	$pdo->exec('DELETE FROM paiements WHERE id_ecole = '.(int) $_POST['id']);
	$pdo->exec('DELETE FROM participants WHERE id_ecole = '.(int) $_POST['id']);

	$empty = true;*/
}


if (!empty($_POST['add_ecole']) &&
	!empty($_POST['nom'])) {
	
	$pdo->exec('set FOREIGN_KEY_CHECKS = 0');
	$pdo->exec('INSERT INTO ecoles SET '.
		'_auteur = '.(int) $_SESSION['user']['id'].', '.
		'_date = NOW(), '.
		'_message = "Ajout de l\'école", '.
		'_etat = "active", '.
		//------------//
		'adresse = "", '.
		'nom = "'.secure($_POST['nom']).'"') or die(print_r($pdo->errorInfo()));
	$pdo->exec('set FOREIGN_KEY_CHECKS = 1');

	$id = $pdo->lastInsertId();

	die(header('location:'.url('admin/module/ecoles/'.$id, false, false)));
}

$quotas_ = $pdo->query('SELECT '.
		'id_ecole, '.
		'quota, '.
		'valeur '.
	'FROM quotas_ecoles '.
	'WHERE _etat = "active"')
	->fetchAll(PDO::FETCH_ASSOC);

$quotas = [];
foreach ($quotas_ as $quota)
	$quotas[$quota['id_ecole']][$quota['quota']] = $quota['valeur'];


$ecoles = $pdo->query('SELECT '.
		'e.id AS eid, '.
		'e.id, '.
		'e.nom, '.
		'e.abreviation, '.
		'e.etat_inscription, '.
		'e.ecole_lyonnaise, '.
		'e.format_long, '.
		'i.token, '.
		'i.width, '.
		'i.height, '.
		'(SELECT COUNT(p1.id) FROM participants AS p1 WHERE p1._etat = "active" AND p1.id_ecole = e.id) AS nb_inscriptions, '.
		'(SELECT COUNT(p2.id) FROM participants AS p2 WHERE p2._etat = "active" AND p2.id_ecole = e.id AND p2.sportif = 1) AS nb_sportif, '.
		'(SELECT COUNT(p3.id) FROM participants AS p3 WHERE p3._etat = "active" AND p3.id_ecole = e.id AND p3.pompom = 1) AS nb_pompom, '.
		'(SELECT COUNT(p4.id) FROM participants AS p4 WHERE p4._etat = "active" AND p4.id_ecole = e.id AND p4.fanfaron = 1) AS nb_fanfaron, '.
		'(SELECT COUNT(p5.id) FROM participants AS p5 WHERE p5._etat = "active" AND p5.id_ecole = e.id AND p5.cameraman = 1) AS nb_cameraman '.
	'FROM ecoles AS e '.
	'LEFT JOIN images AS i ON '.
		'i.id = e.id_image AND '.
		'i._etat = "active" '.
	'WHERE '.
		'e._etat = "active" '.
	'ORDER BY e.nom ASC')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$ecoles = $ecoles->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);


//Historique de l'école
if (!empty($_POST['listing']) &&
	in_array($_POST['listing'], array_keys($ecoles))) {

	$history = "SELECT T2.*, T1.lvl, T3.nom AS _auteur_nom, T3.prenom AS _auteur_prenom FROM ".
		"(SELECT ".
	        "@r AS _id, ".
	        "(SELECT @r := _ref FROM ecoles WHERE id = _id) AS parent, ".
	        "@l := @l + 1 AS lvl ".
	    "FROM ".
	        "(SELECT @r := ".$_POST['listing'].", @l := 0) vars, ".
	        "ecoles m ".
	    "WHERE @r <> 0) T1 ".
		"JOIN ecoles T2 ON T1._id = T2.id ".
		"JOIN utilisateurs T3 ON T3.id = T2._auteur ".
		"ORDER BY T1.lvl ASC";
	$history = $pdo->query($history)->fetchAll(PDO::FETCH_ASSOC);
	$titre = 'Historique de "'.stripslashes($ecoles[$_POST['listing']]['nom']).'"';
	die(require DIR.'templates/admin/historique.php');
}


//Inclusion du bon fichier de template
require DIR.'templates/admin/ecoles/liste.php';
