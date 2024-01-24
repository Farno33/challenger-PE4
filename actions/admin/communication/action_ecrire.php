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

define('WYSIWYG', true);



$modeles = $pdo->query('SELECT '.
		'm.id, m.type, m.nom, m.titre, m.modele '.
	'FROM modeles AS m '.
	'WHERE m._etat = "active" '.
	'ORDER BY m.type ASC, m.nom ASC')
	->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);


if (!empty($_GET['recu']) &&
	intval($_GET['recu'])) {
	$exists = $pdo->query('SELECT '.
			'r.from, '.
			'r.type, '.
			'r.titre, '.
			'r.message, '.
			'r.id_participant, '.
			'p.email, '.
			'p.telephone '.
		'FROM recus AS r '.
		'LEFT JOIN participants AS p ON '.
			'p.id = r.id_participant AND '.
			'p._etat = "active" '.
		'WHERE '.
			'r.id = '.(int) $_GET['recu'])
		->fetch(PDO::FETCH_ASSOC);


	if (!empty($exists) &&
		$exists['type'] == 'email') {

		if (!empty($exists['from'])) {
			preg_match('/<([^>]*)>/', $exists['from'], $matches);
			$exists['from'] = !empty($matches[1]) ? $matches[1] : $exists['from'];
		}

		preg_match('/<body(?:[^>]*)>(.*)<\/body>/i', $exists['message'], $matches);
		$exists['message'] = !empty($matches[1]) ? $matches[1] : $exists['message'];
	}

	if (!empty($exists) &&
		empty($exists['id_participant'])) {
		$exists['email'] = $exists['type'] == 'email' ? $exists['from'] : '';
		$exists['telephone'] = $exists['type'] == 'sms' ? $exists['from'] : '';
	}
}


else if (!empty($_GET['id']) && 
	intval($_GET['id'])) {
	$exists = $pdo->query('SELECT '.
			'p.id AS id_participant, '.
			'p.email, '.
			'p.telephone '.
		'FROM participants AS p '.
		'WHERE '.
			'p.id = '.(int) $_GET['id'].' AND '.
			'p._etat = "active"')
		->fetch(PDO::FETCH_ASSOC);

	if (!empty($exists)) {
		$exists['type'] = !empty($_GET['type']) && $_GET['type'] == 'sms' ? 'sms' : 'email';
	}
}


else if (!empty($_GET['to'])) {
	$type = !empty($_GET['type']) && $_GET['type'] == 'sms' ? 'sms' : 'email';
	$exists = [
		'type' => $type,
		'email' => $type == 'email' ? $_GET['to'] : '',
		'telephone' => $type == 'sms' ? $_GET['to'] : ''
	];
}


if (!empty($_POST['send']) && (
		!empty($_POST['type']) && isset($_POST['sms']) ||
		empty($_POST['type']) && isset($_POST['email']) && isset($_POST['titre']))) {
	$isSms = !empty($_POST['type']);
	$isDelayed = !empty($_POST['retard']) && 
		!empty($_POST['date']) &&
		!empty($_POST['time']);

	$titre = $isSms ? null : $_POST['titre'];
	$contenu = $isSms ? $_POST['sms'] : $_POST['email'];
	$to = $isSms ? getPhone(trim($_POST['to'])) : trim(strtolower($_POST['to']));

	if (!($isSms && isValidSmsPhone($to) ||
		!$isSms && isValidEmail($to))) {
		$to = null;
	}

	if (!empty($_POST['id']) &&
		intval($_POST['id'])) {
			$id_participant = $pdo->query('SELECT id '.
			'FROM participants '.
			'WHERE '.
				'_etat = "active" AND '.
				'id = '.(int) $_POST['id'])
			->fetch(PDO::FETCH_ASSOC);

		if (!empty($id_participant))
			$id_participant = $id_participant['id'];
	}

	if (empty($id_participant)) {
		$id_participant = $pdo->query('SELECT id '.
			'FROM participants '.
			'WHERE '.
				'_etat = "active" AND '.
				($isSms ? 'telephone' : 'email').' LIKE "'.secure($to).'"')
			->fetch(PDO::FETCH_ASSOC);

		if (!empty($id_participant))
			$id_participant = $id_participant['id'];
	}

	$date = null;
	if ($isDelayed) {
		$date = DateTime::createFromFormat('Y-m-d H:i', $_POST['date'].' '.$_POST['time']);
		$date = $date === false ? null : $date->format('Y-m-d H:i:s');
		
		if ($date == null)
			$isDelayed = false;
	}

	$date = !$isDelayed || $date === false || $date === null ? 'NOW()' : '"'.$date.'"';

	$pdo->exec('INSERT INTO messages SET '.
		'type = "'.($isSms ? 'sms' : 'email').'", '.
		'titre = "'.secure($titre).'", '.
		'contenu = "'.secure($contenu).'", '.
		'publipostage = 0, '.
		'label = "", '.
		'_date = NOW(), '.
		'_auteur = '.(int) (int) $_SESSION['user']['id']);


	$mid = $pdo->lastInsertId();
	$pdo->exec('INSERT INTO envois SET '.
		'id_recu = '.(!empty($exists) ? (int) $_GET['recu'] : 'NULL').', '.
		'`to` = "'.secure(!empty($id_participant) ? '' : $to).'", '.
		'date = '.$date.', '.
		'tentatives = 0, '.
		'echec = NULL, '.
		'data = "[]", '.
		'id_participant = '.(!empty($id_participant) ? (int) $id_participant : 'NULL').', '.
		'id_message = '.(int) $mid);
}



require DIR.'templates/admin/communication/ecrire.php';