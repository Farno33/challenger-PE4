<?php

$adresseECL = true;
$registered = false;
$envoye = true;
$noerror = true;
$comptecree = false;
$admin = !empty($_SESSION['user']) && !empty($_SESSION['user']['privileges']);

function randomPassword()
{
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 12; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}
if (!empty($_POST['creation'])) {
    if (!empty($_POST['email']) && !empty($_POST['nom']) && !empty($_POST['prenom']) && !empty($_POST['sexe']) && !empty($_POST['tel'])) {
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $adresse_mail = $_POST['email'];
        $sexe = $_POST['sexe'];
        $tel = $_POST['tel'];


        //check email centrale first
        $pattern = "#@((ecl|alternance|auditeur|master)[0-9]{0,4}\.)?ec-lyon\.fr$#i";
        if (preg_match($pattern, $adresse_mail) || $admin) {
            $user = $pdo->query('SELECT id ' .
                'FROM utilisateurs ' .
                'WHERE ' .
                'email = "' . secure($adresse_mail) . '" AND ' .
                'cas = 1 AND ' .
                '_etat = "active"') or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
            $user = $user->fetch(PDO::FETCH_ASSOC);
            //Utilisateur centralien non existant, on le rajoute
            if (empty($user)) {

                $password = randomPassword();
                $envoye = false;
                $annee = date('Y');

                //Inclusion et démarrage de la bibliothèque PHPMailer
                require_once DIR . 'includes/PHPMailer/PHPMailerAutoload.php';

                //Préparation de l'envoi des emails
                unset($mail);
                $mail = new PHPMailer();
                $mail->isSMTP();
                $mail->SMTPDebug = false;
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


                $mail->Subject = "[Challenge] Confirmation de création de compte sur le Challenger";

                $mail->addAddress($adresse_mail);
                $mail->Body = "Bonjour,<br />
                    <br />Une demande de création de compte pour cette adresse email a été effectuée sur le Challenger (site d'inscription au Challenge).
                    <br />L'équipe du Challenge est ravie de te faire part des informations nécessaires pour te connecter et continuer ton inscription : <br />
                    <br />Identifiant : $adresse_mail
                    <br />Mot de passe : $password<br /> 
                    <br />Ces informations sont <b>À CONSERVER</b> tant que votre inscription n'est pas complète </b> <br /> 
                    <br />Rappel, le Challenger est accessible à l'adresse suivante : https://challenger.challenge-centrale-lyon.fr <br />
                    <br />À bientôt ! <br />
                    <br />L'équipe du Challenge $annee";

                if (!$mail->Send()) {
                    if (DEBUG_ACTIVE)
                        print_r($mail->ErrorInfo);
                    $envoye = false;
                    $noerror = false;
                } else {
                    $envoye = true;
                    $comptecree = true;
                }

                $mail->ClearAllRecipients();
                unset($mail);

                if ($envoye) {
                    $pdo->exec('INSERT INTO utilisateurs SET ' .
                        '_auteur = NULL, ' .
                        '_date = NOW(), ' .
                        '_message = "Ajout centralien automatiquement", ' .
                        '_etat = "active", ' .
                        //---------------//
                        'pass = "' . hashPass($password) . '", ' .
                        'login = "' . secure($adresse_mail) . '", ' .
                        'nom = "' . secure(ucname($nom)) . '", ' .
                        'prenom = "' . secure(ucname($prenom)) . '", ' .
                        'email = "' . secure($adresse_mail) . '", ' .
                        'telephone = "' . secure($tel) . '", ' .
                        'cas = 1, ' .
                        'sexe = "' . secure(ucname($sexe)) . '", ' .
                        'responsable = 0');
                }
            } else {
                $registered = true;
                $noerror = false;
            }
        } else {
            $adresseECL = false;
            $noerror = false;
        }
    } else {
        $noerror = false;
        $envoye = false;
    }
}

//Inclusion du bon fichier de template
require DIR . 'templates/public/creation.php';
