<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/admin/competition/action_fanfarons_ecoles.php ***/
/* Liste des fanfarons *************************************/
/* *********************************************************/
/* Dernière modification : le 18/12/14 *********************/
/* *********************************************************/



$fanfarons = $pdo->query('SELECT '.
		'e.id, '.
		'e.nom, '.
		'e.ecole_lyonnaise, '.
		'p.nom AS pnom, '.
		'p.prenom AS pprenom, '.
		'p.sexe AS psexe, '.
		'p.sportif AS psportif, '.
		'p.telephone AS ptelephone, '.
		'p.id AS pid, '.
		'q.valeur AS quota_fanfaron '.
	'FROM ecoles AS e '.
	'LEFT JOIN participants AS p ON '.
		'p.fanfaron = 1 AND '.
		'p.id_ecole = e.id AND '.
		'p._etat = "active " '.
	'LEFT JOIN quotas_ecoles AS q ON '.
		'q.id_ecole = e.id AND '.
		'q.quota = "cameraman" AND '.
		'q._etat = "active" '.
	'WHERE '.
		'e._etat = "active" '.
	'ORDER BY '.
		'e.nom ASC, '.
		'p.nom ASC, '.
		'p.prenom ASC ')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$fanfarons = $fanfarons->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_GROUP);


foreach ($fanfarons as $i => $groupe) {
	foreach ($groupe as $j => $fanfaron) {
		$fanfarons[$i][$j]['sportif'] = $fanfaron['psportif'] ? 'Oui' : 'Non';	
	}
}


//Labels pour le XLSX
$labels = [
	'Nom' => 'pnom',
	'Prénom' => 'pprenom',
	'Sexe' => 'psexe',
	'Sportif' => 'sportif',
	'Téléphone' => 'ptelephone',
];

//Téléchargement du fichier XLSX concerné
if (!empty($_GET['excel']) &&
	intval($_GET['excel']) &&
	in_array($_GET['excel'], array_keys($fanfarons))) {

	$titre = 'Liste des fanfarons ('.unsecure($fanfarons[$_GET['excel']][0]['nom']).')';
	$fichier = 'liste_fanfarons_ecole_'.onlyLetters(utf8_decode(unsecure($fanfarons[$_GET['excel']][0]['nom'])));
	$items = $fanfarons[$_GET['excel']];
	exportXLSX($items, $fichier, $titre, $labels);

}

else if (isset($_GET['excel'])) {

	$fichier = 'liste_fanfarons_ecoles';
	$items = $titres = $feuilles[] = [];
	
	$i = 0;
	foreach ($fanfarons as $fanfarons_ecole) {
		$titres[$i] = 'Liste des fanfarons ('.unsecure($fanfarons_ecole[0]['nom']).')';
		$feuilles[$i] = unsecure($fanfarons_ecole[0]['nom']);

		foreach ($fanfarons_ecole as $k => $fanfaron) {
			if (empty($fanfaron['pid']))
				unset($fanfarons_ecole[$k]);
		}

		$items[$i] = $fanfarons_ecole;
		$i++;
	}
	
	exportXLSXGroupe($items, $fichier, $feuilles, $titres, $labels);

}


//Inclusion du bon fichier de template
require DIR.'templates/admin/competition/fanfarons_ecoles.php';
