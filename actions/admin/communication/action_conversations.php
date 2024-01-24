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


if (isset($_GET['publipostage']))
	$_SESSION['publipostage'] = !empty($_GET['publipostage']);

$withPublipostage = !empty($_SESSION['publipostage']) || !empty($id_conversation);

$conversations = $pdo->query('SELECT '.
		'p.id, '.
		'p.id AS cid, '.
		'p.prenom, '.
		'p.nom, '.
		'p.telephone, '.
		'p.email, '.
		'(SELECT COUNT(e.id) FROM envois AS e WHERE e.id_participant = p.id) AS envois, '.
		'(SELECT COUNT(r.id) FROM recus AS r WHERE r.id_participant = p.id) AS recus, '.
		'(SELECT COUNT(rb.id) FROM recus AS rb WHERE rb.id_participant = p.id AND rb.ouvert = 0) AS unread, '.
		'(SELECT MAX(eb.date) FROM envois AS eb JOIN messages AS mb ON mb.id = eb.id_message '.(!$withPublipostage ? 'AND mb.publipostage = 0 ' : '').'WHERE eb.id_participant = p.id) AS last_envoi, '.
		'(SELECT MAX(rc._date) FROM recus AS rc WHERE rc.id_participant = p.id) AS last_recu '.
	'FROM participants AS p '.
	'WHERE p._etat = "active"')
	->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);

if (!empty($id_conversation)) {
	$id_conversation = explode('_', $id_conversation);

	if (count($id_conversation) == 1 &&
		!isset($conversations[(int) $id_conversation[0]]))
		die(require DIR.'templates/_error.php');
}


$recus_unknown = $pdo->query('SELECT '.
		'r.id, '.
		'r.from, '.
		'r._date, '.
		'r.ouvert '.
	'FROM recus AS r '.
	'WHERE '.
		'r.id_participant IS NULL AND '.
		'NOT (r.from = "'.getPhone(SMS_PHONE).'" AND '.
			'(r.message LIKE "test%" OR r.message LIKE "challenge test%") OR '.
		'r.from LIKE "%'.EMAIL_MAIL.'%" AND '.
		'r.titre LIKE "test%" AND '.
		'r.message LIKE "test%") '.
	'ORDER BY r._date DESC')
	->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);



if (!empty($id_conversation) &&
	count($id_conversation) == 2 &&
	$id_conversation[0] == 'recu' &&
	!isset($recus_unknown[(int) $id_conversation[1]]))
	die(require DIR.'templates/_error.php');



$envois_unknown = $pdo->query('SELECT '.
		'e.id, '.
		'e.to, '.
		'e.date '.
	'FROM envois AS e '.
	'WHERE '.
		'e.id_participant IS NULL '.
	'ORDER BY e.date DESC')
	->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);


if (!empty($id_conversation) &&
	count($id_conversation) == 2 &&
	$id_conversation[0] == 'envoi' &&
	!isset($envois_unknown[(int) $id_conversation[1]]))
	die(require DIR.'templates/_error.php');


foreach ($recus_unknown as $cuid => $cu) {
	$nom = preg_replace('/^([^<]*)<(?:[^>]*)>(?:.*)$/', '$1', $cu['from']);
	$email = preg_replace('/^(?:[^<]*)<([^>]*)>(?:.*)$/', '$1', $cu['from']);
	$email = isValidEmail($email) ? $email : '';
	$telephone = empty($email) && isValidPhone($cu['from']) ? $cu['from'] : '';

	if (empty($email) && empty($telephone))
		continue;

	if (empty($conversations[$email.$telephone]))
		$conversations[$email.$telephone] = [
			'cid' => $cuid,
			'exte' => true, 
			'prenom' => empty($telephone) ? $nom : '', 
			'nom' => '',
			'email' => $email, 
			'telephone' => $telephone, 
			'last_recu' => $cu['_date'],
			'recus' => 1,
			'unread' => !$cu['ouvert'],
			'last_envoi' => null,
			'envois' => 0];

	else {
		$conversations[$email.$telephone]['recus']++;
		
		if (empty($conversations[$email.$telephone]['unread']))
			$conversations[$email.$telephone]['unread'] = !$cu['ouvert']; 
	}
}


