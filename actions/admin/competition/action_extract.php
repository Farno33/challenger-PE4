<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/admin/competition/
/*          action_sportifs_ecoles_groupes.php *************/
/* Liste des sportifs par école groupés par sport **********/
/* *********************************************************/
/* Dernière modification : le 18/12/14 *********************/
/* *********************************************************/


$links = [
	'centraliens'				=> [
		'participants'			=> ['id_participant', 	'id'],
		'utilisateurs'			=> ['id_utilisateur', 	'id']],

	'chambres' 					=> [],
	
	'chambres_participants' 	=> [
		'chambres' 				=> ['id_chambre',		'id'],
		'participants'			=> ['id_participant', 	'id']],
	
	'concurrents' 				=> [
		'equipes' 				=> ['id_equipe', 		'id'],
		'sportifs' 				=> ['id_sportif', 		'id'],
		'sports' 				=> ['id_sport', 		'id']],

	'configurations' 			=> [],

	'connexions' 				=> [
		'utilisateurs' 			=> ['id_utilisateur', 	'id']],

	'contacts' 					=> [
		'utilisateurs' 			=> ['id_utilisateur', 	'id']],

	'droits_admin' 				=> [
		'utilisateurs' 			=> ['id_utilisateur', 	'id']],

	'droits_ecoles' 			=> [
		'ecoles' 				=> ['id_ecole', 		'id'],
		'utilisateurs' 			=> ['id_utilisateur', 	'id']],

	'ecoles' 					=> [
		'utilisateurs' 			=> ['id_respo', 		'id'],
		'images'				=> ['id_image', 		'id']],

	'ecoles_sports' 			=> [
		'ecoles' 				=> ['id_ecole', 		'id'],
		'sports' 				=> ['id_sport', 		'id']],

	'envois' 					=> [
		'recus'					=> ['id_recu', 			'id'],
		'participants' 			=> ['id_participant', 	'id'],
		'messages'				=> ['id_message', 		'id']],

	'equipes' 					=> [
		'ecoles_sports' 		=> ['id_ecole_sport', 	'id'], 
		'participants' 			=> ['id_capitaine', 	'id']],

	'erreurs' 					=> [
		'participants' 			=> ['id_participant', 	'id']],

	'groupes' 					=> [
		'phases'				=> ['id_phase', 		'id']],

	'images' 					=> [],

	'licences' 					=> [],

	//Pas de lien vers concurrents car il y en a deux
	'matchs' 					=> [
		'sites' 				=> ['id_site', 			'id'],
		'phases' 				=> ['id_phase', 		'id']],

	'messages' 					=> [],

	'modeles' 					=> [],

	'paiements' 				=> [
		'ecoles' 				=> ['id_ecole', 		'id']],
	
	'participants' 				=> [
		'ecoles' 				=> ['id_ecole', 		'id'],
		'tarifs_ecoles' 		=> ['id_tarif_ecole', 	'id']],
	
	'phases' 					=> [
		'sports' 				=> ['id_sport',			'id'],
		'phases' 				=> ['id_phase_suivante', 'id']],

	'phases_concurrents' 		=> [
		'phases' 				=> ['id_phase', 		'id'], 
		'groupes' 				=> ['id_groupe', 		'id'], 
		'concurrents' 			=> ['id_concurrent', 	'id']],

	//Pas de lien vers concurrents car il y en a quatre
	'podiums' 					=> [
		'sports' 				=> ['id_sport', 		'id']],

	'points' 					=> [
		'ecoles' 				=> ['id_ecole', 		'id']],
	
	'quotas_ecoles' 			=> [
		'ecoles'				=> ['id_ecole', 		'id']],

	'recus' 					=> [
		'participants'			=> ['id_participant', 	'id']],

	'remember' 					=> [
		'utilisateurs' 			=> ['id_utilisateur', 	'id']],

	'signatures' 				=> [
		'participants' 			=> ['id_participant', 	'id']],

	'sites' 					=> [],

	'sportifs' 					=> [
		'equipes' 				=> ['id_equipe', 		'id'],
		'participants' 			=> ['id_participant', 	'id']],

	'sports' 					=> [
		'utilisateurs' 			=> ['id_respo', 		'id']],

	'taches' 					=> [],

	'tarifs' 					=> [
		'sports'				=> ['id_sport_special', 'id'],
		'ecoles'				=> ['id_ecole_for_special', 'id']],
	
	'tarifs_ecoles' 			=> [
		'tarifs' 				=> ['id_tarif', 		'id'],
		'ecoles' 				=> ['id_ecole', 		'id']],
	
	'tentes' 					=> [
		'ecoles' 				=> ['id_ecole', 		'id'],
		'zones'					=> ['id_zone', 			'id']],
	
	'tokens' 					=> [
		'utilisateurs' 			=> ['id_utilisateur', 	'id']],
	
	'utilisateurs' 				=> [],
	
	'visites' 					=> [
		'participants' 			=> ['id_participant', 	'id']],
	
	'zones' 					=> []];


//Liens réciproques
foreach ($links as $t1 => $link_links) {
	foreach ($link_links as $t2 => $link_fields) {
		list($l1, $l2) = $link_fields;
		$links[$t2][$t1] = [$l2, $l1];
	}
}


$noetat = [
	'signatures',
	'visites',
	'connexions',
	'messages',
	'licences',
	'tokens',
	'envois',
	'recus',
	'remember',
	'signatures'];

