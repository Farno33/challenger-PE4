<?php

$actions = [
	'listActions'		=> 'Return all actions available in ecole module',
	'getEcoles' 		=> 'Return list of ecoles with ID',
	'getEcole'			=> 'Return ecole associated to an ID',
	'getPoints' 		=> 'Return points of each ecole'];


function ecole_listActions() {
	global $actions;
	returnJson(0, ['actions' => $actions]);
}


function ecole_getEcoles($data, $pdo) {
	$ecoles = $pdo->query('SELECT '.
			'e.id, e.nom, e.abreviation, e.ecole_lyonnaise, i.token AS image '.
		'FROM ecoles AS e '.
		'LEFT JOIN images AS i ON '.
			'i.id = e.id_image AND '.
			'i._etat = "active" '.
		'WHERE '.
			(isset($data['ecole_lyonnaise']) ? 'e.ecole_lyonnaise = '.(!empty($data['ecole_lyonnaise']) ? 1 : 0). ' AND ' : '').
			(isset($data['nom']) ? 'e.nom LIKE "%'.secure($data['nom']).'%" AND ' : '').
			'e._etat = "active"')
		->fetchAll(PDO::FETCH_ASSOC);

	foreach ($ecoles as $k => $ecole)
		$ecoles[$k]['image'] = empty($ecole['image']) ? null : url('image/'.$ecole['image'], true, false);

	returnJson(0, ['ecoles' => $ecoles]);
}

function ecole_getEcole($data, $pdo) {
	if (empty($data['id']) ||
		!intval($data['id']) ||
		$data['id'] < 0)
		returnJson(101, ['message' => 'Ecole\'s id is invalid or not specified']);

	$ecole = $pdo->query('SELECT '.
			'e.nom, e.abreviation, e.ecole_lyonnaise, i.token AS image '.
		'FROM ecoles AS e '.
		'LEFT JOIN images AS i ON '.
			'i.id = e.id_image AND '.
			'i._etat = "active" '.
		'WHERE '.
			'e._etat = "active" AND '.
			'e.id = '.(int) $data['id'])
		->fetch(PDO::FETCH_ASSOC);

	if (empty($ecole))
		returnJson(102, ['message' => 'Ecole ID does not exist']);

	$ecole['image'] = empty($ecole['image']) ? null : url('image/'.$ecole['image'], true, false);
	returnJson(0, ['ecole' => $ecole]);
}

