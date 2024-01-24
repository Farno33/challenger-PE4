<?php

$actions = [
	'listActions'				=> 'Return all actions available in tournoi module',
	'getTournois' 				=> 'Return list of tournois',
	'getTournoi'				=> 'Return tournoi associated to an ID',
	'getPhasesTournoi' 			=> 'Return all phases associated to a tournoi',
	'getPhase' 					=> 'Return phase associated to an ID',
	'getGroupesPhase' 			=> 'Return all groupes associated to a phase',
	'getConcurrentsPhase'		=> 'Return all concurrents associated to a phase',
	'getMatchsPhase' 			=> 'Return all matchs associated to a phase',
	'getMatch'					=> 'Return match associated to an ID',
	'getSportIDFromMatch'		=> 'Return sport ID associated to a match (intended for local use (app backend))',
	'getPodiums' 				=> 'Return all podiums associated to sports\' ID',
	'getSites' 					=> 'Return all sites where it may have a tournoi',
	'getSite' 					=> 'Return site where it may have a tournoi with ID',
	'S_setSetsMatch' 			=> 'Edit sets of a match having its ID'];


function tournoi_listActions() {
	global $actions;
	returnJson(0, ['actions' => $actions]);
}


function tournoi_getTournois($data, $pdo) {
	$tournois = $pdo->query('SELECT '.
			'id, sport, sexe, individuel, tournoi_initie '.
		'FROM sports '.
		'WHERE _etat = "active"')
		->fetchAll(PDO::FETCH_ASSOC);

	returnJson(0, ['tournois' => $tournois]);
}

function tournoi_getTournoi($data, $pdo, $user, $return = false) {
	if (empty($data['id']) ||
		!intval($data['id']) ||
		$data['id'] < 0)
		returnJson(101, ['message' => 'Tournoi\'s ID is invalid or not specified']);

	$tournoi = $pdo->query('SELECT '.
			'sport, sexe, individuel '.
		'FROM sports '.
		'WHERE '.
			'_etat = "active" AND '.
			'id = '.(int) $data['id'])
		->fetch(PDO::FETCH_ASSOC);

	if (empty($tournoi))
		returnJson(102, ['message' => 'Tournoi ID does not exist']);

	if ($return)
		return $tournoi;
	
	returnJson(0, ['tournoi' => $tournoi]);
}

function tournoi_getPhasesTournoi($data, $pdo, $user) {
	$tournoi = tournoi_getTournoi($data, $pdo, $user, true);
	
	$phases = $pdo->query('SELECT '.
			'id, nom, type, id_phase_suivante, cloturee, '.
			'points_victoire, points_nul, points_defaite, points_forfait '.
		'FROM phases '.
		'WHERE '.
			'id_sport = '.(int) $data['id'].' AND '.
			'_etat = "active"')
		->fetchAll(PDO::FETCH_ASSOC);

	foreach ($phases as $k => $phase) {
		if (!in_array($phase['type'], ['poules', 'championnat'])) {
			unset($phases[$k]['points_forfait']);
			unset($phases[$k]['points_defaite']);
			unset($phases[$k]['points_nul']);
			unset($phases[$k]['points_victoire']);
		}
	}

	returnJson(0, ['phases' => $phases]);
}

function tournoi_getPhase($data, $pdo, $user, $return = false) {
	if (empty($data['id']) ||
		!intval($data['id']) ||
		$data['id'] < 0)
		returnJson(101, ['message' => 'Phase\'s ID is invalid or not specified']);

	$phase = $pdo->query('SELECT '.
			'nom, type, id_phase_suivante, cloturee, '.
			'points_victoire, points_nul, points_defaite, points_forfait '.
		'FROM phases '.
		'WHERE '.
			'_etat = "active" AND '.
			'id = '.(int) $data['id'])
		->fetch(PDO::FETCH_ASSOC);

	if (empty($phase))
		returnJson(102, ['message' => 'Phase ID does not exist']);

	if (!in_array($phase['type'], ['poules', 'championnat'])) {
		unset($phase['points_forfait']);
		unset($phase['points_defaite']);
		unset($phase['points_nul']);
		unset($phase['points_victoire']);
	}

	if ($return)
		return $phase;
	
	returnJson(0, ['phase' => $phase]);
}

