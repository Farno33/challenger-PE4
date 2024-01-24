<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/admin/competition/action_sportifs_ecoles.php ****/
/* Liste des sportifs par école ****************************/
/* *********************************************************/
/* Dernière modification : le 18/12/14 *********************/
/* *********************************************************/



$sportifs = $pdo->query('SELECT '.
		'e.id, '.
		'e.nom, '.
		'e.ecole_lyonnaise, '.
		's.sport, '.
		's.sexe AS ssexe, '.
		's.id AS sid, '.
		'p.nom AS pnom, '.
		'p.prenom AS pprenom, '.
		'p.licence AS plicence, '.
		'p.sexe AS psexe, '.
		'p.telephone AS ptelephone, '.
		'p.email AS pemail, '.
		'p.id AS pid, '.
		'eq.id_capitaine, '.
		'eq.label, '.
		'q.valeur AS quota_sportif '.
	'FROM ecoles AS e '.
	'LEFT JOIN (ecoles_sports AS es '.
		'JOIN equipes AS eq ON '.
			'eq.id_ecole_sport = es.id AND '.
			'eq._etat = "active" '.
		'JOIN sportifs AS sp ON '.
			'sp.id_equipe = eq.id AND '.
			'sp._etat = "active" '.
		'JOIN sports AS s ON '.
			's.id = es.id_sport AND '.
			's._etat = "active" '.
		'JOIN participants AS p ON '.
			'p.id = sp.id_participant AND '.
			'p._etat = "active")  ON '.
		'es.id_ecole = e.id AND '.
		'es._etat = "active" '.
	'LEFT JOIN quotas_ecoles AS q ON '.
		'q.id_ecole = e.id AND '.
		'q.quota = "sportif" AND '.
		'q._etat = "active" '.
	'WHERE '.
		'e._etat = "active" '.
	'ORDER BY '.
		'e.nom ASC, '.
		'p.nom ASC, '.
		'p.prenom ASC ')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$sportifs = $sportifs->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_GROUP);


foreach ($sportifs as $i => $groupe) {
	foreach ($groupe as $j => $sportif) {
		$sportifs[$i][$j]['sport_sexe'] = $sportif['sport'].' '.strip_tags(printSexe($sportif['ssexe']));
		$sportifs[$i][$j]['capitaine'] = $sportif['id_capitaine'] == $sportif['pid'] ? 'Oui' : '';	
	}
}


//Labels pour le XLSX
$labels = [
	'Capitaine' => 'capitaine',
	'Nom' => 'pnom',
	'Prénom' => 'pprenom',
	'Sexe' => 'psexe',
	'Sport' => 'sport_sexe',
	'Equipe' => 'label',
	'Licence' => 'plicence',
	'Téléphone' => 'ptelephone',
	'Email' => 'pemail',
];


//Téléchargement du fichier XLSX concerné
if (!empty($_GET['excel']) &&
	intval($_GET['excel']) &&
	in_array($_GET['excel'], array_keys($sportifs))) {

	$titre = 'Liste des sportifs ('.unsecure($sportifs[$_GET['excel']][0]['nom']).')';
	$fichier = 'liste_sportifs_ecole_'.onlyLetters(utf8_decode(unsecure($sportifs[$_GET['excel']][0]['nom'])));
	$items = $sportifs[$_GET['excel']];
	exportXLSX($items, $fichier, $titre, $labels);

}


else if (isset($_GET['excel'])) {

	$fichier = 'liste_sportifs_ecoles';
	$items = $titres = $feuilles[] = [];
	
	$i = 0;
	foreach ($sportifs as $sportifs_ecole) {
		$titres[$i] = 'Liste des sportifs ('.unsecure($sportifs_ecole[0]['nom']).')';
		$feuilles[$i] = unsecure($sportifs_ecole[0]['nom']);

		foreach ($sportifs_ecole as $k => $sportif) {
			if (empty($sportif['pid']))
				unset($sportifs_ecole[$k]);
		}

		$items[$i] = $sportifs_ecole;
		$i++;
	}
	
	exportXLSXGroupe($items, $fichier, $feuilles, $titres, $labels);

}


//Inclusion du bon fichier de template
require DIR.'templates/admin/competition/sportifs_ecoles.php';
