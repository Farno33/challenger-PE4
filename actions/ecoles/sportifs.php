<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/ecoles/sportifs.php *****************************/
/* Edition des sportifs ************************************/
/* *********************************************************/
/* Dernière modification : le 13/12/14 *********************/
/* *********************************************************/


$id = $args[1][0];
if (!(!empty($_SESSION['user']) && (
		!empty($_SESSION['user']['privileges']) &&
		in_array('ecoles', $_SESSION['user']['privileges']) ||
		!empty($_SESSION['user']['ecoles']) &&
		in_array($id, $_SESSION['user']['ecoles']))))
	die(header('location:'.url('accueil', false, false)));


$ecole = $pdo->query('SELECT '.
		'e.*, '.
		'(SELECT COUNT(p1.id) FROM participants AS p1 WHERE p1.id_ecole = e.id AND p1._etat = "active") AS nb_inscriptions, '.
		'(SELECT COUNT(p2.id) FROM participants AS p2 WHERE p2.id_ecole = e.id AND p2._etat = "active" AND p2.sportif = 1) AS nb_sportif, '.
		'(SELECT COUNT(p3.id) FROM participants AS p3 WHERE p3.id_ecole = e.id AND p3._etat = "active" AND p3.pompom = 1) AS nb_pompom, '.
		'(SELECT COUNT(p4.id) FROM participants AS p4 WHERE p4.id_ecole = e.id AND p4._etat = "active" AND p4.fanfaron = 1) AS nb_fanfaron, '.
		'(SELECT COUNT(p5.id) FROM participants AS p5 WHERE p5.id_ecole = e.id AND p5._etat = "active" AND p5.cameraman = 1) AS nb_cameraman, '.
		'(SELECT COUNT(p6.id) FROM participants AS p6 WHERE p6.id_ecole = e.id AND p6._etat = "active" AND p6.pompom = 1 AND p6.sportif = 0) AS nb_pompom_nonsportif, '.
		'(SELECT COUNT(p7.id) FROM participants AS p7 WHERE p7.id_ecole = e.id AND p7._etat = "active" AND p7.fanfaron = 1 AND p7.sportif = 0) AS nb_fanfaron_nonsportif, '.
		'(SELECT COUNT(p10.id) FROM participants AS p10 WHERE p10.id_ecole = e.id AND p10._etat = "active" AND p10.cameraman = 1 AND p10.sportif = 0) AS nb_cameraman_nonsportif, '.
		'(SELECT COUNT(p8.id) FROM participants AS p8 JOIN tarifs_ecoles AS te8 ON te8.id = p8.id_tarif_ecole AND te8._etat = "active" JOIN tarifs AS t8 ON t8.id = te8.id_tarif AND t8.logement = 1 AND t8._etat = "active" WHERE p8.id_ecole = e.id AND p8.sexe = "f" AND p8._etat = "active") AS nb_filles_logees, '.
		'(SELECT COUNT(p9.id) FROM participants AS p9 JOIN tarifs_ecoles AS te9 ON te9.id = p9.id_tarif_ecole AND te9._etat = "active" JOIN tarifs AS t9 ON t9.id = te9.id_tarif AND t9.logement = 1 AND t9._etat = "active" WHERE p9.id_ecole = e.id AND p9.sexe = "h" AND p9._etat = "active") AS nb_garcons_loges '.
	'FROM ecoles AS e '.
	'WHERE '.
		'e._etat = "active" AND '.
		'e.id = '.(int) $id)
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$ecole = $ecole->fetch(PDO::FETCH_ASSOC);


$quotas = $pdo->query('SELECT '.
		'quota, '.
		'valeur, '.
		'id '.
	'FROM quotas_ecoles '.
	'WHERE '.
		'id_ecole = '.(int) $id.' AND '.
		'_etat = "active"')
	->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);


foreach ($quotas as $quota => $valeur)
	$quotas[$quota] = $valeur['valeur'];


$quotas_reserves = $pdo->query('SELECT '.
		'es.id_sport, '.
		'es.quota_reserves, '.
		'(SELECT COUNT(p.id) FROM participants AS p JOIN sportifs AS sp ON sp.id_participant = p.id AND sp._etat = "active" JOIN equipes AS eq ON eq._etat = "active" AND eq.id = sp.id_equipe WHERE p.id_ecole = es.id_ecole AND p._etat = "active" AND eq.id_ecole_sport = es.id) AS sportifs '.
	'FROM ecoles_sports AS es WHERE '.
		'es.id_ecole = '.$ecole['id'].' AND '.
		'es.quota_reserves > 0 AND '.
		'es._etat = "active"')
	->fetchAll(PDO::FETCH_ASSOC);

