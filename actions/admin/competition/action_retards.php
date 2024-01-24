<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/admin/competition/action_fanfarons.php **********/
/* Liste des fanfarons *************************************/
/* *********************************************************/
/* Dernière modification : le 18/12/14 *********************/
/* *********************************************************/


$retards = $pdo->query('SELECT '.
		'e.id, '.
		'e.nom AS enom, '.
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
	'FROM participants AS p '.
	'JOIN tarifs_ecoles AS te ON '.
		'te.id = p.id_tarif_ecole AND '.
		'te._etat = "active" '.
	'JOIN tarifs AS t ON '.
		't.id = te.id_tarif AND '.
		't._etat = "active" '.
	'JOIN ecoles AS e ON '.
		'p.id_ecole = e.id AND '.
		'e._etat = "active" '.
	'WHERE '.
		'p.date_inscription > "'.APP_DATE_MALUS.'" AND '.
		'p._etat = "active" '.
	'ORDER BY '.
		'p.nom ASC, '.
		'p.prenom ASC, '.
		'e.nom ASC')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$retards = $retards->fetchAll(PDO::FETCH_ASSOC);



foreach ($retards as $i => $retard) {
	$retards[$i]['sportif'] = $retard['psportif'] ? 'Oui' : 'Non';	
	$retards[$i]['excuse'] = $retard['hors_malus'] ? 'Oui' : 'Non';	
	$retards[$i]['tlogement'] = $retard['logement'] ? 'Full package' : 'Light package';	
}

//Téléchargement du fichier XLSX concerné
if (isset($_GET['excel'])) {

	$titre = 'Liste des retards';
	$fichier = 'liste_retards';
	$items = $retards;
	$labels = [
		'Nom' => 'pnom',
		'Prénom' => 'pprenom',
		'Sexe' => 'psexe',
		'Sportif' => 'sportif',
		'Tarif' => 'tarif',
		'Logement' => 'tlogement',
		'Excuse' => 'excuse',
		'Ecole' => 'enom',
		'Téléphone' => 'ptelephone',
	];
	exportXLSX($items, $fichier, $titre, $labels);

}


//Inclusion du bon fichier de template
require DIR.'templates/admin/competition/retards.php';