//Les éléments de ce tableau peuvent être utilisés pour grouper
$orders = [
	//'centraliens'			=> ['id'],
	'chambres' 				=> ['numero', 'id'],
	//'chambres_participants'=> ['id'],
	//'concurrents' 		=> ['id'],
	'configurations'		=> ['flag', 'id'],
	'connexions'			=> ['connexion DESC', 'id'],
	'contacts' 				=> ['poste', 'id'],
	'droits_admin' 			=> ['module', 'id'],
	//'droits_ecoles'		=> ['id'],
	'ecoles' 				=> ['nom', 'id'],
	//'ecoles_sports' 		=> ['id'],
	'envois'				=> ['date DESC', 'to', 'id'],
	'equipes' 				=> ['label', 'id'],
	'erreurs' 				=> ['_date DESC', 'id'],
	'groupes'				=> ['nom', 'id'],
	//'images'				=> ['id'],
	'licences' 				=> ['nom', 'prenom', 'licence'],
	'matchs'				=> ['date', 'id'], 
	'messages'				=> ['titre', 'id'], 
	'modeles' 				=> ['nom', 'titre', 'id'],
	'paiements' 			=> ['_date DESC', 'id'],
	'participants' 			=> ['nom', 'prenom', 'id'],
	'phases'				=> ['nom', 'id'],
	//'phases_concurrents'	=> ['id'],
	//'podiums'				=> ['id'],
	//'points' 				=> ['id'],
	'quotas_ecoles' 		=> ['quota', 'id'],
	'recus' 				=> ['_date DESC', 'id'],
	//'remember'			=> [],
	//'signatures' 			=> ['id'],
	'sites' 				=> ['nom', 'id'],
	//'sportifs'			=> ['id'],
	'sports' 				=> ['sport', 'sexe', 'id'],
	'taches'				=> ['nom', 'id'],
	'tarifs' 				=> ['nom', 'id'],
	//'tarifs_ecoles'		=> ['id'],
	'tentes' 				=> ['numero', 'id'],
	'tokens' 				=> ['expire'],
	'utilisateurs' 			=> ['nom', 'prenom', 'login', 'id'],
	'visites'				=> ['connexion DESC'],
	'zones' 				=> ['zone', 'id']];


$fields = [
	'centraliens'			=> ['soiree', 'pfsamedi',  'tshirt', 'gourde', 'tombola', 'paye'],
	//'centraliens'			=> ['soiree', 'pfvendredi', 'pfsamedi', 'vegetarien','tshirt', 'gourde', 'tombola', 'paye'],
	'chambres' 				=> ['numero', 'nom', 'prenom'],
	'chambres_participants'	=> ['respo'],
	//'concurrents' 		=> [],
	'configurations' 		=> ['flag', 'nom', 'valeur'],
	'connexions' 			=> ['connexion', 'dernier', 'methode', 'ip'],
	'contacts' 				=> ['poste', 'photo'],
	'droits_admin'			=> ['module'],
	//'droits_ecoles' 		=> [], 
	'ecoles' 				=> ['nom', 'abreviation', 'format_long', 'ecole_lyonnaise'],
	//'ecoles_sports' 		=> ['quota_max', 'quota_reserves', 'quota_equipes'],
	'envois'				=> ['to', 'date', 'envoi', 'echec'],
	'equipes' 				=> ['label'],
	'erreurs' 				=> ['message', 'etat'],
	'groupes'				=> ['nom'],
	'images'				=> ['token', 'height', 'width'],
	'licences' 				=> ['nom', 'prenom', 'licence', 'ecole', 'type', 'ia', 'rc'],
	'matchs' 				=> ['numero_elimination', 'phase_elimination', 'date', 'gagne', 'forfait'],
	'messages' 				=> ['type', 'label', 'titre'],
	'modeles' 				=> ['nom', 'type'],
	'paiements' 			=> ['libelle', 'montant', 'etat', 'type', 'saisie'],
	'participants' 			=> ['nom', 'prenom', 'sexe', 'sportif', 'licence', 'fanfaron', 'cameraman', 'pompom', 'telephone', 'email', 'recharge'],
	'phases'				=> ['nom', 'type', 'points_victoire', 'points_defaite', 'points_nul', 'points_forfait', 'cloturee'], 
	'phases_concurrents' 	=> ['qualifie'],
	'podiums' 				=> ['coeff', 'ex_12', 'ex_23', 'ex_3'],
	'points'				=> ['dd', 'pompom', 'fairplay'],
	'quotas_ecoles' 		=> ['quota', 'valeur'],
	'recus'					=> ['type', 'from', 'titre', 'ouvert'],
	'remember'				=> ['token', 'hash', 'iv', 'expire'],
	//'signatures'			=> [],
	'sites' 				=> ['nom', 'description', 'latitude', 'longitude'],
	//'sportifs' 			=> [],
	'sports' 				=> ['sport', 'sexe', 'individuel', 'tournoi_initie'],
	'taches'				=> ['nom', 'periodicite', 'script', 'active', 'execution'],
	'tarifs' 				=> ['nom', 'tarif', 'logement', 'sportif', 'ecole_lyonnaise', 'format_long'],
	//'tarifs_ecoles' 		=> [],
	'tentes' 				=> ['numero'],
	'tokens'				=> ['token', 'expire'],
	'utilisateurs' 			=> ['nom', 'prenom', 'login', 'telephone', 'email', 'cas', 'responsable'],
	'visites' 				=> ['connexion', 'dernier', 'ip'],
	'zones' 				=> ['zone', 'color']];

