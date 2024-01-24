<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/admin/competition/action_sportifs.php ***********/
/* Liste des sportifs **************************************/
/* *********************************************************/
/* Dernière modification : le 18/12/14 *********************/
/* *********************************************************/



$sportifs = $pdo->query('SELECT '.
		'e.id, '.
		'e.nom AS enom, '.
		'e.ecole_lyonnaise, '.
		'eq.label, '.
		's.sport, '.
		's.sexe AS ssexe, '.
		's.id AS sid, '.
		'p.nom AS pnom, '.
		'p.prenom AS pprenom, '.
		'p.licence AS plicence, '.
		'p.sexe AS psexe, '.
		'p.telephone AS ptelephone, '.
		'p.email AS pemail, '.
		'p.id AS pid, '.
		'eq.id_capitaine '.
	'FROM sportifs AS sp '.
	'JOIN equipes AS eq ON '.
		'sp.id_equipe = eq.id AND '.
		'eq._etat = "active" '.
	'JOIN ecoles_sports AS es ON '.
		'es.id = eq.id_ecole_sport AND '.
		'es._etat = "active" '.
	'JOIN sports AS s ON '.
		's.id = es.id_sport AND '.
		's._etat = "active" '.
	'JOIN participants AS p ON '.
		'p.id = sp.id_participant AND '.
		'p._etat = "active" '.
	'JOIN ecoles as e ON '.
		'e.id = es.id_ecole AND '.
		'e._etat = "active" '.
	'WHERE '.
		'sp._etat = "active" '.
	'ORDER BY '.
		'p.nom ASC, '.
		'p.prenom ASC, '.
		'e.nom ASC')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$sportifs = $sportifs->fetchAll(PDO::FETCH_ASSOC);


foreach ($sportifs as $i => $sportif) {
	$sportifs[$i]['sport_sexe'] = $sportif['sport'].' '.strip_tags(printSexe($sportif['ssexe']));
	$sportifs[$i]['capitaine'] = $sportif['id_capitaine'] == $sportif['pid'] ? 'Oui' : '';	
}


//Téléchargement du fichier XLSX concerné
if (isset($_GET['excel'])) {

	$titre = 'Liste des sportifs';
	$fichier = 'liste_sportifs';
	$items = $sportifs;
	$labels = [
		'Capitaine' => 'capitaine',
		'Nom' => 'pnom',
		'Prénom' => 'pprenom',
		'Sexe' => 'psexe',
		'Ecole' => 'enom',
		'Sport' => 'sport_sexe',
		'Equipe' => 'label',
		'Licence' => 'plicence',
		'Téléphone' => 'ptelephone',
		'Email' => 'pemail',
	];
	exportXLSX($items, $fichier, $titre, $labels);

}


//Inclusion du bon fichier de template
require DIR.'templates/admin/competition/sportifs.php';
