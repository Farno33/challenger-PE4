<?php

$private_token = "90795a0ffaa8b88c0e250546d8439bc9c31e5a5e"; 
$public_token = "aaf4c61ddcc5e8a2dabede0f3b482cd9aea9434d";
$url_api = "http://challenger.challenge-centrale-lyon.fr/api";


function calculateSignature($data) {
	global $private_token;

	unset($data['signature']);

	ksort($data);
	return sha1(http_build_query($data).'&'.$private_token);
}

function httpPost($url, $post = []) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
	$response = curl_exec($ch);
	return !curl_errno($ch) ? $response : curl_error($ch);
}

function api($data) {
	global $public_token;
	global $url_api;

	$data['public_token'] = $public_token;
	$data['signature'] = calculateSignature($data);

	return json_decode(httpPost($url_api, $data));
}

$req = api([
	'module'	=> 'ecole',
	'action'	=> 'getEcoles']);

if (!isset($req->error) || !empty($req->error))
	die('<b>Error : </b>' . (!empty($req->message) ? $req->message : 'Unknown error'));


echo '<table>';
foreach ($req->ecoles as $ecole) {
	echo '<tr>'.
		'<td>'.$ecole->id.'</td>'.
		'<td>'.$ecole->nom.'</td>'.
		'<td>'.($ecole->ecole_lyonnaise ? 'Oui' : 'Non').'</td>'.
		'</tr>';
}
echo '</table>';