if (isset($quotas['total'])) {
	$places_reservees = 0;

	foreach ($quotas_reserves as $quota_reserves) {
		if ($quota_reserves['sportifs'] < $quota_reserves['quota_reserves']) {
			$quotas['total'] -= $quota_reserves['quota_reserves'];
			$places_reservees += $quota_reserves['quota_reserves'];
		}
	}
}


if (empty($ecole) ||
	$ecole['etat_inscription'] == 'fermee' && (
		empty($_SESSION['user']['privileges']) ||
		!in_array('ecoles', $_SESSION['user']['privileges'])))
	die(header('location:'.url('', false, false)));


if (!in_array($ecole['etat_inscription'], ['ouverte', 'limitee', 'close']) && (
		empty($_SESSION['user']['privileges']) ||
		!in_array('ecoles', $_SESSION['user']['privileges'])))
	die(header('location:'.url('ecoles/'.$ecole['id'].'/recapitulatif', false, false)));


if ((!empty($_POST['listing']) ||
		!empty($_POST['delete'])) && 
	!empty($_POST['sport'])) {

	$pid = (int) (!empty($_POST['delete']) ? $_POST['delete'] : $_POST['listing']);
	
	if (empty($_POST['equipe']))
		$found = $pdo->query('SELECT '.
				'eq.id, '.
				'eq.label, '.
				's.sport, '.
				's.sexe, '.
				'GROUP_CONCAT(sp.id) AS id_sportifs '.
			'FROM equipes AS eq '.
			'JOIN ecoles_sports AS es ON '.
				'es.id = eq.id_ecole_sport AND '.
				'es._etat = "active" '.
			'JOIN sports AS s ON '.
				's.id = es.id_sport AND '.
				's._etat = "active" '.
			'LEFT JOIN (sportifs AS sp '.
				'JOIN participants AS p ON '.
					'p.id = sp.id_participant AND '.
					'p._etat = "active") ON '.
				'sp.id_equipe = eq.id AND '.
				'sp._etat = "active" '.
			'WHERE '.
				'eq._etat = "active" AND '.
				'es.id_ecole = '.(int) $ecole['id'].' AND '.
				'eq.id = '.(int) $pid.' '.
			'GROUP BY eq.id')
			->fetch(PDO::FETCH_ASSOC);

	else
		$found = $pdo->query('SELECT '.
				'sp.id AS id, '.
				'p.prenom, '.
				'p.nom, '.
				'p.sexe, '.
				's.sport, '.
				's.sexe AS ssexe '.
			'FROM sportifs AS sp '.
			'JOIN participants AS p ON '.
				'p.id = sp.id_participant AND '.
				'p._etat = "active" '.
			'JOIN equipes AS eq ON '.
				'eq.id = sp.id_equipe AND '.
				'eq._etat = "active" '.
			'JOIN ecoles_sports AS es ON '.
				'es.id = eq.id_ecole_sport AND '.
				'es.id_ecole = p.id_ecole AND '.
				'es._etat = "active" '.
			'JOIN sports AS s ON '.
				's.id = es.id_sport AND '.
				'es._etat = "active" '.
			'WHERE '.
				'sp._etat = "active" AND '.
				'p.id_ecole = '.(int) $ecole['id'].' AND '.
				'sp.id_equipe = '.(int) $_POST['equipe'].' AND '.
				'p.id = '.(int) $pid)
			->fetch(PDO::FETCH_ASSOC);
}


