<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/admin/module_configurations.php *****************/
/* Supervision du module des constantes ********************/
/* *********************************************************/
/* Dernière modification : le 17/02/14 *********************/
/* *********************************************************/


//Liste des actions possibles
$actionsModule = [
	'liste'		=> 'Liste',
	'planning'	=> 'Planning',
	'sites'		=> 'Sites',
	'points' 	=> 'Points'
];



//On récupère l'action désirée par l'utilisateur
$action = !empty($args[2][0]) ? $args[2][0] : 'liste';
if (!in_array($action, array_keys($actionsModule)) &&
	!intval($action) && 
	!preg_match('`^phase_([1-9][0-9]*)$`', $action))
	die(require DIR.'templates/_error.php');

//On recupère l'école si une édition est demandée
if (is_numeric($action)) {
	$id_sport = $action;
	die(require DIR.'actions/public/score/action_tournoi.php');
} else if (strpos($action, 'phase') !== false) {
	$id_phase = preg_replace('/^phase_/', '', $action);
	die(require DIR.'actions/public/score/action_phase.php');
} 


//On insére le module concerné
require DIR.'actions/public/score/action_'.$action.'.php';
