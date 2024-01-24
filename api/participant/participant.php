<?php

$actions = [
	'listActions'		=> 'Return all actions available in general module',
    'getUserData'       => 'Return user\'s basic piece of data associated to key',
    'checkKey'          => 'Returns an error if key is invalid',
];


function participant_listActions() {
	global $actions;
	returnJson(0, ['actions' => $actions]);
}

function participant_getUserData($data, $pdo, $user) {
    if (empty($data['key']))
        returnJson(101, ['message' => 'Key is not specified']);

    $id_participant = substr($data['key'], 0, strpos($data['key'], '/'));

    if (!is_numeric($id_participant))
        returnJson(102, ['message' => 'Key is malformed']);

    $participant = $pdo->query('SELECT '.
            'p.prenom AS prenom, '.
            'p.id_ecole AS idEcole, '.
            's.id_equipe AS idEquipe, '.
            'es.id_sport AS idSport '.
        'FROM participants AS p '.
        'left join sportifs s on p.id = s.id_participant AND s._etat = "active" '.
        'left join equipes e on s.id_equipe = e.id AND e._etat = "active" '.
        'left join concurrents c on c._etat = "active" AND (c.id_equipe = e.id OR c.id_sportif = s.id) '.
        'left join ecoles_sports es on e.id_ecole_sport = es.id AND es._etat = "active" '.
        'WHERE p._etat = "active" '.
            'AND p.id = '.(int) $id_participant)
        ->fetch(PDO::FETCH_ASSOC);

    $check = $participant ? substr(sha1(APP_SEED.  
        $participant['idEcole'].'_'.
        $id_participant), 0, 20) : null;

    if ($check != substr($data['key'], strpos($data['key'], '/') + 1))
        returnJson(103, ['message' => 'Key is invalid']);

    
	returnJson(0, ['user' => $participant]);
}

function participant_checkKey($data, $pdo, $user) {
    if (empty($data['key']))
        returnJson(101, ['message' => 'Key is not specified']);

    $id_participant = substr($data['key'], 0, strpos($data['key'], '/'));

    if (!is_numeric($id_participant))
        returnJson(102, ['message' => 'Key is malformed']);

    $participant = $pdo->query('SELECT '.
            'p.id_ecole AS idEcole, '.
        'FROM participants AS p '.
        'WHERE p._etat = "active" '.
            'AND p.id = '.(int) $id_participant)
        ->fetch(PDO::FETCH_ASSOC);

    $check = substr(sha1(APP_SEED.  
        $participant['idEcole'].'_'.
        $id_participant), 0, 20);

    if ($check != substr($data['key'], strpos($data['key'], '/') + 1))
        returnJson(103, ['message' => 'Key is invalid']);

    
	returnJson(0, []);
}
