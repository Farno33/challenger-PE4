<?php 

/*
require_once DIR."includes/ImapClient/ImapClientException.php";
require_once DIR."includes/ImapClient/ImapConnect.php";
require_once DIR."includes/ImapClient/ImapClient.php";

use SSilence\ImapClient\ImapClientException;
use SSilence\ImapClient\ImapConnect;
use SSilence\ImapClient\ImapClient;


// open connection
$imap = new ImapClient([
    'flags' => [
        'service' => ImapConnect::SERVICE_IMAP,
        'encrypt' => null,
        'debug' => null,
    ],
    'mailbox' => [
        'remote_system_name' => EMAIL_IMAP,
        'port' => 143,
    ],
    'connect' => [
        'username' => EMAIL_USER,
        'password' => EMAIL_PASS
    ]
]);

$imap->selectFolder('INBOX');
$emails = @$imap->getMessages();

$insert = $pdo->prepare('INSERT INTO recus SET '.
	'type = "email", '.
	'`from` = :from, '.
	'id_participant = :id_participant, '.
	'titre = :titre, '.
	'message = :message, '.
	'ouvert = 0, '.
	'_date = :date');

$search = $pdo->prepare('SELECT id '.
	'FROM participants '.
	'WHERE '.
		'_etat = "active" AND '.
		'email = :email');

$date = new DateTime();

foreach ($emails as $k => $email) {
	if ($email['unread']) {
		$addr = isValidEmail($email['from']) ? $email['from'] : 
			preg_replace('/^(?:.*)<([^>]*)>$/', '$1', $email['from']);

		$search->execute([':email' => $addr]);
		$id = $search->fetch(PDO::FETCH_ASSOC);
		$date->setTimestamp($email['udate']); 

		if ($addr == EMAIL_MAIL &&
			strpos(trim($email['subject']), 'test') === 0 &&
			strpos(trim($email['body']), 'test') === 0) {
			$exists = $pdo->query('SELECT id '.
				'FROM recus WHERE '.
					'`from` LIKE "%'.$addr.'%" AND '.
					'titre LIKE "test%" AND '.
					'message LIKE "test%"')
				->fetch(PDO::FETCH_ASSOC);

			if (!empty($exists)) {
				$pdo->exec('UPDATE recus SET '.
						'_date = "'.$date->format('Y-m-d H:i:s').'" '.
					'WHERE '.
						'id = '.$exists['id']);

				$imap->setUnseenMessage($email['uid']);
				$imap->deleteMessage($email['uid']);
				continue;
			}

		}

		$insert->execute([
			':from' => $email['from'],
			':id_participant' => !empty($id) ? $id['id'] : null, 
			':titre' => removeUnicode($email['subject']), 
			':message' => removeUnicode($email['body']), 
			':date' => $date->format('Y-m-d H:i:s')]);
	}

	$imap->setUnseenMessage($email['uid']);
	$imap->deleteMessage($email['uid']);
}

$imap->hideErrors();
$imap->close();
unset($imap);*/

