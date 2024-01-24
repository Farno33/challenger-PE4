<?php
$ecoles = $pdo->query('SELECT ' .
        'e.id, ' .
        'p.dd, ' .
        'p.pompom, ' .
        'p.fairplay, ' .
        'e.nom, ' .
        '0 AS points, ' .
        '0 AS gold, ' .
        '0 AS argent, ' .
        '0 AS bronze ' .
    'FROM ecoles AS e ' .
        'LEFT JOIN points AS p ON ' .
            'p.id_ecole = e.id AND ' .
            'p._etat = "active" ' .
    'WHERE ' .
        'e._etat = "active" ' .
    'ORDER BY e.nom ASC')
    or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$ecoles = $ecoles->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);

$concurrents = $pdo->query('SELECT ' .
        'c.id, ' .
        'CASE WHEN esp.id IS NULL THEN e.id ELSE esp.id END AS eid ' .
    'FROM concurrents AS c ' .
        'LEFT JOIN (sportifs AS sp ' .
                'JOIN participants AS p ON ' .
                    'p.id = sp.id_participant AND ' .
                    'p._etat = "active" ' .
                'JOIN equipes AS eqsp ON ' .
                    'eqsp.id = sp.id_equipe AND ' .
                    'eqsp._etat = "active" ' .
                'JOIN ecoles_sports AS essp ON ' .
                    'essp.id = eqsp.id_ecole_sport AND ' .
                    'essp._etat = "active" ' .
                'JOIN ecoles AS esp ON ' .
                    'esp.id = essp.id_ecole AND ' .
                    'esp._etat = "active") ON ' .
            'sp.id = c.id_sportif AND ' .
            'sp._etat = "active" ' .
        'LEFT JOIN (equipes AS eq ' .
                'JOIN ecoles_sports AS es ON ' .
                    'es.id = eq.id_ecole_sport AND ' .
                    'es._etat = "active" ' .
                'JOIN ecoles AS e ON ' .
                    'e.id = es.id_ecole AND ' .
                    'e._etat = "active") ON ' .
            'eq.id = c.id_equipe AND ' .
            'eq._etat = "active" ' .
    'WHERE ' .
        'c._etat = "active"')
    ->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);


$classements = $pdo->query('SELECT ' .
        '*' .
    'FROM podiums ' .
    'WHERE ' .
        '_etat = "active" ')
    or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$classements = $classements->fetchAll(PDO::FETCH_ASSOC);


