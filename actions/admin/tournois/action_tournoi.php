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

$typesPhases = ['elimination', 'championnat', 'poules', 'series'];
$typesPhasesPoints = ['championnat', 'poules'];

$sport = $pdo->query('SELECT '.
	's.id, '.
	's.sport, '.
	's.sexe, '.
	's.individuel, '.
	's.tournoi_initie, '.
	's.infos, '.
	'p.id AS poid, '.
	'p.coeff, '.
	'p.id_concurrent1, '.
	'p.id_concurrent2, '.
	'p.id_concurrent3, '.
	'p.id_concurrent3ex, '.
	'p.ex_12, '.
	'p.ex_23, '.
	'p.ex_3, '.
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
'LEFT JOIN podiums AS p ON '.
	'p._etat = "active" AND '.
	'p.id_sport = s.id '.
'WHERE '.
	's._etat = "active" AND '.
	's.id = '.(int) $id_sport)
->fetch(PDO::FETCH_ASSOC);


if (empty($sport['id']))
	die(require DIR.'templates/_error.php');


if (!empty($_POST['init']) &&
	$sport['nb_concurrents']) {
	$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
	$ref = pdoRevision('sports', $sport['id']);
	$pdo->exec('UPDATE sports SET '.
			'_auteur = '.(int) $_SESSION['user']['id'].', '.
			'_date = NOW(), '.
			'_message = "Initialisation du tournoi", '.
			'_ref = '.(int) $ref.', '.
			//----------//
			'tournoi_initie = 1 '.
		'WHERE '.
			'id = '.(int) $sport['id']);
	$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
	$sport['tournoi_initie'] = 1;
}


$phases_ = $pdo->query($phases_sql = 'SELECT '.
		'ph.id AS pid, '.
		'ph.id, '.
		'ph.cloturee, '.
		'ph.nom, '.
		'ph.type, '.
		'ph.id_phase_suivante, '.
		'phs.nom AS nom_suivante, '.
		'ph.points_victoire, '.
		'ph.points_defaite, '.
		'ph.points_nul, '.
		'ph.points_forfait, '.
		'ph.commentaire, '.
		'ph.cloturee, '.
		'(SELECT COUNT(pha.id) '.
			'FROM phases AS pha '.
			'WHERE '.
				'pha._etat = "active" AND '.
				'pha.id_sport = ph.id_sport AND '.
				'pha.id_phase_suivante = ph.id) AS nb_precedentes, '.
		'(SELECT COUNT(phc.id) '.
			'FROM phases AS phc '.
			'WHERE '.
				'phc._etat = "active" AND '.
				'phc.id_sport = ph.id_sport AND '.
				'phc.id_phase_suivante = ph.id AND '.
				'phc.cloturee <> 1) AS nb_precedentes_ouvertes, '.
		'(SELECT COUNT(gr.id) '.
			'FROM groupes AS gr '.
			'WHERE '.
				'gr._etat = "active" AND '.
				'gr.id_phase = ph.id) AS nb_groupes, '.
		'(SELECT COUNT(phco.id) '.
			'FROM phases_concurrents AS phco '.
			'WHERE '.
				'phco._etat = "active" AND '.
				'phco.id_phase = ph.id AND '.
				'phco.qualifie = 1) AS nb_qualifies '.
	'FROM phases AS ph '.
	'LEFT JOIN phases AS phs ON '.
		'phs.id = ph.id_phase_suivante AND '.
		'phs._etat = "active" '.
	'WHERE '.
		'ph.id_sport = '.(int) $sport['id'].' AND '.
		'ph._etat = "active" '.
	'ORDER BY ph.nom ASC')
	->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);


if (!$sport['tournoi_initie'] &&
	!empty($_POST['add_phase']) &&
	!empty($_POST['type']) &&
	in_array($_POST['type'], $typesPhases) &&
	!empty($_POST['nom']) &&
	isset($_POST['pts_g']) &&
	isset($_POST['pts_n']) &&
	isset($_POST['pts_p']) &&
	isset($_POST['pts_f']) &&
	isset($_POST['next']) && (
		empty($_POST['next']) ||
		in_array($_POST['next'], array_keys($phases_)))) {
	$pdo->exec('set FOREIGN_KEY_CHECKS = 0');
	$pdo->exec('INSERT INTO phases SET '.
		'_auteur = '.(int) $_SESSION['user']['id'].', '.
		'_date = NOW(), '.
		'_message = "Ajout de la phase", '.
		//----------//
		'id_sport = '.$id_sport.', '.
		'points_victoire = '.(int) $_POST['pts_g'].', '.
		'points_nul = '.(int) $_POST['pts_n'].', '.
		'points_defaite = '.(int) $_POST['pts_p'].', '.
		'points_forfait = '.(int) $_POST['pts_f'].', '.
		'id_phase_suivante = '.(!empty($_POST['next']) ? (int) $_POST['next'] : 'NULL').', '.
		'type = "'.secure($_POST['type']).'", '.
		'nom = "'.secure($_POST['nom']).'"');
	$pdo->exec('set FOREIGN_KEY_CHECKS = 1');

	$phases_ = $pdo->query($phases_sql)
		->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);
}


