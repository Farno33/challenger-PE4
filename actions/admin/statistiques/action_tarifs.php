<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/admin/statistiques/action_tarifs.php ************/
/* Liste des Tarifs ****************************************/
/* *********************************************************/
/* Dernière modification : le 23/01/15 *********************/
/* *********************************************************/


$tarifs_ecoles = $pdo->query('SELECT '.
		'e.id, '.
		'e.nom, '.
		'ecole_lyonnaise, '.
		'COUNT(p.id) AS pnb, '.
		'te.id_tarif '.
	'FROM ecoles AS e '.
	'LEFT JOIN tarifs_ecoles AS te ON '.
		'te.id_ecole = e.id AND '.
		'te._etat = "active" '.
	'LEFT JOIN participants AS p ON '.
		'p.id_tarif_ecole = te.id AND '.
		'p.id_ecole = e.id AND '.
		'p._etat = "active" '.
	'WHERE '.
		'e._etat = "active" '.
	'GROUP BY '.
		'e.id, te.id_tarif '.
	'ORDER BY '.
		'ecole_lyonnaise DESC, '.
		'e.nom ASC')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$tarifs_ecoles = $tarifs_ecoles->fetchAll(PDO::FETCH_ASSOC);


$tarifs = $pdo->query('SELECT '.
		'id, '.
		'nom, '.
		'sportif, '.
		'tarif, '.
		'ecole_lyonnaise '.
	'FROM tarifs '.
	'WHERE '.
		'_etat = "active" '.
	'ORDER BY '.
		'sportif ASC, '.
		'nom ASC')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$tarifs = $tarifs->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);


$ecoles = [];
foreach ($tarifs_ecoles as $tarif) {
	if (!isset($ecoles[$tarif['id']]))
		$ecoles[$tarif['id']] = array_merge($tarif, array('tarifs' => []));

	$ecoles[$tarif['id']]['tarifs'][$tarif['id_tarif']] = $tarif['pnb'];
}

$typesEcoles = [1 => 'Ecoles Lyonnaises', 0 => 'Ecoles Non Lyonnaises'];
$typesSportifs = [1 => 'Tarifs Sportifs', 0 => 'Tarifs Non Sportifs'];


if (isset($_GET['excel']) &&
	isset($_GET['e']) &&
	isset($_GET['s']) &&
	in_array($_GET['e'], array_keys($typesEcoles)) &&
	in_array($_GET['s'], array_keys($typesSportifs))) {

	foreach ($ecoles as $ecole) {
		if ($_GET['e'] != $ecole['ecole_lyonnaise'])
			continue;

		foreach ($tarifs as $t => $tarif) {
			$ecole['sum_'.$t] = 
				empty($ecole['tarifs'][$t]) ? '' :
					$ecole['tarifs'][$t]. ' ('.
					printMoney($ecole['tarifs'][$t] * $tarif['tarif']).')';
		}

		$ecoles_filtred[] = $ecole;
	}

	$titre = 'Statistiques sur les tarifs ('.$typesEcoles[$_GET['e']].' / '.$typesSportifs[$_GET['s']].')';
	$fichier = 'stats_tarifs_'.($_GET['e'] ? '' : 'non').'lyonnaise_'.($_GET['s'] ? '' : 'non').'sportifs';
	$items = $ecoles_filtred;
	$labels = [
		'Nom' => 'nom'
	];

	foreach ($tarifs as $i => $tarif) {
		if ($_GET['s'] == 1 && $tarif['sportif'] == '0' ||
			$_GET['s'] == 0 && $tarif['sportif'] == '1' ||
			$_GET['e'] != $tarif['ecole_lyonnaise'])
			continue;

		$labels[$tarif['nom']] = 'sum_'.$i;
	}


	exportXLSX($items, $fichier, $titre, $labels);
}				


//Inclusion du bon fichier de template
require DIR.'templates/admin/statistiques/tarifs.php';
