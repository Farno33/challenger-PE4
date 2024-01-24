<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/admin/competition/action_pompoms_ecoles.php *****/
/* Liste des pompoms ***************************************/
/* *********************************************************/
/* Dernière modification : le 18/12/14 *********************/
/* *********************************************************/



$pompoms = $pdo->query('SELECT '.
		'e.id, '.
		'e.nom, '.
		'e.ecole_lyonnaise, '.
		'p.nom AS pnom, '.
		'p.prenom AS pprenom, '.
		'p.sexe AS psexe, '.
		'p.sportif AS psportif, '.
		'p.telephone AS ptelephone, '.
		'p.id AS pid, '.
		'q.valeur AS quota_pompom '.
	'FROM ecoles AS e '.
	'LEFT JOIN participants AS p ON '.
		'p.pompom = 1 AND '.
		'p.id_ecole = e.id AND '.
		'p._etat = "active" '.
	'LEFT JOIN quotas_ecoles AS q ON '.
		'q.id_ecole = e.id AND '.
		'q.quota = "pompom" AND '.
		'q._etat = "active" '.
	'WHERE '.
		'e._etat = "active" '.
	'ORDER BY '.
		'e.nom ASC, '.
		'p.nom ASC, '.
		'p.prenom ASC ')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$pompoms = $pompoms->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_GROUP);


foreach ($pompoms as $i => $groupe) {
	foreach ($groupe as $j => $pompom) {
		$pompoms[$i][$j]['sportif'] = $pompom['psportif'] ? 'Oui' : 'Non';	
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
	in_array($_GET['excel'], array_keys($pompoms))) {

	$titre = 'Liste des pompoms ('.unsecure($pompoms[$_GET['excel']][0]['nom']).')';
	$fichier = 'liste_pompoms_ecole_'.onlyLetters(utf8_decode(unsecure($pompoms[$_GET['excel']][0]['nom'])));
	$items = $pompoms[$_GET['excel']];

	exportXLSX($items, $fichier, $titre, $labels);

}

else if (isset($_GET['excel'])) {

	$fichier = 'liste_pompoms_ecoles';
	$items = $titres = $feuilles[] = [];
	
	$i = 0;
	foreach ($pompoms as $pompoms_ecole) {
		$titres[$i] = 'Liste des pompoms ('.unsecure($pompoms_ecole[0]['nom']).')';
		$feuilles[$i] = unsecure($pompoms_ecole[0]['nom']);

		foreach ($pompoms_ecole as $k => $pompom) {
			if (empty($pompom['pid']))
				unset($pompoms_ecole[$k]);
		}

		$items[$i] = $pompoms_ecole;
		$i++;
	}
	
	exportXLSXGroupe($items, $fichier, $feuilles, $titres, $labels);

}


//Inclusion du bon fichier de template
require DIR.'templates/admin/competition/pompoms_ecoles.php';