if (!$sport['tournoi_initie'] &&
	!empty($_POST['edit_phase']) &&
	!empty($_POST['id']) &&
	in_array($_POST['id'], array_keys($phases_)) &&
	!empty($_POST['type']) &&
	in_array($_POST['type'], $typesPhases) &&
	!empty($_POST['nom']) &&
	isset($_POST['pts_g']) &&
	isset($_POST['pts_n']) &&
	isset($_POST['pts_p']) &&
	isset($_POST['pts_f']) &&
	isset($_POST['next']) && (
		empty($_POST['next']) ||
		in_array($_POST['next'], array_keys($phases_)))) {
	$ref = pdoRevision('phases', $_POST['id']);
	$pdo->exec('UPDATE phases SET '.
			'_auteur = '.(int) $_SESSION['user']['id'].', '.
			'_date = NOW(), '.
			'_message = "Modification de la phase", '.
			'_ref = '.(int) $ref.', '.
			//----------//
			'points_victoire = '.(int) $_POST['pts_g'].', '.
			'points_nul = '.(int) $_POST['pts_n'].', '.
			'points_defaite = '.(int) $_POST['pts_p'].', '.
			'points_forfait = '.(int) $_POST['pts_f'].', '.
			'id_phase_suivante = '.(!empty($_POST['next']) ? (int) $_POST['next'] : 'NULL').', '.
			'type = "'.secure($_POST['type']).'", '.
			'nom = "'.secure($_POST['nom']).'" '.
		'WHERE '.
			'id = '.(int) $_POST['id']);

	//La flemme de modifié les champs un à un, on recharge
	$phases_ = $pdo->query($phases_sql)
		->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);
}


if (!$sport['tournoi_initie'] &&
	!empty($_POST['del_phase']) &&
	!empty($_POST['id']) &&
	in_array($_POST['id'], array_keys($phases_)) &&
	$phases_[$_POST['id']]['nb_precedentes'] == 0) {
	$ref = pdoRevision('phases', $_POST['id']);
	$pdo->exec('UPDATE phases SET '.
			'_auteur = '.(int) $_SESSION['user']['id'].', '.
			'_date = NOW(), '.
			'_message = "Suppression de la phase", '.
			'_ref = '.(int) $ref.', '.
			'_etat = "desactive" '.
			//----------//
		'WHERE '.
			'id = '.(int) $_POST['id']);

	$phases_ = $pdo->query($phases_sql)
		->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);
}

if (!$sport['individuel']) {
	$concurrents = $pdo->query('SELECT '.
			'eq.id, '.
			'eq.label, '.
			'e.nom AS enom, '.
			'co.id AS coid '.
		'FROM equipes AS eq '.
		'JOIN ecoles_sports AS es ON '.
			'eq.id_ecole_sport = es.id AND '.
			'es._etat = "active" '.
		'JOIN ecoles AS e ON '.
			'e.id = es.id_ecole AND '.
			'e._etat = "active" '.
		'LEFT JOIN concurrents AS co ON '.
			'co.id_equipe = eq.id AND '.
			'co.id_sport = es.id_sport AND '.
			'co._etat = "active" '.
		'WHERE '.
			'eq._etat = "active" AND '.
			'es.id_sport = '.(int) $sport['id'].' '.
		'ORDER BY e.nom ASC, eq.label ASC')
		->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);
} else {
	$concurrents = $pdo->query('SELECT '.
			'sp.id, '.
			'p.nom, '.
			'p.prenom, '.
			'p.sexe, '.
			'e.nom AS enom, '.
			'co.id AS coid '.
		'FROM sportifs AS sp '.
		'JOIN participants AS p ON '.
			'p.id = sp.id_participant AND '.
			'p._etat = "active" '.
		'JOIN equipes AS eq ON '.
			'eq.id = sp.id_equipe AND '.
			'eq._etat = "active" '.
		'JOIN ecoles_sports AS es ON '.
			'eq.id_ecole_sport = es.id AND '.
			'es._etat = "active" '.
		'JOIN ecoles AS e ON '.
			'e.id = es.id_ecole AND '.
			'e._etat = "active" '.
		'LEFT JOIN concurrents AS co ON '.
			'co.id_sportif = sp.id AND '.
			'co.id_sport = es.id_sport AND '.
			'co._etat = "active" '.
		'WHERE '.
			'sp._etat = "active" AND '.
			'es.id_sport = '.(int) $sport['id'].' '.
		'ORDER BY e.nom ASC, p.nom ASC, p.prenom ASC')
		->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);
}