foreach ($envois_unknown as $cuid => $cu) {
	$email = isValidEmail($cu['to']) ? $cu['to'] : '';
	$telephone = empty($email) && isValidPhone($cu['to']) ? $cu['to'] : '';

	if (empty($email) && empty($telephone))
		continue;

	if (empty($conversations[$email.$telephone]))
		$conversations[$email.$telephone] = [
			'cid' => $cuid,
			'exte' => true, 
			'prenom' => '', 
			'nom' => '',
			'email' => $email, 
			'telephone' => $telephone, 
			'last_recu' => null,
			'recus' => 0,
			'unread' => 0,
			'last_envoi' => $cu['date'],
			'envois' => 1];

	else {
		if ($conversations[$email.$telephone]['last_envoi'] === null) {
			$conversations[$email.$telephone]['last_envoi'] = $cu['date'];
			$conversations[$email.$telephone]['envois'] = 1;
		} else 
			$conversations[$email.$telephone]['envois']++;
	}
}

if (isset($_GET['ajax']) &&
	!empty($_POST['filtre'])) {
	$json = [];
	$filtre = strtolower(removeAccents(trim($_POST['filtre'])));
	$filtre = isValidPhone($filtre) ? str_replace(' ', '', getPhone($filtre)) : $filtre;
	$filtre = make_filtres($filtre);

	foreach ($conversations as $key => $conversation) {
		$haystack = strtolower(removeAccents((is_string($key) ? $key : '').' '.
			$conversation['nom'].' '.$conversation['prenom'].' '.
			$conversation['email'].' '.str_replace(' ', '', $conversation['telephone'])));

		if (search_filtres($haystack, $filtre))
			$json[] = [	
				'url' => (empty($conversation['exte']) ? '' : (empty($conversation['recus']) ? 'envoi_' : 'recu_')).$conversation['cid'],
				'exte' => !empty($conversation['exte']),
				'id' => empty($conversation['exte']) ? $conversation['cid'] : null,
				'nom' => $conversation['nom'],
				'prenom' => $conversation['prenom'],
				'email' => $conversation['email'],
				'telephone' => $conversation['telephone']];

		if (count($json) >= 20)
			break;
	}

	die(json_encode($json));
}


foreach ($conversations as $k => $conversation) {
	if (empty($conversation['last_envoi']) &&
		empty($conversation['last_recu']))
		unset($conversations[$k]);
}


function sortConvDate($a, $b) {
	$ra = empty($a['last_recu']) ? null : new DateTime($a['last_recu']);
	$ea = empty($a['last_envoi']) ? null : new DateTime($a['last_envoi']);
	$rb = empty($b['last_recu']) ? null : new DateTime($b['last_recu']);
	$eb = empty($b['last_envoi']) ? null : new DateTime($b['last_envoi']);

	$da = $ra === null ? $ea : ($ea === null ? $ra : max($ra, $ea));
	$db = $rb === null ? $eb : ($eb === null ? $rb : max($rb, $eb));

	return $da === null ? 1 : ($db === null ? -1 : $da->getTimestamp() - $db->getTimestamp());
}

uasort($conversations, 'sortConvDate');


if (empty($id_conversation)) {
	$recus_unread = $pdo->query('SELECT '.
			'r.id, '.
			'r.type, '.
			'r.from, '.
			'r.titre, '.
			'r.message, '.
			'r.ouvert, '.
			'r._date AS date, '.
			'p.id AS pid, '.
			'p.prenom, '.
			'p.nom, '.
			'p.sexe, '.
			'e.nom AS enom '.
		'FROM recus AS r '.
		'LEFT JOIN (participants AS p '.
			'JOIN ecoles AS e ON '.
				'e.id = p.id_ecole AND '.
				'e._etat = "active") ON '.
			'p.id = r.id_participant AND '.
			'p._etat = "active" '.
		'WHERE '.
			'r.ouvert = 0 AND '.
			'NOT (r.from = "'.getPhone(SMS_PHONE).'" AND '.
			'(r.message LIKE "test%" OR r.message LIKE "challenge test%") OR '.
			'r.from LIKE "%'.EMAIL_MAIL.'%" AND '.
			'r.titre LIKE "test%" AND '.
			'r.message LIKE "test%") '.
		'ORDER BY '.
			'r._date DESC')
		->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);
} 

