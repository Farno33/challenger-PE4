<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/admin/competition/action_sports.php *************/
/* Edition des sports **************************************/
/* *********************************************************/
/* Dernière modification : le 13/12/14 *********************/
/* *********************************************************/

//Liste des Responsables
$respos = $pdo->query('SELECT '.
		'id, '.
		'nom, '.
		'prenom '.
	'FROM utilisateurs '.
	'WHERE '.
		'responsable = 1 AND '.
		'_etat = "active" '.
	'ORDER BY '.
		'nom ASC, '.
		'prenom ASC')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$respos = $respos->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);


$sports = $pdo->query($sports_sql = 'SELECT '.
		's.id AS sid, '.
		's.id, '.
		's.sport, '.
		's.sexe, '.
		's.quota_inscription, '.
		's.id_respo, '.
		's.groupe_multiple, '.
		's.individuel, '.
		'(SELECT COUNT(es.id) '.
			'FROM ecoles_sports AS es '.
			'WHERE '.
				'es.id_sport = s.id AND '.
				'es._etat = "active") AS nb_ecoles, '.
		'(SELECT COUNT(sp.id) '.
			'FROM sportifs AS sp '.
			'JOIN equipes AS eqs ON '.
				'eqs.id = sp.id_equipe AND '.
				'eqs._etat = "active" '.
			'JOIN ecoles_sports AS ess ON '.
				'ess.id = eqs.id_ecole_sport AND '.
				'ess._etat = "active" '.
			'WHERE '.
				'ess.id_sport = s.id AND '.
				'sp._etat = "active") AS nb_inscriptions '.
	'FROM sports AS s '.
	'WHERE '.
		's._etat = "active" '.
	'ORDER BY '.
		's.sport ASC, '.
		's.sexe ASC')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$sports = $sports->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);


//Historique du sport
if (!empty($_POST['listing']) &&
	in_array($_POST['listing'], array_keys($sports))) {

	$history = "SELECT T2.*, T1.lvl, T3.nom AS _auteur_nom, T3.prenom AS _auteur_prenom FROM ".
		"(SELECT ".
	        "@r AS _id, ".
	        "(SELECT @r := _ref FROM sports WHERE id = _id) AS parent, ".
	        "@l := @l + 1 AS lvl ".
	    "FROM ".
	        "(SELECT @r := ".$_POST['listing'].", @l := 0) vars, ".
	        "sports m ".
	    "WHERE @r <> 0) T1 ".
		"JOIN sports T2 ON T1._id = T2.id ".
		"JOIN utilisateurs T3 ON T3.id = T2._auteur ".
		"ORDER BY T1.lvl ASC";
	$history = $pdo->query($history)->fetchAll(PDO::FETCH_ASSOC);
	$titre = 'Historique de "'.stripslashes($sports[$_POST['listing']]['sport']).' '.printSexe($sports[$_POST['listing']]['sexe']).'"';
	die(require DIR.'templates/admin/historique.php');
}



$groupeMax = 0;
$sportsGroupes = [];
foreach ($sports as $id => $sport) {
	if ($sport['groupe_multiple'] > $groupeMax)
		$groupeMax = $sport['groupe_multiple'];
	
	$sportsGroupes[$sport['groupe_multiple']][] = $id;
}

if (!isset($_POST['individuel'])) $_POST['individuel'] = array();

//Ajout d'un sport
if (isset($_POST['add']) &&
	!empty($_POST['sport'][0]) &&
	!empty($_POST['sexe'][0]) &&
	in_array($_POST['sexe'][0], ['m', 'f', 'h']) && 
	isset($_POST['groupe'][0]) && 
		($_POST['groupe'][0] == 0 || 
		intval($_POST['groupe'][0]) &&
		$_POST['groupe'][0] > 0 &&
		$_POST['groupe'][0] <= $groupeMax + 1) &&
	isset($_POST['inscriptions'][0]) && (
		is_numeric($_POST['inscriptions'][0]) ||
		empty($_POST['inscriptions'][0])) &&
	!empty($_POST['respo'][0]) &&
	in_array($_POST['respo'][0], array_keys($respos))) {
	
	$pdo->exec('set FOREIGN_KEY_CHECKS = 0');
	$pdo->exec('INSERT INTO sports SET '.
		'_auteur = '.(int) $_SESSION['user']['id'].', '.
		'_date = NOW(), '.
		'_message = "Ajout du sport", '.
		'_etat = "active", '.
		//---------------//
		'sport = "'.secure($_POST['sport'][0]).'", '.
		'sexe = "'.secure($_POST['sexe'][0]).'", '.
		'id_respo = '.(int) $_POST['respo'][0].', '.
		'individuel = '.(in_array('0', $_POST['individuel']) ? 1 : 0).', '.
		'groupe_multiple = '.(int) $_POST['groupe'][0].', '.
		'quota_inscription = '.(empty($_POST['inscriptions'][0]) ? 'NULL' : (int) $_POST['inscriptions'][0])) 
		or die(print_r($pdo->errorInfo()));
	$pdo->exec('set FOREIGN_KEY_CHECKS = 1');

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
	}


