<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/admin/competition/action_capitaines_sports.php **/
/* Liste des capitaines par sport **************************/
/* *********************************************************/
/* Dernière modification : le 18/12/14 *********************/
/* *********************************************************/



$capitaines = $pdo->query('SELECT '.
		's.id, '.
		'e.nom AS enom, '.
		'e.ecole_lyonnaise, '.
		's.sport, '.
		's.sexe, '.
		'e.id AS eid, '.
		'p.nom AS pnom, '.
		'p.prenom AS pprenom, '.
		'p.licence AS plicence, '.
		'p.sexe AS psexe, '.
		'p.telephone AS ptelephone, '.
		'p.id AS pid, '.
		'eq.label '.
	'FROM sports AS s '.
	'LEFT JOIN (ecoles_sports AS es '.
		'JOIN equipes AS eq ON '.
			'eq.id_ecole_sport = es.id AND '.
			'eq._etat = "active" '.
		'JOIN participants AS p ON '.
			'p.id = eq.id_capitaine AND '.
			'p._etat = "active" '.
		'JOIN ecoles AS e ON '.
			'e.id = es.id_ecole AND '.
			'e._etat = "active") ON '.
		'es.id_sport = s.id AND '.
		'es._etat = "active" '.
	'WHERE '.
		's._etat = "active" '.
	'ORDER BY '.
		's.sport ASC, '.
		'p.nom ASC, '.
		'p.prenom ASC ')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$capitaines = $capitaines->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_GROUP);


//Labels XLSX
$labels = [
	'Nom' => 'pnom',
	'Prénom' => 'pprenom',
	'Sexe' => 'psexe',
	'Ecole' => 'enom',
	'Equipe' => 'label',
	'Licence' => 'plicence',
	'Téléphone' => 'ptelephone',
];


//Téléchargement du fichier XLSX concerné
if (!empty($_GET['excel']) &&
	intval($_GET['excel']) &&
	in_array($_GET['excel'], array_keys($capitaines))) {

	$titre = 'Liste des capitaines ('.unsecure($capitaines[$_GET['excel']][0]['sport'].' '.strip_tags(printSexe($capitaines[$_GET['excel']][0]['sexe']))).')';
	$fichier = 'liste_capitaines_sport_'.onlyLetters(utf8_decode(unsecure($capitaines[$_GET['excel']][0]['sport']))).
		'_'.strip_tags(printSexe($capitaines[$_GET['excel']][0]['sexe']));
	$items = $capitaines[$_GET['excel']];
	exportXLSX($items, $fichier, $titre, $labels);

}

else if (isset($_GET['excel'])) {

	$fichier = 'liste_capitaines_sports';
	$items = $titres = $feuilles[] = [];
	
	$i = 0;
	foreach ($capitaines as $capitaines_sport) {
		$titres[$i] = 'Liste des capitaines ('.unsecure($capitaines_sport[0]['sport'].' '.strip_tags(printSexe($capitaines_sport[0]['sexe']))).')';
		$feuilles[$i] = unsecure($capitaines_sport[0]['sport'].' '.strip_tags(printSexe($capitaines_sport[0]['sexe'])));

		foreach ($capitaines_sport as $k => $capitaine) {
			if (empty($capitaine['pid']))
				unset($capitaines_sport[$k]);
		}

		$items[$i] = $capitaines_sport;
		$i++;
	}
	
	exportXLSXGroupe($items, $fichier, $feuilles, $titres, $labels);

}


//Inclusion du bon fichier de template
require DIR.'templates/admin/competition/capitaines_sports.php';