function ecole_getPoints($data, $pdo) {
	$ecoles = $pdo->query($r='SELECT '.
			'e.id, '.
			'e.nom, '.
			'CASE WHEN p.dd IS NULL THEN 0 ELSE p.dd END AS dd, '.
			'CASE WHEN p.pompom IS NULL THEN 0 ELSE p.pompom END AS pompom, '.
			'CASE WHEN p.fairplay IS NULL THEN 0 ELSE p.fairplay END AS fairplay, '.
			'0 AS sports '.
		'FROM ecoles AS e '.
		'LEFT JOIN points AS p ON '.
			'p.id_ecole = e.id AND '.
			'p._etat = "active" '.
		'WHERE '.
			'e._etat = "active"')
		->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);

	$concurrents = $pdo->query('SELECT '.
			'c.id, '.
			'eq.label, '.
			'p.prenom, '.
			'p.nom, '.
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


	$classements = $pdo->query('SELECT '.
			'p.* '.
		'FROM podiums AS p '.
		'WHERE '.
			'p._etat = "active"')
		->fetchAll(PDO::FETCH_ASSOC);


	foreach ($classements as $classement) {
		if (isset($concurrents[$classement['id_concurrent1']]) &&
			isset($ecoles[$concurrents[$classement['id_concurrent1']]['eid']]))
			$ecoles[$concurrents[$classement['id_concurrent1']]['eid']]['sports'] += $classement['coeff'] *
				(!$classement['ex_12'] ? 
					APP_POINTS_1ER :
					(!$classement['ex_23'] ? 
						(APP_POINTS_1ER + APP_POINTS_2E) / 2 : 
						(APP_POINTS_1ER + APP_POINTS_2E + APP_POINTS_3E) / ($classement['ex_3'] ? 4 : 3)));

		
		if (isset($concurrents[$classement['id_concurrent2']]) &&
			isset($ecoles[$concurrents[$classement['id_concurrent2']]['eid']]))
			$ecoles[$concurrents[$classement['id_concurrent2']]['eid']]['sports'] += $classement['coeff'] *
				(!$classement['ex_12'] ? 
					(!$classement['ex_23'] ? 
						APP_POINTS_2E : 
						(APP_POINTS_2E + APP_POINTS_3E) / ($classement['ex_3'] ? 3 : 2)) : 
					(!$classement['ex_23'] ? 
						(APP_POINTS_1ER + APP_POINTS_2E) / 2 : 
						(APP_POINTS_1ER + APP_POINTS_2E + APP_POINTS_3E) / ($classement['ex_3'] ? 4 : 3)));

		if (isset($concurrents[$classement['id_concurrent3']]) &&
			isset($ecoles[$concurrents[$classement['id_concurrent3']]['eid']]))
			$ecoles[$concurrents[$classement['id_concurrent3']]['eid']]['sports'] += $classement['coeff'] *
				(!$classement['ex_23'] ? 
						APP_POINTS_3E / ($classement['ex_3'] ? 2 : 1) : 
						(!$classement['ex_12'] ? 
							(APP_POINTS_2E + APP_POINTS_3E) / ($classement['ex_3'] ? 3 : 2) : 
							(APP_POINTS_1ER + APP_POINTS_2E + APP_POINTS_3E) / ($classement['ex_3'] ? 4 : 3)));

		if ($classement['ex_3'] && 
			isset($concurrents[$classement['id_concurrent3ex']]) &&
			isset($ecoles[$concurrents[$classement['id_concurrent3ex']]['eid']]))
			$ecoles[$concurrents[$classement['id_concurrent3ex']]['eid']]['sports'] += $classement['coeff'] * 
				(!$classement['ex_23'] ? 
					APP_POINTS_3E / 2 : 
					(!$classement['ex_12'] ? 
						(APP_POINTS_2E + APP_POINTS_3E) / 3 :
						(APP_POINTS_1ER + APP_POINTS_2E + APP_POINTS_3E) / 4));

	}

	foreach ($ecoles as $eid => $ecole) 
		$ecoles[$eid]['total'] = $ecole['dd'] + $ecole['fairplay'] + $ecole['pompom'] + $ecole['sports'];

	uasort($ecoles, function ($ecoleA, $ecoleB) {
		if ($ecoleA['total'] == $ecoleB['total']) return 0;
		return $ecoleA['total'] > $ecoleB['total'] ? -1 : 1;
	});

	$p = 0;
	$i = 0;
	$previous = -1;
	$prevKey = -1;
	$ecoles_ = [];
	foreach ($ecoles as $key => $ecole) {
		if (isset($ecoles[$prevKey]) && $ecoles[$prevKey]['total'] == $ecole['total']) {
			$ecoles[$prevKey]['exaequo'] = true;
			$ecoles[$key]['exaequo'] = true;
		}

		$prevKey = $key;
	}

	foreach ($ecoles as $eid => $ecole) {
		$isECL = preg_match('`centrale (.*)lyon`i', $ecole['nom']);

		if (!$isECL)
			$p++;

		if ($i == 0 || $previous > $ecole['total'])
			$i = $p;

		$previous = $ecole['total'];

		unset($ecole['nom']);
		$ecole['classement'] = $isECL ? null : $i;
		$ecole['exaequo'] = empty($ecole['exaequo']) ? 0 : 1;
		$ecoles_[] = array_merge(['id' => $eid], $ecole);
	} 

	returnJson(0, ['ecoles' => $ecoles_]);
}