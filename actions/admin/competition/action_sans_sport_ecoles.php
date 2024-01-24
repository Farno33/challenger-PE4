<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/admin/competition/action_sans_sport_ecoles.php **/
/* Liste des sportifs sans sport groupés par école *********/
/* *********************************************************/
/* Dernière modification : le 20/01/15 *********************/
/* *********************************************************/


$sans_sport = $pdo->query('SELECT '.
		'e.id AS eid, '.
		'p.id, '.
		'p.nom, '.
		'p.prenom, '.
		'p.sexe, '.
		'p.telephone, '.
		'p.licence, '.
		'e.nom AS enom '.
	'FROM ecoles AS e '.
	'LEFT JOIN participants AS p ON '.
		'e.id = p.id_ecole AND '.
		'p.sportif = 1 AND '.
		'p._etat = "active" AND '.
		'p.id NOT IN (SELECT '.
				'pp.id '.
			'FROM participants AS pp '.
			'JOIN sportifs AS sp ON '.
				'sp.id_participant = pp.id AND '.
				'sp._etat = "active" '.
			'JOIN equipes AS eq ON '.
				'eq.id = sp.id_equipe AND '.
				'eq._etat = "active" '.
			'JOIN ecoles_sports AS es ON '.
				'es.id = eq.id_ecole_sport AND '.
				'es._etat = "active" '.
			'JOIN sports AS s ON '.
				's.id = es.id_sport AND '.
				's._etat = "active" '.
			'WHERE '.
				'pp._etat = "active" AND '.
				'es.id_ecole = e.id) '.
	'WHERE '.
		'e._etat = "active" '.
	'ORDER BY '.
		'e.nom ASC, '.
		'p.nom ASC, '.
		'p.prenom ASC')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$sans_sport = $sans_sport->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_GROUP);


//Labels pour le XLSX
$labels = [
	'Nom' => 'nom',
	'Prénom' => 'prenom',
	'Sexe' => 'sexe',
	'Licence' => 'licence',
	'Téléphone' => 'telephone',
];


//Téléchargement du fichier XLSX concerné
if (!empty($_GET['excel']) &&
	intval($_GET['excel']) &&
	in_array($_GET['excel'], array_keys($sans_sport))) {

	$titre = 'Liste des sportifs sans sport ('.unsecure($sans_sport[$_GET['excel']][0]['enom']).')';
	$fichier = 'liste_sans_sport_ecole_'.onlyLetters(utf8_decode(unsecure($sans_sport[$_GET['excel']][0]['enom'])));
	$items = $sans_sport[$_GET['excel']];
	exportXLSX($items, $fichier, $titre, $labels);

}


else if (isset($_GET['excel'])) {

	$fichier = 'liste_sans_sport_ecoles';
	$items = $titres = $feuilles[] = [];
	
	$i = 0;
	foreach ($sans_sport as $sans_sport_ecole) {
		$titres[$i] = 'Liste des sportifs sans sport ('.unsecure($sans_sport_ecole[0]['enom']).')';
		$feuilles[$i] = unsecure($sans_sport_ecole[0]['enom']);

		foreach ($sans_sport_ecole as $k => $sans_sport) {
			if (empty($sans_sport['pid']))
				unset($sans_sport_ecole[$k]);
		}

		$items[$i] = $sans_sport_ecole;
		$i++;
	}
	
	exportXLSXGroupe($items, $fichier, $feuilles, $titres, $labels);

}


//Inclusion du bon fichier de template
require DIR.'templates/admin/competition/sans_sport_ecoles.php';
