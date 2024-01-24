<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/admin/ecoles/action_accueil.php *****************/
/* Accueil du module des Ecoles, gestion du message ********/
/* *********************************************************/
/* Dernière modification : le 21/11/14 *********************/
/* *********************************************************/


if (!empty($_POST['edit']) &&
	isset($_POST['message'])) {

	$confs = $pdo->query('SELECT flag, id FROM configurations WHERE '.
		'(flag = "APP_ACTIVE_MESSAGE" OR flag = "APP_MESSAGE_LOGIN") AND '.
		'_etat = "active"')
		->fetchAll(PDO::FETCH_UNIQUE);


	foreach (['APP_ACTIVE_MESSAGE', 'APP_MESSAGE_LOGIN'] as $flag) {
		if (isset($confs[$flag])) {
			$ref = pdoRevision('configurations', $confs[$flag]['id']);
			$pdo->exec('UPDATE configurations SET '.
					'_auteur = '.(int) $_SESSION['user']['id'].', '.
					'_ref = '.(int) $ref.', '.
					'_date = NOW(), '.
					'_message = "Modification de la constante", '.
					//-----------------//
					'valeur = "'.($flag == 'APP_ACTIVE_MESSAGE' ? 
						(!empty($_POST['active']) ? 'true' : 'false') :
						secure($_POST['message'])).'" '.
				'WHERE '.
					'id = '.$confs[$flag]['id']) or die(print_r($pdo->errorInfo()));
		}


		else {
			$pdo->exec('INSERT INTO configurations SET '.
				'_auteur = '.(int) $_SESSION['user']['id'].', '.
				'_date = NOW(), '.
				'_message = "Ajout de la constante", '.
				//-----------------//
				'flag = "'.$flag.'", '.
				'valeur = "'.($flag == 'APP_ACTIVE_MESSAGE' ? 
					(!empty($_POST['active']) ? 'true' : 'false') :
					secure($_POST['message'])).'", '.
				'nom = "'.($flag == 'APP_ACTIVE_MESSAGE' ? 
					'Activation du message affiché sur la page de login' :
					 'Message affiché sur la page de login des écoles').'"') or die(print_r($pdo->errorInfo()));
		}
	}

	$edit = true;
} 


$_POST['active'] = empty($_POST['edit']) ? 
	(empty(APP_ACTIVE_MESSAGE) ? false : APP_ACTIVE_MESSAGE) : !empty($_POST['active']);

$_POST['message'] = empty($_POST['message']) ? 
	(empty(APP_MESSAGE_LOGIN) ? null : APP_MESSAGE_LOGIN) : $_POST['message'];


//Inclusion du bon fichier de template
require DIR.'templates/admin/ecoles/message.php';
