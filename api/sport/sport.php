<?php

$actions = [
	'listActions'			=> 'Return all actions available in sport module',
	'getSports' 			=> 'Return list of sports with id',
	'getSport'				=> 'Return sport associated to an ID',
	'S_setSportInfo'		=> 'Set sport\'s infos string',
	'getEquipesSport' 		=> 'Return all equipes associated to a sport',
	'S_getSportifsSport' 	=> 'Return all sportifs associated to a sport (secured action)',
	'S_getCapitaines'		=> 'Return all capitaines (secured action)'
];


function sport_listActions()
{
	global $actions;
	returnJson(0, ['actions' => $actions]);
}


function sport_getSports($data, $pdo)
{
	$sports = $pdo->query('SELECT ' .
		'id, sport, sexe, individuel, infos ' .
		'FROM sports ' .
		'WHERE _etat = "active"')
		->fetchAll(PDO::FETCH_ASSOC);

	returnJson(0, ['sports' => $sports]);
}

function sport_getSport($data, $pdo, $user, $return = false)
{
	if (
		empty($data['id']) ||
		!intval($data['id']) ||
		$data['id'] < 0
	)
		returnJson(101, ['message' => 'Sport\'s id is invalid or not specified']);

	$sport = $pdo->query('SELECT ' .
		'sport, sexe, individuel, infos ' .
		'FROM sports ' .
		'WHERE ' .
		'_etat = "active" AND ' .
		'id = ' . (int) $data['id'])
		->fetch(PDO::FETCH_ASSOC);

	if (empty($sport))
		returnJson(102, ['message' => 'Sport ID does not exist']);

	if ($return)
		return $sport;

	returnJson(0, ['sport' => $sport]);
}

function sport_S_setSportInfo($data, $pdo, $user)
{
	if (
		empty($data['id']) ||
		!intval($data['id']) ||
		$data['id'] < 0
	)
		returnJson(101, ['message' => 'Sport\'s id is invalid or not specified']);

	$sport = $pdo->query('SELECT ' .
		'sport, sexe, individuel, infos ' .
		'FROM sports ' .
		'WHERE ' .
		'_etat = "active" AND ' .
		'id = ' . (int) $data['id'])
		->fetch(PDO::FETCH_ASSOC);

	if (empty($sport))
		returnJson(102, ['message' => 'Sport ID does not exist']);

	if (!isset($data['infos']))
		returnJson(103, ['message' => 'Infos are not specified']);

	$ref = pdoRevision('sports', $sport['id']);
	$pdo->prepare('UPDATE sports SET ' .
				'_auteur = :uid, ' .
				'_date = NOW(), ' .
				'_message = "Modification des informations", ' .
				'_ref = :ref, ' .
				//----------//
				'infos = :infos ' .
			'WHERE ' .
				'id = :id')
		->execute([
			':uid' => $user['id'],
			':ref' => $ref,
			':infos' => $data['infos'],
			':id' => $data['id']
		]);
	
	returnJson(0, ['message' => 'Sport\'s infos updated']);
}

function sport_getEquipesSport($data, $pdo, $user)
{
	$sport = sport_getSport($data, $pdo, $user, true);

	$equipes = $pdo->query('SELECT ' .
		'c.id, e.id AS id_ecole, e.nom, eq.label, count(s.id) as effectif ' .
		'FROM sportifs as s ' .
		'JOIN equipes AS eq  ON ' .
		'eq.id = s.id_equipe ' .
		'JOIN concurrents AS c ON ' .
		'c._etat = "active" AND ' .
		'(c.id_equipe = eq.id OR c.id_sportif = s.id) ' .
		'JOIN ecoles_sports AS es ON ' .
		'eq.id_ecole_sport = es.id AND ' .
		'es._etat = "active" ' .
		'JOIN ecoles AS e ON ' .
		'e.id = es.id_ecole AND ' .
		'e._etat = "active" ' .
		'WHERE ' .
		'eq._etat = "active" AND ' .
		'es.id_sport = ' . (int) $data['id'] . ' ' .
		'GROUP BY ' .
		's.id_equipe')
		->fetchAll(PDO::FETCH_ASSOC);

	returnJson(0, ['equipes' => $equipes]);
}

function sport_S_getSportifsSport($data, $pdo, $user)
{
	$sport = sport_getSport($data, $pdo, $user, true); // utilisé pour vérifier l'existence du sport demandé

	$sportifs = $pdo->query('SELECT ' .
		'p.id, p.nom, p.prenom, p.sexe ' .
		'FROM sportifs AS sp ' .
		'JOIN participants AS p ON ' .
		'p.id = sp.id_participant AND ' .
		'p._etat = "active" ' .
		'JOIN equipes AS eq ON ' .
		'eq.id = sp.id_equipe AND ' .
		'eq._etat = "active" ' .
		'JOIN ecoles_sports AS es ON ' .
		'eq.id_ecole_sport = es.id AND ' .
		'es._etat = "active" ' .
		'JOIN ecoles AS e ON ' .
		'e.id = es.id_ecole AND ' .
		'e._etat = "active" ' .
		'WHERE ' .
		'sp._etat = "active" AND ' .
		'es.id_sport = ' . (int) $data['id'])
		->fetchAll(PDO::FETCH_ASSOC);

	returnJson(0, ['sportifs' => $sportifs]);
}

function sport_S_getCapitaines($data, $pdo, $user)
{
	$capitaines = $pdo->query('SELECT ' .
		'p.id, p.nom, p.prenom, p.sexe ' .
		'FROM participants AS p ' .
		'JOIN equipes AS eq ON ' .
		'eq.id_capitaine = p.id AND ' .
		'eq._etat = "active" ' .
		'WHERE ' .
		'p._etat = "active"')
		->fetchAll(PDO::FETCH_ASSOC);

	returnJson(0, ['capitaines' => $capitaines]);
}
