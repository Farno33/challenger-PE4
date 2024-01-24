<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/admin/ecoles/action_edition.php *****************/
/* Edition d'une école *************************************/
/* *********************************************************/
/* Dernière modification : le 16/02/15 *********************/
/* *********************************************************/

if (isset($_POST['connected']))
	die;

$modeles = $pdo->query('SELECT '.
		'm.id, m.type, m.nom, m.titre, m.modele '.
	'FROM modeles AS m '.
	'WHERE m._etat = "active" '.
	'ORDER BY m.type ASC, m.nom ASC')
	->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);

if (isset($_GET['modele']) &&
	!empty($_POST['type']) &&
	!empty($_POST['modele']) &&
	intval($_POST['modele']) &&
	in_array($_POST['modele'], array_keys($modeles)) &&
	$modeles[$_POST['modele']]['type'] == $_POST['type']) {
	die(json_encode([
		'titre' => html_entity_decode($modeles[$_POST['modele']]['titre']),
		'modele' => html_entity_decode($modeles[$_POST['modele']]['modele'])]));
}

define('WYSIWYG', true);

if (!empty($_POST['next']) && (
		!empty($_POST['type']) && isset($_POST['sms']) ||
		empty($_POST['type']) && isset($_POST['email']) && isset($_POST['titre']))) {
	$isSms = !empty($_POST['type']);
	$isForced = !empty($_POST['force']);
	$isDelayed = !empty($_POST['retard']) && 
		!empty($_POST['date']) &&
		!empty($_POST['time']);

	$titre = $isSms ? null : $_POST['titre'];
	$contenu = $isSms ? $_POST['sms'] : $_POST['email'];
	$to = $isForced ? trim($_POST['to']) : null;
	$date = null;

	if ($isDelayed) {
		$date = DateTime::createFromFormat('Y-m-d H:i', $_POST['date'].' '.$_POST['time']);
		$date = $date === false ? null : $date->format('Y-m-d H:i:s');
		
		if ($date == null)
			$isDelayed = false;
	}

	if ($isForced) {
		$to = $isSms ? getPhone($to) : $to;
		
		if (!($isSms && isValidSmsPhone($to) ||
			!$isSms && isValidEmail($to))) {
			$to = null;
			$isForced = false;
		}
	} 

	$nextStep = true;
} else if (!empty($_POST['send']) && 
	isset($_POST['titre']) &&
	isset($_POST['contenu']) &&
	isset($_POST['to']) &&
	isset($_POST['retard']) &&
	!empty($_POST['type']) &&
	in_array($_POST['type'], ['sms', 'email']) &&
	!empty($_POST['participants']) &&
	is_array($_POST['participants'])) {

	$isSms = $_POST['type'] == 'sms';
	$to = trim($_POST['to']);
	$to = !($isSms && isValidSmsPhone($to) ||
		!$isSms && isValidEmail($to)) ? '' :
		($isSms ? getPhone($to) : $to);

	$date = DateTime::createFromFormat('Y-m-d H:i:s', $_POST['retard']);
	$date = $date === false ? 'NOW()' : '"'.$date->format('Y-m-d H:i:s').'"';

	$participants = $pdo->query('SELECT '.
			'p.id, '.
			'p.id AS pid '.
		'FROM participants AS p '.
		'WHERE '.
			'p._etat = "active"')
		->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);

	$pdo->exec('INSERT INTO messages SET '.
		'type = "'.secure($_POST['type']).'", '.
		'titre = "'.secure($_POST['titre']).'", '.
		'contenu = "'.secure($_POST['contenu']).'", '.
		'publipostage = 1, '.
		'label = "", '.
		'_date = NOW(), '.
		'_auteur = '.(int) (int) $_SESSION['user']['id']);

	$mid = $pdo->lastInsertId();
	$envoi = $pdo->prepare('INSERT INTO envois SET '.
		'id_recu = NULL, '.
		'`to` = "'.secure($to).'", '.
		'date = '.$date.', '.
		'tentatives = 0, '.
		'echec = NULL, '.
		'data = "[]", '.
		'id_participant = :id_participant, '.
		'id_message = '.(int) $mid);

	$count = 0;
	foreach ($_POST['participants'] as $pid) {
		if (empty($participants[$pid]))
			continue;

		$count++;
		$envoi->execute([':id_participant' => $pid]);
	}
}