function tournoi_getMatchsPhase($data, $pdo, $user) {
	$phase = tournoi_getPhase($data, $pdo, $user, true);
	
	$matchs = $pdo->query('SELECT '.
			'id, id_concurrent_a, id_concurrent_b, '.
			($phase['type'] == 'elimination' ? 'numero_elimination, phase_elimination, ' : '').
			'date, id_site, sets_a, sets_b, gagne, forfait, Commentaire '.
		'FROM matchs '.
		'WHERE '.
			'id_phase = '.(int) $data['id'].' AND '.
			'_etat = "active"')
		->fetchAll(PDO::FETCH_ASSOC);

	foreach ($matchs as $k => $match) {
		$matchs[$k]['sets_a'] = json_decode(unsecure($match['sets_a']));
		$matchs[$k]['sets_b'] = json_decode(unsecure($match['sets_b']));
	}

	returnJson(0, ['matchs' => $matchs]);
}

function tournoi_getMatch($data, $pdo, $user) {
	if (empty($data['id']) ||
		!intval($data['id']) ||
		$data['id'] < 0)
		returnJson(101, ['message' => 'Match\'s ID is invalid or not specified']);

	$match = $pdo->query('SELECT '.
			'id, id_concurrent_a, id_concurrent_b, '.
			'numero_elimination, phase_elimination, '.
			'date, id_site, sets_a, sets_b, gagne, forfait, Commentaire '.
		'FROM matchs '.
		'WHERE '.
			'id = '.(int) $data['id'].' AND '.
			'_etat = "active"')
		->fetch(PDO::FETCH_ASSOC);

	if (!empty($match['sets_a'])) $match['sets_a'] = json_decode(unsecure($match['sets_a']));
	if (!empty($match['sets_b'])) $match['sets_b'] = json_decode(unsecure($match['sets_b']));
	
	returnJson(0, ['match' => $match]);
}

function tournoi_getSportIDFromMatch($data, $pdo, $user) {
	if (empty($data['id']) ||
		!intval($data['id']) ||
		$data['id'] < 0)
		returnJson(101, ['message' => 'Match\'s ID is invalid or not specified']);

	$match = $pdo->query('SELECT sports.id '.
	'FROM sports '.
			'JOIN phases ON sports.id = phases.id_sport AND phases._etat = "active" '.
			'JOIN matchs ON phases.id = matchs.id_phase AND matchs._etat = "active" '.
	'WHERE matchs.id = '.(int) $data['id'].' '.
		'AND sports._etat = "active"')
	->fetch(PDO::FETCH_COLUMN);
	
	returnJson(0, ['id' => $match]);
}

function tournoi_getGroupesPhase($data, $pdo, $user) {
	$phase = tournoi_getPhase($data, $pdo, $user, true);

	$groupes = $pdo->query('SELECT '.
			'id, nom '.
		'FROM groupes '.
		'WHERE '.
			'id_phase = '.(int) $data['id'].' AND '.
			'_etat = "active"')
		->fetchAll(PDO::FETCH_ASSOC);

	returnJson(0, ['groupes' => $groupes]);
}