//Historique de l'équipe ou du sportif
if (!empty($_POST['listing']) &&
	!empty($_POST['sport']) &&
	!empty($found)) {
	$type = empty($_POST['equipe']) ? "equipes" : "sportifs";

	$history = "SELECT T2.*, T1.lvl, T3.nom AS _auteur_nom, T3.prenom AS _auteur_prenom FROM ".
		"(SELECT ".
	        "@r AS _id, ".
	        "(SELECT @r := _ref FROM ".$type." WHERE id = _id) AS parent, ".
	        "@l := @l + 1 AS lvl ".
	    "FROM ".
	        "(SELECT @r := ".(int) $found['id'].", @l := 0) vars, ".
	        $type." m ".
	    "WHERE @r <> 0) T1 ".
		"JOIN ".$type." T2 ON T1._id = T2.id ".
		"JOIN utilisateurs T3 ON T3.id = T2._auteur ".
		"ORDER BY T1.lvl ASC";
	$history = $pdo->query($history)->fetchAll(PDO::FETCH_ASSOC);
	
	$titre = $type == "equipes" ? 
		'Historique de l\'équipe "'.stripslashes($found['label']).'"<br />'.
		'<small>'.stripslashes($found['sport']).' '.printSexe($found['sexe']).'</small>' : 
		'Historique du sportif "'.stripslashes($found['nom'].' '.$found['prenom']).'"<br />'.
		'<small>'.stripslashes($found['sport']).' '.printSexe($found['ssexe']).'</small>'; 

	die(require DIR.'templates/ecoles/historique.php');
}


if (!empty($_POST['delete']) &&
	!empty($found)) {

	if (!empty($_POST['equipe'])) {
		$ref = pdoRevision('sportifs', $found['id']);
		$pdo->exec('set FOREIGN_KEY_CHECKS = 0');
		$pdo->exec('UPDATE sportifs SET '.
			'_auteur = '.(int) $_SESSION['user']['id'].', '.
			'_ref = '.(int) $ref.', '.
			'_date = NOW(), '.
			'_message = "Suppression du sportif", '.
			'_etat = "desactive" '.
		'WHERE '.
			'id = '.(int) $found['id']);
		$pdo->exec('set FOREIGN_KEY_CHECKS = 1');

		$delete = $_POST['equipe'];
	}

	else {
		//Suppression des sportifs avant de supprimer l'équipe
		$id_sportifs = explode(',', $found['id_sportifs']);
		$id_sportifs = array_unique(array_filter($id_sportifs));
		
		$pdo->exec('set FOREIGN_KEY_CHECKS = 0');
		$del_sportif = $pdo->prepare('UPDATE sportifs SET '.
			'_auteur = '.(int) $_SESSION['user']['id'].', '.
			'_ref = :ref, '.
			'_date = NOW(), '.
			'_message = "Suppression du sportif", '.
			'_etat = "desactive" '.
		'WHERE '.
			'id = :id');
		$pdo->exec('set FOREIGN_KEY_CHECKS = 1');

		foreach ($id_sportifs as $id_sportif) {
			$ref = pdoRevision('sportifs', $id_sportif);
			$del_sportif->execute([
				':ref' => $ref, 
				':id' => $id_sportif]);
		}

		$ref = pdoRevision('equipes', $found['id']);
		$pdo->exec('set FOREIGN_KEY_CHECKS = 0');
		$pdo->exec('UPDATE equipes SET '.
			'_auteur = '.(int) $_SESSION['user']['id'].', '.
			'_ref = '.(int) $ref.', '.
			'_date = NOW(), '.
			'_message = "Suppression de l\'équipe", '.
			'_etat = "desactive" '.
		'WHERE '.
			'id = '.(int) $found['id']);
		$pdo->exec('set FOREIGN_KEY_CHECKS = 1');

		$remove = $_POST['sport'];
	}

}


$groupes_ = $pdo->query('SELECT '.
		'id, '.
		'groupe_multiple '.
	'FROM sports WHERE '.
		'_etat = "active" AND '.
		'groupe_multiple > 0')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$groupes_ = $groupes_->fetchAll(PDO::FETCH_ASSOC);
$groupes = [];

foreach ($groupes_ as $groupe) 
	$groupes[$groupe['groupe_multiple']][] = $groupe['id'];