$forms = [
	'sexe' => 'sexe', 
	'capitaine' => 'capitaine',
	'sportif' => null,
	'long' => null,
	'respo' => null,
	'bracelet' => null,
	'individuel' => null,
	'cas' => null,
	'ex_12' => null,
	'ex_23' => null,
	'ex_3' => null,
	'active' => null,
	'responsable' => null,
	'ecole_lyonnaise' => null,
	'forfait' => null,
	'tournoi_initie' => null, 
	'cloturee' => null,
	'format_long' => 'format',
	'logement' => 'package', 
	'fanfaron' => 'extra-fanfaron',
	'pompom' => 'extra-pompom',
	'cameraman' => 'extra-video',
	'retard' => 'retard',
	'hors_malus' => 'retard-excuse',
	'ouvert' => null,
	'soiree' => null,
	'pfsamedi' => null,
	//'vegetarien'=> null,
	'pfvendredi' => null,
	'tshirt' => null,
	'gourde' => null];

$money = [
	'recharge',
	'montant',
	'tarif'];

$sexe = [
	'sexe'];





if (!isset($_GET['excel']))
	unset($_SESSION['cql']);

$cql = !empty($_POST['cql']) ? $_POST['cql'] : (!empty($_SESSION['cql']) ? $_SESSION['cql'] : '');
$_SESSION['cql'] = $cql;

$commands = array_filter(explode("\n", $cql));
$datas_from = [];
$group = null;
$subgroup = null;
$filters = [];
$omits = [];
$prints = [];
$cases = [];
$constraints = [];
$havingZero = true; 
$havingOne = true; 
$havingSubOne = true; 
$active = true;
$erreur = false;
$linked = [];
$left = false;
$joins = [];
$selects = [];
$orderbys = [];
$whereands = [];
$mainon = [];
$sql = '';

$family_commands = [
	'T' 	=> 'T',
	'T_'  	=> 'T',
	'TL'	=> 'T',
	'G'		=> 'G',
	'G0'	=> 'G',
	'G1'	=> 'G',
	'G2'	=> 'G',
	'GN'	=> 'G',
	'S'		=> 'S',
	'S1'	=> 'S',
	'S2'	=> 'S',
	'SN'	=> 'S',
	'W'		=> 'W',
	'F'		=> 'F',
	'O'		=> 'O',
	'C'		=> 'C',
	'B'		=> 'B'
];

$operators = [
	'like' 		=> 'LIKE',
	'notlike'	=> 'NOT LIKE', 
	'not like'	=> 'NOT LIKE', 
	'=' 		=> '=',
	'=='		=> '=',
	'==='		=> '=',
	'<>'		=> '<>',
	'!='		=> '<>',
	'!=='		=> '<>',
	'<'			=> '<',
	'>'			=> '>',
	'>='		=> '>=',
	'<='		=> '<='];


function checkName($name) {
	return preg_match('/^[a-z_][a-z0-9_]*$/i', trim($name)) &&
		!in_array($name, ['_group_','_subgroup_', '_id_']);
}

function explode_quotes($string, $delimiter) {
	$explode = explode(':', $string);
	$explodes = [];
	$buffer = [];

	foreach ($explode as $part) {
		if (count($buffer))
			$buffer[] = $part;

		else if (substr(trim($part), 0, 1) == '"' && 
			(substr(trim($part), -1, 1) != '"' || substr(trim($part), -2, 2) == '\\"')) 
			$buffer[] = $part;

		else
			$explodes[] = trim($part);

		if (count($buffer) && substr(trim($part), -1, 1) == '"' && substr(trim($part), -2, 2) != '\\"') {
			$explodes[] = trim(implode(':', $buffer));
			$buffer = [];
		}
	}

	return $explodes;
}

//Add a table
function T($group_command, $command) {
	global $datas_from, $erreur;

	$link = null;

	if (count($command)  == 1) 
		$table = $alias = $command[0]; 
	
	else if (count($command) == 2) {
		$table = $alias = $command[0];
		$link = empty($command[1]) ? null : trim($command[1]);
	}

	else {
		$alias = $command[0];
		$table = $command[1];
		$link = empty($command[2]) ? null : trim($command[2]);
	}
	
	if (!checkName($alias))
		return $erreur = 'Incorrect alias in T command : '.implode(':', $command);

	if (!checkName($table))
		return $erreur = 'Incorrect table in T command : '.implode(':', $command);

	if ($link && !checkName($link) && $link != '#')
		return $erreur = 'Incorrect link in T command : '.implode(':', $command);

	$datas_from[trim($alias)] = [trim($table), $link, $group_command != 'T'];
}


//Add group
function G($group_command, $command) {
	global $datas_from, $group, $havingOne, $havingZero, $erreur;

	$havingZero = $group_command == 'G' || $group_command == 'G0';
	$havingOne = $group_command != 'G2' && $group_command != 'GN';

	$link = null;
	$table = null;
	
	if (count($command) == 1 && in_array($command[0], array_keys($datas_from))) 
		$alias = $command[0];

	else if (count($command) == 1)
		$alias = $table = $command[0];

	else if (count($command) == 2) {
		$alias = $table = $command[0];
		$link = empty($command[1]) ? null : trim($command[1]);
	}

	else {
		$alias = $command[0];
		$table = $command[1];
		$link = empty($command[2]) ? null : trim($command[2]);
	}

	if (!checkName($alias))
		return $erreur = 'Incorrect alias in G command : '.implode(':', $command);

	if ($table && !checkName($table))
		return $erreur = 'Incorrect table in G command : '.implode(':', $command);

	if ($link && !checkName($link))
		return $erreur = 'Incorrect link in G command : '.implode(':', $command);

	if (empty($table))
		$group = trim($alias); //Weak link

	else 
		$group = [trim($alias), trim($table), $link]; //Strong link
}


