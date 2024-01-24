<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/admin/competition/
						action_participants_ecoles.php *****/
/* Liste des participants par école ************************/
/* *********************************************************/
/* Dernière modification : le 23/02/15 *********************/
/* *********************************************************/



$participants = $pdo->query('SELECT '.
		'e.id, '.
		'e.nom AS nom, '.
		'e.ecole_lyonnaise, '.
		'p.nom AS pnom, '.
		'p.prenom AS pprenom, '.
		'p.sexe AS psexe, '.
		'p.telephone AS ptelephone, '.
		'p.id AS pid, '.
		'p.sportif, '.
		'p.fanfaron, '.
		'p.pompom, '.
		'p.cameraman, '.
		'p.recharge, '.
		't.nom AS tnom, '.
		't.logement, '.
		'q.valeur AS quota_total '.
	'FROM ecoles AS e '.
	'LEFT JOIN (participants AS p '.
		'JOIN tarifs_ecoles AS te ON '.
			'te.id = p.id_tarif_ecole AND '.
			'te._etat = "active" '.
		'JOIN tarifs AS t ON '.
			'te.id_tarif = t.id AND '.
			't._etat = "active") ON '.
		'p.id_ecole = e.id AND '.
		'p._etat = "active" '.
	'LEFT JOIN quotas_ecoles AS q ON '.
		'q.id_ecole = e.id AND '.
		'q.quota = "total" AND '.
		'q._etat = "active" '.
	'WHERE '.
		'e._etat = "active" '.
	'ORDER BY '.
		'e.nom ASC, '.
		'p.nom ASC, '.
		'p.prenom ASC ')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$participants = $participants->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_GROUP);


foreach ($participants as $i => $groupe) {
	foreach ($groupe as $j => $participant) {
		$participants[$i][$j]['psportif'] = $participant['sportif'] ? 'Oui' : '';	
		$participants[$i][$j]['pfanfaron'] = $participant['fanfaron'] ? 'Oui' : '';	
		$participants[$i][$j]['ppompom'] = $participant['pompom'] ? 'Oui' : '';	
		$participants[$i][$j]['pcameraman'] = $participant['cameraman'] ? 'Oui' : '';	
		$participants[$i][$j]['plogement'] = $participant['logement'] ? 'Full' : 'Light';	
	}
}


//Labels pour le XLSX
$labels = [
	'Nom' => 'pnom',
	'Prénom' => 'pprenom',
	'Sexe' => 'psexe',
	'Sportif' => 'psportif',
	'Fanfaron' => 'pfanfaron',
	'Pompom' => 'ppompom',
	'Cameraman' => 'pcameraman',
	'Téléphone' => 'ptelephone',
	'Gourde' => 'recharge',
	'Tarif' => 'tnom',
	'Logement' => 'plogement'
];


//Téléchargement du fichier XLSX concerné
if (!empty($_GET['excel']) &&
	intval($_GET['excel']) &&
	in_array($_GET['excel'], array_keys($participants))) {

	$titre = 'Liste des particpants ('.unsecure($participants[$_GET['excel']][0]['nom']).')';
	$fichier = 'liste_participants_ecole_'.onlyLetters(utf8_decode(unsecure($participants[$_GET['excel']][0]['nom'])));
	$items = $participants[$_GET['excel']];
	exportXLSX($items, $fichier, $titre, $labels);

}


else if (isset($_GET['excel'])) {

	$fichier = 'liste_participants_ecoles';
	$items = $titres = $feuilles[] = [];
	
	$i = 0;
	foreach ($participants as $participants_ecole) {
		$titres[$i] = 'Liste des participants ('.unsecure($participants_ecole[0]['nom']).')';
		$feuilles[$i] = unsecure($participants_ecole[0]['nom']);

		foreach ($participants_ecole as $k => $participant) {
			if (empty($participant['pid']))
				unset($participants_ecole[$k]);
		}

		$items[$i] = $participants_ecole;
		$i++;
	}
	
	exportXLSXGroupe($items, $fichier, $feuilles, $titres, $labels);

}


//Inclusion du bon fichier de template
require DIR.'templates/admin/competition/participants_ecoles.php';
