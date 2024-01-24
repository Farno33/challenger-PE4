<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/public/contact.php ******************************/
/* Page de contact *****************************************/
/* *********************************************************/
/* Dernière modification : le 12/12/14 *********************/
/* *********************************************************/

if (!empty($_SESSION['user']) && 
	!empty($_SESSION['user']['ecoles'])) {
	$responsables = $pdo->query('SELECT '.
			'e.id, '.
			'e.nom, '.
			'e.id_respo AS respo_ecole, '.
			's.id_respo AS respo_sport, '.
			's.id AS sport '.
		'FROM ecoles AS e '.
		'LEFT JOIN ecoles_sports AS es ON '.
			'es.id_ecole = e.id AND '.
			'es._etat = "active" '.
		'LEFT JOIN sports AS s ON '.
			's.id = es.id_sport AND '.
			's._etat = "active" '.
		'WHERE '.
			implode(' OR ',array_map( 
				function($id) {
					return 'e.id = '.$id;
				}, $_SESSION['user']['ecoles'])))
		->fetchAll(PDO::FETCH_ASSOC);

	$ecoles = [];
	foreach ($responsables as $responsable) {
		if (empty($ecoles[$responsable['id']])) {
			$ecoles[$responsable['id']] = [
				'nom' => $responsable['nom'],
				'respos' => [
					$responsable['respo_ecole'] => [
						'ecole' => true,
						'sports' => []
					]
				]
			];
		}

		if (empty($ecoles[$responsable['id']]['respos'][$responsable['respo_sport']]))
			$ecoles[$responsable['id']]['respos'][$responsable['respo_sport']] = [
				'ecole' => false,
				'sports' => []];

		$ecoles[$responsable['id']]['respos'][$responsable['respo_sport']]['sports'][] = $responsable['sport'];
	}


	$sports = $pdo->query('SELECT '.
			's.id, '.
			's.sport, '.
			's.sexe '.
		'FROM sports AS s '.
		'WHERE s._etat = "active"')
		->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);
}

$contacts = $pdo->query('SELECT '.
		'u.id, '.
		'c.id AS cid, '.
		'c.poste, '.
		'c.photo, '.
		'u.login, '.
		'u.nom, '.
		'u.prenom, '.
		'u.email, '.
		'u.telephone '.
	'FROM utilisateurs AS u '.
	'LEFT JOIN contacts AS c ON '.
		'u.id = c.id_utilisateur AND '.
		'c._etat = "active" '.
	'WHERE '.
		'u._etat = "active" '.
	'ORDER BY '.
		'nom ASC, '.
		'prenom ASC')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$contacts = $contacts->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);


//Inclusion du bon fichier de template
require DIR.'templates/public/contact.php';