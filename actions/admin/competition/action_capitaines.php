<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/admin/competition/action_capitaines.php *********/
/* Liste des capitaines par école **************************/
/* *********************************************************/
/* Dernière modification : le 19/01/15 *********************/
/* *********************************************************/



$capitaines = $pdo->query('SELECT '.
		's.id AS sid, '.
		'e.nom AS enom, '.
		'e.ecole_lyonnaise, '.
		's.sport, '.
		's.sexe AS ssexe, '.
		's.individuel AS sindividuel, '.
		'e.id AS eid, '.
		'p.nom AS pnom, '.
		'p.prenom AS pprenom, '.
		'p.licence AS plicence, '.
		'p.sexe AS psexe, '.
		'p.telephone AS ptelephone, '.
		'p.id AS id, '.
		'eq.label '.
	'FROM equipes AS eq '.
	'JOIN ecoles_sports AS es ON '.
		'es.id = eq.id_ecole_sport AND '.
		'es._etat = "active" '.
	'JOIN sports AS s ON '.
		'es.id_sport = s.id AND '.
		's._etat = "active" '.
	'JOIN participants AS p ON '.
		'p.id = eq.id_capitaine AND '.
		'p._etat = "active" '.
	'JOIN ecoles AS e ON '.
		'e.id = es.id_ecole AND '.
		'e._etat = "active" '.
	'WHERE '.
		'eq._etat = "active" '.
	'ORDER BY '.
		'p.nom ASC, '.
		'p.prenom ASC ')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$capitaines = $capitaines->fetchAll(PDO::FETCH_ASSOC);


foreach ($capitaines as $i => $capitaine) {
	$capitaines[$i]['sport_sexe'] = $capitaine['sport'].' '.strip_tags(printSexe($capitaine['ssexe']));
	$capitaines[$i]['sport_individuel'] = $capitaine['sindividuel'] ? 'Oui' : 'Non';
}

//Téléchargement du fichier XLSX concerné
if (isset($_GET['excel'])) {

	$titre = 'Liste des capitaines';
	$fichier = 'liste_capitaines';
	$items = $capitaines;
	$labels = [
		'Nom' => 'pnom',
		'Prénom' => 'pprenom',
		'Sexe' => 'psexe',
		'Ecole' => 'enom',
		'Sport' => 'sport_sexe',
		'Individuel' => 'sport_individuel',
		'Equipe' => 'label',
		'Licence' => 'plicence',
		'Téléphone' => 'ptelephone',
	];
	exportXLSX($items, $fichier, $titre, $labels);

}


//Inclusion du bon fichier de template
require DIR.'templates/admin/competition/capitaines.php';
