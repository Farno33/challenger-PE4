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

$phase = $pdo->query('SELECT '.
		's.id AS sid, '.
		's.sport, '.
		's.sexe, '.
		's.individuel, '.
		's.tournoi_initie, '.
		'pha.id, '.
		'pha.cloturee, '.
		'pha.nom, '.
		'pha.type, '.
		'pha.id_phase_suivante, '.
		'phb.nom AS nom_suivante, '.
		'pha.points_victoire, '.
		'pha.points_defaite, '.
		'pha.points_nul, '.
		'pha.points_forfait, '.
		'pha.commentaire, '.
		'pha.cloturee, '.
		'pha.prevision, '.
		'(SELECT COUNT(ph.id) '.
			'FROM phases AS ph '.
			'WHERE '.
				'ph._etat = "active" AND '.
				'ph.id_sport = pha.id_sport AND '.
				'ph.id_phase_suivante = pha.id) AS nb_precedentes, '.
		'(SELECT COUNT(phc.id) '.
			'FROM phases AS phc '.
			'WHERE '.
				'phc._etat = "active" AND '.
				'phc.id_sport = pha.id_sport AND '.
				'phc.id_phase_suivante = pha.id AND '.
				'phc.cloturee <> 1) AS nb_precedentes_ouvertes, '.
		'(SELECT COUNT(gr.id) '.
			'FROM groupes AS gr '.
			'WHERE '.
				'gr._etat = "active" AND '.
				'gr.id_phase = pha.id) AS nb_groupes, '.
		'(SELECT COUNT(ma.id) '.
			'FROM matchs AS ma '.
			'WHERE '.
				'ma._etat = "active" AND '.
				'ma.id_phase = pha.id) AS nb_matchs '.
	'FROM sports AS s '.
	'JOIN phases AS pha ON '.
		'pha.id_sport = s.id AND '.
		'pha._etat = "active" '.
	'LEFT JOIN phases AS phb ON '.
		'phb.id = pha.id_phase_suivante AND '.
		'phb._etat = "active" '.
	'WHERE '.
		's._etat = "active" AND '.
		'pha.id = '.(int) $id_phase) 
	->fetch(PDO::FETCH_ASSOC);

if (empty($phase['id']) ||
	!$phase['tournoi_initie'] ||
	(!empty($vpTournois) && !in_array($phase['sid'], array_keys($vpTournois))))
	die(require DIR.'templates/_error.php');



//Ajout d'un admin
if ($phase['type'] == 'poules' && 
	isset($_POST['add']) &&
	!empty($_POST['nom'][0])) {
	
	$pdo->exec('INSERT INTO groupes SET '.
		'_auteur = '.(int) $_SESSION['user']['id'].', '.
		'_date = NOW(), '.
		'_message = "Ajout du groupe", '.
		'_etat = "active", '.
		//---------------//
		'nom = "'.secure($_POST['nom'][0]).'", '.
		'id_phase = '.(int) $id_phase);
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
if ($phase['type'] == 'poules' &&
	!empty($i) &&
	empty($_POST['delete']) &&
	!empty($_POST['nom'][$i])) {

	$exists = $pdo->query('SELECT id '.
		'FROM groupes '.
		'WHERE '.
			'_etat = "active" AND '.
			'id = '.(int) $_POST['id'][$i])
		or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
	$exists = $exists->fetch(PDO::FETCH_ASSOC);

	if (empty($exists))
		$modify = false;

	else {
		$ref = pdoRevision('groupes', $_POST['id'][$i]);
		$pdo->exec('UPDATE groupes SET '.
				'_auteur = '.(int) $_SESSION['user']['id'].', '.
				'_ref = '.(int) $ref.', '.
				'_date = NOW(), '.
				'_message = "Modification du groupe", '.
				//---------------//
				'nom = "'.secure($_POST['nom'][$i]).'" '.
			'WHERE '.
				'_etat = "active" AND '.
				'id = '.(int) $_POST['id'][$i]);
		$modify = true;
	}
}


//On supprime un admin
else if ($phase['type'] == 'poules' &&
	!empty($i) &&
	!empty($_POST['delete']) &&
	!empty($_POST['id'][$i]) &&
	intval($_POST['id'][$i])) {

	$exists = $pdo->query('SELECT gr.id, '.
			'(SELECT COUNT(phco.id) FROM phases_concurrents AS phco WHERE '.
				'phco.id_groupe = gr.id AND '.
				'phco._etat = "active") AS nb_concurrents '.
		'FROM groupes AS gr '.
		'WHERE '.
			'gr._etat = "active" AND '.
			'gr.id = '.(int) $_POST['id'][$i])
		or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
	$exists = $exists->fetch(PDO::FETCH_ASSOC);

	if (!empty($exists) &&
		empty($exists['nb_concurrents'])) {
		$ref = pdoRevision('groupes', $_POST['id'][$i]);
		$pdo->exec('UPDATE groupes SET '.
				'_auteur = '.(int) $_SESSION['user']['id'].', '.
				'_ref = '.(int) $ref.', '.
				'_date = NOW(), '.
				'_etat = "desactive", '.
				'_message = "Suppression du groupe" '.
			'WHERE id = '.(int) $_POST['id'][$i]);

		$delete = true;
	} else
		$delete = false;
}




if (!$phase['cloturee'] &&
	!empty($_POST['maj']) &&
	!empty($_POST['nom']) && (
		isset($_POST['pts_g']) &&
		isset($_POST['pts_n']) &&
		isset($_POST['pts_p']) &&
		isset($_POST['pts_f']) || 
		$phase['type'] == 'elimination')) {

	$ref = pdoRevision('phases', $id_phase);
	$pdo->exec('set FOREIGN_KEY_CHECKS = 0');
	$pdo->exec('UPDATE phases SET '.
			'_auteur = '.(int) $_SESSION['user']['id'].', '.
			'_date = NOW(), '.
			'_message = "Modification des données de la phase", '.
			'_ref = '.(int) $ref.', '.
			//----------//			
			($phase['type'] == 'elimination' ? '' : 
				'points_victoire = '.(int) $_POST['pts_g'].', '.
				'points_nul = '.(int) $_POST['pts_n'].', '.
				'points_defaite = '.(int) $_POST['pts_p'].', '.
				'points_forfait = '.(int) $_POST['pts_f'].', ').
			'nom = "'.secure($_POST['nom']).'" '.
			($phase['nb_precedentes_ouvertes'] == 0 ? ', cloturee = '.(!empty($_POST['cloturee']) ? 1 : 0).' ' : '').
		'WHERE id = '.$id_phase);
	$pdo->exec('set FOREIGN_KEY_CHECKS = 1');

	if ($phase['nb_precedentes_ouvertes'] == 0)
		$phase['cloturee'] = !empty($_POST['cloturee']) ? 1 : 0;
	
	$phase['nom'] = secure($_POST['nom']);

	if ($phase['type'] != 'elimination') {
		$phase['points_victoire'] = (int) $_POST['pts_g'];
		$phase['points_nul'] = (int) $_POST['pts_n'];
		$phase['points_defaite'] = (int) $_POST['pts_p'];
		$phase['points_forfait'] = (int) $_POST['pts_f'];
	}

	$edit_phase = true;
} else if (!$phase['cloturee'] &&
	!empty($_POST['maj']))
	$edit_phase = false;


$sites = $pdo->query('SELECT '.
		'si.id, '.
		'si.nom '.
	'FROM sites AS si '.
	'WHERE '.
		'si._etat = "active" '.
	'ORDER BY si.nom ASC')
	->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);

