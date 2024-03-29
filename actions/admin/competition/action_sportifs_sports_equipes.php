<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/admin/competition/
/*                    action_sportifs_sports_groupes.php ***/
/* Liste des sportifs par sports groupés par école *********/
/* *********************************************************/
/* Dernière modification : le 18/12/14 *********************/
/* *********************************************************/



$sportifs = $pdo->query('SELECT '.
		's.id, '.
		's.sport, '.
		's.sexe, '.
		's.quota_inscription, '.
		'p.nom AS pnom, '.
		'p.prenom AS pprenom, '.
		'p.licence AS plicence, '.
		'p.sexe AS psexe, '.
		'p.telephone AS ptelephone, '.
		'p.email AS pemail, '.
		'p.id AS pid, '.
		'eq.id_capitaine, '.
		'eq.label, '.
		'eq.id AS eqid, '.
		'e.nom AS enom, '.
		'e.id AS eid '.
	'FROM sports AS s '.
	'LEFT JOIN (ecoles_sports AS es '.
		'JOIN equipes AS eq ON '.
			'eq.id_ecole_sport = es.id AND '.
			'eq._etat = "active" '.
		'JOIN sportifs AS sp ON '.
			'sp.id_equipe = eq.id AND '.
			'sp._etat = "active" '.
		'JOIN participants AS p ON '.
			'p.id = sp.id_participant AND '.
			'p._etat = "active" '.
		'JOIN ecoles AS e ON '.
			'e.id = es.id_ecole AND '.
			'e._etat = "active") ON '.
		'es.id_sport = s.id AND '.
		'es._etat ="active" '.
	'WHERE '.
		's._etat = "active" '.
	'ORDER BY '.
		's.sport ASC, '.
		's.sexe ASC, '.
		'e.nom ASC, '.
		'e.id ASC, '.
		'eq.label ASC, '.
		'eq.id ASC, '.
		'p.nom ASC, '.
		'p.prenom ASC ')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$sportifs = $sportifs->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_GROUP);


foreach ($sportifs as $i => $groupe) {
	foreach ($groupe as $j => $sportif) {
		$sportifs[$i][$j]['capitaine'] = $sportif['id_capitaine'] == $sportif['pid'] ? 'Oui' : '';	
	}
}


//Labels pour le XLSX
$labels = [
	'Ecole' => 'enom',
	'Equipe' => 'label',
	'Capitaine' => 'capitaine',
	'Nom' => 'pnom',
	'Prénom' => 'pprenom',
	'Sexe' => 'psexe',
	'Licence' => 'plicence',
	'Téléphone' => 'ptelephone',
	'Email' => 'pemail',
];


//Téléchargement du fichier XLSX concerné
if (!empty($_GET['excel']) &&
	intval($_GET['excel']) &&
	in_array($_GET['excel'], array_keys($sportifs))) {

	$titre = 'Liste des sportifs ('.unsecure($sportifs[$_GET['excel']][0]['sport'].' '.strip_tags(printSexe($sportifs[$_GET['excel']][0]['sexe']))).')';
	$fichier = 'liste_sportifs_sport_'.onlyLetters(utf8_decode(unsecure($sportifs[$_GET['excel']][0]['sport']))).
		'_'.strip_tags(printSexe($sportifs[$_GET['excel']][0]['sexe'])).'_equipes';
	$items = $sportifs[$_GET['excel']];

	exportXLSX($items, $fichier, $titre, $labels);

}


else if (isset($_GET['excel'])) {

	$fichier = 'liste_sportifs_sports_equipes';
	$items = $titres = $feuilles[] = [];
	
	$i = 0;
	foreach ($sportifs as $sportifs_sport) {
		$titres[$i] = 'Liste des sportifs ('.unsecure($sportifs_sport[0]['sport'].' '.strip_tags(printSexe($sportifs_sport[0]['sexe']))).')';
		$feuilles[$i] = unsecure($sportifs_sport[0]['sport'].' '.strip_tags(printSexe($sportifs_sport[0]['sexe'])));

		foreach ($sportifs_sport as $k => $sportif) {
			if (empty($sportif['pid']))
				unset($sportifs_sport[$k]);
		}

		$items[$i] = $sportifs_sport;
		$i++;
	}
	
	exportXLSXGroupe($items, $fichier, $feuilles, $titres, $labels);

}



//Inclusion du bon fichier de template
require DIR.'templates/admin/competition/sportifs_sports_equipes.php';