$sportifs = $pdo->query($sportifs_sql = 'SELECT '.
		'p.id, '.
		'p.nom, '.
		'p.prenom, '.
		'p.telephone, '.
		'p.licence, '.
		'p.sexe, '.
		'CASE WHEN t.id_ecole_for_special = '.$ecole['id'].' OR t.id_ecole_for_special IS NULL THEN st.id ELSE NULL END AS id_sport_special, '.
		'GROUP_CONCAT(CASE WHEN s.id IS NULL THEN NULL ELSE eq.id END) AS id_equipes, '.
		'GROUP_CONCAT(CASE WHEN s.id IS NULL THEN NULL ELSE s.id END) AS id_sports '.
	'FROM participants AS p '.
	'LEFT JOIN sportifs AS sp ON '.
		'sp.id_participant = p.id AND '.
		'sp._etat = "active" '.
	'LEFT JOIN equipes AS eq ON '.
		'eq.id = sp.id_equipe AND '.
		'eq._etat = "active" '.
	'LEFT JOIN ecoles_sports AS es ON '.
		'es.id = eq.id_ecole_sport AND '.
		'es._etat = "active" '.
	'LEFT JOIN sports AS s ON '.
		's.id = es.id_sport AND '.
		's._etat = "active" '.
	'LEFT JOIN tarifs_ecoles AS te ON '.
		'te.id = p.id_tarif_ecole AND '.
		'te._etat = "active" '.
	'LEFT JOIN tarifs AS t ON '.
		't.id = te.id_tarif AND '.
		't._etat = "active" '.
	'LEFT JOIN sports AS st ON '.
		'st.id = t.id_sport_special AND '.
		'st._etat = "active" AND '.
		'(t.id_ecole_for_special = '.$ecole['id'].' OR t.id_ecole_for_special IS NULL) '.
	'WHERE '.
		'p.sportif = 1 AND '.
		'p.id_ecole = '.$ecole['id'].' AND '.
		'p._etat = "active" '.
	'GROUP BY '.
		'p.id '.
	'ORDER BY '.
		'p.nom ASC, '.
		'p.prenom ASC')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$sportifs = $sportifs->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);


$sports_groupes = $pdo->query($s = 'SELECT '.
		's.id, '.
		's.sport, '.
		's.sexe '.
	'FROM sports AS s '.
	'WHERE '.
		's._etat = "active" AND '.
		's.groupe_multiple > 0')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$sports_groupes = $sports_groupes->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);


$ecoles_sports_ = $pdo->query($ecoles_sports_sql = 'SELECT '.
		's.id AS sid, '.
		'es.id AS esid, '.
		'es.quota_max, '.
		'es.quota_equipes, '.
		's.quota_inscription, '.
		's.groupe_multiple, '.
		's.sport, '.
		's.sexe, '.
		'(SELECT COUNT(pt.id) '.
			'FROM participants AS pt '.
			'JOIN sportifs AS spt ON '.
				'spt.id_participant = pt.id AND '.
				'spt._etat = "active" '.
			'JOIN equipes AS eqt ON '.
				'eqt.id = spt.id_equipe AND '.
				'eqt._etat = "active" '.
			'JOIN ecoles_sports AS est ON '.
				'est.id = eqt.id_ecole_sport AND '.
				'est._etat = "active" '.
			'WHERE '.
				'est.id_sport = s.id AND '.
				'est.id_ecole = es.id_ecole AND '.
				'pt.id_ecole = es.id_ecole AND '.
				'pt._etat = "active") AS nb_sportifs, '.
		'(SELECT COUNT(pc.id) '.
			'FROM participants AS pc '.
			'JOIN ecoles AS ec ON '.
				'ec.id = pc.id_ecole AND '.
				'ec._etat = "active" '.
			'JOIN sportifs AS spc ON '.
				'spc.id_participant = pc.id AND '.
				'spc._etat = "active" '.
			'JOIN equipes AS eqc ON '.
				'eqc.id = spc.id_equipe AND '.
				'eqc._etat = "active" '.
			'JOIN ecoles_sports AS esc ON '.
				'esc.id = eqc.id_ecole_sport AND '.
				'esc._etat = "active" '.
			'WHERE '.
				'esc.id_sport = s.id AND '.
				'pc._etat = "active") AS nb_inscriptions, '.
		'eq.id_capitaine, '.
		'eq.id AS eid, '.
		'eq.label, '.
		'(SELECT COUNT(t.id) '.
			'FROM tarifs AS t '.
			'WHERE '.
				't.id_sport_special = s.id AND '.
				't._etat = "active" AND '.
				'(t.id_ecole_for_special = '.$ecole['id'].' OR t.id_ecole_for_special IS NULL)'.
			') AS special '. 
	'FROM ecoles_sports AS es '.
	'JOIN sports AS s ON '.
		's.id = es.id_sport AND '.
		's._etat = "active" '.
	'LEFT JOIN (equipes AS eq '.
		'JOIN participants AS p ON '.
			'p.id = eq.id_capitaine AND '.
			'p._etat = "active") ON '.
		'p.id_ecole = es.id_ecole AND '.
		'eq.id_ecole_sport = es.id AND '.
		'eq._etat = "active" '.
	'WHERE '.
		'es.id_ecole = '.$ecole['id'].' AND '.
		'es._etat = "active" '.
	'ORDER BY '.
		's.sport ASC, '.
		's.sexe ASC') or die(print_r($pdo->errorInfo()));