//Subgroup
function S($group_command, $command) {
	global $subgroup, $havingSubOne, $datas_from, $erreur;

	$havingSubOne = $group_command != 'S2' && $group_command != 'SN';
	$sub = explode('.', $command[0]);

	if (!empty($sub[0]) && !checkName($sub[0]))
		return $erreur = 'Incorrect alias in S command : '.implode(':', $command);

	if (!empty($sub[0]) && !in_array($sub[0], array_keys($datas_from)))
		return $erreur = 'Incorrect alias in S command : '.implode(':', $command);

	if (!empty($sub[1]) && !checkName($sub[1]))
		return $erreur = 'Incorrect field in S command : '.implode(':', $command);

	$subgroup = empty($command[0]) ? null : [trim($sub[0]), empty($sub[1]) ? 'id' : trim($sub[1])];
}


//Cases
function W($group_command, $command) {
	global $cases, $datas_from, $erreur, $operators;

	if (count($command) < 3)
		return $erreur = 'Not enough parameters in W command : '.implode(':', $command);

	$name = $command[0];
	$field = explode('.', $command[1]);

	if (!checkName($name))
		return $erreur = 'Incorrect name in W command : '.implode(':', $command);

	if (count($field) != 2 || !checkName($field[0]) || !checkName($field[1]))
		return $erreur = 'Incorrect field in W command : '.implode(':', $command);

	if (!empty($field[0]) && !in_array($field[0], array_keys($datas_from)))
		return $erreur = 'Incorrect alias in W command : '.implode(':', $command);

	if (count($command) == 3) {
		$operator = '=';
		$value = $command[2];
	}

	else {
		$operator = strtolower($command[2]);
		$value = $command[3];
	}

	if (!in_array($operator, array_keys($operators)) ||
		in_array(strtolower($value), ['null', 'notnull', 'not null', 'false', 'true']) &&
		!in_array($operators[$operator], ['=', '<>', 'LIKE', 'NOT LIKE']))
		return $erreur = 'Invalid operator in W command : '.implode(':', $command);

	$operator = $operators[$operator];

	if (in_array(strtolower($value), ['null', 'notnull', 'not null']))
		$test = (in_array($operator, ['LIKE', 'NOT LIKE']) ? 
				(($operator == 'NOT LIKE' XOR strtolower($value) !== 'null') ? 'NOT ' : '').'LIKE' : 'IS').' '.
			(in_array($operator, ['LIKE', 'NOT LIKE']) ||
			strtolower($value) == 'null' && $operator == '=' || 
			strtolower($value) != 'null' && $operator == '<>' ? '' : 'NOT ').'NULL';

	else if (in_array(strtolower($value), ['true', 'false']) || is_numeric($value)) {
		$value = strtolower($value) == 'true' ? '1' : (strtolower($value) == 'false' ? '0' : $value);
		$test = $operator.' '.$value;
	}

	else if (preg_match('/^"(.*)"$/', $value))
		$test = $operator.' "'.secure(substr($value, 1, -1)).'"';

	else {
		$value = explode('.', $value);

		if (count($value) != 2 || !checkName($value[0]) || !checkName($value[1]))
			return $erreur = 'Incorrect field value in W command : '.implode(':', $command);

		if (!empty($value[0]) && !in_array($value[0], array_keys($datas_from)))
			return $erreur = 'Incorrect alias value in W command : '.implode(':', $command);

		$test = $operator.' '.trim($value[0]).'.'.trim($value[1]);
	}

	$cases[trim($name)] = 'CASE WHEN '.trim($field[0]).'.'.trim($field[1]).' '.$test.' THEN 1 ELSE 0 END AS '.trim($name);
}


//Filters
function F($group_command, $command) {
	global $filters, $datas_from, $erreur;

	$sub = explode('.', $command[0]);

	if (!empty($sub[0]) && !checkName($sub[0]))
		return $erreur = 'Incorrect alias in F command : '.implode(':', $command);

	if (!empty($sub[1]) && !checkName($sub[1]) && $sub[1] != '*')
		return $erreur = 'Incorrect field in F command : '.implode(':', $command);

	if (!empty($sub[0]) && !in_array($sub[0], array_keys($datas_from)))
		return $erreur = 'Incorrect alias in F command : '.implode(':', $command);

	if (!empty($sub[0]))
		$filters[] = $sub[0].'.'.(empty($sub[1]) ? '*' : $sub[1]);
}


//Print
function O($group_command, $command) {
	global $omits, $datas_from, $erreur;

	$sub = explode('.', $command[0]);

	if (!empty($sub[0]) && !checkName($sub[0]))
		return $erreur = 'Incorrect alias in O command : '.implode(':', $command);

	if (!empty($sub[1]) && !checkName($sub[1]) && $sub[1] != '*')
		return $erreur = 'Incorrect field in O command : '.implode(':', $command);

	if (!empty($sub[0]) && !in_array($sub[0], array_keys($datas_from)))
		return $erreur = 'Incorrect alias in O command : '.implode(':', $command);

	if (!empty($sub[0]))
		$omits[] = $sub[0].'.'.(empty($sub[1]) ? '*' : $sub[1]);
}


