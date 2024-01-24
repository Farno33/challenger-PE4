<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/public/logout.php *******************************/
/* Gére la déconnexion dans les différents modules *********/
/* *********************************************************/
/* Dernière modification : le 20/11/14 *********************/
/* *********************************************************/

$where = ['0'];
foreach ($mes_equipes as $eid => $equipe)
	$where[] = 'eq.id = ' . $eid;

$sportifs = $pdo->query('SELECT ' .
	'eq.id, ' .
	'p.id AS pid, ' .
	'p.nom, ' .
	'p.prenom, ' .
	'p.sexe, ' .
	'CASE WHEN p.id = eq.id_capitaine THEN 1 ELSE 0 END AS capitaine ' .
	'FROM participants AS p ' .
	'JOIN sportifs AS sp ON ' .
	'sp.id_participant = p.id AND ' .
	'sp._etat = "active" ' .
	'JOIN equipes AS eq ON ' .
	'eq.id = sp.id_equipe AND ' .
	'eq._etat = "active" ' .
	'WHERE ' .
	'eq._etat = "active" AND ' .
	'(' . implode(' OR ', $where) . ') ' .
	'ORDER BY ' .
	'p.nom ASC, ' .
	'p.prenom ASC')
	->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_GROUP);


if (
	!empty($_POST['delete']) &&
	!empty($mes_equipes[$_POST['id_equipe']]) &&
	$mes_equipes[$_POST['id_equipe']]['cap'] == $options['id_participant'] &&
	!empty($sportifs[$_POST['id_equipe']]) &&
	false !== array_search($_POST['delete'], array_column($sportifs[$_POST['id_equipe']], 'pid'))
) {
	$bye = (int)$_POST['delete'];
	$eq = (int)$_POST['id_equipe'];

	if ($options['id_participant'] == $bye) {
		delete_equipe($eq);
		unset($mes_equipes[$eq]);
	} else {
		$equipier = $pdo->query('SELECT  ' .
			'id, ' .
			'id_participant, ' .
			'(SELECT COUNT(*) FROM sportifs as sp where sp.id_participant = sportifs.id_participant AND sp._etat = "active") AS nb ' .
			'FROM sportifs  ' .
			'WHERE  ' .
			'id_participant = ' . (int)$bye . ' AND ' .
			'id_equipe = ' . (int)$eq . ' AND ' .
			'_etat = "active"')
			->fetchAll(PDO::FETCH_ASSOC);

		delete_equipier($equipier[0]);
		unset($sportifs[$eq][array_search($bye, array_column($sportifs[$eq], 'pid'))]);
	}
}

if (
	(!empty($_POST['lock']) &&
	!empty($mes_equipes[$_POST['lock']]) &&
	$mes_equipes[$_POST['lock']]['cap'] == $options['id_participant']) || 
	(!empty($_POST['unlock']) &&
	!empty($mes_equipes[$_POST['unlock']]) &&
	$mes_equipes[$_POST['unlock']]['cap'] == $options['id_participant'])
) {
	$lock = !empty($_POST['lock']);
	$eq = $lock ? (int)$_POST['lock'] : (int)$_POST['unlock'];

	$ref = pdoRevision('equipes', $eq);
	$pdo->exec('UPDATE equipes SET ' .
		'_message = "'.($lock ? 'locked' : 'open').'", ' .
		'_ref = ' . $ref . ' ' .
		'WHERE ' .
		'id = ' . $eq . ' AND ' .
		'_etat = "active"');

	die($lock ? 'locked' : 'open');
}

require DIR . 'templates/centralien/equipes.php';