$ecoles_sports_ = $ecoles_sports_->fetchAll(PDO::FETCH_ASSOC);

$ecoles_sports = [];
foreach ($ecoles_sports_ as $ecole_sport)
	$ecoles_sports[$ecole_sport['sid']][empty($ecole_sport['eid']) ? 0 : $ecole_sport['eid']] = $ecole_sport;


$sportifs_equipes = [];
foreach ($sportifs as $id => $sportif) {
	$equipes = explode(',', empty($sportif['id_equipes']) ? "" : $sportif['id_equipes']);
	$equipes = array_unique(array_filter($equipes));

	foreach ($equipes as $equipe) {
		$sportifs_equipes[$equipe][$id] = $sportif;
	}
}



//Ajout d'un sportif
if (isset($_POST['add']) &&
	!empty($_POST['sport']) &&
	!empty($_POST['equipe']) &&
	!empty($ecoles_sports[$_POST['sport']][$_POST['equipe']]) &&
	!empty($_POST['sportif']) &&
	in_array($_POST['sportif'], array_keys($sportifs)) && (
		empty($sportifs[$_POST['sportif']]['id_sports']) ||
		$ecoles_sports[$_POST['sport']][$_POST['equipe']]['groupe_multiple'] > 0 &&
		in_array_multiple(explode(',', $sportifs[$_POST['sportif']]['id_sports']), $groupes[$ecoles_sports[$_POST['sport']][$_POST['equipe']]['groupe_multiple']]) &&
		!in_array($_POST['sport'], explode(',', $sportifs[$_POST['sportif']]['id_sports']))) &&
	$ecoles_sports[$_POST['sport']][$_POST['equipe']]['quota_max'] - $ecoles_sports[$_POST['sport']][$_POST['equipe']]['nb_sportifs'] > 0 && (
		empty($ecoles_sports[$_POST['sport']][$_POST['equipe']]['quota_inscription']) ||
		$ecoles_sports[$_POST['sport']][$_POST['equipe']]['quota_inscription'] - $ecoles_sports[$_POST['sport']][$_POST['equipe']]['nb_inscriptions'] > 0) && (
		$ecoles_sports[$_POST['sport']][$_POST['equipe']]['special'] &&
		$sportifs[$_POST['sportif']]['id_sport_special'] == $_POST['sport'] ||
		!$ecoles_sports[$_POST['sport']][$_POST['equipe']]['special'])) {
		
		
	$pdo->exec('INSERT INTO sportifs SET '.
		'_auteur = '.(int) $_SESSION['user']['id'].', '.
		'_date = NOW(), '.
		'_message = "Ajout du sportif", '.
		'_etat = "active", '.
		//----------------//
		'id_participant = '.(int) $_POST['sportif'].', '.
		'id_equipe = '.(int) $_POST['equipe']) or die(print_r($pdo->errorInfo()));

	$add = $_POST['equipe'];
}


//Ajout d'une équipe
if (isset($_POST['add']) &&
	!empty($_POST['sport']) &&
	!empty($_POST['capitaine']) &&
	in_array($_POST['capitaine'], array_keys($sportifs)) && 
	!empty($ecoles_sports[$_POST['sport']])) {
	$sport_equipes = $ecoles_sports[$_POST['sport']];
	$nb_equipes = array_keys($sport_equipes)[0] == 0 ? 0 : count($sport_equipes);
}