if (isset($_GET['filter'])) {
	if (isset($_POST['selected']) &&
		empty($_POST['ids']))
		$_POST['ids'] = [];

	$participants_ = $pdo->query('SELECT '.
			'p.id, '.
			'p.nom, '.
			'p.sexe, '.
			'p.sportif, '.
			'p.fanfaron, '.
			'p.pompom, '.
			'p.cameraman, '.
			'p.prenom, '.
			'p.telephone, '.
			'p.email, '.
			'e.nom AS ecole, '.
			's.sport, '.
			's.sexe AS ssexe, '.
			'CASE WHEN p.id = eq.id_capitaine THEN 1 ELSE 0 END AS capitaine, '.
			'eq.label AS equipe, '.
			'c.numero AS chambre, '.
			't.logement, '.
			'CASE WHEN si.id IS NULL THEN 0 ELSE 1 END AS signature, '.
			'CASE WHEN p.date_inscription > "'.APP_DATE_MALUS.'" THEN 1 ELSE 0 END AS retard '.
		'FROM participants AS p '.
		'JOIN ecoles AS e ON '.
			'e.id = p.id_ecole AND '.
			'e._etat = "active" '.
		'JOIN tarifs_ecoles AS te ON '.
			'te.id = p.id_tarif_ecole AND '.
			'te._etat = "active" '.
		'JOIN tarifs AS t ON '.
			't.id = te.id_tarif AND '.
			't._etat = "active" '.
		'LEFT JOIN signatures AS si ON '.
			'si.id_participant = p.id '. 
		'LEFT JOIN (chambres_participants AS cp '.
			'JOIN chambres AS c ON '.
				'c.id = cp.id_chambre AND '.
				'c._etat = "active") ON '.
			'cp.id_participant = p.id AND '.
			'cp._etat = "active" '.
		'LEFT JOIN (sportifs AS sp '.
			'JOIN equipes AS eq ON '.
				'eq.id = sp.id_equipe AND '.
				'eq._etat = "active" '.
			'JOIN ecoles_sports AS es ON '.
				'es.id = eq.id_ecole_sport AND '.
				'es._etat = "active" '.
			'JOIN sports AS s ON '.
				's.id = es.id_sport AND '.
				's._etat = "active") ON '.
			'sp.id_participant = p.id AND '.
			'sp._etat = "active" '.
		'WHERE '.
			'p._etat = "active" '.
			(isset($_POST['selected']) && is_array($_POST['ids']) ? ' AND p.id IN ('.(empty($_POST['ids']) ? 'NULL' : '').implode(', ', array_map(function($value) {
				return (int) $value; }, $_POST['ids'])).') ' :  '').
			(isset($_POST['sportif']) ? 'AND p.sportif = '.(int) $_POST['sportif'].' ' : '').
			(isset($_POST['capitaine']) ? 'AND p.id '.($_POST['capitaine'] == '1' ? '=' : '<>').' eq.id_capitaine ' : '').
			(isset($_POST['sexe']) ? 'AND p.sexe = "'.($_POST['sexe'] == 'h' ? 'h' : 'f').'"' : '').
			(isset($_POST['fanfaron']) ? 'AND p.fanfaron = '.(int) $_POST['fanfaron'].' ' : '').
			(isset($_POST['pompom']) ? 'AND p.pompom = '.(int) $_POST['pompom'].' ' : '').
			(isset($_POST['cameraman']) ? 'AND p.cameraman = '.(int) $_POST['cameraman'].' ' : '').
			(isset($_POST['ecole']) ? 'AND e.id = '.(int) $_POST['ecole'].' ' : '').
			(isset($_POST['sport']) ? 'AND s.id = '.(int) $_POST['sport'].' ' : '').
			(isset($_POST['chambre']) ? 'AND c.id = '.(int) $_POST['chambre'].' ' : '').
			(isset($_POST['logement']) ? 'AND t.logement = '.(int) $_POST['logement'].' ' : '').
			(isset($_POST['retard']) ? 'AND p.date_inscription '.($_POST['retard'] ? '> ' : '<=').' "'.APP_DATE_MALUS.'" ' : '').
			(isset($_POST['signature']) ? 'AND si.id IS '.($_POST['signature'] ? 'NOT ' : '').'NULL ' : '').
		'ORDER BY '.
			'p.nom ASC, '.
			'p.prenom ASC ') 
		->fetchAll(PDO::FETCH_ASSOC);

	$participants = [];
	foreach ($participants_ as $participant) {
		if ($participant['chambre'] === null) {
			$participant['chambre'] = '';
		}

		if ($participant['equipe'] === null) {
			$participant['equipe'] = '';
			$participant['sport'] = '';
		} else {
			$participant['sport'] .= ' '.printSexe($participant['ssexe']);
		}

		unset($participant['ssexe']);

		if (empty($participants[$participant['id']]))
			$participants[$participant['id']] = $participant;

		//C'est un multi-sportifs
		else {
			if ($participants[$participant['id']]['capitaine'] == '0')
				$participants[$participant['id']]['capitaine'] = $participant['capitaine'];

			$participants[$participant['id']]['sport'] .= ' / '.$participant['sport'];
			$participants[$participant['id']]['equipe'] .= ' / '.$participant['equipe'];
		}
	}

	die(json_encode(array_values($participants)));
}

$ecoles = $pdo->query('SELECT '.
		'e.id, e.nom '.
	'FROM ecoles AS e '.
	'WHERE '.
		'e._etat = "active" '.
	'ORDER BY e.nom ASC')
	->fetchAll(PDO::FETCH_ASSOC);


$sports = $pdo->query('SELECT '.
		's.id, s.sport, s.sexe '.
	'FROM sports AS s '.
	'WHERE '.
		's._etat = "active" '.
	'ORDER BY s.sport ASC, s.sexe ASC')
	->fetchAll(PDO::FETCH_ASSOC);


$chambres = $pdo->query('SELECT '.
		'c.id, c.numero '.
	'FROM chambres AS c '.
	'WHERE '.
		'c._etat = "active" AND '.
		'(c.etat = "lachee" OR c.etat = "amies" OR c.etat = "amiesplus") '.
	'ORDER BY c.numero ASC')
	->fetchAll(PDO::FETCH_ASSOC);


require DIR.'templates/admin/communication/publipostage.php';