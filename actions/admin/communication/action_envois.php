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
	$_SESSION['onlypublipostage'] = !empty($_GET['publipostage']);

$onlyPublipostage = !empty($_SESSION['onlypublipostage']);

if (!empty($_GET['cancel']) &&
	intval($_GET['cancel'])) {
	$pdo->exec('UPDATE envois SET '.
			'echec = "Annulation manuelle par '.secure($user['login'].' ('.$user['prenom'].' '.$user['nom'].')').'" '.
		'WHERE '.
			'date > NOW() AND '.
			'envoi IS NULL AND '.
			'echec IS NULL AND '.
			'id_message = '.(int) $_GET['cancel']);
}

if (!empty($_GET['cancel_envoi']) &&
	intval($_GET['cancel_envoi'])) {
	$pdo->exec('UPDATE envois SET '.
			'echec = "Annulation manuelle par '.secure($user['login'].' ('.$user['prenom'].' '.$user['nom'].')').'" '.
		'WHERE '.
			'date > NOW() AND '.
			'envoi IS NULL AND '.
			'echec IS NULL AND '.
			'id = '.(int) $_GET['cancel_envoi']);
}

$envois = $pdo->query('SELECT '.
		'm.id, '.
		'm.type, '.
		'm.label, '.
		'm.titre, '.
		'm.contenu, '.
		'u.nom, '.
		'u.prenom, '.
		'(SELECT e.date FROM envois AS e WHERE e.id_message = m.id LIMIT 1) AS date, '.
		'(SELECT COUNT(e.id) FROM envois AS e '.
			'WHERE e.id_message = m.id AND e.envoi IS NULL AND ('.
				'e.echec IS NOT NULL OR '.
				'm.type = "sms" AND e.tentatives >= '.SMS_FAILS.' OR '.
				'm.type = "email" AND e.tentatives >= '.EMAIL_FAILS.')) AS echecs, '.
		'(SELECT COUNT(e.id) FROM envois AS e WHERE e.id_message = m.id) AS participants, '.
		'(SELECT COUNT(e.id) FROM envois AS e WHERE e.envoi IS NOT NULL AND e.id_message = m.id) AS envois '.
	'FROM messages AS m '.
	'JOIN utilisateurs AS u ON '.
		'u.id = m._auteur AND '.
		'u._etat = "active" '.
	(!empty($onlyPublipostage) ? 'WHERE m.publipostage = 1 ' : '').
	'ORDER BY m._date DESC') 
	->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE); 


if (!empty($id_message) &&
	empty($envois[$id_message]))
	die(require DIR.'templates/_error.php');



if (isset($_GET['iframe']))
	die(strip_quotes_from_message(simplifyText(html_entity_decode($envois[$id_message]['contenu']), null, true)));



if (!empty($id_message)) {
	$persons = $pdo->query('SELECT '.
			'en.id, '.
			'p.id, '.
			'p.prenom, '.
			'p.nom, '.
			'en.to, '.
			'en.tentatives, '.
			'en.envoi, '.
			'en.echec, '.
			'en.date '.
		'FROM envois AS en '.
		'LEFT JOIN ('.
			'participants AS p '.
			'JOIN ecoles AS e ON '.
				'e.id = p.id_ecole AND '.
				'e._etat = "active") ON '.
			'p.id = en.id_participant AND '.
			'p._etat = "active" '.
		'WHERE '.
			'en.id_message = '.(int) $id_message.' '.
		'ORDER BY p.nom ASC, p.prenom ASC')
		->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);
}
 

require DIR.'templates/admin/communication/envois.php';