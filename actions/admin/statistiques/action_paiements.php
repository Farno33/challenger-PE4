<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/admin/statistiques/action_paiements.php **********/
/* Liste des paiements *************************************/
/* *********************************************************/
/* Dernière modification : le 24/01/15 *********************/
/* *********************************************************/



$paiements = $pdo->query('SELECT '.
		'e.id, '.
		'e.nom, '.
		'e.ecole_lyonnaise, '.
		'pa.id AS paid, '.
		'pa._date AS date, '.
		'pa.montant, '.
		'pa.etat, '.
		'pa.type '.
	'FROM ecoles AS e '.
	'JOIN paiements AS pa ON '.
		'pa.id_ecole = e.id AND '.
		'pa._etat = "active" '.
	'WHERE '.
		'e._etat = "active" '.
	'ORDER BY '.
		'e.nom ASC, '.
		'pa._date DESC')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$paiements = $paiements->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_GROUP);



//Labels pour le XLSX
$labels = [
	'Date' => 'date',
	'Montant' => 'montant',
	'Etat' => 'etat',
	'Type' => 'type',
];


//Téléchargement du fichier XLSX concerné
if (!empty($_GET['excel']) &&
	intval($_GET['excel']) &&
	in_array($_GET['excel'], array_keys($paiements))) {

	$titre = 'Liste des paiements ('.unsecure($paiements[$_GET['excel']][0]['nom']).')';
	$fichier = 'liste_paiements_ecole_'.onlyLetters(utf8_decode(unsecure($paiements[$_GET['excel']][0]['nom'])));
	$items = $paiements[$_GET['excel']];

	exportXLSX($items, $fichier, $titre, $labels);

}

else if (isset($_GET['excel'])) {

	$fichier = 'liste_paiements_ecoles';
	$items = $titres = $feuilles[] = [];
	
	$i = 0;
	foreach ($paiements as $paiements_ecole) {
		$titres[$i] = 'Liste des paiements ('.unsecure($paiements_ecole[0]['nom']).')';
		$feuilles[$i] = unsecure($paiements_ecole[0]['nom']);

		foreach ($paiements_ecole as $k => $paiement) {
			if (empty($paiement['paid']))
				unset($paiements_ecole[$k]);
		}

		$items[$i] = $paiements_ecole;
		$i++;
	}
	
	exportXLSXGroupe($items, $fichier, $feuilles, $titres, $labels);

}



//Inclusion du bon fichier de template
require DIR.'templates/admin/statistiques/paiements.php';