function tournoi_getConcurrentsPhase($data, $pdo, $user) {
	$phase = tournoi_getPhase($data, $pdo, $user, true);
	//Ne renvoit que les concurrents des phases de poules (ceux qui sont dans des groupes)

	if ($phase['type'] != 'poules')
		returnJson(103, ['message' => 'Phase\'s type must be "Poules" to use this action']);

	$matchs = $pdo->query($matchs_sql = 'SELECT '.
			'm.id, '.
			'm.*'.
		'FROM matchs AS m '.
		'WHERE '.
			'm.id_phase = '.(int) $data['id']. ' AND '.
			'm._etat = "active"')
		->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);

	$concurrents = $pdo->query('SELECT '.
			'c.id, c.id_sportif, c.id_equipe, pc.id_groupe, 0 AS points '.
		'FROM phases_concurrents AS pc '.
		'JOIN concurrents AS c ON '.
			'c.id = pc.id_concurrent AND '.
			'c._etat = "active" '.
		'WHERE '.
			'pc.id_phase = '.(int) $data['id'].' AND '.
			'pc._etat = "active"') 
		->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);

	foreach ($matchs as $match) {
		if (!empty($concurrents[$match['id_concurrent_a']]) && 
			!empty($concurrents[$match['id_concurrent_b']])) {

			if ($match['gagne'] == 'a') {
				$concurrents[$match['id_concurrent_a']]['points'] += $phase['points_victoire'];
				$concurrents[$match['id_concurrent_b']]['points'] += $match['forfait'] ? $phase['points_forfait'] : $phase['points_defaite'];
			} else if ($match['gagne'] == 'b') {
				$concurrents[$match['id_concurrent_b']]['points'] += $phase['points_victoire'];
				$concurrents[$match['id_concurrent_a']]['points'] += $match['forfait'] ? $phase['points_forfait'] : $phase['points_defaite'];
			}  else if ($match['gagne'] == 'nul') {
				$concurrents[$match['id_concurrent_b']]['points'] += $phase['points_nul'];
				$concurrents[$match['id_concurrent_a']]['points'] += $phase['points_nul'];
			} 
		}
	}

	uasort($concurrents, function ($cA, $cB) {
		if ($cA['points'] == $cB['points']) return 0;
		return $cA['points'] > $cB['points'] ? -1 : 1;
	});

	$p = [];
	$i = [];
	$previous = [];
	$prevKey = [];
	$concurrents_ = [];
	foreach ($concurrents as $key => $concurrent) {
		if (empty($concurrent['id_groupe']))
			continue; 

		if (isset($prevKey[$concurrent['id_groupe']]) &&
			isset($concurrents[$prevKey[$concurrent['id_groupe']]]) && 
			$concurrents[$prevKey[$concurrent['id_groupe']]]['points'] == $concurrent['points']) {
			$concurrents[$prevKey[$concurrent['id_groupe']]]['exaequo'] = true;
			$concurrents[$key]['exaequo'] = true;
		}

		$prevKey[$concurrent['id_groupe']] = $key;
	}

	foreach ($concurrents as $cid => $concurrent) {
		if (!empty($concurrent['id_groupe'])) {
			$p[$concurrent['id_groupe']] = empty($p[$concurrent['id_groupe']]) ? 1 : $p[$concurrent['id_groupe']] + 1;

			if (empty($i[$concurrent['id_groupe']]) || 
				empty($previous[$concurrent['id_groupe']]) ||
				$previous[$concurrent['id_groupe']] > $concurrent['points'])
				$i[$concurrent['id_groupe']] = $p[$concurrent['id_groupe']];

			$previous[$concurrent['id_groupe']] = $concurrent['points'];
		} 

		$concurrent['classement'] = empty($i[$concurrent['id_groupe']]) ? null : $i[$concurrent['id_groupe']];
		$concurrent['exaequo'] = empty($concurrent['exaequo']) ? 0 : 1;
		$concurrents_[] = array_merge(['id' => $cid], $concurrent);
	} 

	returnJson(0, ['concurrents' => $concurrents_]);
}

function tournoi_getPodiums($data, $pdo, $user) {
	$concurrents = $pdo->query('SELECT '.
			'c.id, '.
			'CONCAT(p.prenom, " ", SUBSTR(p.nom, 1, 1), ".") AS diminutif, '.
			'eq.label, '.
			'c.id_sportif, '.
			'c.id_equipe, '.
			'CASE WHEN essp.id_ecole IS NULL THEN es.id_ecole ELSE essp.id_ecole END AS eid '.
		'FROM concurrents AS c '.
		'LEFT JOIN (sportifs AS sp '.
			'JOIN participants AS p ON '.
				'p.id = sp.id_participant AND '.
				'p._etat = "active" '.
			'JOIN equipes AS eqsp ON '.
				'eqsp.id = sp.id_equipe AND '.
				'eqsp._etat = "active" '.
			'JOIN ecoles_sports AS essp ON '.
				'essp.id = eqsp.id_ecole_sport AND '.
				'essp._etat = "active") ON '.
			'sp.id = c.id_sportif AND '.
			'sp._etat = "active" '.
		'LEFT JOIN (equipes AS eq '.
			'JOIN ecoles_sports AS es ON '.
				'es.id = eq.id_ecole_sport AND '.
				'es._etat = "active") ON '.
			'eq.id = c.id_equipe AND '.
			'eq._etat = "active" '.
		'WHERE '.
			'c._etat = "active"')
		->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);

	$podiums = $pdo->query('SELECT '.
			's.id, '.
			'p.coeff, '.
			'p.id_concurrent1, '.
			'p.id_concurrent2, '.
			'p.id_concurrent3, '.
			'p.id_concurrent3ex, '.
			'p.ex_12, '.
			'p.ex_23, '.
			'p.ex_3 '.
		'FROM sports AS s '.
		'LEFT JOIN podiums AS p ON '.
			'p.id_sport = s.id AND '.
			'p._etat = "active" '.
		'WHERE '.
			's._etat = "active"')
		->fetchAll(PDO::FETCH_ASSOC);

	$tos = ['1', '2', '3', '3ex'];
	foreach ($podiums as $k => $podium) {
		foreach ($tos as $to) {
			if (empty($concurrents[$podium['id_concurrent'.$to]])) {
				$podiums[$k]['id_ecole'.$to] = null;
				$podiums[$k]['diminutif'.$to] = null;
				$podiums[$k]['label'.$to] = null;
			} else {
				$podiums[$k]['id_ecole'.$to] = $concurrents[$podium['id_concurrent'.$to]]['eid'];
				$podiums[$k]['diminutif'.$to] = ucname($concurrents[$podium['id_concurrent'.$to]]['diminutif']);
				$podiums[$k]['label'.$to] = $concurrents[$podium['id_concurrent'.$to]]['label'];;
			}
		}
	}

	returnJson(0, ['podiums' => $podiums]);
}

