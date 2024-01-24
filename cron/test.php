<?php

//if (is_connected()) {
/*	if (SMS_ACTIVE && 
		isValidSmsPhone(SMS_PHONE)) {
		sendSms(SMS_PHONE, 'challenge test '.rand());
	}
*/

	if (EMAIL_ACTIVE) {
		//Inclusion et démarrage de la bibliothèque PHPMailer
		require_once DIR.'includes/PHPMailer/PHPMailerAutoload.php';

		//Préparation de l'envoi des emails
		unset($mail);
		$mail = new PHPMailer();
		$mail->isSMTP();
		$mail->SMTPDebug = false;	// *vraiment* trop verbeux
		$mail->CharSet = 'UTF-8';
		$mail->Debugoutput = 'html';
		$mail->Host = EMAIL_SMTP;
		$mail->Port = EMAIL_PORT;
		$mail->SMTPSecure = EMAIL_SECURE;
		$mail->SMTPAuth = EMAIL_AUTH;
		$mail->Username = EMAIL_USER;
		$mail->Password = EMAIL_PASS;
		$mail->setFrom(EMAIL_MAIL, EMAIL_NAME);
		$mail->addReplyTo(EMAIL_MAIL, EMAIL_NAME);
		$mail->isHTML(true);


		$mail->Subject = 'test';
		$mail->addAddress(EMAIL_USER, EMAIL_USER);
		$mail->msgHTML('test');
		$mail->AltBody = 'test';

		$mail->Send();
		$mail->ClearAllRecipients();

		unset($mail);
	}
//}
