<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/admin/logement/action_chambres.php **************/
/* Liste des batiments *************************************/
/* *********************************************************/
/* Dernière modification : le 16/02/15 *********************/
/* *********************************************************/


$batiments = $pdo->query('SELECT '.
		'b.batiment, '.
		'(SELECT '.
				'COUNT(c1.id) '.
			'FROM chambres AS c1 WHERE '.
				'(c1.etat = "lachee" OR '.
				'c1.etat = "amies" OR '.
				'c1.etat = "amiesplus") AND '.
				'SUBSTR(c1.numero, 1, 1) = b.batiment AND '.
				'c1._etat = "active") AS nb_active, '.
		'(SELECT '.
				'SUM(c2.places) '.
			'FROM chambres AS c2 WHERE '.
				'(c2.etat = "lachee" OR '.
				'c2.etat = "amies" OR '.
				'c2.etat = "amiesplus") AND '.
				'SUBSTR(c2.numero, 1, 1) = b.batiment AND '.
				'c2._etat = "active") AS nb_places, '.
		'(SELECT '.
				'COUNT(cp.id_participant) '.
			'FROM chambres_participants AS cp '.
			'JOIN chambres AS c4 ON '.
				'c4.id = cp.id_chambre AND '.
				'c4._etat = "active" '.
			'WHERE '.
				'SUBSTR(c4.numero, 1, 1) = b.batiment AND '.
				'cp._etat = "active") AS nb_filles '.
	'FROM ('.implode(' UNION ', array_map(function($v) { return "SELECT '$v' AS batiment"; }, str_split('UVTXABC'))).') as b')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$batiments = $batiments->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);


foreach (str_split('UVTXABC') AS $batiment) {

	if (empty($batiments[$batiment]))
		$batiments[$batiment] = array(
			'nb_active' => 0,
			'nb_filles' => 0);

	$batiments[$batiment]['nb_etages'] = in_array($batiment, array('A', 'B', 'C')) ? 4 : 6;
	$batiments[$batiment]['nb_chambres'] = 0;	

	foreach (range(0, $batiments[$batiment]['nb_etages']) as $etage) {
		$batiments[$batiment]['nb_chambres'] += $etage == 0 ? 
			($batiments[$batiment]['nb_etages'] == 6 ? 8 : 20) :
			($batiments[$batiment]['nb_etages'] == 6 ? 16 : 17);
	}

}

//Inclusion du bon fichier de template
require DIR.'templates/admin/logement/chambres.php';
