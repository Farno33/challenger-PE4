<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/public/login_ecoles.php *************************/
/* Gére la connexion pour les écoles ***********************/
/* *********************************************************/
/* Dernière modification : le 20/11/14 *********************/
/* *********************************************************/


if (!empty($_POST['remove']) &&
	intval($_POST['remove'])) {
	$cookie = empty($_COOKIE['signatures']) ? [] : json_decode($_COOKIE['signatures'], true);
	$cookie = $cookie === null || !is_array($cookie) ? [] : $cookie; 
	unset($cookie[(int) $_POST['remove']]);

	setcookie('signatures', json_encode($cookie), strtotime("+1 month"), url('', false, false));
	die;
}

if (!empty($_SESSION['tentatives']) &&
	time() - $_SESSION['tentatives']['start'] > APP_WAIT_AUTH)
	unset($_SESSION['tentatives']);


if (!empty($_SESSION['user']) && 
	empty($_SESSION['user']['privileges']) &&
	!empty($_SESSION['user']['ecoles']) &&
	count($_SESSION['user']['ecoles']) == 1) {

	$etatEcole = $pdo->query('SELECT '.
			'nom, '.
			'etat_inscription '.
		'FROM ecoles '.
		'WHERE '.
			'id = '.(int)$_SESSION['user']['ecoles'][0].' AND '.
			'_etat = "active"')
		->fetch(PDO::FETCH_ASSOC);

	if (empty($etatEcole))
		unset($_SESSION['user']);

	else if ($etatEcole['etat_inscription'] == 'fermee')
		$fermee = true;

	else 
		die(header('location:'.url('ecoles/'.$_SESSION['user']['ecoles'][0], false, false)));
}

else if (!empty($_SESSION['user'])) {
	$path = isset($_SESSION['path']) ? $_SESSION['path'] : 'accueil';
	unset($_SESSION['path']);
	die(header('location:'.url($path, false, false)));
}


if (!empty($_POST['creation']))
	die(header('location:'.url('creation', false, false)));


