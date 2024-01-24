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
	'ecrire'		=> 'Ecrire',
	'publipostage'	=> 'Publipostage',
	'conversations'	=> 'Conversations',
	'envois'		=> 'Envois',
	'modeles'		=> 'Modèles',
];



//On récupère l'action désirée par l'utilisateur
$action = !empty($args[2][0]) ? $args[2][0] : 'conversations';
if (!in_array($action, array_keys($actionsModule)) &&
	!preg_match('`^modele_([1-9][0-9]*)$`', $action) &&
	!preg_match('`^message_([1-9][0-9]*)$`', $action) &&
	!preg_match('`^conversation_(recu_|envoi_)?([1-9][0-9]*)$`', $action) &&
	!preg_match('`^(recu|envoi)_([1-9][0-9]*)$`', $action))
	die(require DIR.'templates/_error.php');


if (preg_match('`^modele_([1-9][0-9]*)$`', $action)) {
	$id_modele = str_replace('modele_', '', $action);
	$action = 'modeles';
}

if (preg_match('`^conversation_(recu_|envoi_)?([1-9][0-9]*)$`', $action)) {
	$id_conversation = str_replace('conversation_', '', $action);
	$action = 'conversations';
}

if (preg_match('`^(recu|envoi)_([1-9][0-9]*)$`', $action)) {
	$id_message = $action;
	$action = 'message';
}

if (preg_match('`^message_([1-9][0-9]*)$`', $action)) {
	$id_message = str_replace('message_', '', $action);
	$action = 'envois';
}


$mailOk = $pdo->query('SELECT MAX(_date) AS _date '.
	'FROM recus WHERE '.
		'type = "email" AND '.
		'`from` LIKE "%'.EMAIL_MAIL.'%" AND '.
		'titre LIKE "test%" AND '.
		'message LIKE "test%"')
	->fetch(PDO::FETCH_ASSOC);

$smsOk = $pdo->query('SELECT MAX(_date) AS _date '.
	'FROM recus WHERE '.
		'type = "sms" AND '.
		'`from` LIKE "%'.getPhone(SMS_PHONE).'%" AND '.
		'(message LIKE "test%" OR message LIKE "challenge test%")')
	->fetch(PDO::FETCH_ASSOC);

$now = new DateTime();

if (!empty($mailOk['_date'])) {
	$date = new DateTime($mailOk['_date']);
	$date->add(new DateInterval('PT1H'));
	$mailOk = $date >= $now;
} else
	$mailOk = false;


if (!empty($smsOk['_date'])) {
	$date = new DateTime($smsOk['_date']);
	$date->add(new DateInterval('PT1H'));
	$smsOk = $date >= $now;
} else
	$smsOk = false;



//On insére le module concerné
require DIR.'actions/admin/communication/action_'.$action.'.php';