if (!empty($_POST['podium']) &&
	isset($_POST['coeff']) && (
		empty($_POST['coeff']) ||
		intval($_POST['coeff'])) &&
	isset($_POST['id_concurrent1']) &&
	isset($_POST['id_concurrent2']) &&
	isset($_POST['id_concurrent3']) &&
	isset($_POST['id_concurrent3ex']) && (
		empty($_POST['id_concurrent1']) ||
		in_array($_POST['id_concurrent1'], array_keys($concurrents)) &&
		!empty($concurrents[$_POST['id_concurrent1']]['coid'])) && (
		empty($_POST['id_concurrent2']) ||
		in_array($_POST['id_concurrent2'], array_keys($concurrents)) &&
		!empty($concurrents[$_POST['id_concurrent2']]['coid'])) && (
		empty($_POST['id_concurrent3']) ||
		in_array($_POST['id_concurrent3'], array_keys($concurrents)) &&
		!empty($concurrents[$_POST['id_concurrent3']]['coid'])) && (
		empty($_POST['id_concurrent3ex']) ||
		in_array($_POST['id_concurrent3ex'], array_keys($concurrents)) &&
		!empty($concurrents[$_POST['id_concurrent3ex']]['coid']))) {
	$sport['id_concurrent1'] = empty($_POST['id_concurrent1']) ? null : $concurrents[$_POST['id_concurrent1']]['coid'];
	$sport['id_concurrent2'] = empty($_POST['id_concurrent2']) ? null : $concurrents[$_POST['id_concurrent2']]['coid'];
	$sport['id_concurrent3'] = empty($_POST['id_concurrent3']) ? null : $concurrents[$_POST['id_concurrent3']]['coid'];
	$sport['id_concurrent3ex'] = empty($_POST['id_concurrent3ex']) ? null : $concurrents[$_POST['id_concurrent3ex']]['coid'];
	$sport['coeff'] = $_POST['coeff'];
	$sport['ex_12'] = !empty($_POST['ex_12']) ? '1' : '0';
	$sport['ex_23'] = !empty($_POST['ex_23']) ? '1' : '0';
	$sport['ex_3'] = !empty($_POST['ex_3']) ? '1' : '0';

	if (!empty($sport['poid'])) {
		$ref = pdoRevision('podiums', $sport['poid']);
		$pdo->exec('UPDATE podiums SET '.
				'_auteur = '.(int) $_SESSION['user']['id'].', '.
				'_date = NOW(), '.
				'_message = "Modification du podium", '.
				'_ref = '.(int) $ref.', '.
				//-------------//
				'id_concurrent1 = '.(empty($sport['id_concurrent1']) ? 'NULL' : (int) $sport['id_concurrent1']).', '.
				'id_concurrent2 = '.(empty($sport['id_concurrent2']) ? 'NULL' : (int) $sport['id_concurrent2']).', '.
				'id_concurrent3 = '.(empty($sport['id_concurrent3']) ? 'NULL' : (int) $sport['id_concurrent3']).', '.
				'id_concurrent3ex = '.(empty($sport['id_concurrent3ex']) ? 'NULL' : (int) $sport['id_concurrent3ex']).', '.
				'coeff = '.(int) $_POST['coeff'].', '.
				'ex_12 = '.$sport['ex_12'].', '.
				'ex_23 = '.$sport['ex_23'].', '.
				'ex_3 = '.$sport['ex_3'].' '.
			'WHERE '.
				'id = '.$sport['poid']);
	} else {
		$pdo->exec('INSERT INTO podiums SET '.
			'_auteur = '.(int) $_SESSION['user']['id'].', '.
			'_date = NOW(), '.
			'_message = "Insertion du podium", '.
			//----------//
			'id_sport = '.$sport['id'].', '.
			'id_concurrent1 = '.(empty($sport['id_concurrent1']) ? 'NULL' : (int) $sport['id_concurrent1']).', '.
			'id_concurrent2 = '.(empty($sport['id_concurrent2']) ? 'NULL' : (int) $sport['id_concurrent2']).', '.
			'id_concurrent3 = '.(empty($sport['id_concurrent3']) ? 'NULL' : (int) $sport['id_concurrent3']).', '.
			'id_concurrent3ex = '.(empty($sport['id_concurrent3ex']) ? 'NULL' : (int) $sport['id_concurrent3ex']).', '.
			'coeff = '.(int) $_POST['coeff'].', '.
			'ex_12 = '.$sport['ex_12'].', '.
			'ex_23 = '.$sport['ex_23'].', '.
			'ex_3 = '.$sport['ex_3']);
	}

}


