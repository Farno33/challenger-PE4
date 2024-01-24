<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/admin/competition/action_capitaines_ecoles.php **/
/* Liste des capitaines par école **************************/
/* *********************************************************/
/* Dernière modification : le 18/12/14 *********************/
/* *********************************************************/



$capitaines = $pdo->query('SELECT '.
		'e.id AS id, '.
		'e.nom, '.
		'e.ecole_lyonnaise, '.
		's.sport, '.
		's.sexe AS ssexe, '.
		's.individuel AS sindividuel, '.
		's.id AS sid, '.
		'p.nom AS pnom, '.
		'p.prenom AS pprenom, '.
		'p.licence AS plicence, '.
		'p.sexe AS psexe, '.
		'p.telephone AS ptelephone, '.
		'p.id AS pid, '.
		'eq.label '.
	'FROM ecoles AS e '.
	'LEFT JOIN (ecoles_sports AS es  '.
		'JOIN equipes AS eq ON '.
			'eq.id_ecole_sport = es.id AND '.
			'eq._etat = "active" '.
		'JOIN participants AS p ON '.
			'p.id = eq.id_capitaine AND '.
			'p._etat = "active" '.
		'JOIN sports AS s ON '.
			's.id = es.id_sport AND '.
			's._etat = "active") ON '.
		'es.id_ecole = e.id AND '.
		'es._etat = "active" '.
	'WHERE '.
		'e._etat = "active" '.
	'ORDER BY '.
		'e.nom ASC, '.
		'p.nom ASC, '.
		'p.prenom ASC ')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$capitaines = $capitaines->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_GROUP);


foreach ($capitaines as $i => $groupe) {
	foreach ($groupe as $j => $capitaine) {	
		$capitaines[$i][$j]['sport_sexe'] = $capitaine['sport'].' '.strip_tags(printSexe($capitaine['ssexe']));
		$capitaines[$i][$j]['sport_individuel'] = $capitaine['sindividuel'] ? 'Oui' : 'Non';
	}
}


//Labels XLSX
$labels = [
	'Nom' => 'pnom',
	'Prénom' => 'pprenom',
	'Sexe' => 'psexe',
	'Sport' => 'sport_sexe',
	'Individuel' => 'sport_individuel',
	'Equipes' => 'label',
	'Licence' => 'plicence',
	'Téléphone' => 'ptelephone',
];


//Téléchargement du fichier XLSX concerné
if (!empty($_GET['excel']) &&
	intval($_GET['excel']) &&
	in_array($_GET['excel'], array_keys($capitaines))) {

	$titre = 'Liste des capitaines ('.unsecure($capitaines[$_GET['excel']][0]['nom']).')';
	$fichier = 'liste_capitaines_ecole_'.onlyLetters(utf8_decode(unsecure($capitaines[$_GET['excel']][0]['nom'])));
	$items = $capitaines[$_GET['excel']];
	
	exportXLSX($items, $fichier, $titre, $labels);

}

else if (isset($_GET['excel'])) {

	$fichier = 'liste_capitaines_ecoles';
	$items = $titres = $feuilles[] = [];
	
	$i = 0;
	foreach ($capitaines as $capitaines_ecole) {
		$titres[$i] = 'Liste des capitaines ('.unsecure($capitaines_ecole[0]['nom']).')';
		$feuilles[$i] = unsecure($capitaines_ecole[0]['nom']);

		foreach ($capitaines_ecole as $k => $capitaine) {
			if (empty($capitaine['pid']))
				unset($capitaines_ecole[$k]);
		}

		$items[$i] = $capitaines_ecole;
		$i++;
	}
	
	exportXLSXGroupe($items, $fichier, $feuilles, $titres, $labels);

}




//Inclusion du bon fichier de template
require DIR.'templates/admin/competition/capitaines_ecoles.php';
