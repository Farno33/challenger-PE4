<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/admin/module_competition.php ********************/
/* Supervision du module de Compétition ********************/
/* *********************************************************/
/* Dernière modification : le 23/02/15 *********************/
/* *********************************************************/


//Liste des actions à afficher
$actionsModule = [
	'extract'					=> 'Extract',
	'sports' 					=> 'Sports',
	'participants'				=> 'Participants',
	'retards'					=> 'Retards',
	'sportifs'					=> 'Sportifs',
	'sans_sport'				=> 'Sans Sport',
	'multiples_sports'			=> 'Multiples Sports',
	'pompoms'					=> 'Pompoms',
	'fanfarons'					=> 'Fanfarons',
	'cameramans'				=> 'Cameramans',
	'capitaines'				=> 'Capitaines',
];

//Liste des actions possibles
$actionsExtended = array_merge(
	array_keys($actionsModule), [
		'participants_ecoles',
		'retards_ecoles',
		'sportifs_sports',
		'sportifs_sports_ecoles',
		'sportifs_sports_equipes',
		'sportifs_ecoles',
		'sportifs_ecoles_sports',
		'sportifs_ecoles_equipes',
		'sans_sport_ecoles',
		'multiples_sports_ecoles',
		'pompoms_ecoles',
		'fanfarons_ecoles',
		'cameramans_ecoles',
		'capitaines_sports',
		'capitaines_ecoles']);


//On récupère l'action désirée par l'utilisateur
$action = !empty($args[2][0]) ? $args[2][0] : 'sports';
if (!in_array($action, $actionsExtended))
	die(require DIR.'templates/_error.php');


//On insére le module concerné
require DIR.'actions/admin/competition/action_'.$action.'.php';
