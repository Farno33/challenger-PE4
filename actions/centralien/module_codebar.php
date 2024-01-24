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

$data = sygma([
	'login' 	=> $user['login'],
	'uid'		=> $options['uid']]);

$uid = $options['uid'];

if (null === $data || !empty($data->error))
	$data = new stdClass;

if (empty($data->ventes))
	$data->ventes = [];

if (empty($data->solde))
	$data->solde = [];

if (empty($data->recharges))
	$data->recharges = [];

require DIR.'templates/centralien/depenses.php';