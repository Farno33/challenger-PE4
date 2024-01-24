<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/admin/module_ecoles.php *************************/
/* Supervision du module des Ecoles ************************/
/* *********************************************************/
/* Dernière modification : le 20/11/14 *********************/
/* *********************************************************/


//Liste des actions possibles
$actionsModule = [
	'message' 			=> 'Message',
	'liste'				=> 'Liste des Ecoles',
	'tarification'		=> 'Tarification',
	'visibilite'		=> 'Visibilité Tarifs',
	'erreurs'			=> 'Erreurs'];


//On récupère l'action désirée par l'utilisateur
$action = !empty($args[2][0]) ? $args[2][0] : 'liste';
if (!in_array($action, array_keys($actionsModule)) &&
	!intval($action))
	die(require DIR.'templates/_error.php');


//On recupère l'école si une édition est demandée
if (intval($action)) {
	$id_ecole = $action;
	$ecole = $pdo->query('SELECT '.
			'e.*, '.
			'i.token, '.
			'i.width, '.
			'i.height, '.
			'(SELECT COUNT(p1.id) FROM participants AS p1 WHERE p1.id_ecole = e.id AND p1._etat = "active") AS nb_inscriptions, '.
			'(SELECT COUNT(p2.id) FROM participants AS p2 WHERE p2.id_ecole = e.id AND p2._etat = "active" AND p2.sportif = 1) AS nb_sportif, '.
			'(SELECT COUNT(p3.id) FROM participants AS p3 WHERE p3.id_ecole = e.id AND p3._etat = "active" AND p3.pompom = 1) AS nb_pompom, '.
			'(SELECT COUNT(p4.id) FROM participants AS p4 WHERE p4.id_ecole = e.id AND p4._etat = "active" AND p4.fanfaron = 1) AS nb_fanfaron, '.
			'(SELECT COUNT(p5.id) FROM participants AS p5 WHERE p5.id_ecole = e.id AND p5._etat = "active" AND p5.cameraman = 1) AS nb_cameraman, '.
			'(SELECT COUNT(p6.id) FROM participants AS p6 WHERE p6.id_ecole = e.id AND p6._etat = "active" AND p6.pompom = 1 AND p6.sportif = 0) AS nb_pompom_nonsportif, '.
			'(SELECT COUNT(p7.id) FROM participants AS p7 WHERE p7.id_ecole = e.id AND p7._etat = "active" AND p7.fanfaron = 1 AND p7.sportif = 0) AS nb_fanfaron_nonsportif, '.
			'(SELECT COUNT(p10.id) FROM participants AS p10 WHERE p10.id_ecole = e.id AND p10._etat = "active" AND p10.cameraman = 1 AND p10.sportif = 0) AS nb_cameraman_nonsportif, '.
			'(SELECT COUNT(p8.id) FROM participants AS p8 JOIN tarifs_ecoles AS te8 ON te8.id = p8.id_tarif_ecole AND te8._etat = "active" JOIN tarifs AS t8 ON t8.id = te8.id_tarif AND t8.logement = 1 AND t8._etat = "active" WHERE p8.id_ecole = e.id AND p8.sexe = "f" AND p8._etat = "active") AS nb_filles_logees, '.
			'(SELECT COUNT(p9.id) FROM participants AS p9 JOIN tarifs_ecoles AS te9 ON te9.id = p9.id_tarif_ecole AND te9._etat = "active" JOIN tarifs AS t9 ON t9.id = te9.id_tarif AND t9.logement = 1 AND t9._etat = "active" WHERE p9.id_ecole = e.id AND p9.sexe = "h" AND p9._etat = "active") AS nb_garcons_loges '.
		'FROM ecoles AS e '.
		'LEFT JOIN images AS i ON '.
			'i.id = e.id_image AND '.
			'i._etat = "active" '.
		'WHERE '.
			'e._etat = "active" AND '.
			'e.id = '.(int) $id_ecole)
		or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
	$ecole = $ecole->fetch(PDO::FETCH_ASSOC);

	$quotas = $pdo->query('SELECT '.
			'quota, '.
			'valeur, '.
			'id '.
		'FROM quotas_ecoles '.
		'WHERE '.
			'id_ecole = '.(int) $id_ecole.' AND '.
			'_etat = "active"')
		->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);


	if (empty($ecole['id']))
		die(require DIR.'templates/_error.php');


	die(require DIR.'actions/admin/ecoles/action_edition.php');
}

//On insére le module concerné
require DIR.'actions/admin/ecoles/action_'.$action.'.php';