function tournoi_getSites($data, $pdo, $user) {	
	$sites = $pdo->query('SELECT '.
			'id, nom, description, latitude, longitude '.
		'FROM sites '.
		'WHERE '.
			'_etat = "active"')
		->fetchAll(PDO::FETCH_ASSOC);

	returnJson(0, ['sites' => $sites]);
}

function tournoi_getSite($data, $pdo, $user) {
	if (empty($data['id']) ||
		!intval($data['id']) ||
		$data['id'] < 0)
		returnJson(101, ['message' => 'Site\'s ID is invalid or not specified']);

	$site = $pdo->query('SELECT '.
			'nom, description, latitude, longitude '.
		'FROM sites '.
		'WHERE '.
			'_etat = "active" AND '.
			'id = '.(int) $data['id'])
		->fetch(PDO::FETCH_ASSOC);

	if (empty($site))
		returnJson(102, ['message' => 'Site ID does not exist']);
	
	returnJson(0, ['site' => $site]);
}

function tournoi_S_setSetsMatch($data, $pdo, $user) {
	if (empty($data['id']) ||
		!intval($data['id']) ||
		$data['id'] < 0)
		returnJson(101, ['message' => 'Match\'s ID is invalid or not specified']);

	$sets_a = json_decode(empty($data['sets_a']) ? '' : $data['sets_a']);
	$sets_b = json_decode(empty($data['sets_b']) ? '' : $data['sets_b']);

	if (empty($data['sets_a']) ||
		empty($data['sets_b']) ||
		$sets_a === false ||
		$sets_b === false)
		returnJson(101, ['message' => 'sets_a and sets_b must be specified and valid JSON string']);

	//Vérifier que les sets sont des tableaux et contenant uniquement des chaines et sans indices particiliers
	//Idéalement cette action (à renommer devrait aussi permettre de définir le vainquer : a/b/nul)
	//Enfin qu'en est-il des matchs où les concurrents ne sont pas fixés (eliminination par ex) ?

	$match = $pdo->query('SELECT '.
			'id '.
		'FROM matchs '.
		'WHERE '.
			'_etat = "active" AND '.
			'id = '.(int) $data['id'])
		->fetch(PDO::FETCH_ASSOC);

	if (empty($match))
		returnJson(102, ['message' => 'Match ID does not exist']);

	$ref = pdoRevision('matchs', $data['id']);
	$pdo->exec('UPDATE matchs SET '.
			'_auteur = '.(int) $user['id'].', '.
			'_date = NOW(), '.
			'_ref = '.(int) $ref.', '.
			'_message = "Edition du score via l\'API", '.
			//---------
			'sets_a = "'.secure($data['sets_a']).'", '.
			'sets_b = "'.secure($data['sets_b']).'" '.
		'WHERE '.
			'id = '.(int) $data['id']);

	returnJson(0, ['success' => 1]);
}
