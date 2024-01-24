<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/admin/logement/action_recapitulaltif.php ********/
/* Récapitualitf sur les logements *************************/
/* *********************************************************/
/* Dernière modification : le 16/02/15 *********************/
/* *********************************************************/


$batiments = $pdo->query($s='SELECT '.
		'b.batiment, '.
		'(SELECT '.
				'COUNT(c1.id) '.
			'FROM chambres AS c1 WHERE '.
				'c1.etat = "amies" AND '.
				'SUBSTR(c1.numero, 1, 1) = b.batiment AND '.
				'c1._etat = "active") AS nb_amies, '.
		'(SELECT '.
				'COUNT(c5.id) '.
			'FROM chambres AS c5 WHERE '.
				'c5.etat = "amiesplus" AND '.
				'SUBSTR(c5.numero, 1, 1) = b.batiment AND '.
				'c5._etat = "active") AS nb_amiesplus, '.
		'(SELECT '.
				'COUNT(c2.id) '.
			'FROM chambres AS c2 WHERE '.
				'c2.etat = "lachee" AND '.
				'SUBSTR(c2.numero, 1, 1) = b.batiment AND '.
				'c2._etat = "active") AS nb_lachee, '.
		'(SELECT '.
				'SUM(c3.places) '.
			'FROM chambres AS c3 WHERE '.
				'(c3.etat = "lachee" OR c3.etat = "amies" OR c3.etat = "amiesplus") AND '.
				'SUBSTR(c3.numero, 1, 1) = b.batiment AND '.
				'c3._etat = "active") AS nb_places, '.
		'(SELECT '.
				'COUNT(cp.id_participant) '.
			'FROM chambres_participants AS cp '.
			'JOIN chambres AS c4 ON '.
				'c4.id = cp.id_chambre  AND '.
				'c4._etat = "active"'.
			'WHERE '.
				'SUBSTR(c4.numero, 1, 1) = b.batiment AND '.
				'cp._etat = "active") AS nb_filles '.
	'FROM ('.implode(' UNION ', array_map(function($v) { return "SELECT '$v' AS batiment"; }, str_split('UVTXABC'))).') as b')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$batiments = $batiments->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);


$total = array(
	'nb_amies' => 0,
	'nb_amiesplus' => 0,
	'nb_lachee' => 0,
	'nb_filles' => 0,
	'nb_places' => 0,
	'nb_chambres' => 0);


foreach (str_split('UVTXABC') AS $batiment) {

	if (empty($batiments[$batiment]))
		$batiments[$batiment] = array(
			'nb_amies' => 0,
			'nb_amiesplus' => 0,
			'nb_lachee' => 0,
			'nb_places' => 0,
			'nb_filles' => 0);

	$batiments[$batiment]['nb_etages'] = in_array($batiment, array('A', 'B', 'C')) ? 4 : 6;
	$batiments[$batiment]['nb_chambres'] = 0;
	

	foreach (range(0, $batiments[$batiment]['nb_etages']) as $etage) {
		$batiments[$batiment]['nb_chambres'] += $etage == 0 ? 
			($batiments[$batiment]['nb_etages'] == 6 ? 8 : 20) :
			($batiments[$batiment]['nb_etages'] == 6 ? 16 : 17);
	}


	$total['nb_amies'] += $batiments[$batiment]['nb_amies'];
	$total['nb_amiesplus'] += $batiments[$batiment]['nb_amiesplus'];
	$total['nb_lachee'] += $batiments[$batiment]['nb_lachee'];
	$total['nb_filles'] += $batiments[$batiment]['nb_filles'];
	$total['nb_chambres'] += $batiments[$batiment]['nb_chambres'];
	$total['nb_places'] += $batiments[$batiment]['nb_places'];

}


$nb_filles = $pdo->query('SELECT '.
		'COUNT(p.id) AS tot '.
	'FROM participants AS p '.
	'JOIN tarifs_ecoles AS te ON '.
		'te.id = p.id_tarif_ecole AND '.
		'te._etat = "active" '.
	'JOIN tarifs AS t ON '.
		't.id = te.id_tarif AND '.
		't.logement = 1 AND '.
		't._etat = "active" '.
	'WHERE '.
		'p.sexe = "f" AND '.
		'p._etat = "active"')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$nb_filles = $nb_filles->fetch(PDO::FETCH_ASSOC);


//Inclusion du bon fichier de template
require DIR.'templates/admin/logement/recapitulatif.php';
