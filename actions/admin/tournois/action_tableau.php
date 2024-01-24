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

$sports = $pdo->query($s='SELECT '.
		's.sport, '.
		's.sexe, '.
		'(SELECT COUNT(eq.id) '.
			'FROM equipes AS eq '.
			'JOIN ecoles_sports AS es ON '.
				'es.id = eq.id_ecole_sport AND '.
				'es._etat = "active" '.
			'WHERE '.
				'eq._etat = "active" AND '.
				'es.id_sport = s.id) AS nb_equipes, '.
		'(SELECT COUNT(po.id) '.
			'FROM _poules AS po '.
			'WHERE '.
				'po._etat = "active" AND '.
				'po.id_sport = s.id) AS nb_poules '.
	'FROM sports AS s '.
	'WHERE s._etat = "active" '.
	'ORDER BY s.sport ASC, s.sexe ASC')
	->fetchAll(PDO::FETCH_ASSOC);


$poules_ = $pdo->query('SELECT '.
		'po.id, '.
		'po.nom, '.
		'pe.qualifiee, '.
		'e.nom, '.
		'eq.label, '.
		's.sport, '.
		's.sexe '.
	'FROM _poules AS po '.
	'JOIN sports AS s ON '.
		's.id = po.id_sport AND '.
		's._etat = "active" '.
	'LEFT JOIN (_poules_equipes AS pe '.
		'JOIN equipes AS eq ON '.
			'eq.id = pe.id_equipe AND '.
			'eq._etat = "active" '.
		'JOIN ecoles_sports AS es ON '.
			'es.id = eq.id_ecole_sport AND '.
			'es._etat = "active" '.
		'JOIN ecoles AS e ON '.
			'e.id = es.id_ecole AND '.
			'e._etat = "active") ON '.
		'pe.id_poule = po.id AND '.
		'pe._etat = "active" '.
	'WHERE '.
		'po._etat = "active"') or die(print_r($pdo->errorInfo()));
$poules_ = $poules_->fetchAll(PDO::FETCH_ASSOC);


$items_ = $pdo->query('SELECT * FROM _tournois')->fetchAll(PDO::FETCH_ASSOC);



//Inclusion du bon fichier de template
require DIR.'templates/admin/tournois/tableau.php';