if (!$sport['tournoi_initie'] &&
	isset($_GET['ajax']) &&
	!empty($_POST['concurrent']) &&
	isset($_POST['active']) && 
	in_array($_POST['concurrent'], array_keys($concurrents))) {

	if (!empty($concurrents[$_POST['concurrent']]['coid'])) {
		$ref = pdoRevision('concurrents', $concurrents[$_POST['concurrent']]['coid']);
		$pdo->exec('UPDATE concurrents SET '.
				'_auteur = '.(int) $_SESSION['user']['id'].', '.
				'_date = NOW(), '.
				'_message = "Suppression du concurrent", '.
				'_etat = "desactive", '.
				'_ref = '.(int) $ref.' '.
			'WHERE '.
				'id = '.$concurrents[$_POST['concurrent']]['coid']);
	} else {
		$pdo->exec('INSERT INTO concurrents SET '.
			'_auteur = '.(int) $_SESSION['user']['id'].', '.
			'_date = NOW(), '.
			'_message = "Ajout du concurrent", '.
			//----------//
			'id_sport = '.$sport['id'].', '.
			($sport['individuel'] ? 
				'id_sportif = '.(int) $_POST['concurrent'] : 
				'id_equipe = '.(int) $_POST['concurrent']));
	}

	die; //Pas besoin de les recharger
}

if (!empty($_POST['infos'])) {
	$ref = pdoRevision('sports', $sport['id']);
	$mod = $pdo->prepare('UPDATE sports SET '.
			'_auteur = '.(int) $_SESSION['user']['id'].', '.
			'_date = NOW(), '.
			'_message = "Modification des informations", '.
			'_ref = :ref, '.
			//----------//
			'infos = :infos '.
		'WHERE '.
			'id = :id');
	$mod->execute([
		':ref' => $ref,
		':infos' => $_POST['infos'],
		':id' => $sport['id']
	]);
	$sport['infos'] = $_POST['infos'];
}

$phases = [
	'initiales' => [], 
	'intermediaires' => [], 
	'finales' => []];

foreach ($phases_ as $phase) {
	if ($phase['nb_precedentes'] == 0)
		$phases['initiales'][] = $phase;

	else if (empty($phase['id_phase_suivante']) ||
		empty($phases_[$phase['id_phase_suivante']]))
		$phases['finales'][] = $phase;

	else
		$phases['intermediaires'][] = $phase;
}


if (!empty($_POST['delete_tournoi'])) {
	$id_sport = $sport['id'];

	$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
	$pdo->exec('DELETE FROM concurrents WHERE '.
			'id_sport = '.(int) $id_sport);
	$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

	$phases_del = $pdo->query('SELECT p.id '.
			'FROM phases AS p '.
			'WHERE '.
			'id_sport = '.(int) $id_sport);
	
	foreach ($phases_del as $phase) {
		$id_phase = $phase['id'];

		$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
		$pdo->exec('DELETE FROM groupes WHERE '.
			'id_phase = '.(int) $id_phase);
		$pdo->exec('DELETE FROM matchs WHERE '.
			'id_phase = '.(int) $id_phase);
		$pdo->exec('DELETE FROM phases_concurrents WHERE '.
			'id_phase = '.(int) $id_phase);
		$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
	}

	$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
	$pdo->exec('DELETE FROM phases WHERE '.
			'id_sport = '.(int) $id_sport);
	$pdo->exec('UPDATE sports SET tournoi_initie = 0 WHERE '.
			'id = '.(int) $id_sport);
	$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

	header('location:'.url((empty($vpTournois) ? 'admin/module/tournois' : 'centralien/vptournoi'), false, false));
}

//Inclusion du bon fichier de template
require DIR.'templates/admin/tournois/tournoi.php';
