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

$sports = $pdo->query('SELECT '.
		's.id, '.
		's.sport, '.
		's.sexe, '.
		's.individuel, '.
		's.tournoi_initie, '.
		'(SELECT COUNT(sp.id) '.
			'FROM sportifs AS sp '.
			'JOIN equipes AS eq2 ON '.
				'sp.id_equipe = eq2.id AND '.
				'sp._etat = "active" '.
			'JOIN ecoles_sports AS es3 ON '.
				'es3.id = eq2.id_ecole_sport AND '.
				'es3._etat = "active" '.
			'WHERE '.
				'eq2._etat = "active" AND '.
				'es3.id_sport = s.id) AS nb_sportifs, '.
		'(SELECT COUNT(eq.id) '.
			'FROM equipes AS eq '.
			'JOIN ecoles_sports AS es ON '.
				'es.id = eq.id_ecole_sport AND '.
				'es._etat = "active" '.
			'WHERE '.
				'eq._etat = "active" AND '.
				'es.id_sport = s.id) AS nb_equipes, '.
		'(SELECT COUNT(es2.id) '.
			'FROM ecoles_sports AS es2 '.
			'WHERE '.
				'es2._etat = "active" AND '.
				'es2.id_sport = s.id) AS nb_ecoles, '.
		'(SELECT COUNT(ph.id) '.
			'FROM phases AS ph '.
			'WHERE '.
				'ph._etat = "active" AND '.
				'ph.id_sport = s.id) AS nb_phases, '.
		'(SELECT COUNT(co.id) '.
			'FROM concurrents AS co '.
			'WHERE '.
				'co._etat = "active" AND '.
				'co.id_sport = s.id) AS nb_concurrents '.
	'FROM sports AS s '.
	'WHERE s._etat = "active" '.
	'ORDER BY s.individuel ASC, s.tournoi_initie ASC, s.sport ASC, s.sexe ASC') 
	->fetchAll(PDO::FETCH_ASSOC);



//Inclusion du bon fichier de template
require DIR.'templates/public/score/liste.php';