foreach ($classements as $classement) {
    if (
        isset($concurrents[$classement['id_concurrent1']]) &&
        isset($ecoles[$concurrents[$classement['id_concurrent1']]['eid']])
    ) {
        $ecoles[$concurrents[$classement['id_concurrent1']]['eid']]['points'] += $classement['coeff'] *
            (!$classement['ex_12'] ?
                APP_POINTS_1ER : (!$classement['ex_23'] ?
                    (APP_POINTS_1ER + APP_POINTS_2E) / 2 : (APP_POINTS_1ER + APP_POINTS_2E + APP_POINTS_3E) / ($classement['ex_3'] ? 4 : 3)));
        $ecoles[$concurrents[$classement['id_concurrent1']]['eid']]['gold'] += 1;
    }


    if (
        isset($concurrents[$classement['id_concurrent2']]) &&
        isset($ecoles[$concurrents[$classement['id_concurrent2']]['eid']])
    ) {
        $ecoles[$concurrents[$classement['id_concurrent2']]['eid']]['points'] += $classement['coeff'] *
            (!$classement['ex_12'] ?
                (!$classement['ex_23'] ?
                    APP_POINTS_2E : (APP_POINTS_2E + APP_POINTS_3E) / ($classement['ex_3'] ? 3 : 2)) : (!$classement['ex_23'] ?
                    (APP_POINTS_1ER + APP_POINTS_2E) / 2 : (APP_POINTS_1ER + APP_POINTS_2E + APP_POINTS_3E) / ($classement['ex_3'] ? 4 : 3)));
        $ecoles[$concurrents[$classement['id_concurrent2']]['eid']][$classement['ex_12'] ? 'gold' : 'argent'] += 1;
    }

    if (
        isset($concurrents[$classement['id_concurrent3']]) &&
        isset($ecoles[$concurrents[$classement['id_concurrent3']]['eid']])
    ) {
        $ecoles[$concurrents[$classement['id_concurrent3']]['eid']]['points'] += $classement['coeff'] *
            (!$classement['ex_23'] ?
                APP_POINTS_3E / ($classement['ex_3'] ? 2 : 1) : (!$classement['ex_12'] ?
                    (APP_POINTS_2E + APP_POINTS_3E) / ($classement['ex_3'] ? 3 : 2) : (APP_POINTS_1ER + APP_POINTS_2E + APP_POINTS_3E) / ($classement['ex_3'] ? 4 : 3)));
        $ecoles[$concurrents[$classement['id_concurrent3']]['eid']][$classement['ex_23'] ? 'argent' : 'bronze'] += 1;
    }
    if (
        $classement['ex_3'] &&
        isset($concurrents[$classement['id_concurrent3ex']]) &&
        isset($ecoles[$concurrents[$classement['id_concurrent3ex']]['eid']])
    ) {
        $ecoles[$concurrents[$classement['id_concurrent3ex']]['eid']]['points'] += $classement['coeff'] *
            (!$classement['ex_23'] ?
                APP_POINTS_3E / 2 : (!$classement['ex_12'] ?
                    (APP_POINTS_2E + APP_POINTS_3E) / 3 : (APP_POINTS_1ER + APP_POINTS_2E + APP_POINTS_3E) / 4));
        $ecoles[$concurrents[$classement['id_concurrent3ex']]['eid']]['bronze'] += 1;
    }
}

function promethus_unescape($str)
{
    return html_entity_decode($str . "");
}

header('Content-Type: text/plain; version=0.0.4; charset=utf-8');

echo '# HELP challenger_points Nombre de points par école et par type' . PHP_EOL;
echo '# TYPE challenger_points gauge' . PHP_EOL;
echo '# HELP challenger_medailles_gold Nombre de médailles par école et par type' . PHP_EOL;
echo '# TYPE challenger_medailles_gold gauge' . PHP_EOL;

foreach ($ecoles as $eid => $ecole) {
    if (!empty($ecole['dd']))        echo 'challenger_points{type="dd", ecole="' .        promethus_unescape($ecole['nom']) . '"} ' . $ecole['dd']        . PHP_EOL;
    if (!empty($ecole['fairplay']))  echo 'challenger_points{type="fairplay", ecole="' .  promethus_unescape($ecole['nom']) . '"} ' . $ecole['fairplay']  . PHP_EOL;
    if (!empty($ecole['pompom']))    echo 'challenger_points{type="pompom", ecole="' .    promethus_unescape($ecole['nom']) . '"} ' . $ecole['pompom']    . PHP_EOL;
    if (!empty($ecole['points']))    echo 'challenger_points{type="sport", ecole="' .     promethus_unescape($ecole['nom']) . '"} ' . $ecole['points']    . PHP_EOL;
    if (!empty($ecole['gold']))      echo 'challenger_medailles{type="gold", ecole="' .   promethus_unescape($ecole['nom']) . '"} ' . $ecole['gold']      . PHP_EOL;
    if (!empty($ecole['argent']))    echo 'challenger_medailles{type="argent", ecole="' . promethus_unescape($ecole['nom']) . '"} ' . $ecole['argent']    . PHP_EOL;
    if (!empty($ecole['bronze']))    echo 'challenger_medailles{type="bronze", ecole="' . promethus_unescape($ecole['nom']) . '"} ' . $ecole['bronze']    . PHP_EOL;
}
