<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/admin/competition/action_sans_sport.php *********/
/* Liste des sportifs sans sport ***************************/
/* *********************************************************/
/* Dernière modification : le 20/01/15 *********************/
/* *********************************************************/


$multiples_sports_ = $pdo->query('SELECT '.
		'p.id, '.
		'p.nom, '.
		'p.prenom, '.
		'p.sexe, '.
		'p.telephone, '.
		'p.licence, '.
		'e.nom AS enom, '.
		's.sport, '.
		's.sexe AS ssexe, '.
		'eq.label, '.
		'CASE WHEN eq.id_capitaine = p.id THEN 1 ELSE 0 END AS capitaine '.
	'FROM participants AS p '.
	'JOIN ecoles AS e ON '.
		'e.id = p.id_ecole AND '.
		'e._etat = "active" '.
	'JOIN sportifs AS sp ON '.
		'sp.id_participant = p.id AND '.
		'sp._etat = "active" '.
	'JOIN equipes AS eq ON '.
		'eq.id = sp.id_equipe AND '.
		'eq._etat = "active" '.
	'JOIN ecoles_sports AS es ON '.
		'es.id = eq.id_ecole_sport AND '.
		'es._etat = "active" '.
	'JOIN sports AS s ON '.
		's.id = es.id_sport AND '.
		's._etat = "active" '.
	'WHERE '.
		'p._etat = "active" AND '.
		'(SELECT COUNT(spi.id) '.
			'FROM sportifs AS spi '.
			'WHERE '.
				'spi.id_participant = p.id AND '.
				'spi._etat = "active") > 1 '.
	'ORDER BY '.
		'p.nom ASC, '.
		'p.prenom ASC, '.
		's.sport ASC, '.
		's.sexe ASC')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$multiples_sports_ = $multiples_sports_->fetchAll(PDO::FETCH_ASSOC);


$multiples_sports = [];
foreach ($multiples_sports_ as $i => $multiple_sport) {
	$multiples_sports[$multiple_sport['id']][] = $multiple_sport;
	$multiples_sports_[$i]['capitainex'] = $multiple_sport['capitaine'] ? 'Oui' : 'Non';
	$multiples_sports_[$i]['sport_sexe'] = $multiple_sport['sport'].' '.strip_tags(printSexe($multiple_sport['ssexe']));
}


//Téléchargement du fichier XLSX concerné
if (isset($_GET['excel'])) {

	$titre = 'Liste des multi sportifs';
	$fichier = 'liste_multi_sportifs';
	$items = $multiples_sports_;
	$labels = [
		'Nom' => 'nom',
		'Prénom' => 'prenom',
		'Sexe' => 'sexe',
		'Ecole' => 'enom',
		'Licence' => 'licence',
		'Téléphone' => 'telephone',
		'Capitaine' => 'capitainex',
		'Sport' => 'sport_sexe',
		'Equipe' => 'label',
	];
	exportXLSX($items, $fichier, $titre, $labels);

}


//Inclusion du bon fichier de template
require DIR.'templates/admin/competition/multiples_sports.php';
