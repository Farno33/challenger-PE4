<?php

//Message reçu et sms activés
if (!SMS_ACTIVE ||
	empty($_POST['number']) ||
    !isset($_POST['message']) ||
   	empty($_POST['signature']) ||
    $_POST['signature'] != md5(SMS_SALT_RECEIVE.$_POST['number'].$_POST['message'])) {
	die();
}

//Formatage des données
$content = trim($_POST['message']);
$num = getPhone($_POST['number']);


if ($num == getPhone(SMS_PHONE) &&
	strpos($content, 'test') === 0) {
	$exists = $pdo->query('SELECT id '.
		'FROM recus WHERE '.
			'`from` = "'.getPhone(SMS_PHONE).'" AND '.
			'(message LIKE "test%" OR message LIKE "challenge test%")')
		->fetch(PDO::FETCH_ASSOC);

	if (!empty($exists)) {
		$pdo->exec('UPDATE recus SET '.
				'_date = NOW() '.
			'WHERE '.
				'id = '.$exists['id']);

		die('1');
	}
}

$people = $pdo->query('SELECT id '.
	'FROM participants '.
	'WHERE '.
		'_etat = "active" AND '.
		'telephone = "'.secure($num).'"')
	->fetch(PDO::FETCH_ASSOC);


//sendSms('0688264139', 'Un nouvel sms est arrivé');
$pdo->exec('INSERT INTO recus SET '.
	'type = "sms", '.
	'`from` = "'.secure($num).'", '.
	'id_participant = '.(!empty($people) ? (int) $people['id'] : 'NULL').', '.
	'message = "'.secure($content).'", '.
	'ouvert = 0, '.
	'_date = NOW()');

//Rajouter ici la gestion des commandes par sms
//Les sms doivent-ils entrer en compte dans la liste des conversations sur l'interface graphique ???

die('1');