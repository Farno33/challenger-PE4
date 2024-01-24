<?php

$private_token = "90795a0ffaa8b88c0e250546d8439bc9c31e5a5e"; 
$public_token = "aaf4c61ddcc5e8a2dabede0f3b482cd9aea9434d";
$local = LOCAL;
$url_api = $local ? "http://localhost/ECL/Projets/challenger/api" : "http://challenger.challenge-centrale-lyon.fr/api";


function calculateSignature($data) {
	global $private_token;

	unset($data['signature']);
	unset($data['_debug']);

	ksort($data);
	$sig = sha1(http_build_query($data).'&'.$private_token);
	return $sig;
}

function apiSimple($data) {
	global $url_api;

	return json_decode(http_post($url_api, $data));
}

function apiChallenger($data) {
	global $public_token;

	$data['public_token'] = $public_token;
	$data['signature'] = calculateSignature($data);

	return apiSimple($data);
}

if (empty($_SESSION['tokens'])) {
	if (!isset($_SESSION['difference'])) {
		$req1 = apiChallenger([
			'module'	=> 'general',
			'action'	=> 'getTimestamp']);

		if (!isset($req1->error) || !empty($req1->error))
			die('<b>Error on request 1 : </b>' . $req1->message);

		echo '<b>Req 1 : </b> '.$req1->timestamp.'<br />';
		$_SESSION['difference'] = time() - $req1->timestamp;
	}

	$req2 = apiChallenger([
		'module'	=> 'general',
		'timestamp' => isset($_SESSION['difference']) ? time() - $_SESSION['difference'] : $req1->timestamp,
		'action'	=> 'T_generateToken']);

	if (!isset($req2->error) || !empty($req2->error)) {
		unset($_SESSION['difference']);
		die('<b>Error on request 2 : </b>' . $req2->message);
	}

	echo '<b>Req 2 : </b> '.$req2->token.'<br />';
	$token = $req2->token;
} else 
	$token = array_shift($_SESSION['token']);

$req3 = apiChallenger([
	'module'	=> 'general',
	'action_token' => $token,
	'action'	=> 'S_testToken']);

if (!isset($req3->error) || !empty($req3->error))
	die('<b>Error on request 3 : </b>' . $req3->message);

echo '<b>Req 3 : </b> '.$req3->message.'<br />';