//Constraints
function C($group_command, $command) {
	global $constraints, $datas_from, $erreur, $operators;

	if (count($command) < 1 || count($command) == 1 && !in_array(strtoupper($command[0]), ['OR', 'AND', 'UP', 'DOWN']))
		return $erreur = 'Not enough parameters in C command : '.implode(':', $command);

	if (count($command) >= 2) {
		$field = explode('.', $command[0]);

		if (count($field) != 2 || !checkName($field[0]) || !checkName($field[1]))
			return $erreur = 'Incorrect field in C command : '.implode(':', $command);

		if (!empty($field[0]) && !in_array($field[0], array_keys($datas_from)))
			return $erreur = 'Incorrect alias in C command : '.implode(':', $command);

		if (count($command) == 2) {
			$operator = '=';
			$value = $command[1];
		}

		else {
			$operator = strtolower($command[1]);
			$value = $command[2];
		}

		if (!in_array($operator, array_keys($operators)) ||
			in_array(strtolower($value), ['null', 'notnull', 'not null', 'false', 'true']) &&
			!in_array($operators[$operator], ['=', '<>', 'LIKE', 'NOT LIKE']))
			return $erreur = 'Invalid operator in C command : '.implode(':', $command);

		$operator = $operators[$operator];

		if (in_array(strtolower($value), ['null', 'notnull', 'not null']))
			$test = (in_array($operator, ['LIKE', 'NOT LIKE']) ? 
					(($operator == 'NOT LIKE' XOR strtolower($value) !== 'null') ? 'NOT ' : '').'LIKE' : 'IS').' '.
				(in_array($operator, ['LIKE', 'NOT LIKE']) ||
				strtolower($value) == 'null' && $operator == '=' || 
				strtolower($value) != 'null' && $operator == '<>' ? '' : 'NOT ').'NULL';

		else if (in_array(strtolower($value), ['true', 'false']) || is_numeric($value)) {
			$value = strtolower($value) == 'true' ? '1' : (strtolower($value) == 'false' ? '0' : $value);
			$test = $operator.' '.$value;
		}

		else if (preg_match('/^"(.*)"$/', $value))
			$test = $operator.' "'.secure(substr($value, 1, -1)).'"';

		else {
			$value = explode('.', $value);

			if (count($value) != 2 || !checkName($value[0]) || !checkName($value[1]))
				return $erreur = 'Incorrect field value in C command : '.implode(':', $command);

			if (!empty($value[0]) && !in_array($value[0], array_keys($datas_from)))
				return $erreur = 'Incorrect alias value in C command : '.implode(':', $command);
			
			$test = $operator.' '.trim($value[0]).'.'.trim($value[1]);
		}

		$constraints[] = trim($field[0]).'.'.trim($field[1]).' '.$test;
	} else {
		$constraints[] = strtoupper($command[0]); //OR, AND
	}
}

//Order Bys
function B($group_command, $command) {
	global $group, $orderbys, $datas_from, $erreur;

	$sub = explode('.', $command[0]);
	$order = !empty($command[1]) ? $command[1] : '';

	if (!empty($sub[0]) && !checkName($sub[0]))
		return $erreur = 'Incorrect alias in B command : '.implode(':', $command);

	if (!empty($sub[0]) && !in_array($sub[0], array_keys($datas_from)) && (
			!is_array($group) ||
			$sub[0] != $group[0]))
		return $erreur = 'Incorrect alias in B command : '.implode(':', $command);

	if (!empty($sub[1]) && !checkName($sub[1]))
		return $erreur = 'Incorrect field in B command : '.implode(':', $command);

	if (!empty($order) && !in_array(strtolower($order), ['asc', 'desc', '']))
		return $erreur = 'Incorrect order in B command : '.implode(':', $command);

	if (!empty($command[0]))
		$orderbys[] = trim($sub[0]).'.'.(empty($sub[1]) ? 'id' : trim($sub[1])).
			(strtolower($order) == 'desc' ? ' DESC' : '');
}


foreach ($commands as $command) {
	$group_parameters = explode('|', $command);
	$group_command = array_shift($group_parameters);

	foreach ($group_parameters as $parameters) {
		$command = empty($parameters) ? '' : $parameters;

		if (!in_array($group_command, array_keys($family_commands))) {
			 $erreur = 'Commande inconnue : '.$group_command;
			 break;
		}
		
		$family_commands[$group_command]($group_command, explode_quotes($command, ':'));
	}
}

if (!empty($erreur)) {}

else if (!count($datas_from))
	$erreur = 'La table principale n\'a pas été sélectionnée';

else if (empty($orders[$datas_from[array_keys($datas_from)[0]][0]]))
	$erreur = 'La table "'.$datas_from[array_keys($datas_from)[0]][0].'" ne peut-être table principale';

else if (!empty($group) && (
	!is_array($group) && (
		!in_array($group, array_keys($datas_from)) ||
		!in_array($datas_from[$group][0], array_keys($orders))) ||
	is_array($group) && (
		in_array($group[0], array_keys($datas_from)) ||
		!in_array($group[1], array_keys($orders)))))
	$erreur = 'La table de l\'alias de groupe "'.(is_array($group) ? $group[0] : $group).'" n\'est pas valide';

else if (!empty($subgroup) &&
		!in_array($subgroup[0], array_keys($datas_from)))
	$erreur = 'L\'alias du sous-groupe "'.$subgroup[0].'" n\'est pas valide';

else if (!in_array($datas_from[array_keys($datas_from)[0]][0], array_keys($links)))
	$erreur = 'La table principale "'.$datas_from[array_keys($datas_from)[0]][0].'" n\'existe pas';