if (!empty($sport_equipes) &&
	$nb_equipes < $sport_equipes[array_keys($sport_equipes)[0]]['quota_equipes'] &&
	!empty($sportifs[$_POST['capitaine']]['telephone']) && (
		empty($sportifs[$_POST['capitaine']]['id_sports']) ||
		$sport_equipes[array_keys($sport_equipes)[0]]['groupe_multiple'] > 0 &&
		in_array_multiple(explode(',', $sportifs[$_POST['capitaine']]['id_sports']), $groupes[$sport_equipes[array_keys($sport_equipes)[0]]['groupe_multiple']]) &&
		!in_array($_POST['sport'], explode(',', $sportifs[$_POST['capitaine']]['id_sports']))) &&
	$sport_equipes[array_keys($sport_equipes)[0]]['quota_max'] - $sport_equipes[array_keys($sport_equipes)[0]]['nb_sportifs'] > 0 && (
		empty($sport_equipes[array_keys($sport_equipes)[0]]['quota_inscription']) ||
		$sport_equipes[array_keys($sport_equipes)[0]]['quota_inscription'] - $sport_equipes[array_keys($sport_equipes)[0]]['nb_inscriptions'] > 0) && (
		$sport_equipes[array_keys($sport_equipes)[0]]['special'] &&
		$sportifs[$_POST['capitaine']]['id_sport_special'] == $_POST['sport'] ||
		!$sport_equipes[array_keys($sport_equipes)[0]]['special'])) {
		
	$pdo->exec('INSERT INTO equipes SET '.
		'_auteur = '.(int) $_SESSION['user']['id'].', '.
		'_date = NOW(), '.
		'_message = "Ajout de l\'équipe", '.
		'_etat = "active", '.
		//----------------//
		'label = "'.secure(empty($_POST['label']) ? 'N°'.($nb_equipes+1) : $_POST['label']).'", '.
		'id_capitaine = '.(int) $_POST['capitaine'].', '.
		'id_ecole_sport = '.(int) $sport_equipes[array_keys($sport_equipes)[0]]['esid']) or die(print_r($pdo->errorInfo()));


	$pdo->exec('INSERT INTO sportifs SET '.
		'_auteur = '.(int) $_SESSION['user']['id'].', '.
		'_date = NOW(), '.
		'_message = "Ajout du sportif", '.
		'_etat = "active", '.
		//----------------//
		'id_participant = '.(int) $_POST['capitaine'].', '.
		'id_equipe = '.(int) $pdo->lastInsertId()) or die(print_r($pdo->errorInfo()));

	$new = $_POST['sport'];
}


//Changement de capitaine
if (isset($_POST['captain']) &&
	!empty($_POST['equipe']) &&
	!empty($sportifs_equipes[$_POST['equipe']][$_POST['captain']]) &&
	!empty($sportifs_equipes[$_POST['equipe']][$_POST['captain']]['telephone'])) {
		
	$ref = pdoRevision('equipes', $_POST['equipe']);

	$pdo->exec('set FOREIGN_KEY_CHECKS = 0');
	$pdo->exec('UPDATE equipes SET '.
			'_auteur = '.(int) $_SESSION['user']['id'].', '.
			'_ref = '.(int) $ref.', '.
			'_date = NOW(), '.
			'_message = "Modification du capitaine", '.
			//----------------//
			'id_capitaine = '.(int) $_POST['captain'].' '.
		'WHERE '.
			'id = '.(int) $_POST['equipe']);
	$pdo->exec('set FOREIGN_KEY_CHECKS = 1');

	$captain = $_POST['equipe'];
}


if (!empty($add) ||
	!empty($captain) ||
	!empty($new)) {

	$sportifs = $pdo->query($sportifs_sql);
	$sportifs = $sportifs->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);


	$ecoles_sports_ = $pdo->query($ecoles_sports_sql);
	$ecoles_sports_ = $ecoles_sports_->fetchAll(PDO::FETCH_ASSOC);

	$ecoles_sports = [];
	foreach ($ecoles_sports_ as $ecole_sport)
		$ecoles_sports[$ecole_sport['sid']][$ecole_sport['eid']] = $ecole_sport;

	$sportifs_equipes = [];
	foreach ($sportifs as $id => $sportif) {
		$equipes = explode(',', empty($sportif['id_equipes']) ? '' : $sportif['id_equipes']);
		$equipes = array_unique(array_filter($equipes));

		foreach ($equipes as $equipe) {
			$sportifs_equipes[$equipe][$id] = $sportif;
		}
	}
}


//Inclusion du bon fichier de template
require DIR.'templates/ecoles/sportifs.php';
