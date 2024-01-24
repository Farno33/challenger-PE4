<?php
require DIR . 'includes/FetchImap/autoload.php';

/*
use Fetch\Server;
use Fetch\Message;
*/

// open connection
$server = new Fetch\Server(EMAIL_IMAP, EMAIL_PORT_IMAP);
$server->setAuthentication(EMAIL_USER, EMAIL_PASS);
$emails = $server->getMessages();

$insert = $pdo->prepare('INSERT INTO recus SET ' .
	'type = "email", ' .
	'`from` = :from, ' .
	'id_participant = :id_participant, ' .
	'titre = :titre, ' .
	'message = :message, ' .
	'ouvert = 0, ' .
	'_date = :date');

$search = $pdo->prepare('SELECT id ' .
	'FROM participants ' .
	'WHERE ' .
	'_etat = "active" AND ' .
	'email = :email');

$date = new DateTime();
$new = false;
foreach ($emails as $k => $email) {
	if (!$email->checkFlag(Fetch\Message::FLAG_SEEN)) {
		$from = $email->getAddresses('from');
		$addr = empty($from['address']) ? '' : trim($from['address']);

		$from = empty($from['name']) ? $addr : $from['name'] . ' <' . $addr . '>';
		$search->execute([':email' => $addr]);
		$id = $search->fetch(PDO::FETCH_ASSOC);
		$date->setTimestamp($email->getDate());
		$subject = $email->getSubject();
		$body = $email->getMessageBody(true);
		$body = empty($body) ? nl2br(htmlentities($email->getPlainTextBody())) : $body;
		$attachements = $email->getAttachments();
		$images = [];

		if (
			$addr == EMAIL_MAIL &&
			strpos(trim($subject), 'test') === 0 &&
			strpos(trim($body), 'test') === 0
		) {
			$exists = $pdo->query('SELECT id ' .
				'FROM recus WHERE ' .
				'`from` LIKE "%' . $addr . '%" AND ' .
				'titre LIKE "test%" AND ' .
				'message LIKE "test%"')
				->fetch(PDO::FETCH_ASSOC);

			if (!empty($exists)) {
				$pdo->exec('UPDATE recus SET ' .
					'_date = "' . $date->format('Y-m-d H:i:s') . '" ' .
					'WHERE ' .
					'id = ' . $exists['id']);

				$email->setFlag(Fetch\Message::FLAG_SEEN);
				$email->delete();
				continue;
			}
		}
		/*
				var_dump($attachements);
array(1) {
  [0]=>
  object(Fetch\Attachment)#44 (9) {
    ["structure":protected]=>
    object(stdClass)#38 (15) {
      ["type"]=>
      int(5)
      ["encoding"]=>
      int(3)
      ["ifsubtype"]=>
      int(1)
      ["subtype"]=>
      string(3) "PNG"
      ["ifdescription"]=>
      int(1)
      ["description"]=>
      string(34) "Capture-20221204162319-734x555.png"
      ["ifid"]=>
      int(1)
      ["id"]=>
      string(38) "<1b343501-3196-4947-bb9d-8cc37a9045dd>"
      ["bytes"]=>
      int(434838)
      ["ifdisposition"]=>
      int(1)
      ["disposition"]=>
      string(6) "inline"
      ["ifdparameters"]=>
      int(1)
      ["dparameters"]=>
      array(4) {
        [0]=>
        object(stdClass)#39 (2) {
          ["attribute"]=>
          string(8) "filename"
          ["value"]=>
          string(34) "Capture-20221204162319-734x555.png"
        }
        [1]=>
        object(stdClass)#40 (2) {
          ["attribute"]=>
          string(4) "size"
          ["value"]=>
          string(6) "317764"
        }
        [2]=>
        object(stdClass)#41 (2) {
          ["attribute"]=>
          string(13) "creation-date"
          ["value"]=>
          string(29) "Mon, 20 Feb 2023 15:53:36 GMT"
        }
        [3]=>
        object(stdClass)#42 (2) {
          ["attribute"]=>
          string(17) "modification-date"
          ["value"]=>
          string(29) "Mon, 20 Feb 2023 15:53:36 GMT"
        }
      }
      ["ifparameters"]=>
      int(1)
      ["parameters"]=>
      array(1) {
        [0]=>
        object(stdClass)#43 (2) {
          ["attribute"]=>
          string(4) "name"
          ["value"]=>
          string(34) "Capture-20221204162319-734x555.png"
        }
      }
    }
    ["messageId":protected]=>
    int(61455)
    ["imapStream":protected]=>
    object(IMAP\Connection)#8 (0) {
    }
    ["partId":protected]=>
    int(2)
    ["filename":protected]=>
    string(34) "Capture-20221204162319-734x555.png"
    ["size":protected]=>
    int(434838)
    ["data":protected]=>
    NULL
    ["mimeType"]=>
    string(9) "image/png"
    ["encoding"]=>
    int(3)
  }
}
*/
		foreach ($attachements as $k => $attachement) {
			$cid = $attachement->getStructure()->id;
			$images[$cid] = $k;
		}


		$body = preg_replace_callback(
			'#(<img (?>(?!src=")[^>])*?src="(?=cid:))([^"]*)("[^>]*>)#i',
			function ($match) use (&$images, &$attachements) {
				list($total, $before, $data, $after) = $match;
				$attachement = $attachements[$images["<" . substr($data, 4) . ">"]];

				if ($attachement === null) {
					return $total;	// on change rien car on a pas trouvé l'image
				}

				$im = 'data:' . $attachement->getMimeType() . ';base64,' . base64_encode($attachement->getData());
				return "$before$im$after";  // new <img> tag
			},
			$body
		);


		$new = true;
		$insert->execute([
			':from' => $from,
			':id_participant' => !empty($id) ? $id['id'] : null,
			':titre' => removeUnicode($subject),
			':message' => removeUnicode($body),
			':date' => $date->format('Y-m-d H:i:s')
		]);
	}

	$email->setFlag(Fetch\Message::FLAG_SEEN);
	$email->delete();
}

$server->expunge();
unset($server);
