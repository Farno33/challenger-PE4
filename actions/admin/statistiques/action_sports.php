<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/admin/statistiques/action_sports.php ************/
/* Liste des Sports ****************************************/
/* *********************************************************/
/* Dernière modification : le 23/01/15 *********************/
/* *********************************************************/


$equipes = $pdo->query('SELECT '.
		'e.id, '.
		'e.nom, '.
		'ecole_lyonnaise, '.
		'COUNT(sp.id_participant) AS spnb, '.
		'es.id_sport '.
	'FROM ecoles AS e '.
	'LEFT JOIN (ecoles_sports AS es '.
		'JOIN equipes AS eq ON '.
			'eq.id_ecole_sport = es.id AND '.
			'eq._etat = "active" '.
		'LEFT JOIN sportifs AS sp ON '.
			'sp.id_equipe = eq.id AND '.
			'sp._etat = "active") ON '.
		'es.id_ecole = e.id AND '.
		'es._etat = "active" '.
	'WHERE '.
		'e._etat = "active" '.
	'GROUP BY '.
		'eq.id '.
	'ORDER BY '.
		'e.nom ASC')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$equipes = $equipes->fetchAll(PDO::FETCH_ASSOC);


$sports = $pdo->query('SELECT '.
		'id, '.
		'individuel, '.
		'sport, '.
		'sexe '.
	'FROM sports '.
	'WHERE _etat = "active" '.
	'ORDER BY '.
		'individuel ASC, '.
		'sport ASC, '.
		'sexe ASC')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$sports = $sports->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);


$ecoles = [];
foreach ($equipes as $equipe) {
	if (!isset($ecoles[$equipe['id']]))
		$ecoles[$equipe['id']] = array_merge($equipe, array('sports' => []));

	if (!isset($ecoles[$equipe['id']]['sports'][$equipe['id_sport']]))
		$ecoles[$equipe['id']]['sports'][$equipe['id_sport']] = [];

	$ecoles[$equipe['id']]['sports'][$equipe['id_sport']][] = $equipe;
}


//Téléchargement du fichier XLSX concerné
if (isset($_GET['excel'])) {

	$titre = 'Statistiques sur les sports';
	$fichier = 'stats_sports';
	$labels = ['Nom' => 'nom'];

	foreach ($sports as $s => $sport) {
		$sports[$s]['count_equipes'] = 0;
		$sports[$s]['count_sportifs'] = 0;
	}

	$totalEquipes = 0;
	$totalSportifs = 0;
	foreach ($ecoles as $i => $ecole) {
		$ecoles[$i]['count_sportifs'] = 0;
		$ecoles[$i]['count_equipes'] = 0;

		foreach ($sports as $s => $sport) {
			$eqs = empty($ecoles[$i]['sports'][$s]) ? [] : $ecoles[$i]['sports'][$s];
			$ecoles[$i]['count_equipes_'.$s] = count($eqs);
			$ecoles[$i]['count_sportifs_'.$s] = [];
			
			if (!$sport['individuel']) {
				$totalEquipes += count($eqs);
				$ecoles[$i]['count_equipes'] += count($eqs);
				$sports[$s]['count_equipes'] += count($eqs);
			}

			foreach ($eqs as $eq) {
				$ecoles[$i]['count_sportifs'] += $eq['spnb'];
				$sports[$s]['count_sportifs'] += $eq['spnb'];
				$ecoles[$i]['count_sportifs_'.$s][] = $eq['spnb'];
				$totalSportifs += $eq['spnb'];
			}

			$ecoles[$i]['count_sportifs_'.$s] = implode(" - ", $ecoles[$i]['count_sportifs_'.$s]);
		}
	}

	$ecoles['total_equipes'] = ['nom' => 'TOTAUX équipes'];
	$ecoles['total_sportifs'] = ['nom' => 'TOTAUX sportifs'];

	foreach ($sports as $s => $sport) {
		$labels[strip_tags($sport['sport'].' '.printSexe($sport['sexe']))] = 'count_sportifs_'.$s;
		$ecoles['total_equipes']['count_sportifs_'.$s] = $sport['individuel'] ? '' : $sport['count_equipes'];
		$ecoles['total_sportifs']['count_sportifs_'.$s] = $sport['count_sportifs'];
	}

	$labels['TOTAUX équipes'] = 'count_equipes';
	$labels['TOTAUX sportifs'] = 'count_sportifs';
	$ecoles['total_equipes']['count_equipes'] = $totalEquipes;
	$ecoles['total_sportifs']['count_sportifs'] = $totalSportifs;
	$ecoles['total_sportifs']['count_equipes'] = $ecoles['total_equipes']['count_sportifs'] = '';

	$items = $ecoles;
	exportXLSX($items, $fichier, $titre, $labels);

}

//Inclusion du bon fichier de template
require DIR.'templates/admin/statistiques/sports.php';
