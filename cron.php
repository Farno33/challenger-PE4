<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/admin/ecoles/action_envoi.php *******************/
/* Envoi et stats emails pour une école ********************/
/* *********************************************************/
/* Dernière modification : le 16/01/16 *********************/
/* *********************************************************/

set_time_limit(-1);
ini_set('max_execution_time', 0);


if (!defined('DIR')) {
	//Quelques constantes pour les routes et les inclusions
	define('NO_LOGIN', true);
	define('DIR', dirname(__FILE__).'/');
	define('DIR_APP', substr(dirname($_SERVER['PHP_SELF']), -1) == '/' ? '' : dirname($_SERVER['PHP_SELF']));


	//Inclusion des fichiers nécessaires au fonctionnement général du projet
	require DIR.'includes/_includes.php';
}

$taches = $pdo->query('SELECT '.
		'id, '.
		'script, '.
		'periodicite '.
	'FROM taches '.
	'WHERE '.
		'_etat = "active" AND '.
		'(periodicite = "test" OR '.
			'execution IS NULL OR '.
			'DATE_ADD(execution, INTERVAL 50 SECOND) <= NOW()) AND '.
		'active = 1')
	->fetchAll(PDO::FETCH_ASSOC);

$update = $pdo->prepare('UPDATE taches SET '.
		'execution = NOW() '.
	'WHERE id = :id');

$hasTest = false;
$toExec = [];
foreach ($taches as $tache) {
	if ($tache['periodicite'] == 'test') {
		$hasTest = true;
		$update->execute([
			':id' => $tache['id']]);
		continue;
	}

	if (!isDueCronTab($tache['periodicite']))
		continue;

	$tache['script'] = secureFile($tache['script']);
	if (empty($tache['script']) ||
		!file_exists(DIR.'cron/'.$tache['script']))
		continue; 

	$update->execute([
		':id' => $tache['id']]);

	$toExec[] = $tache['script'];
}

if (!$hasTest) {
	$pdo->exec('INSERT INTO taches SET '.
		'_auteur = NULL, '.
		'_date = NOW(), '.
		'_message = "Ajout tâche test CRON", '.
		//----------//
		'nom = "Test CRON", '.
		'periodicite = "test", '.
		'script = "", '.
		'active = 1, '.
		'execution = NOW()');
}

if(DEBUG_ACTIVE) var_dump($toExec);
foreach ($toExec as $script) {
	if(DEBUG_ACTIVE){
		echo $script.'>'.PHP_EOL;
		require DIR.'cron/'.$script;
	} else  {
		ob_start();
		require DIR.'cron/'.$script;
		ob_get_clean();
	}
}