else {
	$main_alias = array_keys($datas_from)[0];
	$main_table = $datas_from[$main_alias][0];
	$left = $datas_from[$main_alias][2];
	$linked[] = $main_alias;

	if (!empty($group) &&
		!is_array($group)) {
		$group_alias = $group;
		$group_table = $datas_from[$group][0];
		$group_weak = true;

		foreach ($orders[$group_table] as $order)
			$orderbys[] = $group_alias.'.'.$order;
	}

	if (!empty($group) &&
		empty($group_weak)) {
		list($group_alias, $group_table, $group_link_alias) = $group;

		if (!in_array($main_table, $noetat))
			$mainon[] = $main_alias.'._etat = "active"';
		
		if (!in_array($group_table, $noetat))
			$whereands[] = $group_alias.'._etat = "'.($active ? '' : 'des').'active"';

		foreach ($orders[$group_table] as $order)
			$orderbys[] = $group_alias.'.'.$order;

		foreach ($fields[$group_table] as $field)
			$selects[] = $group_alias.'.'.$field;
	} else {
		if (!in_array($main_table, $noetat))
			$whereands[] = $main_alias.'._etat = "'.($active ? '' : 'des').'active"';
	}


	if (!empty($fields[$main_table])) {
		foreach ($fields[$main_table] as $field)
			$selects[] = $main_alias.'.'.$field;
	}

	if (!empty($subgroup)){
		$orderbys[] = $subgroup[0].'.'.$subgroup[1];
	}

	if (!empty($orders[$main_table])) {
		foreach ($orders[$main_table] as $field)
			$orderbys[] = $main_alias.'.'.$field;
	}

	foreach ($datas_from as $futur_link_alias => $futur_link_data) {
		list($futur_link_table, $force_link_alias, $futur_link_left) = $futur_link_data;

		if ($futur_link_alias == $main_alias)
			continue;

		if (!in_array($futur_link_table, array_keys($links))) {
			$erreur = 'La table "'.$futur_link_table.'" n\'existe pas';
			break;
		}

		if (!empty($force_link_alias) &&
			$force_link_alias != '#' &&
			!in_array($force_link_alias, array_keys($datas_from))) {
			$erreur = 'L\'alias de forcage "'.$force_link_alias.'" n\'existe pas';
			break;
		}

		if (!empty($force_link_alias)) {
			if ($force_link_alias != '#') {
				$force_link_table = $datas_from[$force_link_alias][0];

				if (/*$futur_link_table != $force_link_table &&*/
					!in_array($futur_link_table, array_keys($links[$force_link_table]))) {
					$erreur = 'La table de forcage "'.$futur_link_table.'" n\'existe pas';
					break;
				}

				if (!in_array($force_link_alias, $linked)) {
					$erreur = 'L\'alias de forcage "'.$force_link_alias.'" n\'a pas encore été instancié';
					break;
				} 
			}

			if (!empty($fields[$futur_link_table])) {
				foreach ($fields[$futur_link_table] as $field)
					$selects[] = $futur_link_alias.'.'.$field;
			}

			$linked[] = $futur_link_alias;
			$joins[] = (!empty($left) || $futur_link_left ? 'LEFT ' : '').'JOIN '.$futur_link_table.' AS '.$futur_link_alias.' ON '.
				($force_link_alias == '#' ? '' : (
					$force_link_alias.'.'.($futur_link_table == $force_link_table ? 'id' : $links[$futur_link_table][$force_link_table][1]).' = '.
					$futur_link_alias.'.'.($futur_link_table == $force_link_table ? 'id' : $links[$futur_link_table][$force_link_table][0]).
					($active && !in_array($futur_link_table, $noetat) ? ' AND ' : ''))).
				($active && !in_array($futur_link_table, $noetat) ? $futur_link_alias.'._etat = "active"' : '');
		}

		else {
			$erreur = true;


			foreach ($links[$futur_link_table] as $to_link_table => $to_link_fields) {
				foreach ($linked as $link_alias) {
					$link_table = $datas_from[$link_alias][0];

					if ($link_table == $to_link_table) {
						if (!empty($fields[$futur_link_table])) {
							foreach ($fields[$futur_link_table] as $field)
								$selects[] = $futur_link_alias.'.'.$field;
						}

						$linked[] = $futur_link_alias;
						$datas_from[$futur_link_alias][1] = $link_alias;
						$joins[] = (!empty($left) || $futur_link_left ? 'LEFT ' : '').
							'JOIN '.$futur_link_table.' AS '.$futur_link_alias.' ON '.
							$link_alias.'.'.$to_link_fields[1].' = '.
							$futur_link_alias.'.'.$to_link_fields[0].
							($active && !in_array($futur_link_table, $noetat) ? ' AND '.$futur_link_alias.'._etat = "active"' : '');

						$erreur = false;
						break 2;
					} 
				}
			}
		}

		if (!empty($orders[$futur_link_table])) {
			foreach ($orders[$futur_link_table] as $order) {
				if (!in_array($futur_link_alias.'.'.$order, $orderbys))
					$orderbys[] = $futur_link_alias.'.'.$order;
			}
		}

		if ($erreur === true) {
			$erreur = 'Pas de lien avec l\'alias "'.$futur_link_alias.'"';
			break;
		}
	}

	if ($erreur === false &&
		!empty($group) &&
		!empty($group_link_alias) &&
		empty($datas_from[$group_link_alias]))
		$erreur = 'L\'alias de forcage "'.$group_link_alias.'" n\'existe pas';

	else if ($erreur === false &&
		!empty($group) &&
		!empty($group_link_alias)) {
		$group_link_table = $datas_from[$group_link_alias][0];
		$group_link_left = $datas_from[$group_link_alias][2];

		if (!in_array($group_link_table, array_keys($links[$group_table])))
			$erreur = 'La table de forcage "'.$group_link_table.'" n\'existe pas';

		else if (!in_array($group_link_alias, $linked))
			$erreur = 'Le lien de forcage "'.$group_link_alias.'" n\'a pas encore été instancié';
		
		else
			$mainon[] =	'('.(!empty($left) || $group_link_left ? $group_alias.'.'.$links[$group_table][$group_link_table][0].' IS NULL OR ' : '').
				$group_link_alias.'.'.$links[$group_table][$group_link_table][1].' = '.
				$group_alias.'.'.$links[$group_table][$group_link_table][0].')';
	}

	else if ($erreur === false &&
		!empty($group)) {
		$erreur = true;
		
		foreach ($links[$group_table] as $to_link_table => $to_link_fields) {
			foreach ($linked as $link_alias) {
				$link_table = $datas_from[$link_alias][0];
				$link_left = $datas_from[$link_alias][2];

				if ($link_table == $to_link_table) {
					$group_link_alias = $link_alias;
					$mainon[] = '('.(!empty($left) || $link_left ? $link_alias.'.'.
							$to_link_fields[1].' IS NULL OR ' : '').
						$link_alias.'.'.$to_link_fields[1].' = '.
						$group_alias.'.'.$to_link_fields[0].')';

					$erreur = false;
					break 2;
				} 
			}
		}

		if ($erreur === true)
			$erreur = 'Pas de lien avec l\'alias de groupe "'.$group_alias.'"';
	}
}

