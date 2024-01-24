<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/admin/competition/action_cameramans_ecoles.php *****/
/* Liste des cameramans ***************************************/
/* *********************************************************/
/* Dernière modification : le 18/12/14 *********************/
/* *********************************************************/



$cameramans = $pdo->query('SELECT '.
		'e.id, '.
		'e.nom, '.
		'e.ecole_lyonnaise, '.
		'p.nom AS pnom, '.
		'p.prenom AS pprenom, '.
		'p.sexe AS psexe, '.
		'p.sportif AS psportif, '.
		'p.telephone AS ptelephone, '.
		'p.id AS pid, '.
		'q.valeur AS quota_cameraman '.
	'FROM ecoles AS e '.
	'LEFT JOIN participants AS p ON '.
		'p.cameraman = 1 AND '.
		'p.id_ecole = e.id AND '.
		'p._etat = "active" '.
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
$cameramans = $cameramans->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_GROUP);


foreach ($cameramans as $i => $groupe) {
	foreach ($groupe as $j => $cameraman) {
		$cameramans[$i][$j]['sportif'] = $cameraman['psportif'] ? 'Oui' : 'Non';	
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
	in_array($_GET['excel'], array_keys($cameramans))) {

	$titre = 'Liste des cameramans ('.unsecure($cameramans[$_GET['excel']][0]['nom']).')';
	$fichier = 'liste_cameramans_ecole_'.onlyLetters(utf8_decode(unsecure($cameramans[$_GET['excel']][0]['nom'])));
	$items = $cameramans[$_GET['excel']];

	exportXLSX($items, $fichier, $titre, $labels);

}

else if (isset($_GET['excel'])) {

	$fichier = 'liste_cameramans_ecoles';
	$items = $titres = $feuilles[] = [];
	
	$i = 0;
	foreach ($cameramans as $cameramans_ecole) {
		$titres[$i] = 'Liste des cameramans ('.unsecure($cameramans_ecole[0]['nom']).')';
		$feuilles[$i] = unsecure($cameramans_ecole[0]['nom']);

		foreach ($cameramans_ecole as $k => $cameraman) {
			if (empty($cameraman['pid']))
				unset($cameramans_ecole[$k]);
		}

		$items[$i] = $cameramans_ecole;
		$i++;
	}
	
	exportXLSXGroupe($items, $fichier, $feuilles, $titres, $labels);

}


//Inclusion du bon fichier de template
require DIR.'templates/admin/competition/cameramans_ecoles.php';
