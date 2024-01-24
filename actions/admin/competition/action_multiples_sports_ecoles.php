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



$multiples_sports_ = $pdo->query('SELECT '.
		'e.id, '.
		'e.nom, '.
		'e.ecole_lyonnaise, '.
		'p.id AS pid, '.
		'p.nom AS pnom, '.
		'p.prenom AS pprenom, '.
		'p.sexe AS psexe, '.
		'p.telephone AS ptelephone, '.
		'p.licence, '.
		's.sport, '.
		's.sexe AS ssexe, '.
		'eq.label, '.
		'CASE WHEN eq.id_capitaine = p.id THEN 1 ELSE 0 END AS capitaine '.
	'FROM ecoles AS e '.
	'LEFT JOIN (participants AS p '.
		'JOIN sportifs AS sp ON '.
			'sp.id_participant = p.id AND '.
			'sp._etat = "active" '.
		'JOIN equipes AS eq ON '.
			'eq.id = sp.id_equipe AND '.
			'eq._etat = "active" '.
		'JOIN ecoles_sports AS es ON '.
			'es.id = eq.id_ecole_sport AND '.
			'es._etat = "active" '.
		'JOIN sports AS s ON '.
			's.id = es.id_sport AND '.
			's._etat = "active") ON '.
		'p.id_ecole = e.id AND '.
		'p._etat = "active" AND '.
		'(SELECT COUNT(spi.id) '.
			'FROM sportifs AS spi '.
			'WHERE '.
				'spi.id_participant = p.id AND '.
				'spi._etat = "active") > 1 '.
	'WHERE '.
		'e._etat = "active" '.
	'ORDER BY '.
		'e.nom ASC, '.
		'p.nom ASC, '.
		'p.prenom ASC, '.
		's.sport ASC, '.
		's.sexe ASC')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$multiples_sports_ = $multiples_sports_->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_GROUP);


$multiples_sports = [];
foreach ($multiples_sports_ as $i => $groupe) {
	foreach ($groupe as $j => $multiple_sport) {
		$multiples_sports[$i][$multiple_sport['pid']][] = $multiple_sport;
		$multiples_sports_[$i][$j]['capitainex'] = $multiple_sport['capitaine'] ? 'Oui' : 'Non';
		$multiples_sports_[$i][$j]['sport_sexe'] = $multiple_sport['sport'].' '.strip_tags(printSexe($multiple_sport['ssexe']));
	}
}


//Labels pour le XLSX
$labels = [
	'Nom' => 'pnom',
	'Prénom' => 'pprenom',
	'Sexe' => 'psexe',
	'Licence' => 'licence',
	'Téléphone' => 'ptelephone',
	'Capitaine' => 'capitainex',
	'Sport' => 'sport_sexe',
	'Equipe' => 'label',
];


//Téléchargement du fichier XLSX concerné
if (!empty($_GET['excel']) &&
	intval($_GET['excel']) &&
	in_array($_GET['excel'], array_keys($multiples_sports))) {

	$titre = 'Liste des multi sportifs ('.unsecure($multiples_sports_[$_GET['excel']][0]['nom']).')';
	$fichier = 'liste_multi_sportifs_ecole_'.onlyLetters(utf8_decode(unsecure($multiples_sports_[$_GET['excel']][0]['nom'])));
	$items = $multiples_sports_[$_GET['excel']];

	exportXLSX($items, $fichier, $titre, $labels);

}

else if (isset($_GET['excel'])) {

	$fichier = 'liste_multi_sportifs_ecoles';
	$items = $titres = $feuilles[] = [];
	
	$i = 0;
	foreach ($multiples_sports_ as $multiples_sports_ecole) {
		$titres[$i] = 'Liste des multi sportifs ('.unsecure($multiples_sports_ecole[0]['nom']).')';
		$feuilles[$i] = unsecure($multiples_sports_ecole[0]['nom']);

		foreach ($multiples_sports_ecole as $k => $sportif) {
			if (empty($sportif['pid']))
				unset($multiples_sports_ecole[$k]);
		}

		$items[$i] = $multiples_sports_ecole;
		$i++;
	}
	
	exportXLSXGroupe($items, $fichier, $feuilles, $titres, $labels);

}


//Inclusion du bon fichier de template
require DIR.'templates/admin/competition/multiples_sports_ecoles.php';
