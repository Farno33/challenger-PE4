<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/public/classement.php ***************************/
/* Tableau des classements *********************************/
/* *********************************************************/
/* Dernière modification : le 17/02/15 *********************/
/* *********************************************************/

$ecoles = $pdo->query('SELECT '.
		'e.id, '.
		'p.dd, '.
		'p.pompom, '.
		'p.fairplay, '.
		'e.nom, '.
		'0 AS points, '.
		'(SELECT COUNT(eq.id) '.
			'FROM equipes AS eq '.
			'JOIN ecoles_sports AS es ON '.
				'es.id = eq.id_ecole_sport AND '.
				'es._etat = "active" '.
			'WHERE '.
				'es.id_ecole = e.id AND '.
				'es._etat = "active") AS equipes '.
	'FROM ecoles AS e '.
	'LEFT JOIN points AS p ON '.
		'p.id_ecole = e.id AND '.
		'p._etat = "active" '.
	'WHERE '.
		'e._etat = "active" '.
	'ORDER BY e.nom ASC')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$ecoles = $ecoles->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);

$sports = $pdo->query('SELECT '.
		's.id, '.
		'(SELECT COUNT(es.id_ecole) '.
			'FROM ecoles_sports AS es '.
			'WHERE '.
				'es.id_sport = s.id AND '.
				'es._etat = "active") AS nb_equipes, '.
		'(SELECT COUNT(sp.id_participant) '.
			'FROM sportifs AS sp '.
			'JOIN equipes AS eq ON '.
				'eq.id = sp.id_equipe AND '.
				'eq._etat = "active" '.
			'JOIN ecoles_sports AS es ON '.
				'es.id = eq.id_ecole_sport AND '.
				'es._etat = "active" '.
			'WHERE '.
				'sp.id_equipe = eq.id AND '.
				'sp._etat = "active") AS nb_sportifs, '.
		's.sport, '.
		's.sexe '.
	'FROM sports AS s '.
	'WHERE '.
		's._etat = "active" '.
	'ORDER BY '.
		'sport ASC, '.
		'sexe ASC')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$sports = $sports->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);


$concurrents = $pdo->query('SELECT '.
		'c.id, '.
		'eq.label, '.
		'p.prenom, '.
		'p.nom, '.
		'CASE WHEN esp.id IS NULL THEN e.id ELSE esp.id END AS eid '.
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
			'essp._etat = "active" '.
		'JOIN ecoles AS esp ON '.
			'esp.id = essp.id_ecole AND '.
			'esp._etat = "active") ON '.
		'sp.id = c.id_sportif AND '.
		'sp._etat = "active" '.
	'LEFT JOIN (equipes AS eq '.
		'JOIN ecoles_sports AS es ON '.
			'es.id = eq.id_ecole_sport AND '.
			'es._etat = "active" '.
		'JOIN ecoles AS e ON '.
			'e.id = es.id_ecole AND '.
			'e._etat = "active") ON '.
		'eq.id = c.id_equipe AND '.
		'eq._etat = "active" '.
	'WHERE '.
		'c._etat = "active"') 
	->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);


$classements = $pdo->query('SELECT '.
		'p.*, '.
		's.sport, '.
		's.individuel, '.
		's.sexe '.
	'FROM sports AS s '.
	'JOIN podiums AS p ON '.
		's.id = p.id_sport AND '.
		'p._etat = "active" '.
	'WHERE '.
		's._etat = "active" '.
	'ORDER BY '.
		's.individuel DESC, '.
		's.sport ASC, '.
		's.sexe ASC')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$classements = $classements->fetchAll(PDO::FETCH_ASSOC);


foreach ($classements as $classement) {
	if (isset($concurrents[$classement['id_concurrent1']]) &&
		isset($ecoles[$concurrents[$classement['id_concurrent1']]['eid']]))
		$ecoles[$concurrents[$classement['id_concurrent1']]['eid']]['points'] += $classement['coeff'] *
			(!$classement['ex_12'] ? 
				APP_POINTS_1ER :
				(!$classement['ex_23'] ? 
					(APP_POINTS_1ER + APP_POINTS_2E) / 2 : 
					(APP_POINTS_1ER + APP_POINTS_2E + APP_POINTS_3E) / ($classement['ex_3'] ? 4 : 3)));

	
	if (isset($concurrents[$classement['id_concurrent2']]) &&
		isset($ecoles[$concurrents[$classement['id_concurrent2']]['eid']]))
		$ecoles[$concurrents[$classement['id_concurrent2']]['eid']]['points'] += $classement['coeff'] *
			(!$classement['ex_12'] ? 
				(!$classement['ex_23'] ? 
					APP_POINTS_2E : 
					(APP_POINTS_2E + APP_POINTS_3E) / ($classement['ex_3'] ? 3 : 2)) : 
				(!$classement['ex_23'] ? 
					(APP_POINTS_1ER + APP_POINTS_2E) / 2 : 
					(APP_POINTS_1ER + APP_POINTS_2E + APP_POINTS_3E) / ($classement['ex_3'] ? 4 : 3)));

	if (isset($concurrents[$classement['id_concurrent3']]) &&
		isset($ecoles[$concurrents[$classement['id_concurrent3']]['eid']]))
		$ecoles[$concurrents[$classement['id_concurrent3']]['eid']]['points'] += $classement['coeff'] *
			(!$classement['ex_23'] ? 
					APP_POINTS_3E / ($classement['ex_3'] ? 2 : 1) : 
					(!$classement['ex_12'] ? 
						(APP_POINTS_2E + APP_POINTS_3E) / ($classement['ex_3'] ? 3 : 2) : 
						(APP_POINTS_1ER + APP_POINTS_2E + APP_POINTS_3E) / ($classement['ex_3'] ? 4 : 3)));

	if ($classement['ex_3'] && 
		isset($concurrents[$classement['id_concurrent3ex']]) &&
		isset($ecoles[$concurrents[$classement['id_concurrent3ex']]['eid']]))
		$ecoles[$concurrents[$classement['id_concurrent3ex']]['eid']]['points'] += $classement['coeff'] * 
			(!$classement['ex_23'] ? 
				APP_POINTS_3E / 2 : 
				(!$classement['ex_12'] ? 
					(APP_POINTS_2E + APP_POINTS_3E) / 3 :
					(APP_POINTS_1ER + APP_POINTS_2E + APP_POINTS_3E) / 4));

}

foreach ($ecoles as $eid => $ecole) 
	$ecoles[$eid]['total'] = $ecole['dd'] + $ecole['fairplay'] + $ecole['pompom'] + $ecole['points'];


function triPoints ($ecoleA, $ecoleB) {
	if ($ecoleA['total'] == $ecoleB['total']) return strcasecmp($ecoleA['nom'], $ecoleB['nom']);
	return $ecoleA['total'] > $ecoleB['total'] ? -1 : 1;
}

uasort($ecoles, 'triPoints');

//Inclusion du bon fichier de template
require DIR.'templates/public/classement.php';
