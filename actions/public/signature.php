<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/public/signature.php ****************************/
/* Signature du RI et vérification des données *************/
/* *********************************************************/
/* Dernière modification : le 16/01/16 *********************/
/* *********************************************************/


$id_participant = !empty($args[1][0]) ? $args[1][0] : null;
$signature = !empty($args[2][0]) ? $args[2][0] : null;

if (empty($id_participant) ||
	!intval($id_participant) ||
	empty($signature))
	die(require DIR.'templates/_error.php');


$participant = $pdo->query('SELECT '.
		'p.*, '.
		'e.nom AS enom, '.
		'e.langue AS lang, '.
		'e.ecole_lyonnaise, '.
		't.nom AS tnom, '.
		't.description, '.
		't.logement, '.
		't.tarif, '.
		'p.recharge, '.
		'u.login, '.
		'(SELECT MAX(_date) FROM erreurs WHERE _etat = "active" AND id_participant) AS date_erreur, '.
		'(SELECT message FROM erreurs WHERE _etat = "active" AND id_participant = p.id ORDER BY _date DESC LIMIT 1) AS erreur, '.
		'(SELECT MAX(_date) FROM signatures WHERE id_participant = p.id) AS signature '.
		'FROM participants AS p '.
		'LEFT JOIN centraliens AS c ON '.
			'c.id_participant = p.id AND '.
			'c._etat = "active" '.
		'LEFT JOIN utilisateurs AS u ON '.
			'u.id = c.id_utilisateur AND '.
			'u._etat = "active" '.
		'LEFT JOIN ecoles AS e ON '.
			'e.id = p.id_ecole AND '.
			'e._etat = "active" '.
		'JOIN tarifs_ecoles AS te ON '.
			'te.id = p.id_tarif_ecole AND '.
			'te._etat = "active" '.
		'JOIN tarifs AS t ON '.
			't.id = te.id_tarif AND '.
			't._etat = "active" '.
	'WHERE p.id = '.(int) $id_participant)
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$participant = $participant->fetch(PDO::FETCH_ASSOC);


if (empty($participant))
	die(require DIR.'templates/_error.php');

$check = substr(sha1(APP_SEED.
	$participant['id_ecole'].'_'.
	$participant['id']), 0, 20);


if ($signature != $check)
	die(require DIR.'templates/_error.php');


unset($_SESSION['path']);
$cookie = empty($_COOKIE['signatures']) ? [] : json_decode($_COOKIE['signatures'], true);
$cookie = $cookie === null || !is_array($cookie) ? [] : $cookie; 

if (empty($user['id'])) {
	if (empty($cookie[$id_participant]))
		$cookie[$id_participant] = $check;

	setcookie('signatures', json_encode($cookie), strtotime("+1 month"), url('', false, false));
}

if (isset($_GET['codebar'])) {
	$data = sygma([
		'login' 	=> $participant['login'],
		'uid'		=> $participant['uid']]);
	$uid = $participant['uid'];
	if (null === $data || !empty($data->error))
		$data = new stdClass();

	if (empty($data->ventes))
		$data->ventes = [];

	if (empty($data->recharges))
		$data->recharges = [];

	if (empty($data->solde))
		$data->solde = [];

	require DIR.'templates/public/depenses.php';
	die;
}


if ($participant['sportif']) {

	$sportifs = $pdo->query('SELECT '.
			's.sport, '.
			's.sexe, '.
			'eq.id_capitaine '.
		'FROM sportifs AS sp '.
		'JOIN equipes AS eq ON '.
			'eq.id = sp.id_equipe AND '.
			'eq._etat = "active" '.
		'JOIN ecoles_sports AS es ON '.
			'es.id = eq.id_ecole_sport AND '.
			'es._etat = "active" '.
		'JOIN sports AS s ON '.
			's.id = es.id_sport AND '.
			's._etat = "active" '.
		'WHERE '.
			'sp.id_participant = '.(int) $id_participant.' AND '.
			'sp._etat = "active"')
			or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
	$sportifs = $sportifs->fetchAll(PDO::FETCH_ASSOC);

}

$allow = false;

if (empty($participant['signature']) && ( 
		!$participant['sportif'] ||
		count($sportifs))) {

	$allow = true;
	if (!isset($_POST['remarques']))
		$_POST['remarques'] = '';

	if (!empty($_POST['valid'])) {}

	if (!empty($_POST['error']))
		$pdo->query('INSERT INTO erreurs SET '.
			'_date = NOW(), '.
			'_etat = "active", '.
			'_message = "Spécification de l\'erreur", '.
			'_auteur = '.(empty($user['id']) ? 'NULL' : $user['id']).', '.
			//--------------
			'etat = "specifiee", '.
			'message = "'.secure($_POST['remarques']).'", '.
			'id_participant = '.$id_participant);


	if (!empty($_POST['signature'])) {
		$allow = false;
		$pdo->query('INSERT INTO signatures SET '.
			'_date = NOW(), '.
			'_auteur = '.(empty($user['id']) ? 'NULL' : $user['id']).', '.
			//--------------
			'id_participant = '.$id_participant);
	}

	if (!empty($_POST['error']) ||
		!empty($_POST['signature']))
		die(header('location:'.url($id_participant.'/'.$check, false, false)));

}


//Inclusion du bon fichier de template
require DIR.'templates/public/signature.php';
