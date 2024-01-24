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

if (isset($_GET['ajax']) && empty($vpTournois)) {
	if (!empty($_POST['filter'])) {
		$centraliens = $pdo->query('SELECT c.id, u.nom, u.prenom ' .
			'FROM centraliens as c ' .
			'JOIN utilisateurs u on c.id_utilisateur = u.id AND u._etat = "active" ' .
			'WHERE c._etat = "active" ' .
			'AND c.vptournoi IS NULL AND (' .
			'CONCAT(u.nom, " ", u.prenom) LIKE "%' . secure($_POST['filter']) . '%" OR ' .
			'CONCAT(u.prenom, " ", u.nom) LIKE "%' . secure($_POST['filter']) . '%") ' .
			'ORDER BY u.nom ASC')
			->fetchAll(PDO::FETCH_ASSOC);
		die(json_encode($centraliens));
	} else if (!empty($_POST['sport']) && !empty($_POST['id'])) {
		$ref = pdoRevision('centraliens', intval($_POST['id']));
		$res = $pdo->exec('UPDATE centraliens SET ' .
			'_auteur = ' . (int) $_SESSION['user']['id'] . ', ' .
			'_ref = ' . (int) $ref . ', ' .
			'_date = NOW(), ' .
			'_message = "Modification du VP tournoi", ' .
			//-------------//
			'vptournoi = ' . intval($_POST['sport']) . ' ' .
			'WHERE ' .
			'id = ' . intval($_POST['id']));
		if ($res<1){
			http_response_code(400);
			die();
		}
		die('1');
	}
	http_response_code(400);
	die();
}


$sports = $pdo->query('SELECT ' .
	's.id, ' .
	's.sport, ' .
	's.sexe, ' .
	's.individuel, ' .
	's.tournoi_initie, ' .
	'(SELECT COUNT(sp.id) ' .
	'FROM sportifs AS sp ' .
	'JOIN equipes AS eq2 ON ' .
	'sp.id_equipe = eq2.id AND ' .
	'sp._etat = "active" ' .
	'JOIN ecoles_sports AS es3 ON ' .
	'es3.id = eq2.id_ecole_sport AND ' .
	'es3._etat = "active" ' .
	'WHERE ' .
	'eq2._etat = "active" AND ' .
	'es3.id_sport = s.id) AS nb_sportifs, ' .
	'(SELECT COUNT(eq.id) ' .
	'FROM equipes AS eq ' .
	'JOIN ecoles_sports AS es ON ' .
	'es.id = eq.id_ecole_sport AND ' .
	'es._etat = "active" ' .
	'WHERE ' .
	'eq._etat = "active" AND ' .
	'es.id_sport = s.id) AS nb_equipes, ' .
	'(SELECT COUNT(es2.id) ' .
	'FROM ecoles_sports AS es2 ' .
	'WHERE ' .
	'es2._etat = "active" AND ' .
	'es2.id_sport = s.id) AS nb_ecoles, ' .
	'(SELECT COUNT(ph.id) ' .
	'FROM phases AS ph ' .
	'WHERE ' .
	'ph._etat = "active" AND ' .
	'ph.id_sport = s.id) AS nb_phases, ' .
	'(SELECT COUNT(co.id) ' .
	'FROM concurrents AS co ' .
	'WHERE ' .
	'co._etat = "active" AND ' .
	'co.id_sport = s.id) AS nb_concurrents ' .
	'FROM sports AS s ' .
	'WHERE s._etat = "active" ' .
	(empty($vpTournois) ? '' : 'AND s.groupe_multiple = ' . $vpTournois[$options['vptournoi']]['groupe_multiple']) . ' ' .
	'ORDER BY s.individuel ASC, s.tournoi_initie ASC, s.sport ASC, s.sexe ASC')
	->fetchAll(PDO::FETCH_ASSOC);

if (
	!empty($_POST['add']) &&
	empty($vpTournois) &&
	is_numeric($_POST['sport']) &&
	(is_numeric($_POST['id']) || !empty($_POST['newVP']))
) {
	if (!is_numeric($_POST['id']) && !empty($_POST['newVP'])){
		$centraliens = $pdo->query('SELECT c.id, u.nom, u.prenom ' .
			'FROM centraliens as c ' .
			'JOIN utilisateurs u on c.id_utilisateur = u.id AND u._etat = "active" ' .
			'WHERE c._etat = "active" ' .
			'AND c.vptournoi IS NULL AND (' .
			'CONCAT(u.nom, " ", u.prenom) LIKE "%' . secure($_POST['newVP']) . '%" OR ' .
			'CONCAT(u.prenom, " ", u.nom) LIKE "%' . secure($_POST['newVP']) . '%") ' .
			'ORDER BY u.nom ASC')
			->fetchAll(PDO::FETCH_ASSOC);
		if (count($centraliens) == 1){
			$_POST['id'] = $centraliens[0]['id'];
		} else {
			http_response_code(400);
			die();
		}
	}
	$ref = pdoRevision('centraliens', intval($_POST['id']));
	$pdo->exec('UPDATE centraliens SET ' .
		'_auteur = ' . (int) $_SESSION['user']['id'] . ', ' .
		'_ref = ' . (int) $ref . ', ' .
		'_date = NOW(), ' .
		'_message = "Ajout du VP tournoi", ' .
		//-------------//
		'vptournoi = ' . intval($_POST['sport']) . ' ' .
		'WHERE ' .
		'id = ' . intval($_POST['id']));
} elseif (
	!empty($_POST['delete']) &&
	empty($vpTournois) &&
	is_numeric($_POST['id'])
) {
	$ref = pdoRevision('centraliens', intval($_POST['id']));
	$pdo->exec('UPDATE centraliens SET ' .
		'_auteur = ' . (int) $_SESSION['user']['id'] . ', ' .
		'_ref = ' . (int) $ref . ', ' .
		'_date = NOW(), ' .
		'_message = "Suppression du VP tournoi", ' .
		//-------------//
		'vptournoi = NULL ' .
		'WHERE ' .
		'id = ' . intval($_POST['id']));
}

if (empty($vpTournois))
	$vps = $pdo->query('SELECT c.id, c.vptournoi, u.nom, u.prenom ' .
		'FROM centraliens as c ' .
		'JOIN utilisateurs u on c.id_utilisateur = u.id AND u._etat = "active" ' .
		'WHERE c._etat = "active" ' .
		'AND c.vptournoi IS NOT NULL ' .
		'ORDER BY u.nom ASC')
		->fetchAll(PDO::FETCH_ASSOC);


//Inclusion du bon fichier de template
require DIR . 'templates/admin/tournois/liste.php';