$selects_ = $selects;

if (!empty($filters)) {
	$selects = [];

	if (!empty($group)) {
		foreach ($fields[$group_table] as $field)
			$selects[] = $group_alias.'.'.$field;
	}

	foreach ($filters as $filter) {
		list($alias, $field) = explode('.', $filter);

		if (in_array($alias, array_keys($datas_from)) &&
			$field == '*' && !empty($fields[$datas_from[$alias][0]])) {
			foreach ($fields[$datas_from[$alias][0]] as $new_field) {
				if (!in_array($alias.'.'.$new_field, $selects))
					$selects[] = $alias.'.'.$new_field;
			}
		}
		
		else if (in_array($alias, array_keys($datas_from))) {
			if (!in_array($alias.'.'.$field, $selects))
				$selects[] = $alias.'.'.$field;

			if (!in_array($alias.'.'.$field, $selects_))
				$selects_[] = $alias.'.'.$field;
		}
	}

	$selects__ = $selects;
	foreach ($selects_ as $select) {
		if (!in_array($select, $selects))
			$selects__[] = $select;
	}
	$selects_ = $selects__;
	unset($selects__);
}



foreach ($omits as $omit) {
	list($alias, $field) = explode('.', $omit);

	foreach ($selects as $select) {
		if ($field == '*' && strpos($select, $alias.'.') === 0 ||
			$select == $omit)
			array_splice($selects, array_search($select, $selects), 1);
	}
}


$columns = 0;
foreach ($selects as $select) {
	if (empty($group) || strpos($select, (!is_array($group) ? $group : $group[0]).'.') !== 0)
		$columns++;
}

$values = $selects;
foreach ($cases as $alias => $case) {
	array_unshift($selects, '.'.$alias);
	array_unshift($values, $case);
	$columns++;
}

function implode_constraints($ands, $constraints) {	
	$s = implode(' AND ', $ands);
	$t = '';
	$operator = 'AND';
	$depth = 0;
	$upped = false;
	
	reset($constraints);
	$start = key($constraints);

	foreach ($constraints as $key => $constraint) {
		if (!in_array(strtoupper($constraint), ['OR', 'AND', 'UP', 'DOWN'])) {
			if ($key != $start && !$upped)
				$t .= ' '.$operator.' ';


			$t .= $constraint;
			$operator = 'AND';
			$upped = false;
		} else {
			if (in_array($constraint, ['OR', 'AND']))
				$operator = $constraint;

			else if ($constraint == 'UP') {
				$depth++;
				$upped = true;
				$t .= ($key != $start ? ' '.$operator.' ' : '').'(';
			}

			else if ($constraint == 'DOWN') {
				$t .= ')';
				$depth--;
			}
		}
	}

	for (; $depth > 0; $depth--)
		$t .= ')';

	if (strlen($s) && strlen($t))
		$s .= ' AND ';

	return $s . (strlen($t) ? '(' . $t . ')' : '');
}

if ($erreur === false) 
	$sql = 'SELECT '.
		(!empty($group) ? $group_alias.'.'.str_replace([' ASC', ' DESC'], '', $orders[$group_table][count($orders[$group_table]) - 1]).' AS _group_, ' : '').
		(!empty($subgroup) ? 'CASE WHEN '.$subgroup[0].'.'.$subgroup[1].' IS NULL THEN NULL ELSE CONCAT("_", '.$subgroup[0].'.'.$subgroup[1].') END AS _subgroup_, ' : '').
		$main_alias.'.'.str_replace([' ASC', ' DESC'], '', $orders[$main_table][count($orders[$main_table]) -1 ]).' AS _id_, '.
		implode(', ', $values).
		' FROM '.(!empty($group) && empty($group_weak) ? 
			$group_table.' AS '.$group_alias.' '.(!empty($havingZero) ? 'LEFT ' : '').'JOIN ('.$main_table.' AS '.$main_alias.' '.implode(' ', $joins).') ON '.implode_constraints($mainon, $constraints) : 
			$main_table.' AS '.$main_alias.' '.implode(' ', $joins)).
		(!empty($group) && empty($group_weak) && count($whereands) || 
			!(!empty($group) && empty($group_weak)) && count($whereands) + count($constraints) ? 
			' WHERE '.implode_constraints($whereands, !empty($group) && empty($group_weak) ? [] : $constraints) : '').
		(count($orderbys) ? ' ORDER BY '.implode(', ', $orderbys) : '');

if ($columns == 0)
	$erreur = $erreur !== false ? $erreur : 'Aucun champ (hors groupe) n\'est retourné';