$concurrents_ = $pdo->query('SELECT '.
		'co.id, '.
		'eq.label, '.
		'e.nom AS enom, '.
		'e.abreviation AS eabreviation, '.
		'p.nom AS pnom, '.
		'p.prenom AS pprenom, '.
		'p.sexe AS psexe, '.
		'phco.id AS phcoid, '.
		'phco.id_groupe, '.
		'phco.qualifie '.
	'FROM concurrents AS co '.
	'LEFT JOIN phases_concurrents AS phco ON '.
		'phco.id_concurrent = co.id AND '.
		'phco._etat = "active" AND '.
		'phco.id_phase = '.(int) $id_phase.' '.
	'LEFT JOIN (equipes AS eq '.
		'JOIN ecoles_sports AS es ON '.
			'es.id = eq.id_ecole_sport AND '.
			'es._etat = "active") ON '.
		'eq._etat = "active" AND '.
		'eq.id = co.id_equipe '.		
	'LEFT JOIN (sportifs AS sp '.
		'JOIN participants AS p ON '.
			'p.id = sp.id_participant AND '.
			'p._etat = "active") ON '.
		'sp._etat = "active" AND '.
		'sp.id = co.id_sportif '.
	'JOIN ecoles AS e ON '. 
		'e._etat = "active" AND '.
		'(e.id = es.id_ecole OR e.id = p.id_ecole) '.
	'WHERE '.
		'co.id_sport = '.$phase['sid'].' AND '.
		'co._etat = "active"')
	->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);

$qualifies = $pdo->query('SELECT '.
		'phco.id_concurrent '.
	'FROM phases_concurrents AS phco '.
	'JOIN phases AS ph ON '.
		'ph.id = phco.id_phase AND '.
		'ph._etat = "active" '.
	'WHERE '.
		'ph.id_phase_suivante = '.(int) $id_phase. ' AND '.
		'phco.qualifie = 1 AND '.
		'ph.cloturee = 1 AND '.
		'ph.id_sport = '.$phase['sid'].' AND '.
		'phco._etat = "active"')
	->fetchAll(PDO::FETCH_ASSOC);


if ($phase['nb_precedentes'] > 0) {
	$concurrents = [];

	foreach ($qualifies as $qualifie) {
		$concurrents[$qualifie['id_concurrent']] = $concurrents_[$qualifie['id_concurrent']];
	}
} else {
	$concurrents = $concurrents_;
}


$without = false;
$co_groups = [];
foreach ($concurrents as $cid => $concurrent) {
	if (empty($concurrent['id_groupe'])) {
		$without = true;
		break;
	}

	if (empty($co_groups[$concurrent['id_groupe']]))
		$co_groups[$concurrent['id_groupe']] = [];

	$co_groups[$concurrent['id_groupe']][] = $cid;
}


if ($phase['type'] == 'poules' &&
	empty($phase['nb_matchs']) &&
	!empty($_POST['init']) &&
	empty($without)) {

	$prepare = $pdo->prepare('INSERT INTO matchs SET '.
		'_auteur = '.(int) $_SESSION['user']['id'].', '.
		'_date = NOW(), '.
		'_message = "Création du match", '.
		//------------//
		'id_phase = '.(int) $id_phase.', '.
		'id_concurrent_a = :id_concurrent_a, '.
		'id_concurrent_b = :id_concurrent_b'); 		

	foreach ($co_groups as $gid => $cids) {
		foreach ($cids as $c1) {
			foreach ($cids as $c2) {
				if ($c1 >= $c2)
					continue;

				$phase['nb_matchs']++;
				$prepare->execute(array(
					':id_concurrent_a' => $c1,
					':id_concurrent_b' => $c2));
			}
		}
	}
	$groupes = [];
}


if ($phase['type'] == 'championnat' &&
	empty($phase['nb_matchs']) &&
	!empty($_POST['init'])) {

	$prepare = $pdo->prepare('INSERT INTO matchs SET '.
		'_auteur = '.(int) $_SESSION['user']['id'].', '.
		'_date = NOW(), '.
		'_message = "Création du match", '.
		//------------//
		'id_phase = '.(int) $id_phase.', '.
		'id_concurrent_a = :id_concurrent_a, '.
		'id_concurrent_b = :id_concurrent_b'); 		

	foreach ($concurrents as $c1 => $co1) {
		foreach ($concurrents as $c2 => $co2) {
			if ($c1 >= $c2)
				continue;

			$phase['nb_matchs']++;
			$prepare->execute(array(
				':id_concurrent_a' => $c1,
				':id_concurrent_b' => $c2));
		}
	}
}

if ($phase['type'] == 'elimination' &&
	$phase['nb_precedentes_ouvertes'] > 0 &&
	!empty($_POST['preload']) &&
	!empty($_POST['equipes']) &&
	intval($_POST['equipes'])) { 

	$ref = pdoRevision('phases', $phase['id']);
	$pdo->exec('UPDATE phases SET '.
			'_auteur = '.(int) $_SESSION['user']['id'].', '.
			'_date = NOW(), '.
			'_message = "Modification de la prévision du nombre de concurrents", '.
			'_ref = '.(int) $ref.', '.
			//----------//
			'prevision = '.(int) $_POST['equipes'].' '.
		'WHERE '.
			'id = '.(int) $phase['id']);
	$phase['prevision'] = (int) $_POST['equipes'];
}


$matchs = $pdo->query($matchs_sql = 'SELECT '.
		'm.id, '.
		'm.*'.
	'FROM matchs AS m '.
	'WHERE '.
		'm.id_phase = '.(int) $id_phase. ' AND '.
		'm._etat = "active" '.
	'ORDER BY '.
		'm.numero_elimination ASC, '.
		'm.date ASC')
	->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);