if (empty($user) && (isset($_GET['myecl']) || (isset($_GET['code']) && isset($_GET['state'])))) {
	require DIR."/includes/OpenID-Connect-PHP/autoload.php";
	
	$oidc = new Jumbojett\OpenIDConnectClient(CONFIG_OIDC_HOST, CONFIG_OIDC_CLIENTID, CONFIG_OIDC_SECRET);

	try {
		$oidc->authenticate();
	} catch (Exception $e) {
		die(header('location:'.url('login', false, false)));
	}
	
	$sub = $oidc->requestUserInfo('sub');
	$nom = $oidc->requestUserInfo('name');
	$prenom = $oidc->requestUserInfo('firstname');
	$adresse_mail = $oidc->requestUserInfo('email');

	$user_req = $pdo->prepare('SELECT
				id,
				(SELECT COUNT(connexion) FROM connexions WHERE id_utilisateur = id) AS connexions,
				login,
				nom,
				prenom,
				email
			FROM utilisateurs
			WHERE
				login = :login AND
				cas = 1 AND
				_etat = "active"', [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]) or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
	$user_req->execute(['login' => $adresse_mail]);
	$user = $user_req->fetch(PDO::FETCH_ASSOC);

	if (empty($user)) {
		$create = $pdo->prepare('INSERT INTO utilisateurs SET
			_auteur = NULL,
			_date = NOW(),
			_message = "Ajout de l\'utilisateur automatiquement par SSO",
			_etat = "active",
			login = :login,
			nom = :nom,
			prenom = :prenom,
			email = :mail,
			cas = 1,
			responsable = 0', [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);
		$create->execute([':login' => $adresse_mail, 'nom' => ucname($nom), 'prenom' => ucname($prenom), 'mail' => $adresse_mail]);

		$user_req->execute(['login' => $adresse_mail]);
		$user = $user_req->fetch(PDO::FETCH_ASSOC);
	}
}


else if (empty($user) &&
	!empty($_POST['db']) &&
	!empty($_POST['login']) &&
	!empty($_POST['pass']) && (
		empty($_SESSION['tentatives']) ||
		$_SESSION['tentatives']['count'] < APP_MAX_TRY_AUTH)) {

	if (empty($_SESSION['tentatives']) ||
		time() - $_SESSION['tentatives']['start'] > APP_WAIT_AUTH)
		$_SESSION['tentatives'] = [
			'start' => time(),
			'count' => 0];


	$hash = hashPass($_POST['pass']);
	$user = $pdo->query('SELECT '.
			'id, '.
			'(SELECT COUNT(connexion) FROM connexions WHERE id_utilisateur = id) AS connexions '.
		'FROM utilisateurs '.
		'WHERE '.
			'login = "'.secure($_POST['login']).'" AND '.
			'pass IS NOT NULL AND '.
			'pass <> "" AND '.
			'pass = "'.$hash.'" AND '.
			'_etat = "active"') or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
	$user = $user->fetch(PDO::FETCH_ASSOC);

	if (empty($user))
		$_SESSION['tentatives']['count']++;
}



if ((!empty($_POST['db']) ||
	(isset($_GET['code']) && isset($_GET['state']))) &&
	!empty($user)) {
	
	unset($_SESSION['tentatives']);
	
	//Création des infos de mémorisation
	if (empty($cas) &&
		!empty($_POST['remember'])) {
		$remember = true;

		//Création du Token et Hash (dépendant du navigateur, uniquement pour le moment)
		$token = uniqid();
		$hash = md5($token.$_SERVER['HTTP_USER_AGENT']);
		$signature = sign($hash);
	

		//Préparation des cookies
		setcookie('token', $token, strtotime("+1 month"));
		setcookie('data', $signature, strtotime("+1 month"));

		$pdo->exec('INSERT INTO remember SET '.
			'id_utilisateur = '.(int) $user['id'].', '.
			'token = "'.secure($token).'", '.
			'hash = "'.secure($hash).'", '.
//			'iv = "'.secure($signature['iv']).'", '.
			'expire = DATE_ADD(NOW(), INTERVAL 1 MONTH)');
	} else if (empty($cookie)) {
		setcookie('token', '', time());
		setcookie('data', '', time());
	}

	$pdo->exec('INSERT INTO connexions SET '.
		'id_utilisateur = '.(int) $user['id'].', '.
		'methode = "'.(!empty($cas) ? 'cas' : (!empty($cookie) ? 'remember' : 'db')).'", '.
		'connexion = NOW(), '.
		'dernier = NOW(), '.
		'geoip = "", '.
		'ip = "'.secure(getClientIp()).'", '.
		'agent = "'.secure(empty($_SERVER['HTTP_USER_AGENT']) ? 'UNKNOWN' : $_SERVER['HTTP_USER_AGENT']).'"');



	$_SESSION['user'] = [
		'cas' => !empty($cas) ? $cas : null,
		'remember' => !empty($remember) ? true : false,
		'start' => time(),
		'last' => time(),
		'id' => $user['id'],
		'active' => $pdo->lastInsertId(),
		'first' => empty($user['connexions']),
		'geoip' => 1];
	
	$path = isset($_SESSION['path']) ? $_SESSION['path'] : 'accueil';
	unset($_SESSION['path']);
	die(header('location:'.url($path, false, false)));
}

else if (!empty($_POST['db']) ||
	isset($_GET['cas']))
	$error = 'db'; //isset($_GET['cas']) ? 'cas' : 'db'; On a plus cas, ça fait des erreurs bizarres pour les centraliens


$cookie = empty($_COOKIE['signatures']) ? [] : json_decode($_COOKIE['signatures'], true);
$cookie = $cookie === null || !is_array($cookie) ? [] : $cookie; 

$where = ['0'];
foreach ($cookie as $pid => $token) {
	if (intval($pid))
		$where[] = 'p.id = '.(int) $pid;
}

$checks = $pdo->query('SELECT '.
		'p.id, '.
		'p.id_ecole, '.
		'p.prenom, '.
		'p.nom, '.
		'e.nom AS enom, '.
		'i.token '.
	'FROM participants AS p '.
	'JOIN ecoles AS e ON '.
		'e.id = p.id_ecole AND '.
		'e._etat = "active" '.
	'LEFT JOIN images AS i ON '.
		'i.id = e.id_image AND '.
		'i._etat = "active" '.
	'WHERE '.
		'p._etat = "active" AND ('.
		implode(' OR ', $where).')')
	->fetchAll(PDO::FETCH_ASSOC);

foreach ($checks as $k => $data) {
	$checks[$k]['check'] = substr(sha1(APP_SEED.
		$data['id_ecole'].'_'.
		$data['id']), 0, 20);

	if (empty($cookie[$data['id']]) ||
		$cookie[$data['id']] != $checks[$k]['check'])
		unset($checks[$k]);
}


//Inclusion du bon fichier de template
require DIR.'templates/public/login.php';