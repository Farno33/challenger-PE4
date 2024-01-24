<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/public/logout.php *******************************/
/* Gére la déconnexion dans les différents modules *********/
/* *********************************************************/
/* Dernière modification : le 20/11/14 *********************/
/* *********************************************************/

if (!empty($_GET['id']))
	$_SESSION['user']['id'] = $_GET['id'];

if (empty($_SESSION['user']))
	die(header('location:'.url('accueil', false, false)));	

//LIbrairie pour la détection de l'OS et du navigateur depuis l'user agent
require DIR.'includes/ua-parser-2.1.1/uaparser.php';
$uaparser = new UAParser;

$admin_actif = !empty($_SESSION['user']['privileges']);

$connexions = $pdo->query('SELECT '.
		'id, '.
		'methode, '.
		'connexion, '.
		'dernier, '.
		'ip, '.
		'geoip, '.
		'agent '.
	'FROM connexions '.
	'WHERE '.
		'id_utilisateur = '.(int) $_SESSION['user']['id'].' '.
	'ORDER BY '.
		'dernier DESC, '.
		'connexion DESC')
	->fetchAll(PDO::FETCH_ASSOC);


if (!empty($user['cas']) && (
		empty($user['pass']) ||
		!empty($_POST['old']) &&
		hashPass($_POST['old']) == $user['pass']) &&
	!empty($_POST['delete'])) {
	$ref = pdoRevision('utilisateurs', $_SESSION['user']['id']);
	$pdo->exec('UPDATE utilisateurs SET '.
			'_auteur = '.(int) $_SESSION['user']['id'].', '.
			'_ref = '.(int) $ref.', '.
			'_date = NOW(), '.
			'_message = "Suppression du mot-de-passe", '.
			//----------------//
			'pass = "" '.
		'WHERE id = '.(int) $_SESSION['user']['id']);
	
	$user['pass'] = '';
	$delete = true;
}

else if (!empty($user['pass']) &&(
		empty($_POST['old']) ||
		hashPass($_POST['old']) != $user['pass']) &&
	!empty($_POST['delete']))
	$need = true;


if (empty($user['cas']) &&
	!empty($_POST['password']) &&
	!empty($_POST['password_bis']) && 
	$_POST['password'] == $_POST['password_bis'] && (
		empty($user['pass']) ||
		!empty($_POST['old']) &&
		hashPass($_POST['old']) == $user['pass']) &&
	!empty($_POST['save'])) {
	$ref = pdoRevision('utilisateurs', $_SESSION['user']['id']);
	$pdo->exec('set FOREIGN_KEY_CHECKS = 0');
	$pdo->exec('UPDATE utilisateurs SET '.
			'_auteur = '.(int) $_SESSION['user']['id'].', '.
			'_ref = '.(int) $ref.', '.
			'_date = NOW(), '.
			'_message = "Modification du mot-de-passe", '.
			//-------------//
			'pass = "'.secure(hashPass($_POST['password'])).'" '.
		'WHERE id = '.(int) $_SESSION['user']['id']);
	$pdo->exec('set FOREIGN_KEY_CHECKS = 1');
	
	$user['pass'] = hashPass($_POST['password']);
	$save = true;
}

else if (!empty($user['pass']) && (
		empty($_POST['old']) ||
		hashPass($_POST['old']) != $user['pass']) &&
	!empty($_POST['save']))
	$need = true;

else if (!empty($_POST['save']))
	$error = true;


$tables = [
	'centraliens',
	'chambres',
	'chambres_participants',
	'concurrents',
	'configurations',
	'contacts',
	'droits_admin',
	'droits_ecoles',
	'ecoles',
	'ecoles_sports',
	'equipes',
	'erreurs',
	'groupes',
	'images',
	'matchs',
	//'messages', //Ajouté à part
	'modeles',
	'paiements',
	'participants',
	'phases',
	'phases_concurrents',
	'podiums',
	'points',
	'quotas_ecoles',
	'sites',
	'sportifs',
	'sports',
	'taches',
	'tarifs',
	'tarifs_ecoles',
	'tentes',
	'utilisateurs',
	'zones'];


$datas = [];
foreach ($tables as $table) {
	$datas[] = 'SELECT "'.$table.'" AS groupe, id, _ref, _etat, _message, _date FROM `'.$table.'` WHERE '.
		'_auteur = '.(int) $_SESSION['user']['id'];
}

$datas[] = 'SELECT "messages" AS groupe, id, NULL AS _ref, NULL AS _etat, NULL AS _message, _date FROM `messages` WHERE '.
		'_auteur = '.(int) $_SESSION['user']['id'];

$datas = implode(' UNION ', $datas);
$datas = $pdo->query($datas.' ORDER BY _date DESC')
	->fetchAll(PDO::FETCH_ASSOC);

$datas = array_splice($datas, 0, 500);

/********************


$datas = [];
foreach ($tables as $table) {
	$data = $pdo->query('SELECT id, _ref, _etat, _message, UNIX_TIMESTAMP(_date) AS _timestamp FROM `'.$table.'` WHERE '.
		'_auteur = '.(int) $_SESSION['user']['id'])->fetchAll(PDO::FETCH_ASSOC);

	foreach ($data as $item)
		$datas[$item['_timestamp'].'_'.$item['id'].'_'.$table] = array_merge($item, ['table' => $table]);
}

krsort($datas);

***********************/



$id = $_SESSION['user']['id'];
$history = "SELECT T2.*, T1.lvl, T3.nom AS _auteur_nom, T3.prenom AS _auteur_prenom FROM ".
	"(SELECT ".
        "@r AS _id, ".
        "(SELECT @r := _ref FROM utilisateurs WHERE id = _id) AS parent, ".
        "@l := @l + 1 AS lvl ".
    "FROM ".
        "(SELECT @r := $id, @l := 0) vars, ".
        "utilisateurs m ".
    "WHERE @r <> 0) T1 ".
	"JOIN utilisateurs T2 ON T1._id = T2.id ".
	"JOIN utilisateurs T3 ON T3.id = T2._auteur ".
	"ORDER BY T1.lvl ASC";
$history = $pdo->query($history)->fetchAll(PDO::FETCH_ASSOC);

require DIR.'templates/public/profil.php';