if ($phase['nb_precedentes_ouvertes'] > 0 &&
	$phase['type'] == 'elimination' &&
	!empty($_POST['delete_all'])) {
	foreach ($matchs as $mid => $match) {
		$ref = pdoRevision('matchs', $mid);
		$pdo->exec('UPDATE matchs SET '.
				'_auteur = '.(int) $_SESSION['user']['id'].', '.
				'_ref = '.(int) $ref.', '.
				'_date = NOW(), '.
				'_etat = "desactive", '.
				'_message = "Suppression du match" '.
			'WHERE id = '.(int) $mid);
	}

	$phase['nb_matchs'] = 0;
	$matchs = [];
}


if ($phase['type'] == 'poules' &&
	empty($phase['nb_matchs']) &&
	!empty($_POST['cid']) &&
	!empty($_POST['gid']) &&
	isset($_POST['checked']) &&
	in_array($_POST['checked'], [0, 1]) &&
	in_array($_POST['cid'], array_keys($concurrents)) &&
	intval($_POST['gid'])) {

	$exists = $pdo->query('SELECT '.
		'co.id, '.
		'phco.id AS phcoid, '.
		'phco.id_groupe AS old_group, '.
		'(SELECT COUNT(g.id) FROM groupes AS g WHERE '.
			'g.id = '.(int) $_POST['gid'].' AND '.
			'g._etat = "active" AND '.
			'g.id_phase = '.(int) $id_phase.') AS new_group '.
		'FROM concurrents AS co '.
		'LEFT JOIN phases_concurrents AS phco ON '.
			'phco.id_concurrent = co.id AND '.
			'phco.id_phase = '.(int) $id_phase.' AND '.
			'phco._etat = "active" '.
		'WHERE '.
			'co.id = '.(int) $_POST['cid'].' AND '.
			'co._etat = "active" AND '.
			'co.id_sport = '.(int) $phase['sid'])
		->fetch(PDO::FETCH_ASSOC);

	if (!empty($exists) &&
		!empty($exists['new_group']) &&
		!empty($exists['id'])) {

		if (!empty($exists['phcoid']) && (
				!empty($exists['old_group']) &&
				empty($_POST['checked']) ||
				!empty($_POST['checked']))) {
			$ref = pdoRevision('phases_concurrents', $exists['phcoid']);
			$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
			$pdo->exec('UPDATE phases_concurrents SET '.
				'_auteur = '.(int) $_SESSION['user']['id'].', '.
				'_date = NOW(), '.
				'_message = "'.(empty($_POST['checked']) ? 'Suppression du concurrent d\'un groupe' : 'Déplacement du concurrent dans un autre groupe').'", '.
				'_ref = '.(int) $ref.', '.
				//----------//
				'id_groupe = '.(empty($_POST['checked']) ? 'NULL' : (int) $_POST['gid']).' '.
			'WHERE '.
				'id = '.(int) $exists['phcoid']);
			$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
		} else if (!empty($_POST['checked']) &&
			empty($exists['phcoid'])) {
			$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
			$pdo->exec('INSERT INTO phases_concurrents SET '.
				'_auteur = '.(int) $_SESSION['user']['id'].', '.
				'_date = NOW(), '.
				'_message = "Ajout du concurrent dans un groupe", '.
				//----------//
				'id_phase = '.(int) $id_phase.', '.
				'id_groupe = '.(int) $_POST['gid'].', '.
				'id_concurrent = '.(int) $_POST['cid'].', '.
				'qualifie = 0');
			$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
		}
	}

	die;
}



function printConcurrent($id, $abrev = false) {
	global $concurrents, $phase;

	if ($phase['individuel'])
		return stripslashes($concurrents[$id]['pnom'].' '.$concurrents[$id]['pprenom'].' / '.$concurrents[$id]['enom']);
	else
		return stripslashes((empty($concurrents[$id]['eabreviation']) || !$abrev ? $concurrents[$id]['enom'] : $concurrents[$id]['eabreviation']).' / '.$concurrents[$id]['label']);
}

if (!$phase['cloturee'] &&
	isset($_GET['ajax']) &&
	!empty($_POST['concurrent']) &&
	isset($_POST['qualifie']) && 
	in_array($_POST['concurrent'], array_keys($concurrents))) {

	if (!empty($concurrents[$_POST['concurrent']]['phcoid'])) {
		$ref = pdoRevision('phases_concurrents', $concurrents[$_POST['concurrent']]['phcoid']);
		$pdo->exec('set FOREIGN_KEY_CHECKS = 0');
		$pdo->exec('UPDATE phases_concurrents SET '.
				'_auteur = '.(int) $_SESSION['user']['id'].', '.
				'_date = NOW(), '.
				'_message = "Changement du statut de qualifié", '.
				'_ref = '.(int) $ref.', '.
				//----------//
				'qualifie = '.(!empty($_POST['qualifie']) ? 1 : 0).' '.
			'WHERE '.
				'id = '.$concurrents[$_POST['concurrent']]['phcoid']);
		$pdo->exec('set FOREIGN_KEY_CHECKS = 1');
	} else {
		$pdo->exec('set FOREIGN_KEY_CHECKS = 0');
		$pdo->exec('INSERT INTO phases_concurrents SET '.
			'_auteur = '.(int) $_SESSION['user']['id'].', '.
			'_date = NOW(), '.
			'_message = "Ajout du lien phase concurrent", '.
			//----------//
			'id_phase = '.$id_phase.', '.
			'id_concurrent = '.(int) $_POST['concurrent'].', '.
			'qualifie = '.(!empty($_POST['qualifie']) ? 1 : 0));
		$pdo->exec('set FOREIGN_KEY_CHECKS = 1');
	}

	die;
}

