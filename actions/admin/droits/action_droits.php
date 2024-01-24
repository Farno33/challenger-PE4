<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/admin/droits/action_droits.php ******************/
/* Edition des droits **************************************/
/* *********************************************************/
/* Dernière modification : le 24/11/14 *********************/
/* *********************************************************/



$droits_admins = $pdo->query('SELECT '.
		'a.id, '.
		'nom, '.
		'prenom, '.
		'login, '.
		'module, '.
		'da.id AS id_droit_admin '.
	'FROM utilisateurs AS a '.
	'LEFT JOIN droits_admin AS da ON '.
		'da.id_utilisateur = a.id AND '.
		'da._etat = "active" '.
	'WHERE '.
		'a._etat = "active" AND '.
		'(a.responsable = 1 OR da.id IS NOT NULL)'.
	'ORDER BY '.
		'nom ASC, '.
		'prenom ASC, '.
		'login ASC')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$droits_admins = $droits_admins->fetchAll(PDO::FETCH_ASSOC);


$admins = [];
foreach ($droits_admins as $droit) {
	if (!isset($admins[$droit['id']]))
		$admins[$droit['id']] = array_merge($droit, array('modules' => []));

	$admins[$droit['id']]['modules'][$droit['id_droit_admin']] = $droit['module'];
}


if (!empty($_POST['utilisateur']) &&
	!empty($_POST['module']) &&
	$_POST['utilisateur'] != $_SESSION['user']['id']) {

	foreach ($admins as $aid => $admin) {
		if ($aid != $_POST['utilisateur'])
			continue;
						
		foreach ($modulesAdmin as $module => $titre) {
			if ($module != $_POST['module'])
				continue;
			
			//Delete
			if (in_array($module, $admin['modules'])) {
				$ref = pdoRevision('droits_admin', array_search($module, $admin['modules']));
				$pdo->exec('UPDATE droits_admin SET '.
						'_auteur = '.(int) $_SESSION['user']['id'].', '.
						'_ref = '.(int) $ref.', '.
						'_date = NOW(), '.
						'_message = "Suppression du droit à un utilisateur", '.
						'_etat = "desactive" '.
					'WHERE '.
						'id = '.(int) array_search($module, $admin['modules']));
			}

			else {
				$pdo->exec('INSERT INTO droits_admin SET '.
					'_auteur = '.(int) $_SESSION['user']['id'].', '.
					'_date = NOW(), '.
					'_message = "Ajout du droit à un utilisateur", '.
					'_etat = "active", '.
					//-----------//
					'id_utilisateur = '.(int) $aid.', '.
					'module = "'.$module.'"');
			}

			die('1');

			break;
		}

		break;
	}

	die;

}

//Inclusion du bon fichier de template
require DIR.'templates/admin/droits/droits.php';
