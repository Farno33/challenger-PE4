<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/admin/ecoles/action_tarification.php ************/
/* Définition des tarifs accessibles par les école *********/
/* *********************************************************/
/* Dernière modification : le 03/12/14 *********************/
/* *********************************************************/

$sports = $pdo->query('SELECT '.
		'id, '.
		'sport, '.
		'sexe '.
	'FROM sports '.
	'WHERE '.
		'_etat = "active" '.
	'ORDER BY '.
		'sport ASC')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$sports = $sports->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);


$ecoles = $pdo->query('SELECT '.
		'id, '.
		'nom, '.
		'ecole_lyonnaise '.
	'FROM ecoles '.
	'WHERE '.
		'_etat = "active" '.
	'ORDER BY '.
		'nom ASC')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$ecoles = $ecoles->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);




$tarifs = $pdo->query($tarifs_sql = 'SELECT '.
		't.id AS tid, '.
		't.*, '.
		'COUNT(te.id_ecole) AS teid '.
	'FROM tarifs AS t '.
	'LEFT JOIN tarifs_ecoles AS te ON '.
		'te.id_tarif = t.id AND '.
		'te._etat = "active" '.
	'WHERE '.
		't._etat = "active" '.
	'GROUP BY '.
		't.id '.
	'ORDER BY '.
		'nom ASC')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$tarifs = $tarifs->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);


//Historique du tarif
if (!empty($_POST['listing']) &&
	in_array($_POST['listing'], array_keys($tarifs))) {

	$history = "SELECT T2.*, T1.lvl, T3.nom AS _auteur_nom, T3.prenom AS _auteur_prenom FROM ".
		"(SELECT ".
	        "@r AS _id, ".
	        "(SELECT @r := _ref FROM tarifs WHERE id = _id) AS parent, ".
	        "@l := @l + 1 AS lvl ".
	    "FROM ".
	        "(SELECT @r := ".$_POST['listing'].", @l := 0) vars, ".
	        "tarifs m ".
	    "WHERE @r <> 0) T1 ".
		"JOIN tarifs T2 ON T1._id = T2.id ".
		"JOIN utilisateurs T3 ON T3.id = T2._auteur ".
		"ORDER BY T1.lvl ASC";
	$history = $pdo->query($history)->fetchAll(PDO::FETCH_ASSOC);
	$titre = 'Historique de "'.stripslashes($tarifs[$_POST['listing']]['nom']).'"';
	die(require DIR.'templates/admin/historique.php');
}