if (isset($_POST['edit_match']) && (
		$phase['type'] == 'elimination' &&
		!empty($_POST['numero']) &&
		intval($_POST['numero']) &&
		$_POST['numero'] > 0 &&
		isset($_POST['a']) &&
		isset($_POST['a']) &&
		(empty($_POST['a']) || in_array($_POST['a'], array_keys($concurrents))) &&
		(empty($_POST['b']) || in_array($_POST['b'], array_keys($concurrents))) ||
		!empty($_POST['a']) &&
		!empty($_POST['b']) &&
		in_array($_POST['a'], array_keys($concurrents)) &&
		in_array($_POST['b'], array_keys($concurrents))) &&
	isset($_POST['site']) && (
		empty($_POST['site']) || 
		in_array($_POST['site'], array_keys($sites))) &&
	isset($_POST['date']) &&
	isset($_POST['time']) &&
	isset($_POST['commentaire'])) {

	$gagne_a = !empty($_POST['gagne_a']);
	$gagne_b = !empty($_POST['gagne_b']);
	$gagne_nul = !empty($_POST['gagne_nul']);
	$gagne = $gagne_a ? '"a"' : ($gagne_b ? '"b"' : ($gagne_nul ? '"nul"' : 'NULL'));
	$forfait = !empty($_POST['forfait']);
	$site = !empty($_POST['site']) ? (int) $_POST['site'] : 'NULL';
	$sets_a = !empty($_POST['sets_a']) ? $_POST['sets_a'] : [''];
	$sets_b = !empty($_POST['sets_b']) ? $_POST['sets_b'] : [''];
	$date = DateTime::createFromFormat('Y-m-d H:i', $_POST['date'].' '.$_POST['time']);
	$date = $date === false ? 'NULL' : '"'.$date->format('Y-m-d H:i:s').'"';

	for ($i = count($sets_a) - 1; $i > 0; $i--) {

		if (trim($sets_a[$i]) == '')
			unset($sets_a[$i]);

		else
			break;
	}

	for ($i = count($sets_b) - 1; $i > 0; $i--) {
		if (trim($sets_b[$i]) == '')
			unset($sets_b[$i]);

		else
			break;
	}

	$existe = null;
	foreach ($matchs as $match) {
		if ($phase['type'] == 'elimination' &&
			$match['numero_elimination'] == $_POST['numero'] || 
			$phase['type'] != 'elimination' && (
				$match['id_concurrent_a'] == $_POST['a'] && 
				$match['id_concurrent_b'] == $_POST['b'] ||
				$match['id_concurrent_a'] == $_POST['b'] && 
				$match['id_concurrent_b'] == $_POST['a'])) {
			$existe = $match;
			break;
		}
	}

	if (!empty($existe)) {
		$ref = pdoRevision('matchs', $existe['id']);
		$pdo->exec('set FOREIGN_KEY_CHECKS = 0');
		$pdo->exec('UPDATE matchs SET '.
				'_auteur = '.(int) $_SESSION['user']['id'].', '.
				'_date = NOW(), '.
				'_message = "Edition du match", '.
				'_ref = '.(int) $ref.', '.
				//----------//
				'date = '.$date.', '.
				'id_site = '.$site.', '.
				'id_concurrent_a = '.(empty($_POST['a']) ? 'NULL' : (int) $_POST['a']).', '.
				'id_concurrent_b = '.(empty($_POST['b']) ? 'NULL' : (int) $_POST['b']).', '.
				'sets_a = "'.secure(json_encode($sets_a)).'", '.
				'sets_b = "'.secure(json_encode($sets_b)).'", '.
				'gagne = '.$gagne.', '.
				'forfait = '.($forfait ? 1 : 0).', '.
				'commentaire = "'.secure($_POST['commentaire']).'" '.
			'WHERE '.
				'id = '.$existe['id']);
		$pdo->exec('set FOREIGN_KEY_CHECKS = 1');
	} else {
		$pdo->exec('set FOREIGN_KEY_CHECKS = 0');
		$pdo->exec('INSERT INTO matchs SET '.
			'_auteur = '.(int) $_SESSION['user']['id'].', '.
			'_date = NOW(), '.
			'_message = "Ajout du match", '.
			//----------//
			($phase['type'] == 'elimination' ? 'numero_elimination = '.(int) $_POST['numero'].', ' : '').
			'id_phase = '.(int) $id_phase.', '.
			'id_concurrent_a = '.(empty($_POST['a']) ? 'NULL' : (int) $_POST['a']).', '.
			'id_concurrent_b = '.(empty($_POST['b']) ? 'NULL' : (int) $_POST['b']).', '.
			'date = '.$date.', '.
			'id_site = '.$site.', '.
			'sets_a = "'.secure(json_encode($sets_a)).'", '.
			'sets_b = "'.secure(json_encode($sets_b)).'", '.
			'gagne = '.$gagne.', '.
			'forfait = '.($forfait ? 1 : 0).', '.
			'commentaire = "'.secure($_POST['commentaire']).'"');
		$pdo->exec('set FOREIGN_KEY_CHECKS = 1');

		if ($phase['type'] == 'elimination')
			$needAddPhase = [$pdo->lastInsertId(), (int) $_POST['numero']];

		$phase['nb_matchs']++;
	}

	$edit = $_POST['a'].'_'.$_POST['b']; //TODO improve ? 
	$matchs = $pdo->query($matchs_sql)
		->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);
} 

if (isset($_POST['del_match']) && (
		$phase['type'] == 'elimination' &&
		!empty($_POST['numero']) &&
		intval($_POST['numero']) &&
		$_POST['numero'] > 0 &&
		isset($_POST['a']) &&
		isset($_POST['a']) &&
		(empty($_POST['a']) || in_array($_POST['a'], array_keys($concurrents))) &&
		(empty($_POST['b']) || in_array($_POST['b'], array_keys($concurrents))) ||
		!empty($_POST['a']) &&
		!empty($_POST['b']) &&
		in_array($_POST['a'], array_keys($concurrents)) &&
		in_array($_POST['b'], array_keys($concurrents)))) {

	$existe = null;
	foreach ($matchs as $match) {
		if ($phase['type'] == 'elimination' &&
			$match['numero_elimination'] == $_POST['numero'] || 
			$phase['type'] != 'elimination' && (
				$match['id_concurrent_a'] == $_POST['a'] && 
				$match['id_concurrent_b'] == $_POST['b'] ||
				$match['id_concurrent_a'] == $_POST['b'] && 
				$match['id_concurrent_b'] == $_POST['a'])) {
			$existe = $match;
			break;
		}
	}

	if (!empty($existe)) {
		$ref = pdoRevision('matchs', $existe['id']);
		$pdo->exec('UPDATE matchs SET '.
				'_auteur = '.(int) $_SESSION['user']['id'].', '.
				'_date = NOW(), '.
				'_etat = "desactive", '.
				'_message = "Suppression du match", '.
				'_ref = '.(int) $ref.' '.
			'WHERE '.
				'id = '.$existe['id']);

		$edit = $_POST['a'].'_'.$_POST['b']; //TODO improve ?
		$matchs = $pdo->query($matchs_sql)
			->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);
	}
}


