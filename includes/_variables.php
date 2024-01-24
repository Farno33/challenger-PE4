<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* includes/_variables.php *********************************/
/* Mise en place de certaines données dynamiques ***********/
/* *********************************************************/
/* Dernière modification : le 20/11/14 *********************/
/* *********************************************************/


//Mode Debug (Les fichiers commencants par .ht sont innacessibles)
error_reporting(-1);
ini_set('display_errors', DEBUG_ACTIVE);
ini_set('display_startup_errors', DEBUG_ACTIVE);
ini_set('log_errors', 1);
ini_set('error_log', DIR.'.hterrlog');


//Ouvertue de la base de données
$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS);

/**
 * Fonction de révision d'une table
 * 
 * @param  string $table Table à réviser
 * @param  int $id     Identifiant de la ligne à réviser
 * @param  null|string $custom  Condition personnalisée (sera inclus apres where à la place de la clause sur l'id)
 * @param  string $etat    Etat de la révision ("revision" par defaut)
 * @return int           Identifiant de la révision
 */
function pdoRevision($table, $id, $custom = null, $etat = "revision") {
	global $pdo;
	$pdo->exec('CREATE TEMPORARY TABLE _temp_ SELECT * FROM `'.$table.'` WHERE '.(empty($custom) ? 'id = '.$id : $custom));
	$pdo->exec('UPDATE _temp_ SET id = 0, _etat = "'.$etat.'"');
	$pdo->exec('INSERT INTO `'.$table.'` SELECT * FROM _temp_');
	
	$ref = $pdo->lastInsertId();
	$pdo->exec('DROP TABLE _temp_');
	return $ref;
}