//Ajout d'un tarif
if (isset($_POST['add']) &&
	isset($_POST['ecole_lyonnaise'][0]) &&
	in_array($_POST['ecole_lyonnaise'][0], ['0', '1']) &&
	isset($_POST['long'][0]) &&
	in_array($_POST['long'][0], ['0', '1']) &&
	isset($_POST['for_pompom'][0]) &&
	in_array($_POST['for_pompom'][0], ['yes', 'or', 'no']) &&
	isset($_POST['for_cameraman'][0]) &&
	in_array($_POST['for_cameraman'][0], ['yes', 'or', 'no']) &&
	isset($_POST['for_fanfaron'][0]) &&
	in_array($_POST['for_fanfaron'][0], ['yes', 'or', 'no']) &&
	!empty($_POST['nom'][0]) && 
	!empty($_POST['description'][0]) && 
	isset($_POST['montant'][0]) && 
	isset($_POST['special'][0]) && (
		empty($_POST['special'][0]) ||
		in_array($_POST['special'][0], array_keys($sports))) &&
	isset($_POST['ecole_for'][0]) && (
		empty($_POST['ecole_for'][0]) ||
		in_array($_POST['ecole_for'][0], array_keys($ecoles))) &&
	is_numeric($_POST['montant'][0])) {

	if (!isset($_POST['logement'])) $_POST['logement'] = array();
	if (!isset($_POST['sportif'])) $_POST['sportif'] = array();

	$pdo->exec($s = 'INSERT INTO tarifs SET '.
		'_auteur = '.(int) $_SESSION['user']['id'].', '.
		'_date = NOW(), '.
		'_message = "Ajout du tarif", '.
		'_etat = "active", '.
		//---------------//
		'nom = "'.secure($_POST['nom'][0]).'", '.
		'description = "'.secure($_POST['description'][0]).'", '.
		'tarif = '.abs((float) $_POST['montant'][0]).', '.
		'ecole_lyonnaise = '.$_POST['ecole_lyonnaise'][0].', '.
		'format_long = '.$_POST['long'][0].', '.
		'for_pompom = "'.$_POST['for_pompom'][0].'", '.
		'for_cameraman = "'.$_POST['for_cameraman'][0].'", '.
		'for_fanfaron = "'.$_POST['for_fanfaron'][0].'", '.
		'sportif = '.(in_array('0', $_POST['sportif']) ? '1' : '0').', '.
		'logement = '.(in_array('0', $_POST['logement']) ? '1' : '0').', '.
		'id_sport_special = '.(empty($_POST['special'][0]) || !in_array('0', $_POST['sportif']) ? 'NULL' : $_POST['special'][0]).', '.
		'id_ecole_for_special = '.(empty($_POST['ecole_for'][0]) || !in_array('0', $_POST['sportif']) ? 'NULL' : $_POST['ecole_for'][0]))
			 or die($s.print_r($pdo->errorInfo()));

	$add = true;
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


//On edite un tarif
if (isset($i) &&
	empty($_POST['delete']) &&
	in_array($_POST['id'][$i], array_keys($tarifs)) &&
	//empty($tarifs[$_POST['id'][$i]]['teid']) &&
	isset($_POST['ecole_lyonnaise'][$i]) &&
	in_array($_POST['ecole_lyonnaise'][$i], ['0', '1']) &&
	isset($_POST['long'][$i]) &&
	in_array($_POST['long'][$i], ['0', '1']) &&
	isset($_POST['for_pompom'][$i]) &&
	in_array($_POST['for_pompom'][$i], ['yes', 'or', 'no']) &&
	isset($_POST['for_cameraman'][$i]) &&
	in_array($_POST['for_cameraman'][$i], ['yes', 'or', 'no']) &&
	isset($_POST['for_fanfaron'][$i]) &&
	in_array($_POST['for_fanfaron'][$i], ['yes', 'or', 'no']) &&
	!empty($_POST['nom'][$i]) && 
	!empty($_POST['description'][$i]) && 
	isset($_POST['montant'][$i]) && 
	isset($_POST['special'][$i]) && (
		empty($_POST['special'][$i]) ||
		in_array($_POST['special'][$i], array_keys($sports))) &&
	isset($_POST['ecole_for'][$i]) && (
		empty($_POST['ecole_for'][$i]) ||
		in_array($_POST['ecole_for'][$i], array_keys($ecoles))) &&
	is_numeric($_POST['montant'][$i])) {

	if (!isset($_POST['logement'])) $_POST['logement'] = array();
	if (!isset($_POST['sportif'])) $_POST['sportif'] = array();

	$ref = pdoRevision('tarifs', $_POST['id'][$i]);
	$pdo->exec('set FOREIGN_KEY_CHECKS = 0');
	$pdo->exec($s='UPDATE tarifs SET '.
			'_auteur = '.(int) $_SESSION['user']['id'].', '.
			'_ref = '.(int) $ref.', '.
			'_date = NOW(), '.
			'_message = "Modification du tarif", '.
			//-----------//
			'nom = "'.secure($_POST['nom'][$i]).'", '.
			'description = "'.secure($_POST['description'][$i]).'", '.
			'tarif = '.abs((float) $_POST['montant'][$i]).', '.
			'ecole_lyonnaise = '.$_POST['ecole_lyonnaise'][$i].', '.
			'format_long = '.$_POST['long'][$i].', '.
			'for_pompom = "'.$_POST['for_pompom'][$i].'", '.
			'for_cameraman = "'.$_POST['for_cameraman'][$i].'", '.
			'for_fanfaron = "'.$_POST['for_fanfaron'][$i].'", '.
			'sportif = '.(in_array($_POST['id'][$i], $_POST['sportif']) ? '1' : '0').', '.
			'logement = '.(in_array($_POST['id'][$i], $_POST['logement']) ? '1' : '0').', '.
			'id_sport_special = '.(empty($_POST['special'][$i]) || !in_array($_POST['id'][$i], $_POST['sportif']) ? 'NULL' : $_POST['special'][$i]).', '.
			'id_ecole_for_special = '.(empty($_POST['ecole_for'][$i]) || !in_array($_POST['id'][$i], $_POST['sportif']) ? 'NULL' : $_POST['ecole_for'][$i]).' '.
		'WHERE '.
			'_etat = "active" AND '.
			'id = '.abs((int) $_POST['id'][$i]));
	$pdo->exec('set FOREIGN_KEY_CHECKS = 1');
	
	$modify = true;
}


//On supprime un tarif
else if (isset($i) &&
	!empty($_POST['delete']) &&
	in_array($_POST['id'][$i], array_keys($tarifs)) &&
	empty($tarifs[$_POST['id'][$i]]['teid'])) {

	$ref = pdoRevision('tarifs', $_POST['id'][$i]);
	$pdo->exec('set FOREIGN_KEY_CHECKS = 0');
	$pdo->exec('UPDATE tarifs SET '.
			'_auteur = '.(int) $_SESSION['user']['id'].', '.
			'_ref = '.(int) $ref.', '.
			'_date = NOW(), '.
			'_message = "Suppression du tarif", '.
			'_etat = "desactive" '.
		'WHERE '.
			'_etat = "active" AND '.
			'id = '.abs((int) $_POST['id'][$i]));
	$pdo->exec('set FOREIGN_KEY_CHECKS = 1');

	$delete = true;
}


if (isset($add) ||
	isset($modify) ||
	isset($delete)) {
	$tarifs = $pdo->query($tarifs_sql)
		or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
	$tarifs = $tarifs->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);
}

$tarifs_groups = [];
foreach ($tarifs as $id => $tarif) {
	$tarifs_groups[$tarif['ecole_lyonnaise'].'_'.$tarif['format_long']][$id] = $tarif;
}

$sports_speciaux = $pdo->query('SELECT '.
		's.sport, '.
		's.sexe, '.
		'GROUP_CONCAT(CASE WHEN t.id_ecole_for_special IS NULL THEN 0 ELSE t.id_ecole_for_special END, ",") AS ecoles_for_special '.
	'FROM sports AS s '.
	'JOIN tarifs AS t ON '.
		't.id_sport_special = s.id AND '.
		't._etat = "active" '.
	'WHERE '.
		's._etat = "active" '.
	'GROUP BY s.id '.
	'ORDER BY s.sport ASC, s.sexe ASC')
	->fetchAll(PDO::FETCH_ASSOC);


//Inclusion du bon fichier de template
require DIR.'templates/admin/ecoles/tarification.php';