$groupes_ = $pdo->query('SELECT '.
		'gr.id, '.
		'gr.nom, '.
		'phco.id_concurrent '.
	'FROM groupes AS gr '.
	'LEFT JOIN phases_concurrents AS phco ON '.
		'phco.id_groupe = gr.id AND '.
		'phco.id_phase = gr.id_phase AND '.
		'phco._etat = "active" '.
	'WHERE '.
		'gr.id_phase = '.(int) $phase['id'].' AND '.
		'gr._etat = "active" '.
	'ORDER BY gr.nom ASC') 
	->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_ASSOC);

foreach ($matchs as $match) {
	if (!empty($concurrents[$match['id_concurrent_a']]) && 
		!empty($concurrents[$match['id_concurrent_b']])) {

		if (empty($concurrents[$match['id_concurrent_a']]['matchs'])) { 
			$concurrents[$match['id_concurrent_a']]['matchs'] = [];
			$concurrents[$match['id_concurrent_a']]['joues'] = 0;
			$concurrents[$match['id_concurrent_a']]['gagnes'] = 0;
			$concurrents[$match['id_concurrent_a']]['nuls'] = 0;
			$concurrents[$match['id_concurrent_a']]['perdus'] = 0;
			$concurrents[$match['id_concurrent_a']]['forfaits'] = 0;
			$concurrents[$match['id_concurrent_a']]['points'] = 0;
		}

		if (empty($concurrents[$match['id_concurrent_b']]['matchs'])) {
			$concurrents[$match['id_concurrent_b']]['matchs'] = [];
			$concurrents[$match['id_concurrent_b']]['joues'] = 0;
			$concurrents[$match['id_concurrent_b']]['gagnes'] = 0;
			$concurrents[$match['id_concurrent_b']]['nuls'] = 0;
			$concurrents[$match['id_concurrent_b']]['perdus'] = 0;
			$concurrents[$match['id_concurrent_b']]['forfaits'] = 0;
			$concurrents[$match['id_concurrent_b']]['points'] = 0;
		}

		$concurrents[$match['id_concurrent_a']]['matchs'][] = $match;
		$concurrents[$match['id_concurrent_b']]['matchs'][] = $match;

		if ($match['gagne'] !== null) {
			$concurrents[$match['id_concurrent_a']]['joues']++;
			$concurrents[$match['id_concurrent_b']]['joues']++;

			if ($match['gagne'] == 'a') {
				$concurrents[$match['id_concurrent_a']]['gagnes']++;
				$concurrents[$match['id_concurrent_a']]['points'] += $phase['points_victoire'];

				if ($match['forfait']) {
					$concurrents[$match['id_concurrent_b']]['forfaits']++;
					$concurrents[$match['id_concurrent_b']]['points'] += $phase['points_forfait'];
				} else {
					$concurrents[$match['id_concurrent_b']]['perdus']++;
					$concurrents[$match['id_concurrent_b']]['points'] += $phase['points_defaite'];
				}
			} else if ($match['gagne'] == 'b') {
				$concurrents[$match['id_concurrent_b']]['gagnes']++;
				$concurrents[$match['id_concurrent_b']]['points'] += $phase['points_victoire'];

				if ($match['forfait']) {
					$concurrents[$match['id_concurrent_a']]['forfaits']++;
					$concurrents[$match['id_concurrent_a']]['points'] += $phase['points_forfait'];
				} else {
					$concurrents[$match['id_concurrent_a']]['perdus']++;
					$concurrents[$match['id_concurrent_a']]['points'] += $phase['points_defaite'];
				}
			}  else if ($match['gagne'] == 'nul') {
				$concurrents[$match['id_concurrent_b']]['nuls']++;
				$concurrents[$match['id_concurrent_a']]['nuls']++;
				$concurrents[$match['id_concurrent_b']]['points'] += $phase['points_nul'];
				$concurrents[$match['id_concurrent_a']]['points'] += $phase['points_nul'];
			} 
		}
	}
}

$i = 0;
foreach ($concurrents as $id => $concurrent) {
	$concurrents[$id]['ordre'] = $i++;
}

function sortPoints($a, $b) {
	if (!isset($b['joues']) && !isset($a['joues']))
		return 0;
	if (empty($a['joues']))
		return 1;
	if (empty($b['joues']))
		return -1;
	return $b['points'] - $a['points'];
}

function sortOrdre($a, $b) {
	return $a['ordre'] - $b['ordre'];
}

uasort($concurrents, 'sortPoints');

$groupes = [];
foreach ($groupes_ as $concurrent) {
	if (empty($groupes[$concurrent['id']]))
		$groupes[$concurrent['id']] = [
			'nom' => $concurrent['nom'],
			'concurrents' => []];

	if (!empty($concurrent['id_concurrent']))
		$groupes[$concurrent['id']]['concurrents'][$concurrent['id_concurrent']] = $concurrents[$concurrent['id_concurrent']];
}

foreach ($groupes as $gid => $groupe) {
	uasort($groupes[$gid]['concurrents'], 'sortPoints');
}

if (!empty($_GET['a']) && !empty($_GET['b']) &&
	in_array($_GET['a'], array_keys($concurrents)) && 
	in_array($_GET['b'], array_keys($concurrents)) &&
	in_array($phase['type'], ['poules', 'championnat'])) {

	$select = null;
	foreach ($matchs as $match) {
		if ($match['id_concurrent_a'] == $_GET['a'] && 
			$match['id_concurrent_b'] == $_GET['b'] ||
			$match['id_concurrent_a'] == $_GET['b'] && 
			$match['id_concurrent_b'] == $_GET['a']) {
			$select = $match;
			break;
		}
	}

	if ($select == null) {
		$select = [
			'id_concurrent_a' => $_GET['a'],
			'id_concurrent_b' => $_GET['b']];
	}
} 

$classement = 1;
$points = null;
$exaequo = 1;

foreach ($concurrents as $id => $concurrent) {
	if (!empty($concurrent['joues'])) {
		if ($points === null) {
			$points = $concurrent['points'];
		} else if ($concurrent['points'] < $points) {
			$classement += $exaequo;
			$points = $concurrent['points'];
			$exaequo = 1;
		} else {
			$exaequo++;
		}
	} else {
		if ($points !== null) {
			$classement++;
			$points = null;
			$exaequo = 1;
		} else {
			$exaequo++;
		}
	} 

	$concurrents[$id]['classement'] = $classement;
}

