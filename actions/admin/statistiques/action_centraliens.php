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

$centraliens = $pdo->query('SELECT '.
		'c.id, '.
		'u.nom, '.
		'u.prenom, '.
		'c.paye, '.
		'c.soiree, '.
		//'c.pfvendredi, '.
		'c.pfsamedi, '.
		//'c.vegetarien, '.
		'c.tshirt, '.
		'c.gourde, '.
		'c.tombola, '.
		't.nom AS tnom, '.
		't.tarif '.
	'FROM centraliens AS c '.
	'JOIN utilisateurs AS u ON '.
		'u.id = c.id_utilisateur AND '.
		'u._etat = "active" '.
	'LEFT JOIN (participants AS p '.
		'JOIN tarifs_ecoles AS te ON '.
			'te.id = p.id_tarif_ecole AND '.
			'te._etat = "active"'.
		'JOIN tarifs AS t ON '.
			't.id = te.id_tarif AND '.
			't._etat = "active") ON '.
		'p.id = c.id_participant AND '.
		'p._etat = "active" '.
	'WHERE '.
		'c._etat = "active" '.
	'ORDER BY '.
		'u.nom ASC, '.
		'u.prenom ASC')
	->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);


if (!empty($_POST['cid']) &&
	!empty($centraliens[$_POST['cid']])) {
	$ref = pdoRevision('centraliens', $_POST['cid']);
	$pdo->exec('UPDATE centraliens SET '.
			'_auteur = '.(int) $_SESSION['user']['id'].', '.
			'_ref = '.(int) $ref.', '.
			'_date = NOW(), '.
			'_message = "Changement du statut de paiement", '.
			//-------------//
			'paye = 1 - paye '.
		'WHERE '.
			'id = '.(int) $_POST['cid']);
	die;
}

$soirees = 0;
//$pfvendredis = 0;
$pfsamedis = 0;
//$vegetariens = 0;
$tshirts = 0;
$gourdes = 0;
$tombolas = 0;
$tombolas_p = 0;
$supplements = 0;
$totaux = 0;
$payes = 0;
$insciptionVide = 0;

foreach ($centraliens as $cid => $centralien) {
	$tombola = $centralien['tombola'];

	if ($tombola<3){
		$tombola = $tombola*2;
	} elseif ($tombola<5){
		$tombola = 2*($tombola%3) +($tombola -$tombola%3)*4/3;
	} elseif ($tombola<10){
		$tombola = 2*($tombola%5) +($tombola -$tombola%5)*6/5;
	} elseif ($tombola>=10){
		$tombola = 2*($tombola%10) +($tombola -$tombola%10);
	} else {
		$tombola = $tombola*2;
	}
	$centraliens[$cid]['tombola_p'] = $tombola;

	$centraliens[$cid]['total'] = ($centralien['soiree'] ? APP_CENTRALIENS_PRIX_SOIREE : 0) + 
			//($centralien['pfvendredi'] ? 4 : 0) + 
			($centralien['pfsamedi'] ? APP_CENTRALIENS_PRIX_PFOOD : 0) + 
			($centralien['tshirt'] ? APP_CENTRALIENS_PRIX_TSHIRT : 0) +
			($centralien['gourde'] ? APP_CENTRALIENS_PRIX_GOURDE : 0) + 
			$tombola + 
			$centralien['tarif'];

	if ($centraliens[$cid]['total'] == 0) {
		unset($centraliens[$cid]);
		$insciptionVide++;
		continue;
	}

	$payes += $centralien['paye'];
	$soirees += $centralien['soiree'];
	//$pfvendredis += $centralien['pfvendredi'];
	$pfsamedis += $centralien['pfsamedi'];
	//$vegetariens += $centralien['vegetarien'];
	$tshirts += $centralien['tshirt'];
	$gourdes += $centralien['gourde'];
	$tombolas += $centralien['tombola'];
	$tombolas_p += $centraliens[$cid]['tombola_p'];
	$supplements += $centralien['tarif'];
	$totaux += $centraliens[$cid]['total']; 
}

//Téléchargement du fichier XLSX concerné
if (isset($_GET['excel'])) {
	$titre = 'Statistiques des centraliens';
	$fichier = 'stats_centraliens';
	$items = $centraliens;
	$labels = [
		'Nom' => 'nom',
		'Prénom' => 'prenom',
		'Soirée' => 'soiree',
		//'PF Ven' => 'pfvendredi',
		'Full package' => 'pfsamedi',
		//'Vegetarien' => 'vegetarien',
		'T-Shirt' => 'tshirt',
		'Gourde' => 'gourde',
		'Tombola' => 'tombola',
		'Tarif' => 'tnom',
		'Supplément' => 'tarif',
		'Total' => 'total',
	];
	exportXLSX($items, $fichier, $titre, $labels);

}




//Inclusion du bon fichier de template
require DIR.'templates/admin/statistiques/centraliens.php';
