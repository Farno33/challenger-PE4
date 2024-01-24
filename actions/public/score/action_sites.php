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


//On récupère l'indice du champ concerné
if ((!empty($_POST['delete']) || 
	!empty($_POST['edit'])) &&
	isset($_POST['id']) &&
	is_array($_POST['id']))
	$i = array_search(empty($_POST['delete']) ?
		$_POST['edit'] :
		$_POST['delete'],
		$_POST['id']);



$sites = $pdo->query('SELECT '.
		's.id, '.
		's.nom, '.
		's.description, '.
		's.latitude, '.
		's.longitude, '.
		'(SELECT COUNT(m.id) FROM matchs AS m WHERE '.
				'm.id_site = s.id AND '.
				'm._etat = "active") AS nb_matchs '.
	'FROM sites AS s '.
	'WHERE s._etat = "active" '.
	'ORDER BY s.nom ASC') 
	->fetchAll(PDO::FETCH_ASSOC);



//Inclusion du bon fichier de template
require DIR.'templates/public/score/sites.php';
