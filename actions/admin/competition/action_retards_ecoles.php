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



$retards = $pdo->query('SELECT '.
		'e.id, '.
		'e.nom, '.
		'e.ecole_lyonnaise, '.
		'p.nom AS pnom, '.
		'p.prenom AS pprenom, '.
		'p.sexe AS psexe, '.
		'p.sportif AS psportif, '.
		'p.telephone AS ptelephone, '.
		'p.id AS pid, '.
		'p.hors_malus, '.
		't.logement, '.
		't.nom AS tarif '.
	'FROM ecoles AS e '.
	'LEFT JOIN (participants AS p '.
		'JOIN tarifs_ecoles AS te ON '.
			'te.id = p.id_tarif_ecole AND '.
			'te._etat = "active" '.
		'JOIN tarifs AS t ON '.
			't.id = te.id_tarif AND '.
			't._etat = "active") ON '.
		'p.date_inscription > "'.APP_DATE_MALUS.'" AND '.
		'p.id_ecole = e.id AND '.
		'p._etat = "active" '.
	'WHERE '.
		'e._etat = "active" '.
	'ORDER BY '.
		'e.nom ASC, '.
		'p.nom ASC, '.
		'p.prenom ASC ')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$retards = $retards->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_GROUP);


foreach ($retards as $i => $groupe) {
	foreach ($groupe as $j => $retard) {
		$retards[$i][$j]['sportif'] = $retard['psportif'] ? 'Oui' : 'Non';	
		$retards[$i][$j]['excuse'] = $retard['hors_malus'] ? 'Oui' : 'Non';	
		$retards[$i][$j]['tlogement'] = $retard['logement'] ? 'Full package' : 'Light package';	
	}
}


//Labels pour le XLSX
$labels = [
	'Nom' => 'pnom',
	'Prénom' => 'pprenom',
	'Sexe' => 'psexe',
	'Sportif' => 'sportif',
	'Tarif' => 'tarif',
	'Logement' => 'tlogement',
	'Excuse' => 'excuse',
	'Téléphone' => 'ptelephone',
];

//Téléchargement du fichier XLSX concerné
if (!empty($_GET['excel']) &&
	intval($_GET['excel']) &&
	in_array($_GET['excel'], array_keys($retards))) {

	$titre = 'Liste des retards ('.unsecure($retards[$_GET['excel']][0]['nom']).')';
	$fichier = 'liste_retards_ecole_'.onlyLetters(utf8_decode(unsecure($retards[$_GET['excel']][0]['nom'])));
	$items = $retards[$_GET['excel']];
	exportXLSX($items, $fichier, $titre, $labels);

}

else if (isset($_GET['excel'])) {

	$fichier = 'liste_retards_ecoles';
	$items = $titres = $feuilles[] = [];
	
	$i = 0;
	foreach ($retards as $retards_ecole) {
		$titres[$i] = 'Liste des retards ('.unsecure($retards_ecole[0]['nom']).')';
		$feuilles[$i] = unsecure($retards_ecole[0]['nom']);

		foreach ($retards_ecole as $k => $retard) {
			if (empty($retard['pid']))
				unset($retards_ecole[$k]);
		}

		$items[$i] = $retards_ecole;
		$i++;
	}
	
	exportXLSXGroupe($items, $fichier, $feuilles, $titres, $labels);

}


//Inclusion du bon fichier de template
require DIR.'templates/admin/competition/retards_ecoles.php';
