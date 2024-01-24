<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/admin/competition/action_participants.php *******/
/* Liste des participants **********************************/
/* *********************************************************/
/* Dernière modification : le 23/02/15 *********************/
/* *********************************************************/

set_time_limit(60);

$participants = $pdo->query('SELECT '.
		'p.id, '.
		'e.nom AS enom, '.
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
		't.nom AS tnom, '.
		'p.recharge, '.
		't.logement '.
	'FROM participants AS p '.
	'JOIN ecoles AS e ON '.
		'p.id_ecole = e.id AND '.
		'p._etat = "active" '.
	'JOIN tarifs_ecoles AS te ON '.
		'te.id = p.id_tarif_ecole AND '.
		'te._etat = "active" '.
	'JOIN tarifs AS t ON '.
		'te.id_tarif = t.id AND '.
		't._etat = "active" '.
	'WHERE '.
		'p._etat = "active" '.
	'ORDER BY '.
		'p.nom ASC, '.
		'p.prenom ASC, '.
		'e.nom ASC')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$participants = $participants->fetchAll(PDO::FETCH_ASSOC);

foreach ($participants as $i => $participant) {
	$participants[$i]['psportif'] = $participant['sportif'] ? 'Oui' : '';	
	$participants[$i]['pfanfaron'] = $participant['fanfaron'] ? 'Oui' : '';	
	$participants[$i]['ppompom'] = $participant['pompom'] ? 'Oui' : '';	
	$participants[$i]['pcameraman'] = $participant['cameraman'] ? 'Oui' : '';	
	$participants[$i]['plogement'] = $participant['logement'] ? 'Full' : 'Light';
	$participants[$i]['psoiree'] = preg_match('/\bsans soirée du samedi\b/', $participant['tnom']) == false ? 1 : 0;

} 
//code non portable d'une année sur l'autre car test pour psoiree depend du nom du package utilisé

//Téléchargement du fichier XLSX concerné
if (isset($_GET['excel'])) {

	$titre = 'Liste des participants';
	$fichier = 'liste_participants';
	$items = $participants;
	$labels = [
		'Nom' => 'pnom',
		'Prénom' => 'pprenom',
		'Sexe' => 'psexe',
		'Ecole' => 'enom',
		'Sportif' => 'psportif',
		'Fanfaron' => 'pfanfaron',
		'Pompom' => 'ppompom',
		'Caméraman' => 'pcameraman',
		'Téléphone' => 'ptelephone',
		'Gourde' => 'recharge',
		'Tarif' => 'tnom',
		'Logement' => 'plogement',
	];
	exportXLSX($items, $fichier, $titre, $labels);

}


//Inclusion du bon fichier de template
require DIR.'templates/admin/competition/participants.php';
