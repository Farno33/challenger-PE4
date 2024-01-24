<?php

$actions = [
	'getTimestamp' 		=> 'Return current timestamp on Challenger server', 
	'testConnection'	=> 'Return a simple welcome to test connection',
	'listActions'		=> 'Return all actions available in general module',
	'getUserData' 		=> 'Return user\'s pieces of data associated to public token',
	'T_generateToken' 	=> 'Return a generated token for an unique request',
	'S_testToken'		=> 'Return a simple welcome (secured action)',
	'T_testTimestamp'	=> 'Return a simple welcome (timed action)'];


function general_listActions() {
	global $actions;
	returnJson(0, ['actions' => $actions]);
}

function general_getTimestamp() {
	returnJson(0, ['timestamp' => time()]);
}

function general_testConnection($data, $pdo, $user) {
	returnJson(0, ['message' => 'Welcome '.$user['prenom'].' '.$user['nom']]);
}

function general_getUserData($data, $pdo, $user) {
	$user = [
		'login' 		=> $user['login'],
        'nom'			=> $user['nom'],
        'prenom'		=> $user['prenom'],
        'email'			=> $user['email'],
        'telephone'		=> $user['telephone'],
        'cas'			=> $user['cas'],
        'responsable'	=> $user['responsable']];
	returnJson(0, ['user' => $user]);
}

function general_T_generateToken($data, $pdo, $user) {
	$token = sha1(uniqid());
	$pdo->exec('INSERT INTO tokens SET '.
			'id_utilisateur = '.(int) $user['id'].', '.
			'token = "'.$token.'", '.
			'expire = DATE_ADD(NOW(), INTERVAL '.VALIDITY_TOKEN.' SECOND)');

	$expire = new DateTime();
	$expire->add(new DateInterval('PT'.VALIDITY_TOKEN.'S')); 

	returnJson(0, ['token' => $token, 'expire' => $expire->format('Y-m-d H:i:s')]);
}

function general_S_testToken($data, $pdo, $user) {
	returnJson(0, ['message' => 'Welcome on secured action '.$user['prenom'].' '.$user['nom']]);
}

function general_T_testTimestamp($data, $pdo, $user) {
	returnJson(0, ['message' => 'Welcome on timed action '.$user['prenom'].' '.$user['nom']]);
}