foreach ($groupes as $gid => $groupe) {
	$classement = 1;
	$points = null;
	$exaequo = 1;

	foreach ($groupe['concurrents'] as $id => $concurrent) {
		if (!empty($concurrent['joues'])) {
			if ($points === null) {
				$points = $concurrent['points'];
			} else if ($concurrent['points'] < $points) {
				$classement += $exaequo;
				$points = $concurrent['points'];
				$exaequo = 1;
			} else {
				$exaequo++;
			}
		} else {
			if ($points !== null) {
				$classement++;
				$points = null;
				$exaequo = 1;
			} else {
				$exaequo++;
			}
		} 

		$groupes[$gid]['concurrents'][$id]['classement'] = $classement;
	}

	if (empty($_GET['t']))
		uasort($groupes[$gid]['concurrents'], 'sortOrdre');
}

if (empty($_GET['t']))
	uasort($concurrents, 'sortOrdre');

if ($phase['type'] == 'elimination') {


	$matchsN = [];
	$ecole = !empty($_GET['e']) ? $_GET['e'] : '';
	$hasPetiteFinale = true;

	foreach ($matchs as $match) {
		if (!empty($match['numero_elimination'])) {
			$matchsN[$match['numero_elimination']] = $match;
		}
	}
	//--------//

	$lines = [];
	$pow = [];
	$equipes = $phase['nb_precedentes_ouvertes'] == 0 ? count($concurrents) : (int) $phase['prevision'];
	$labelsPhasesFinales = [
		'petite_finale' => 'Petite finale',
		'finale' 		=> 'Finale',
		'demie' 		=> 'Demie',
		'quart' 		=> 'Quart',
		'huitieme' 		=> 'Huitième',
		'seizieme' 		=> 'Seizième'];

	$etapes = $equipes ? log($equipes, 2) : 0; 
	$etapes = $etapes == floor($etapes) ? $etapes : ceil($etapes);

	//if ($equipes <= 4 && $hasPetiteFinale)
	//	$etapes--;

	$pow[0] = 1;
	for ($etape = 1; $etape <= $etapes + 1; $etape++) {
		$pow[$etape] = $pow[$etape - 1] * 2;
	}

	$barrages = $pow[$etapes] - $equipes;
	$lignes = $pow[$etapes] - 1; 

	function alterner($ligne) {
		global $lignes; 
		
		$half = ($lignes + 1) / 2;
		$l = ($ligne + 1) / 2;

		if ($ligne <= $half) {
			if (((int)$l) % 2) return $l;
			else return $half / 2 + $l - 1;
		} 

		else {
			if (((int)$l) % 2) return 3 * $half / 2 - $l + 1;
			else return $half - $l + 2;
		}
	}

	function numero($ligne, $etape) {
		global $etapes, $pow, $barrages;

		if ($etape > 1) {
			$numero = ceil($ligne / $pow[$etape]);
			$numero += $pow[$etapes - $etape + 1] * ($pow[$etape - 1] - 1);
			$numero -= $barrages;
		} else {
			$numero = alterner($ligne) - (max(0, $barrages)); 
		}

		return $numero;
	}

	$nbMatchs = numero(($lignes + 1) / 2, $etapes) + ($hasPetiteFinale ? 1 : 0);

	function phase($numero) {
		global $nbMatchs, $hasPetiteFinale, $etapes, $labelsPhasesFinales, $pow, $barrages;

		if ($numero == $nbMatchs && $hasPetiteFinale)
			return $labelsPhasesFinales[array_keys($labelsPhasesFinales)[0]];

		$nbMatchsBarrages = $barrages ? $pow[$etapes - 1] - $barrages : 0;
		if ($numero >= 1 && $numero <= $nbMatchsBarrages)
			return 'Barrages';
			
		$buffer = $nbMatchs - ($hasPetiteFinale ? 1 : 0);
		$count = count($labelsPhasesFinales) - 1;
		for ($i = 1; $i <= $etapes; $i++) {
			if ($numero <= $buffer && $numero > $buffer - $pow[$i - 1]) {
				return $i > $count ? $pow[$i - 1].'ième' : $labelsPhasesFinales[array_keys($labelsPhasesFinales)[$i]];
			} else {
				$buffer -= $pow[$i - 1];
			}
		}

		return 'Inconnu';
	}

	function isBarrage($ligne) {
		global $barrages;
		$ligneAlternee = alterner($ligne);

		return $ligne % 2 &&
			$ligneAlternee >= 0 &&
			$ligneAlternee <= $barrages;
	}

	function findA($numero, $lose = false) {
		global $matchsN;

		if (empty($matchsN[$numero]) || 
			empty($matchsN[$numero]['id_concurrent_a']) &&
			empty($matchsN[$numero]['from_a'])) {
			return null;
		} else if (!empty($matchsN[$numero]['id_concurrent_a'])) {
			return $matchsN[$numero]['id_concurrent_a'];
		} else {
			return findG($matchsN[$numero]['from_a'], $lose);
		}
	}

	function findB($numero, $lose = false) {
		global $matchsN;

		if (empty($matchsN[$numero]) || 
			empty($matchsN[$numero]['id_concurrent_b']) &&
			empty($matchsN[$numero]['from_b'])) {
			return null;
		} else if (!empty($matchsN[$numero]['id_concurrent_b'])) {
			return $matchsN[$numero]['id_concurrent_b'];
		} else {
			return findG($matchsN[$numero]['from_b'], $lose);
		}
	}

	function findG($numero, $lose = false) {
		global $matchsN; 

		if (empty($matchsN[$numero]) || 
			empty($matchsN[$numero]['gagne']) ||
			!in_array($matchsN[$numero]['gagne'], ['a', 'b'])) {
			return null;
		} else if ($matchsN[$numero]['gagne'] == ($lose ? 'b' : 'a')) {
			return findA($numero);
		} else {
			return findB($numero);
		}
	}

	function medal($medal) {
		$medals = [
			'A' => 'Argent', 
			'G' => 'Gold', 
			'B' => 'Bronze'];
		return '<img src="'.url('assets/images/actions/'.$medals[$medal].'.png', false, false).'" alt="'.$medal.'" />';
	}

	if (!empty($needAddPhase)) {
		$pdo->exec('UPDATE matchs SET '.
				'phase_elimination = "'.secure(phase($needAddPhase[1])).'" '.
			'WHERE id = '.(int) $needAddPhase[0]);
	}

	$prepare = $pdo->prepare('INSERT INTO matchs SET '.
		'_auteur = '.(int) $_SESSION['user']['id'].', '.
		'_date = NOW(), '.
		'_message = "Création du match", '.
		//------------//
		'id_phase = '.(int) $id_phase.', '.
		'numero_elimination = :numero_elimination, '.
		'phase_elimination = :phase_elimination, '.
		'id_concurrent_a = NULL, '.
		'id_concurrent_b = NULL'); 		

	if (!empty($_POST['init']) &&
		empty($phase['nb_matchs']))
		$reloadMatch = true;

	for ($ligne = 1; $ligne <= $lignes; $ligne++) {	
		for ($etape = 1; $etape <= $etapes + ($hasPetiteFinale ? 1 : 0); $etape++) {
			$etape_ = min($etape, $etapes);
			$isMatch = ($ligne - 1 - ($pow[$etape_ - 1] - 1)) % $pow[$etape_] == 0;

			if ($etape == 1 && $isMatch && isBarrage($ligne)) {
				continue 2;
			}

			if ($isMatch) {
				$isFirstA = $etape_ == 1 || $etape_ == 2 && alterner($ligne - 1) <= $barrages;
				$isFirstB = $etape_ == 1 || $etape_ == 2 && alterner($ligne + 1) <= $barrages;
				$numeroA = $isFirstA ? null : numero($ligne - $pow[$etape_ - 2], $etape_ - 1);
				$numeroB = $isFirstB ? null : numero($ligne + $pow[$etape_ - 2], $etape_ - 1);
				$numero = numero($ligne, $etape_) + ($etape > $etapes ? 1 : 0);

				$matchsN[$numero] = empty($matchsN[$numero]) ? [] : $matchsN[$numero];
				$matchsN[$numero]['from_a'] = !$isFirstA ? $numeroA : null;
				$matchsN[$numero]['from_b'] = !$isFirstB ? $numeroB : null;

				/*if ($etape > 1) {
					unset($matchsN[$numero]['id_concurrent_a']);
					unset($matchsN[$numero]['id_concurrent_b']);
				}*/

				if (!empty($reloadMatch)) {
					$phase['nb_matchs']++;
					$prepare->execute(array(
						':numero_elimination' => $numero,
						':phase_elimination' => phase($numero)));
				}
			}
		}
	}

	if (!empty($reloadMatch)) {
		$matchs = $pdo->query($matchs_sql)
			->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);
	}

	if (!empty($_GET['m']) &&
		$nbMatchs &&
		intval($_GET['m']) &&
		$_GET['m'] >= 1 &&
		$_GET['m'] <= $nbMatchs) {

		$select = $matchsN[$_GET['m']];

		if (empty($select['id'])) {
			$select['id'] = $_GET['m'];
			$select['id_concurrent_a'] = '';
			$select['id_concurrent_b'] = '';
			$select['numero_elimination'] = $_GET['m'];
		}
	}
}

