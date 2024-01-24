<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/admin/statistiques/action_ecoles.php ************/
/* Liste des Ecoles ****************************************/
/* *********************************************************/
/* Dernière modification : le 16/02/15 *********************/
/* *********************************************************/


$ecoles = $pdo->query('SELECT '.
		'e.id, '.
		'e.nom, '.
		'e.caution_recue, '.
		'e.caution_logement, '.
		'e.charte_acceptee, '.
		'e.etat_inscription, '.
		'(SELECT q.valeur '.
			'FROM quotas_ecoles AS q '.
			'WHERE '.
				'q.quota = "total" AND '.
				'q.id_ecole = e.id AND '.
				'q._etat = "active") AS quota_total, '.
		'(SELECT COUNT(p1.id) '.
			'FROM participants AS p1 '.
			'WHERE '.
				'p1.id_ecole = e.id AND '.
				'p1._etat = "active") AS quota_inscriptions, '.
		'(SELECT COUNT(DISTINCT p2.id) '.
			'FROM participants AS p2 '.
			'JOIN signatures AS s ON '.
				's.id_participant = p2.id '.
			'WHERE '.
				'p2.id_ecole = e.id AND '.
				'p2._etat = "active") AS quota_signatures, '.
		'(SELECT c.connexion '.
			'FROM connexions AS c '.
			'JOIN utilisateurs AS u ON '.
				'u.id = c.id_utilisateur AND '.
				'u._etat = "active" '.
			'JOIN droits_ecoles AS de ON '.
				'de.id_utilisateur = u.id AND '.
				'de._etat = "active" '.
			'WHERE '.
				'de.id_ecole = e.id '.
			'ORDER BY '.
				'c.connexion DESC '.
			'LIMIT 1) AS connexion '.
	'FROM ecoles AS e '.
	'WHERE e._etat = "active" '.
	'ORDER BY e.nom ASC')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$ecoles = $ecoles->fetchAll(PDO::FETCH_ASSOC);


foreach ($ecoles as $i => $ecole) {
	$ecoles[$i]['caution'] = $ecole['caution_recue'] ? 'Oui' : '';	
	$ecoles[$i]['logement'] = $ecole['caution_logement'] ? 'Oui' : '';	
	$ecoles[$i]['charte'] = $ecole['charte_acceptee'] ? 'Oui' : '';	
	$ecoles[$i]['quota'] = $ecole['quota_total'] === null ? '' : $ecole['quota_total'];	
	$ecoles[$i]['last_connexion'] = $ecole['connexion'] === null ? '' : $ecole['connexion'];	
}


//Téléchargement du fichier XLSX concerné
if (isset($_GET['excel'])) {

	$titre = 'Statistiques sur les écoles';
	$fichier = 'stats_ecoles';
	$items = $ecoles;
	$labels = [
		'Nom' => 'nom',
		'Inscriptions' => 'quota_inscriptions',
		'Quota' => 'quota',
		'Etat' => 'etat_inscription',
		'Caution' => 'caution',
		'Caution Logement' => 'logement',
		'Signatures' => 'quota_signatures',
		'Charte' => 'charte',
		'Connexion' => 'last_connexion',
	];
	exportXLSX($items, $fichier, $titre, $labels);

}




//Inclusion du bon fichier de template
require DIR.'templates/admin/statistiques/ecoles.php';
