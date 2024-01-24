<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/admin/statistiques/action_frais.php *************/
/* Liste des Frais *****************************************/
/* *********************************************************/
/* Dernière modification : le 16/02/15 *********************/
/* *********************************************************/


$ecoles = $pdo->query('SELECT '.
		'e.id, '.
		'e.nom, '.
		'e.malus, '.
		'(SELECT SUM(p0.recharge) '.
			'FROM participants AS p0 '.
			'WHERE '.
				'p0.id_ecole = e.id AND '.
				'p0._etat = "active") AS sum_recharges, '.
		'(SELECT SUM(t1.tarif) '.
			'FROM participants AS p3 '.
			'JOIN tarifs_ecoles AS te1 ON '.
				'te1.id = p3.id_tarif_ecole AND '.
				'te1._etat = "active" '.
			'JOIN tarifs AS t1 ON '.
				't1.id = te1.id_tarif AND '.
				't1._etat = "active" '.
			'WHERE '.
				'p3.id_ecole = e.id AND '.
				'p3._etat = "active") AS sum_tarifs, '.
		'(SELECT SUM(t2.tarif) '.
			'FROM participants AS p4 '.
			'JOIN tarifs_ecoles AS te2 ON '.
				'te2.id = p4.id_tarif_ecole AND '.
				'te2._etat = "active" '.
			'JOIN tarifs AS t2 ON '.
				't2.id = te2.id_tarif AND '.
				't2._etat = "active" '.
			'WHERE '.
				'p4.id_ecole = e.id AND '.
				'p4.hors_malus <> 1 AND '.
				'p4.date_inscription > "'.APP_DATE_MALUS.'" AND '.
				'p4._etat = "active") AS sum_retards, '.
		'(SELECT SUM(pa.montant) '.
			'FROM paiements AS pa '.
			'WHERE '.
				'pa.id_ecole = e.id AND '.
				'pa.etat = "paye" AND '.
				'pa._etat = "active") AS sum_paiements, '.
		'(SELECT COUNT(p1.id) '.
			'FROM participants AS p1 '.
			'WHERE '.
				'p1.id_ecole = e.id AND '.
				'p1._etat = "active") AS quota_inscriptions, '.
		'(SELECT COUNT(p2.id) '.
			'FROM participants AS p2 '.
			'WHERE '.
				'p2.id_ecole = e.id AND '.
				'p2.hors_malus <> 1 AND '.
				'p2.date_inscription > "'.APP_DATE_MALUS.'" AND '.
				'p2._etat = "active") AS quota_retards '.
	'FROM ecoles AS e '.
	'WHERE '.
		'e._etat = "active" '.
	'ORDER BY e.nom ASC')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$ecoles = $ecoles->fetchAll(PDO::FETCH_ASSOC);

$totaux = [];
foreach ($ecoles as $k => $ecole) {
	foreach ($ecole as $key => $value) {
		if (!is_numeric($value))
			continue;
		
		if (!isset($totaux[$key])) {
			$totaux[$key] = $value;
		} else {
			$totaux[$key] += $value;
		}
	}

	$ecoles[$k]['sum_malus_retards'] = $ecole['malus'] * $ecole['sum_retards'] / 100;
	$ecoles[$k]['sum_total'] = $ecoles[$k]['sum_malus_retards'] + $ecole['sum_tarifs'] + $ecole['sum_recharges'];
	$ecoles[$k]['sum_restant'] = $ecoles[$k]['sum_total'] - (float) $ecole['sum_paiements'];
	$ecoles[$k]['sum_paye'] = (float) $ecole['sum_paiements'];
}


//Téléchargement du fichier XLSX concerné
if (isset($_GET['excel'])) {

	$titre = 'Statistiques des frais';
	$fichier = 'stats_frais';
	$items = $ecoles;
	$labels = [
		'Nom' => 'nom',
		'Participants' => 'quota_inscriptions',
		'Retards' => 'quota_retards',
		'Malus' => 'malus',
		'Participation' => 'sum_tarifs',
		'Prix gourde' => 'sum_recharges',
		'Frais' => 'sum_malus_retards',
		'Total' => 'sum_total',
		'Payé' => 'sum_paye',
		'Restant' => 'sum_restant'
	];
	exportXLSX($items, $fichier, $titre, $labels);

}




//Inclusion du bon fichier de template
require DIR.'templates/admin/statistiques/frais.php';