//On edite un sport
if (!empty($i) &&
	empty($_POST['delete']) &&
	!empty($_POST['sport'][$i]) &&
	!empty($_POST['sexe'][$i]) &&
	in_array($_POST['sexe'][$i], ['m', 'f', 'h']) && 
	isset($_POST['groupe'][$i]) && (
		$_POST['groupe'][$i] == 0 || 
		intval($_POST['groupe'][$i]) &&
		$_POST['groupe'][$i] > 0 && (
			in_array($_POST['id'][$i], $sportsGroupes[$groupeMax]) && count($sportsGroupes[$groupeMax]) == 1 && $_POST['groupe'][$i] <= $groupeMax ||
			(!in_array($_POST['id'][$i], $sportsGroupes[$groupeMax]) || count($sportsGroupes[$groupeMax]) > 1) && $_POST['groupe'][$i] <= $groupeMax + 1)) &&
	isset($_POST['inscriptions'][$i]) &&
	!empty($_POST['respo'][$i]) &&
	in_array($_POST['respo'][$i], array_keys($respos)) &&
	in_array($_POST['id'][$i], array_keys($sports)) && (
	// empty($sports[$_POST['id'][$i]]['nb_ecoles']) && (
		intval($_POST['inscriptions'][$i]) &&
		$_POST['inscriptions'][$i] >= $sports[$_POST['id'][$i]]['nb_inscriptions'] ||
		empty($_POST['inscriptions'][$i]))) {

	if ($sports[$_POST['id'][$i]]['groupe_multiple'] > 0 &&
		count($sportsGroupes[$sports[$_POST['id'][$i]]['groupe_multiple']]) == 1 &&
		$_POST['groupe'][$i] != $sports[$_POST['id'][$i]]['groupe_multiple']) {
		
		foreach ($sportsGroupes as $groupe => $ids) {
			if ($groupe >  $sports[$_POST['id'][$i]]['groupe_multiple']) {
				foreach ($ids as $id) {
					$ref = pdoRevision('sports', $id);
					$pdo->exec('set FOREIGN_KEY_CHECKS = 0');
					$pdo->exec('UPDATE sports SET '.
							'_auteur = '.(int) $_SESSION['user']['id'].', '.
							'_ref = '.(int) $ref.', '.
							'_date = NOW(), '.
							'_message = "Decalage du groupe", '.
							//-------------//
							'groupe_multiple = groupe_multiple - 1 '.
						'WHERE '.
							'id = '.$id);
					$pdo->exec('set FOREIGN_KEY_CHECKS = 1');							
				}
			}
		}

		if ($_POST['groupe'][$i] > $sports[$_POST['id'][$i]]['groupe_multiple'])
			$_POST['groupe'][$i]--;
	}

	$ref = pdoRevision('sports', $_POST['id'][$i]);
	$pdo->exec('set FOREIGN_KEY_CHECKS = 0');
	$pdo->exec('UPDATE sports SET '.
			'_auteur = '.(int) $_SESSION['user']['id'].', '.
			'_ref = '.(int) $ref.', '.
			'_date = NOW(), '.
			'_message = "Modification du sport", '.
			//-------------//
			'sport = "'.secure($_POST['sport'][$i]).'", '.
			'sexe = "'.secure($_POST['sexe'][$i]).'", '.
			'id_respo = '.(int) $_POST['respo'][$i].', '.
			'individuel = '.(in_array($_POST['id'][$i], $_POST['individuel']) ? 1 : 0).', '.
			'groupe_multiple = '.(int) $_POST['groupe'][$i].', '.
			'quota_inscription = '.(empty($_POST['inscriptions'][$i]) ? 'NULL' : (int) $_POST['inscriptions'][$i]).' '.
		'WHERE '.
			'id = '.(int) $_POST['id'][$i]) or die(print_r($pdo->errorInfo()));
	$pdo->exec('set FOREIGN_KEY_CHECKS = 1');
	
	$modify = true;
}


//On supprime un sport
else if (!empty($i) &&
	!empty($_POST['delete']) &&
	!empty($_POST['id']) && 
	in_array($_POST['id'][$i], array_keys($sports)) &&
	empty($sports[$_POST['id'][$i]]['nb_ecoles'])) {

	$ref = pdoRevision('sports', $_POST['id'][$i]);
	$pdo->exec('set FOREIGN_KEY_CHECKS = 0');
	$pdo->exec('UPDATE sports SET '.
			'_auteur = '.(int) $_SESSION['user']['id'].', '.
			'_ref = '.(int) $ref.', '.
			'_date = NOW(), '.
			'_message = "Suppression du sport", '.
			'_etat = "desactive" '.
		'WHERE '.
			'id = '.(int) $_POST['id'][$i]);
	$pdo->exec('set FOREIGN_KEY_CHECKS = 1');

	$delete = true;
}

if (!empty($add) ||
	!empty($modify) ||
	!empty($delete)) {

	$sports = $pdo->query($sports_sql)
		or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
	$sports = $sports->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);

	$groupeMax = 0;
	$sportsGroupes = [];
	foreach ($sports as $id => $sport) {
		if ($sport['groupe_multiple'] > $groupeMax)
			$groupeMax = $sport['groupe_multiple'];
		
		$sportsGroupes[$sport['groupe_multiple']][] = $id;
	}

}


//Inclusion du bon fichier de template
require DIR.'templates/admin/competition/sports.php';