else if ($erreur === false) {
	$time = microtime(true);
	$datas_ = $pdo->query($sql);
	$time = microtime(true) - $time;


	if ($datas_ === false)
		$erreur = 'Erreur lors de l\'execution de la requête ('.$sql.')<br />'.$pdo->errorInfo()[2];

	else {

		if (!empty($group))
			$datas = $datas_->fetchAll(PDO::FETCH_NUM | PDO::FETCH_GROUP);

		else {
			$datas = [];
			$datas[0] = $datas_->fetchAll(PDO::FETCH_NUM);
		}

		unset($datas_);


		if (!empty($subgroup)) {
			foreach ($datas as $_group_ => $sous_datas) {
				$previous = false;
				$previous_key = null;
				$count = 0;

				foreach ($sous_datas as $k => $data) {
					if ($previous == $data[0]) 
						$count++;
					
					else {
						if ($previous !== false && $count <= 1 && !$havingSubOne)
							unset($datas[$_group_][$previous_key]);

						$previous = $data[0];
						$count = 1;
					}

					$previous_key = $k;
				}

				if ($previous && $count <= 1 && !$havingSubOne)
					$datas[$_group_][$previous_key][0] = null;
			}
		}


		if (!empty($group)) {
			foreach ($datas as $_group_ => $sous_datas) {
				if (!$havingZero && empty($sous_datas[array_keys($sous_datas)[0]][0]) ||
					!$havingOne && count($sous_datas) == 1)
					unset($datas[$_group_]);

				else {

				}
			}
		} 



		


		$labels = [];
		foreach ($selects as $key => $select) {
			list($alias, $field) = explode('.', $select);
			$labels[empty($alias) ? $field : $select] = $key;
		}

		if (isset($_GET['excel'])) {
			foreach ($selects as $k => $select) {
				list($alias, $field) = explode('.', $select);
				$key = $k + 1 + (!empty($subgroup) ? 1 : 0);
				 
				if ((in_array($field, array_keys($forms)) ||
				 		preg_match('/^(was|is|in|had|has)_/', $field)) && 
					!in_array($field, $sexe)) {
					foreach ($datas as $i => $groupe) {
						foreach ($groupe as $j => $data) {
							$datas[$i][$j][$key] = !isset($data[$key]) || $data[$key] == null ? '' : (empty($data[$key]) ? 'Non' : 'Oui');
						}
					}
				}

				else if (in_array($field, $sexe)) {
					foreach ($datas as $i => $groupe) {
						foreach ($groupe as $j => $data) {
							$datas[$i][$j][$key] = !isset($data[$key]) || $data[$key] == null ? '' : strip_tags(printSexe($data[$key], false));
						}
					}
				}
			}
		}


		//Téléchargement du fichier XLSX concerné
		if (!empty($_GET['excel']) &&
			intval($_GET['excel']) &&
			in_array($_GET['excel'], array_keys($datas)) ||
			isset($_GET['excel']) && empty($group)) {

			if (!empty($group)) {
				$values = [];
				$items = $datas[$_GET['excel']];
				$data_group = $items[0];

				foreach ($orders[$group_table] as $item) {
					if (in_array($item, $fields[$group_table])) {
						$value = $data_group[array_search($group_alias.'.'.$item, $selects) + 1 + (!empty($subgroup) ? 1 : 0)];
						
						if (in_array($item, $sexe))
							$value = '('.$value.')'; //printSexe($value)

						if (in_array($item, $money))
							$value = printMoney($value);

						$values[] = empty($value) ? '' : $value;
					}
				}
				
				$values = implode(' ', $values);
				$titre = 'Liste des données ('.(empty($values) ? 'Inconnu' : strip_tags($values)).')';
				$fichier = 'liste_donnees_'.onlyLetters(empty($values) ? 'inconnu' : strip_tags($values));
			} 

			else {
				$titre = 'Liste des données';
				$fichier = 'liste_donnees';
				$items = $datas[0];
			}

			foreach ($items as $kitem => $item) {
				if (empty($item[0]))
					unset($items[$kitem]);

				else {
					array_shift($items[$kitem]);

					if (!empty($subgroup))
						array_shift($items[$kitem]);
				}
			}

			exportXLSX($items, $fichier, $titre, $labels);

		}


		else if (!empty($group) &&
			isset($_GET['excel'])) {

			$fichier = 'liste_donnees_groupee';
			$items = $titres = $feuilles[] = [];
			
			$i = 0;
			foreach ($datas as $_group_ => $sous_datas) {
				if (empty($_group_))
					$values = 'Groupe non défini';

				else {
					$values = [];
					$data_group = $sous_datas[0];

					foreach ($orders[$group_table] as $item) {
						if (in_array($item, $fields[$group_table])) {
							$value = $data_group[array_search($group_alias.'.'.$item, $selects) + 1 + (!empty($subgroup) ? 1 : 0)];
							
							if (in_array($item, $sexe))
								$value = '('.$value.')'; //printSexe($value)

							if (in_array($item, $money))
								$value = printMoney($value);

							$values[] = empty($value) ? '' : $value;
						}
					}
					
					$values = implode(' ', $values);
				}

				$titres[$i] = 'Liste des données ('.(empty($values) ? 'Inconnu' : strip_tags($values)).')';
				$feuilles[$i] = empty($values) ? 'Inconnu' : strip_tags($values);
				
				foreach ($sous_datas as $kitem => $item) {
					if (empty($item[0]))
						unset($sous_datas[$kitem]);

					else {
						array_shift($sous_datas[$kitem]);
						if (!empty($subgroup))
							array_shift($sous_datas[$kitem]);
					}
				}

				$items[$i] = $sous_datas;
				$i++;
			}
			
			exportXLSXGroupe($items, $fichier, $feuilles, $titres, $labels);

		}
	}
} 

//Inclusion du bon fichier de template
require DIR.'templates/admin/competition/extract.php';
