<?php

$actions = [
    'listActions'        => 'Return all actions available in general module',
    'getUserData'       => 'Return user\'s basic piece of data associated to key',
    'getRights'          => 'Returns a simple object with user\'s rights',
];


function centralien_listActions()
{
    global $actions;
    returnJson(0, ['actions' => $actions]);
}

function centralien_getUserData($data, $pdo, $user)
{
    if (empty($data['id']))
        returnJson(101, ['message' => 'Id is not specified']);

    $id = $data['id'];

    $centralien = $pdo->prepare('SELECT u.prenom, u.responsable, c.vptournoi AS vp, co.id AS id_equipe, es.id_sport, es.id_ecole ' .
        'FROM utilisateurs as u ' .
        'LEFT JOIN centraliens c ON u.id = c.id_utilisateur AND c._etat = "active" ' .
        'LEFT JOIN participants p on p.id = c.id AND p._etat = "active" ' .
        'LEFT JOIN sportifs s on p.id = s.id_participant AND s._etat = "active" ' .
        'LEFT JOIN equipes e on s.id_equipe = e.id AND e._etat = "active" ' .
        'LEFT JOIN concurrents co on co._etat = "active" AND (co.id_equipe = e.id OR co.id_sportif = s.id) '.
        'LEFT JOIN ecoles_sports es on e.id_ecole_sport = es.id AND es._etat = "active" ' .
        'WHERE u._etat = "active" AND u.login = ?');
    $centralien->execute([$id]);
    $centralien = $centralien->fetch(PDO::FETCH_ASSOC);

    if (empty($centralien))
        returnJson(102, ['message' => 'User not found']);

    returnJson(0, ['user' => $centralien]);
}

function centralien_getRights($data, $pdo, $user)
{
    if (empty($data['id']))
        returnJson(101, ['message' => 'Id is not specified']);

    $id = $data['id'];

    $centralien = $pdo->prepare('SELECT u.nom, u.responsable, c.vptournoi AS vp ' .
        'FROM utilisateurs as u ' .
        'LEFT JOIN centraliens c ON u.id = c.id_utilisateur AND c._etat = "active" ' .
        'WHERE u.login = ? AND u._etat = "active"');
    $centralien->execute([$id]);
    $centralien = $centralien->fetch(PDO::FETCH_ASSOC);

    if (empty($centralien))
        returnJson(102, ['message' => 'User not found']);

    returnJson(0, ['user' => $centralien]);
}

// Fonction qui pourrait être utile pour les droits sur les matchs alternatif à l'id match defini dans tounroi
// function centralien_getRightsOnMatch($data, $pdo, $user){
//     if (empty($data['cid']))
//         returnJson(101, ['message' => 'CentralienID is not specified']);

//     if (empty($data['mid']))
//         returnJson(101, ['message' => 'MatchID is not specified']);

//     $cid = $data['cid'];
//     $mid = $data['mid'];

//     $authorized = $pdo->query('SELECT count(*) '.
//     'FROM centraliens '.
//             'Join sports on centraliens.vptournoi = sports.id AND sports._etat = "active" '.
//             'JOIN phases on sports.id = phases.id_sport AND phases._etat = "active" '.
//             'JOIN matchs on phases.id = matchs.id_phase AND matchs._etat = "active" '.
//     'WHERE centraliens._etat = "active" '.
//         'AND matchs.id = ? '.
//         'AND centraliens.id = ?;');
    
//     $authorized->execute([$mid, $cid]);
//     $authorized = $authorized->fetch(PDO::FETCH_ASSOC); // 0 ou 1

//     returnJson(0, ['authorized' => $authorized]);
// }