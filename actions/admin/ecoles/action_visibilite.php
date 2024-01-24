<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/admin/ecoles/action_visibilite.php **************/
/* Edition de la visibilité des tarifs *********************/
/* *********************************************************/
/* Dernière modification : le 18/12/14 *********************/
/* *********************************************************/



$tarifs_ecoles = $pdo->query('SELECT '.
		'e.id, '.
		'e.nom, '.
		'e.ecole_lyonnaise, '.
		'e.format_long, '.
		'COUNT(p.id) AS pnb, '.
		'te.id_tarif, '.
		'te.id AS id_tarif_ecole '.
	'FROM ecoles AS e '.
	'LEFT JOIN tarifs_ecoles AS te ON '.
		'te.id_ecole = e.id AND '.
		'te._etat = "active" '.
	'LEFT JOIN participants AS p ON '.
		'p.id_tarif_ecole = te.id AND '.
		'p.id_ecole = e.id AND '.
		'p._etat = "active" '.
	'WHERE '.
		'e._etat = "active" '.
	'GROUP BY '.
		'e.id, te.id_tarif, e.id, e.nom, e.ecole_lyonnaise, e.format_long, te.id '.
	'ORDER BY '.
		'ecole_lyonnaise DESC, '.
		'e.nom ASC')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$tarifs_ecoles = $tarifs_ecoles->fetchAll(PDO::FETCH_ASSOC);



$tarifs = $pdo->query('SELECT '.
		'id, '.
		'nom, '.
		'sportif, '.
		'ecole_lyonnaise, '.
		'format_long '.
	'FROM tarifs '.
	'WHERE '.
		'_etat = "active" '.
	'ORDER BY '.
		'sportif ASC, '.
		'nom ASC')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$tarifs = $tarifs->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);


$ecoles = [];
foreach ($tarifs_ecoles as $tarif) {
	if (!isset($ecoles[$tarif['id']]))
		$ecoles[$tarif['id']] = array_merge($tarif, array('tarifs' => []));

	$ecoles[$tarif['id']]['tarifs'][$tarif['id_tarif']] = [
		'pnb' => $tarif['pnb'], 
		'id_tarif_ecole' => $tarif['id_tarif_ecole']];
}


if (!empty($_POST['ecole']) &&
	!empty($_POST['tarif'])) {

	foreach ($ecoles as $eid => $ecole) {
		if ($eid != $_POST['ecole'])
			continue;
						
		foreach ($tarifs as $tid => $tarif) {
			if ($tid != $_POST['tarif'] ||
				$ecole['ecole_lyonnaise'] &&
				!$tarif['ecole_lyonnaise'] ||
				!$ecole['ecole_lyonnaise'] &&
				$tarif['ecole_lyonnaise'] || 
				!empty($ecoles[$eid]['tarifs'][$tid]) &&
				$ecoles[$eid]['tarifs'][$tid]['pnb'])
				continue;
			
			//Delete
			if (!empty($ecoles[$eid]['tarifs'][$tid])) {
				$ref = pdoRevision('tarifs_ecoles', $ecoles[$eid]['tarifs'][$tid]['id_tarif_ecole']);
				$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
				$pdo->exec('UPDATE tarifs_ecoles SET '.
						'_auteur = '.(int) $_SESSION['user']['id'].', '.
						'_ref = '.(int) $ref.', '.
						'_date = NOW(), '.
						'_message = "Suppression du lien du tarif associé à une école", '.
						'_etat = "desactive" '.
					'WHERE '.
						'id = '.(int) $ecoles[$eid]['tarifs'][$tid]['id_tarif_ecole']);
				$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
			}

			else {
				$pdo->exec('INSERT INTO tarifs_ecoles SET '.
					'_auteur = '.(int) $_SESSION['user']['id'].', '.
					'_date = NOW(), '.
					'_message = "Ajout du tarif associé à une école", '.
					'_etat = "active", '.
					//-----------//
					'id_ecole = '.(int) $eid.', '.
					'id_tarif = '.(int) $tid);
			}

			die('1');

			break;
		}

		break;
	}

	die;

} 

//Inclusion du bon fichier de template
require DIR.'templates/admin/ecoles/visibilite.php';