if ($phase['type'] == 'elimination' && (
		empty($_GET['p']) || 
		$_GET['p'] == 'resume')) {

	$n = $etapes + ($hasPetiteFinale ? 1 : 0);
	for ($etape = 1; $etape <= $n; $etape++) {
		$etape_ = min($etape, $etapes);
		$line = '<td class="etape">'.($barrages > 0 && $etape == 1 ? 'Barrages' : 
			(empty(array_keys($labelsPhasesFinales)[$n - $etape + ($hasPetiteFinale ? 0 : 1)]) ? $pow[$n - $etape - 1].'ième' : 
				$labelsPhasesFinales[array_keys($labelsPhasesFinales)[$n - $etape + ($hasPetiteFinale ? 0 : 1)]])).'</td>';
		$lines[$ligne][$etape_] = $etape > $etapes ? $line . $lines[$ligne][$etape_] : $line;
	}

	for ($ligne = 1; $ligne <= $lignes; $ligne++) {	
		for ($etape = 1; $etape <= $etapes + ($hasPetiteFinale ? 1 : 0); $etape++) {
			$etape_ = min($etape, $etapes);
			$isMatch = ($ligne - 1 - ($pow[$etape_ - 1] - 1)) % $pow[$etape_] == 0;

			if ($etape_ == 1 && $isMatch && isBarrage($ligne)) {
				continue 2;
			}

			if ($isMatch) {
				$numero = numero($ligne, $etape_) + ($etape > $etapes ? 1 : 0);
				$match = $matchsN[$numero];
				$a = findA($numero, $etape > $etapes);
				$b = findB($numero, $etape > $etapes);
				$sets_a = isset($match['sets_a']) ? json_decode(unsecure($match['sets_a'])) : [];
				$sets_b = isset($match['sets_b']) ? json_decode(unsecure($match['sets_b'])) : [];
				$a_win = $a/* && $b*/ && !empty($match['gagne']) && $match['gagne'] == 'a';
				$b_win = /*$a && */$b && !empty($match['gagne']) && $match['gagne'] == 'b';

				if (count($sets_a) && 
					count($sets_b) &&
					trim($sets_a[0]) == '' &&
					trim($sets_b[0]) == '') {
					unset($sets_a[0]);
					unset($sets_b[0]);
				}

				$sets_a_line = '';
				$sets_b_line = '';
				$sets = max(count($sets_a), count($sets_b));
				for ($i = 0; $i < $sets; $i++) {
					$set_a = isset($sets_a[$i]) && trim($sets_a[$i]) !== '' ? $sets_a[$i] : null;
					$set_b = isset($sets_b[$i]) && trim($sets_b[$i]) !== '' ? $sets_b[$i] : null;

					$sets_a_line .= '<td class="match-set set-'.($set_a === null ? 'empty' : ($set_b === null || $set_a > $set_b ? 'win' : ($set_a === $set_b ? 'par' : 'lose'))).($ecole === $a ? ' set-highlight' : '').'">'.
							($set_a === null ? '-' : $set_a).'</td>';
					$sets_b_line .= '<td class="match-set set-'.($set_b === null ? 'empty' : ($set_a === null || $set_b > $set_a ? 'win' : ($set_b === $set_a ? 'par' : 'lose'))).($ecole === $b ? ' set-highlight' : '').'">'.
							($set_b === null ? '-' : $set_b).'</td>';
				}

				$line = '<td onclick="window.location.href = \''.url((empty($vpTournois) ? 'admin/module/tournois/' : 'centralien/vptournoi/').'phase_'.$id_phase.'?m='.$numero.'&'.rand().'#m'.$numero, false, false).'\'" class="match'.($ecole === $a || $ecole === $b ? ' match-highlight' : '').($etape > $etapes ? ' petite-finale' : '').'">'.
					'<table><tr><td rowspan="2" class="match-numero">'.$numero.'</td>'.
					($etape_ == $etapes ? '<td class="match-medal">'.($a_win ? medal($etape > $etapes ? 'B' : 'G') : ($b_win ? ($etape > $etapes ? '' : medal('A')) : '')).'</td>' : '').
					'<td class="match-team team-a team-'.($a === null ? 'empty' : ($a_win ? 'win' : ($b_win ? 'lose' : 'par'))).($ecole === $a ? ' team-highlight' : '').'"><div><a name="m'.$numero.'"></a>'.
						($a === null && $match['from_a'] ? ($etape > $etapes ? 'P' : 'G').$match['from_a'] : ($a === null ? '-' : printConcurrent($a, true))).'</div></td>'.$sets_a_line.
						(!$a_win && !$b_win && (isset($match['date']) || isset($match['id_site'])) ? '<td class="match-infos" rowspan="2">'.
							(isset($match['date']) ? (new DateTime($match['date']))->format('j/m H:i') : '').(isset($match['id_site']) ? '<br />'.$sites[$match['id_site']]['nom'] : '').'</td>' : '').
					'<td class="match-edit" rowspan="2"><div><span>Edit</span></div></td></tr><tr>'.
					($etape_ == $etapes ? '<td class="match-medal">'.($b_win ? medal($etape > $etapes ? 'B' : 'G') : ($a_win ? ($etape > $etapes ? '' : medal('A')) : '')).'</td>' : '').
						'<td class="match-team team-b team-'.($b === null ? 'empty' : ($b_win ? 'win' : ($a_win ? 'lose' : 'par'))).($ecole === $b ? ' team-highlight' : '').'"><div>'.
						($b === null && $match['from_b'] ? ($etape > $etapes ? 'P' : 'G').$match['from_b'] : ($b === null ? '-' : printConcurrent($b, true))).'</div></td>'.$sets_b_line.'</tr></table></td>';

				$lines[$ligne][$etape_] = $etape > $etapes ? $line . $lines[$ligne][$etape_] : $line;
			} else if ($etape <= $etapes) {
				$hasMatchTop = $etape > 1 && ($ligne - ($etape > 2 && isBarrage($ligne - 1) ? 3 : 2) - ($pow[$etape - 1] - 1)) % $pow[$etape] == 0;
				$hasMatchBottom = $etape > 1 && ($ligne + ($etape > 2 && isBarrage($ligne + 1) ? 1 : 0) - ($pow[$etape - 1] - 1)) % $pow[$etape] == 0;
				$hasMatchLeft = $etape > 1 && ($ligne - $pow[$etape - 2]) % $pow[$etape - 1] == 0;
				$hasALeft = $hasMatchLeft && ($ligne - $pow[$etape - 2]) % $pow[$etape] == 0;
				$hasBLeft = $hasMatchLeft && !$hasALeft;
				$isLine = $etape > 1 ? ($ligne - $pow[$etape - 2]) % $pow[$etape] : 0;
				$isLine = $isLine > 0 && $isLine < $pow[$etape - 1];
				$numeroFrom = false;

				if ($isLine) {
					$numeroFrom = ($ligne - $pow[$etape - 2]) % $pow[$etape - 1] < $pow[$etape - 2];
					$numeroFrom = $numeroFrom ? numero($ligne - ($ligne - $pow[$etape - 2]) % $pow[$etape - 1], $etape - 1) : false;
					$numeroFrom = !$numeroFrom ? numero($ligne + $pow[$etape - 1] - ($ligne + $pow[$etape - 2]) % $pow[$etape - 1], $etape - 1) : $numeroFrom;
				} else if ($hasALeft || $hasBLeft) {
					$numeroFrom = numero($ligne, $etape - 1);
				}

				$classes = [];
				if ($numeroFrom !== false && findG($numeroFrom) === $ecole || $etape == $etapes && findG($numeroFrom, true) === $ecole && $hasPetiteFinale) $classes[] = 'link-highlight';
				if ($hasMatchTop && $etape == $etapes && $hasPetiteFinale) $classes[] = 'has-match-top-finales';
				else if ($hasMatchTop) $classes[] = 'has-match-top';
				if ($hasMatchBottom && $etape == $etapes && $hasPetiteFinale) $classes[] = 'has-match-bottom-finales';
				else if ($hasMatchBottom) $classes[] = 'has-match-bottom';
				if ($hasALeft) $classes[] = 'has-a-left';
				if ($hasBLeft) $classes[] = 'has-b-left';
				if ($isLine) $classes[] = 'line';

				$lines[$ligne][$etape] = '<td'.(count($classes) ? ' class="'.implode(' ', $classes).'"' : '').
					($hasPetiteFinale && $etape == $etapes ? ' colspan="2"' : '').'><div>&nbsp;</div></td>';
			}
		}
	}
}

