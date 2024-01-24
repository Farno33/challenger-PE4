<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/admin/competition/action_cameramans.php ************/
/* Liste des caméramans ***************************************/
/* *********************************************************/
/* Dernière modification : le 18/12/14 *********************/
/* *********************************************************/



$cameramans = $pdo->query('SELECT '.
		'e.id, '.
		'e.nom AS enom, '.
		'e.ecole_lyonnaise, '.
		'p.nom AS pnom, '.
		'p.prenom AS pprenom, '.
		'p.sexe AS psexe, '.
		'p.sportif AS psportif, '.
		'p.telephone AS ptelephone, '.
		'p.id AS pid '.
	'FROM participants AS p '.
	'JOIN ecoles AS e ON '.
		'p.id_ecole = e.id AND '.
		'e._etat = "active" '.
	'WHERE '.
		'p.cameraman = 1 AND '.
		'p._etat = "active" '.
	'ORDER BY '.
		'p.nom ASC, '.
		'p.prenom ASC, '.
		'e.nom ASC')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$cameramans = $cameramans->fetchAll(PDO::FETCH_ASSOC);


foreach ($cameramans as $i => $cameraman) {
	$cameramans[$i]['sportif'] = $cameraman['psportif'] ? 'Oui' : 'Non';	
}


//Téléchargement du fichier XLSX concerné
if (isset($_GET['excel'])) {

	$titre = 'Liste des cameramans';
	$fichier = 'liste_cameramans';
	$items = $cameramans;
	$labels = [
		'Nom' => 'pnom',
		'Prénom' => 'pprenom',
		'Sexe' => 'psexe',
		'Sportif' => 'sportif',
		'Ecole' => 'enom',
		'Téléphone' => 'ptelephone',
	];
	exportXLSX($items, $fichier, $titre, $labels);

}


//Inclusion du bon fichier de template
require DIR.'templates/admin/competition/cameramans.php';