else {
	$filtre = false;

	if (count($id_conversation) == 2) {
		if ($id_conversation[0] == 'envoi') {
			$envoi = $envois_unknown[(int) $id_conversation[1]];

			$email = isValidEmail($envoi['to']) ? $envoi['to'] : '';
			$telephone = empty($email) && isValidPhone($envoi['to']) ? $envoi['to'] : '';
		} else {
			$recu = $recus_unknown[(int) $id_conversation[1]];

			$email = preg_replace('/^(?:[^<]*)<([^>]*)>(?:.*)$/', '$1', $recu['from']);
			$email = isValidEmail($email) ? $email : '';
			$telephone = empty($email) && isValidPhone($recu['from']) ? $recu['from'] : '';
		}

		if (isset($conversations[$email.$telephone]))
			$people = $conversations[$email.$telephone];
	} else {
		$id_participant = $id_conversation[0];
		if (isset($conversations[(int) $id_participant])) {
			$people = $conversations[(int) $id_participant];
			$people['data'] = $pdo->query('SELECT '.
					'e.nom '.
				'FROM participants AS p '.
				'JOIN ecoles AS e ON '.
					'e._etat = "active" AND '.
					'p.id_ecole = e.id '.
				'WHERE '.
					'p._etat = "active" AND '.
					'p.id = '.(int) $id_participant)
				->fetch(PDO::FETCH_ASSOC);
		}
	}


	$pdo->exec('UPDATE recus SET '.
			'ouvert = 1 '.
		'WHERE '.
			(empty($id_participant) 
				? 'id_participant IS NULL AND `from` LIKE "%'.secure($email.$telephone).'%"' 
				: 'id_participant = '.(int) $id_participant));

	$conv_envois = $pdo->query('SELECT '.
			'"envoi" AS direction, '.
			'e.id, '.
			'm.id AS mid, '.
			'm.titre, '.
			'm.contenu, '.
			'm.type, '.
			'e.date, '.
			'e.to, '.
			'e.echec, '.
			'e.envoi, '.
			'u.nom, '.
			'u.prenom '.
		'FROM envois AS e '.
		'JOIN messages AS m ON '.
			'm.id = e.id_message '.
		'JOIN utilisateurs AS u ON '.
			'u.id = m._auteur AND '.
			'u._etat = "active" '.
		'WHERE '.
			(empty($id_participant) 
				? 'e.id_participant IS NULL AND `to` LIKE "%'.secure($email.$telephone).'%"' 
				: 'e.id_participant = '.(int) $id_participant))
		->fetchAll(PDO::FETCH_ASSOC);


	$conv_recus = $pdo->query('SELECT '.
			'"recu" AS direction, '.
			'r.id, '.
			'r.from, '.
			'r.titre, '.
			'r.message, '.
			'r.type, '.
			'r._date AS date '.
		'FROM recus AS r '.
		'WHERE '.
			'NOT (r.from = "'.getPhone(SMS_PHONE).'" AND '.
			'(r.message LIKE "test%" OR r.message LIKE "challenge test%") OR '.
			'r.from LIKE "%'.EMAIL_MAIL.'%" AND '.
			'r.titre LIKE "test%" AND '.
			'r.message LIKE "test%") AND '.
			(empty($id_participant) 
				? 'r.id_participant IS NULL AND `from` LIKE "%'.secure($email.$telephone).'%"' 
				: 'r.id_participant = '.(int) $id_participant))
		->fetchAll(PDO::FETCH_ASSOC);

	
	$conv = array_merge($conv_envois, $conv_recus);

	function sortConvMessages($a, $b) {
		$da = new DateTime($a['date']);
		$db = new DateTime($b['date']);
		return $db->getTimestamp() - $da->getTimestamp();  
	}

	usort($conv, 'sortConvMessages');
	// Deprecated: usort(): Returning bool from comparison function is deprecated, return an integer less than, equal to, or greater than zero in /var/www/html/actions/admin/communication/action_conversations.php on line 335
}

require DIR.'templates/admin/communication/conversations.php';