$formats = [];

$nbEquipes = count($concurrents);
$powEquipe = floor(log($nbEquipes, 2));
$nbEquipesFinales = pow(2, $powEquipe);
$nbPoulesMax = floor($nbEquipes / 2);

for ($nbPoules = $nbPoulesMax; $nbPoules > 1; $nbPoules--) {
	$nbEquipesPoule = floor($nbEquipes / $nbPoules);  //N
	//nbEquipes = N*nbN + N1*nbN1
	//nbN + nbN1 = nbPoules
	//nbEquipes = N*nbN + N1*(nbPoules - nbN) = N*nbN + (N+1)*nbPoules - N*nbN - nbN = (N+1)*nbPoules - nbN
	//nbN = (N+1)*nbPoules - nbEquipes
	//nbN1 = nbPoules - nbN = nbPoules - (N+1)*nbPoules + nbEquipes
	//nbN1 = nbEquipes - N*nbPoules
	$nbPoulesN = ($nbEquipesPoule + 1) * $nbPoules - $nbEquipes;
	$nbPoulesN1 = $nbEquipes - $nbEquipesPoule * $nbPoules;
	$nbEquipesQualifieesPoule = floor($nbEquipesFinales / $nbPoules);
	$nbEquipesRepechees = $nbEquipesFinales - $nbPoules * $nbEquipesQualifieesPoule;
	$pourcentage = $nbEquipesQualifieesPoule * $nbPoules / $nbEquipes;
	$nbMatchsTot = ($nbEquipesPoule * ($nbEquipesPoule - 1) * $nbPoulesN + $nbEquipesPoule * ($nbEquipesPoule + 1) * $nbPoulesN1) / 2;

	//if (2 * $nbMatchsTot < $nbEquipes || $nbMatchsTot > 2 * $nbEquipes || $pourcentage < 0.5)
	//	continue; 

	$formats[$nbPoules] = [
		'nb_matchs' => $nbMatchsTot, 
		'nb_equipes' => $nbEquipesPoule,
		'nb_qualifiees' => $nbEquipesQualifieesPoule,
		'pourcentage' => $pourcentage, 
		'nb_repechees' => $nbEquipesRepechees];
}


//Inclusion du bon fichier de template
require DIR.'templates/admin/tournois/phase_'.$phase['type'].'.php';
