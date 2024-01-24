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


if (!empty($_GET['p']) &&
	$_GET['p'] == 'ajax' &&
	!empty($_POST['filtre']) &&
	strlen($_POST['filtre']) >= 2) {

	$utilisateurs_ = $pdo->query($s='SELECT '.
			'u.login, '.
			'u.id, '.
			'u.prenom, '.
			'u.nom '.
	    'FROM utilisateurs AS u '.
	    'WHERE '.
	    	'u._etat = "active" AND '.
	    	'u.id NOT IN (SELECT de.id_utilisateur '.
	    		'FROM droits_ecoles AS de '.
	    		'WHERE '.
	    			'de.id_ecole = '.$ecole['id'].' AND '.
	    			'de._etat = "active") AND ('.
            'CONCAT(prenom, " ", nom) LIKE "%'.addslashes($_POST['filtre']).'%" OR '.
            'CONCAT(nom, " ", prenom) LIKE "%'.addslashes($_POST['filtre']).'%" OR '.
			'login LIKE "%'.addslashes($_POST['filtre']).'%") '.
	    'ORDER BY nom ASC, prenom ASC '.
	    'LIMIT 15') 
		->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);

	$utilisateurs = [];
	foreach ($utilisateurs_ as $login => $utilisateur) 
	    $utilisateurs[] = [
	            'id' => $utilisateur['id'],
	            'nom' => stripslashes($utilisateur['prenom'].' '.$utilisateur['nom']),
	            'value' => stripslashes($utilisateur['prenom'].' '.$utilisateur['nom'].' ('.$login.')') //HTML
	    ];


	//Envoi des données récupérées en JSON
	header('Content-Type: application/json', true);
	echo json_encode($utilisateurs);
	exit;

}



$respos = $pdo->query('SELECT '.
		'id, '.
		'prenom, '.
		'nom '.
	'FROM utilisateurs '.
	'WHERE '.
		'responsable = 1 AND '.
		'_etat = "active" '.
	'ORDER BY '.
		'nom ASC, '.
		'prenom ASC')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$respos = $respos->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);

if (!empty($_GET['dimg']) &&
	$_GET['dimg'] == $ecole['token']) {
	$ref = pdoRevision('images', $ecole['id_image']);
	$pdo->exec('UPDATE images SET '.
			'_auteur = '.(int) $_SESSION['user']['id'].', '.
			'_date = NOW(), '.
			'_ref = '.(int) $ref.', '.
			'_message = "Suppression de l\'image", '.
			'_etat = "desactive", '.
			//---------// (On met image à NULL pour éviter d'utiliser trop d'espace)
			'image = NULL '.
		'WHERE '.
			'id = '.(int) $ecole['id_image']);

	$ref = pdoRevision('ecoles', $ecole['id']);
	$pdo->exec('UPDATE ecoles SET '.
			'_auteur = '.(int) $_SESSION['user']['id'].', '.
			'_date = NOW(), '.
			'_ref = '.(int) $ref.', '.
			'_message = "Suppression de l\'image", '.
			//--------//
			'id_image = NULL '.
		'WHERE '.
			'id = '.(int) $ecole['id']);

	$ecole['id_image'] = null;
	$ecole['token'] = null;
}

if (!empty($_POST['maj']) &&
	!empty($_POST['nom']) &&
	isset($_POST['abreviation']) &&
	isset($_POST['adresse']) &&
	isset($_POST['code_postal']) && 
	isset($_POST['ville']) &&
	isset($_POST['email_ecole']) &&
	isset($_POST['telephone_ecole']) &&
	isset($_POST['as_code']) &&
	isset($_POST['langue']) &&
	in_array($_POST['langue'], array_keys($languesEcole)) &&
	isset($_POST['nom_respo']) &&
	isset($_POST['prenom_respo']) &&
	isset($_POST['email_respo']) &&
	isset($_POST['telephone_respo']) &&
	isset($_POST['nom_corespo']) &&
	isset($_POST['prenom_corespo']) &&
	isset($_POST['email_corespo']) &&
	isset($_POST['telephone_corespo']) &&
	isset($_POST['commentaire'])) {
	
	$_POST['ecole_lyonnaise'] = !empty($_POST['ecole_lyonnaise']) ? '1' : '0';
	$_POST['format_long'] = !empty($_POST['format_long']) ? '1' : '0';

	if (!empty($_FILES['image']) &&
		empty($_FILES['image']['error']) &&
		isset($_FILES['image']['tmp_name']) &&
		file_exists($_FILES['image']['tmp_name']) &&
		isset($_FILES['image']['size']) &&
		$_FILES['image']['size'] <= 256 * 1024) {
		$info = getimagesize($_FILES['image']['tmp_name']);

		if ($info !== false &&
			in_array($info['mime'], [
				'image/jpeg',
				'image/gif',
				'image/png'])) {
			
			if ($info['mime'] == 'image/jpeg')
				$im = imagecreatefromjpeg($_FILES['image']['tmp_name']);

			else if ($info['mime'] == 'image/gif')
				$im = imagecreatefromgif($_FILES['image']['tmp_name']);

			else //image/png
				$im = imagecreatefrompng($_FILES['image']['tmp_name']);

			list($w, $h) = $info;
			$r = $h == 0 ? 0 : $w / $h;
			$nh = min($h, 200);
			$nw = $r * $nh;

			if ($nw > 300) {
				$nw = min($w, 300);
				$nh = $r == 0 ? 0 : $nw / $r;
			}

			$nim = imagecreatetruecolor($nw, $nh);
		    imagealphablending($nim, false);
		    imagesavealpha($nim, true);

		    ob_start();
			imagecopyresampled($nim, $im, 0, 0, 0, 0, $nw, $nh, $w, $h);
			imagepng($nim);
			$output = ob_get_clean();
			$token = sha1(uniqid());
			$_POST['token'] = $token;
			$_POST['width'] = $nw;
			$_POST['height'] = $nh;

			if (!empty($ecole['id_image'])) {
				$image = $ecole['id_image'];
				$ref = pdoRevision('images', $ecole['id_image']);
				$pdo->exec('UPDATE images SET '.
						'_auteur = '.(int) $_SESSION['user']['id'].', '.
						'_date = NOW(), '.
						'_ref = '.(int) $ref.', '.
						'_message = "Modification de l\'image", '.
						//---------------//
						'image = "'.secure(base64_encode($output)).'", '.
						'width = '.(int) $nw.', '.
						'height = '.(int) $nh.', '.
						'token = "'.$token.'" '.
					'WHERE '.
						'id = '.(int) $ecole['id_image']);
			} else {
				$pdo->exec('INSERT INTO images SET '.
					'_auteur = '.(int) $_SESSION['user']['id'].', '.
					'_date = NOW(), '.
					'_message = "Ajout de l\'image", '.
					//---------------//
					'image = "'.secure(base64_encode($output)).'", '.
					'width = '.(int) $nw.', '.
					'height = '.(int) $nh.', '.
					'token = "'.$token.'"');

				$image = $pdo->lastInsertId();
				$_POST['id_image'] = $image;
			}
		}
	}

	$erreur_maj = false;
	$ref = pdoRevision('ecoles', $ecole['id']);
	$pdo->exec('set FOREIGN_KEY_CHECKS = 0');
	$pdo->exec('UPDATE ecoles SET '.
			'_auteur = '.(int) $_SESSION['user']['id'].', '.
			'_ref = '.(int) $ref.', '.
			'_date = NOW(), '.
			'_message = "Modification des données de l\'école", '.
			//-------------//
			(!empty($image) ? 'id_image = '.(int) $image.', ' : '').
			'nom = "'.secure($_POST['nom']).'", '.
			'abreviation = "'.secure($_POST['abreviation']).'", '.
			($ecole['nb_inscriptions'] == 0 ? 'ecole_lyonnaise = '.secure($_POST['ecole_lyonnaise']).', ' : '').
			($ecole['nb_inscriptions'] == 0 ? 'format_long = '.(empty($_POST['format_long']) ? 0 : secure($_POST['format_long'])).', ' : '').
			'adresse = "'.secure($_POST['adresse']).'", '.
			'code_postal = "'.secure($_POST['code_postal']).'", '.
			'ville = "'.secure($_POST['ville']).'", '.
			'email_ecole = "'.secure($_POST['email_ecole']).'", '.
			'telephone_ecole = "'.secure($_POST['telephone_ecole']).'", '.
			'as_code = "'.secure($_POST['as_code']).'", '.
			'langue = "'.secure($_POST['langue']).'", '.
			'nom_respo = "'.secure($_POST['nom_respo']).'", '.
			'prenom_respo = "'.secure($_POST['prenom_respo']).'", '.
			'email_respo = "'.secure($_POST['email_respo']).'", '.
			'telephone_respo = "'.secure($_POST['telephone_respo']).'", '.
			'nom_corespo = "'.secure($_POST['nom_corespo']).'", '.
			'prenom_corespo = "'.secure($_POST['prenom_corespo']).'", '.
			'email_corespo = "'.secure($_POST['email_corespo']).'", '.
			'telephone_corespo = "'.secure($_POST['telephone_corespo']).'", '.
			'commentaire = "'.secure($_POST['commentaire']).'" '.
		'WHERE '.
			'id = '.$ecole['id']);
	$pdo->exec('set FOREIGN_KEY_CHECKS = 1');

	$_POST['id'] = $ecole['id'];

	foreach ($ecole as $label => $value)
		if (!isset($_POST[$label]))
			$_POST[$label] = $value;

	$ecole = $_POST;

} else if (!empty($_POST['maj']))
	$erreur_maj = 'champs';



if (!empty($_POST['quotas']) &&
	isset($_POST['quota_total']) && (
		intval($_POST['quota_total']) > 0 ||
		empty($_POST['quota_total'])) && 
	isset($_POST['quota_sportif']) && (
		intval($_POST['quota_sportif']) > 0 ||
		empty($_POST['quota_sportif'])) &&
	isset($_POST['quota_nonsportif']) && (
		intval($_POST['quota_nonsportif']) > 0 ||
		empty($_POST['quota_nonsportif'])) &&
	isset($_POST['quota_logement']) && (
		intval($_POST['quota_logement']) > 0 ||
		empty($_POST['quota_logement'])) &&
	isset($_POST['quota_filles_logees']) && (
		intval($_POST['quota_filles_logees']) > 0 ||
		empty($_POST['quota_filles_logees'])) &&
	isset($_POST['quota_garcons_loges']) && (
		intval($_POST['quota_garcons_loges']) > 0 ||
		empty($_POST['quota_garcons_loges'])) && 
	isset($_POST['quota_pompom']) && (
		intval($_POST['quota_pompom']) > 0 ||
		empty($_POST['quota_pompom'])) &&
	isset($_POST['quota_pompom_nonsportif']) && (
		intval($_POST['quota_pompom_nonsportif']) > 0 ||
		empty($_POST['quota_pompom_nonsportif'])) &&
	isset($_POST['quota_fanfaron']) && (
		intval($_POST['quota_fanfaron']) > 0 ||
		empty($_POST['quota_fanfaron'])) &&
	isset($_POST['quota_fanfaron_nonsportif']) && (
		intval($_POST['quota_fanfaron_nonsportif']) > 0 ||
		empty($_POST['quota_fanfaron_nonsportif'])) &&
	isset($_POST['quota_cameraman']) && (
		intval($_POST['quota_cameraman']) > 0 ||
		empty($_POST['quota_cameraman'])) &&
	isset($_POST['quota_cameraman_nonsportif']) && (
		intval($_POST['quota_cameraman_nonsportif']) > 0 ||
		empty($_POST['quota_cameraman_nonsportif']))) 
{
	$quotas_active['total'] = !empty($_POST['quota_total_on']);
	$quotas_active['sportif'] = !empty($_POST['quota_sportif_on']);
	$quotas_active['nonsportif'] = !empty($_POST['quota_nonsportif_on']);
	$quotas_active['logement'] = !empty($_POST['quota_logement_on']);
	$quotas_active['filles_logees'] = !empty($_POST['quota_filles_on']);
	$quotas_active['garcons_loges'] = !empty($_POST['quota_garcons_on']);
	$quotas_active['pompom'] = !empty($_POST['quota_pompom_on']);
	$quotas_active['fanfaron'] = !empty($_POST['quota_fanfaron_on']);
	$quotas_active['cameraman'] = !empty($_POST['quota_cameraman_on']);
	$quotas_active['pompom_nonsportif'] = !empty($_POST['quota_pompom_nonsportif_on']);
	$quotas_active['fanfaron_nonsportif'] = !empty($_POST['quota_fanfaron_nonsportif_on']);
	$quotas_active['cameraman_nonsportif'] = !empty($_POST['quota_cameraman_nonsportif_on']);

	$error = false;
	if( $quotas_active['total'])
	{
		//Verification de la cohérence des quotas
		$error |= ($quotas_active['sportif'] && $_POST['quota_sportif'] > $_POST['quota_total']);
		$error |= ($quotas_active['nonsportif'] && $_POST['quota_nonsportif'] > $_POST['quota_total']);
		if ($quotas_active['logement'])
		{
			$error |= $_POST['quota_logement'] > $_POST['quota_total'];
			$error |= $quotas_active['filles_logees'] * $_POST['filles_logees'] > $_POST['quota_logement'];
			$error |= $quotas_active['garcons_loges'] * $_POST['garcons_loges'] > $_POST['quota_logement'];
		}
		foreach(['pompom', 'fanfaron', 'cameraman'] as $quotaName)
		{	
			$error |= ($quotas_active[$quotaName.'_nonsportif'] && $quotas_active[$quotaName] && $_POST['quota_'.$quotaName.'_nonsportif'] > $_POST['quota_'.$quotaName]);
			$error |= ($quotas_active[$quotaName.'_nonsportif'] && $quotas_active['nonsportif'] && $_POST['quota_'.$quotaName.'_nonsportif'] > $_POST['quota_nonsportif']);
		}

		//Vérification que les quotas n'entre pas en conflit avec les inscriptions déjà remplies
		$error |= ($_POST['quota_total'] < $ecole['nb_inscriptions']);	
		$error |= ($quotas_active['nonsportif'] && $_POST['quota_nonsportif'] < $ecole['nb_inscriptions'] - $ecole['nb_sportif']);
		
		foreach(['sportif', 'filles_logees', 'garcons_loges', 'pompom', 'fanfaron', 'cameraman'] as $quotaName)
		{
			$error |= $quotas_active[$quotaName] && $_POST['quota_'.$quotaName] < $ecole['nb_'.$quotaName];
		}
	}
	if ($error)
	{
		$erreur_quotas = 'quotas';
	} 
	else 
	{
		$erreur_quotas = false;	
		
		foreach ($quotas_active as $quota => $active) 
		{
			if (!isset($quotas[$quota]) &&
				$active)
				$pdo->exec('INSERT INTO quotas_ecoles SET '.
					'_auteur = '.(int) $_SESSION['user']['id'].', '.
					'_date = NOW(), '.
					'_message = "Ajout d\'un quota", '.
					'_etat = "active", '.
					//-------------//
					'id_ecole = '.(int) $ecole['id'].', '.
					'quota = "'.secure($quota).'", '.
					'valeur = '.(empty($_POST['quota_'.$quota]) ? 0 : (int) $_POST['quota_'.$quota])) or die(print_r($pdo->errorInfo()));
					

			else if (isset($quotas[$quota]) && $active && (int) $quotas[$quota]['valeur'] != (int) $_POST['quota_'.$quota]) 
			{
				$ref = pdoRevision('quotas_ecoles', $quotas[$quota]['id']);
				$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
				$pdo->exec('UPDATE quotas_ecoles SET '.
						'_auteur = '.(int) $_SESSION['user']['id'].', '.
						'_ref = '.(int) $ref.', '.
						'_date = NOW(), '.
						'_message = "Modification d\'un quota", '.
						//-------------//
						'valeur = '.(empty($_POST['quota_'.$quota]) ? 0 : (int) $_POST['quota_'.$quota]).' '.
					'WHERE id = '.(int) $quotas[$quota]['id']);
				$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
			}

			else if (isset($quotas[$quota]) &&
				!$active) {
				$ref = pdoRevision('quotas_ecoles', $quotas[$quota]['id']);
				$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
				$pdo->exec('UPDATE quotas_ecoles SET '.
						'_auteur = '.(int) $_SESSION['user']['id'].', '.
						'_ref = '.(int) $ref.', '.
						'_date = NOW(), '.
						'_etat = "desactive", '.
						'_message = "Suppression d\'un quota", '.
						//-------------//
						'valeur = '.(empty($_POST['quota_'.$quota]) ? 0 : (int) $_POST['quota_'.$quota]).' '.
					'WHERE id = '.(int) $quotas[$quota]['id']);
				$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
			}


			if (!empty($quotas_active[$quota]) && 
				$active)
				$quotas[$quota] = ['valeur' => empty($_POST['quota_'.$quota]) ? 0 : (int) $_POST['quota_'.$quota]];

			else
				unset($quotas[$quota]);
		}
	}
} else if (!empty($_POST['quotas']))
	$erreur_quotas = 'champs';




if (!empty($_POST['change_etat']) &&
	!empty($_POST['etat']) &&
	in_array($_POST['etat'], array('fermee', 'ouverte', 'limitee', 'close', 'validee')) &&
	isset($_POST['respo']) && (
		in_array($_POST['respo'], array_keys($respos)) ||
		empty($_POST['respo']) &&
		$_POST['etat'] == 'fermee') &&
	isset($_POST['malus']) &&
	is_numeric($_POST['malus']) &&
	$_POST['malus'] >= 0) {

	$caution = !empty($_POST['caution']);
	$caution_logement = !empty($_POST['caution_logement']);
	$charte = !empty($_POST['charte']);


	$ecole['id_respo'] = !empty($_POST['respo']) ? $_POST['respo'] : null;
	$ecole['etat_inscription'] = $_POST['etat'];
	$ecole['malus'] = abs((float) $_POST['malus']);
	$ecole['caution_recue'] = !empty($_POST['caution']);
	$ecole['caution_logement'] = !empty($_POST['caution_logement']);
	$ecole['charte_acceptee'] = !empty($_POST['charte']);


	$ref = pdoRevision('ecoles', $ecole['id']);
	$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
	$pdo->exec('UPDATE ecoles SET '.
			'_auteur = '.(int) $_SESSION['user']['id'].', '.
			'_ref = '.(int) $ref.', '.
			'_date = NOW(), '.
			'_message = "Modification de l\'état de l\'école", '.
			//-------------//
			'etat_inscription = "'.$_POST['etat'].'", '.
			'malus = '.abs((float) $_POST['malus']).', '.
			(!empty($_POST['respo']) ? 'id_respo = '.$_POST['respo'].', ' : '').
			'caution_recue = '.(int) !empty($_POST['caution']).', '.
			'caution_logement = '.(int) !empty($_POST['caution_logement']).', '.
			'charte_acceptee = '.(int) !empty($_POST['charte']).' '.
		'WHERE '.
			'id = '.$ecole['id']);
	$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
	$erreur_etat = false;
} else if (!empty($_POST['change_etat']))
	$erreur_etat = 'champs';


$sportifs = $pdo->query('SELECT '.
		'p.id, '.
		'p.nom, '.
		'p.prenom, '.
		'p.sexe, '.
		'p.licence, '.
		'p.certificat_medical, '.
		'p.certificat_assurance '.
	'FROM participants AS p '.
	'WHERE '.
		'p._etat = "active" AND '.
		'p.id_ecole = '.(int) $ecole['id'].' '.
	'ORDER BY p.nom ASC, p.prenom ASC')
	->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);



if (!empty($_POST['action']) &&
	$_POST['action'] == 'changeCertificat' &&
	!empty($_POST['id']) &&
	intval($_POST['id']) &&
	in_array($_POST['id'], array_keys($sportifs)) &&
	!empty($_POST['type']) &&
	in_array($_POST['type'], ['medical', 'assurance']) &&
	isset($_POST['check'])) {
	$ref = pdoRevision('participants', $_POST['id']);
	$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
	$pdo->exec('UPDATE participants SET '.
			'_auteur = '.(int) $_SESSION['user']['id'].', '.
			'_ref = '.(int) $ref.', '.
			'_date = NOW(), '.
			'_message = "Modification du certifical '.$_POST['type'].'", '.
			//-------------//
			'certificat_'.$_POST['type'].' = '.(!empty($_POST['check']) ? '1' : '0').' '.
		'WHERE '.
			'id = '.$_POST['id']);
	$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

	die('');
}


$sports = $pdo->query($sports_sql = 'SELECT '.
		's.id, '.
		's.sport, '.
		's.sexe, '.
		's.quota_inscription, '.
		'es.quota_equipes, '.
		'es.quota_max, '.
		'es.quota_reserves, '.
		'SUM(autres.quota_max) AS quota_sum, '.
		'(SELECT COUNT(e.label) '.
			'FROM equipes AS e '.
			'WHERE '.
				'e.id_ecole_sport = es.id AND '.
				'e._etat = "active") AS equipes, '.
		'(SELECT COUNT(DISTINCT sp.id_participant) '.
			'FROM sportifs AS sp '.
			'JOIN equipes AS eq ON '.
				'eq.id = sp.id_equipe AND '.
				'eq._etat = "active" '.
			'WHERE '.
				'eq.id_ecole_sport = es.id AND '.
				'sp._etat = "active") AS inscriptions '.
	'FROM ecoles_sports AS es '.
	'JOIN sports AS s ON '.
		's.id = es.id_sport AND '.
		's._etat = "active" '.
	'LEFT JOIN ecoles_sports AS autres ON '.
		'autres.id_ecole <> es.id_ecole AND '.
		'autres.id_sport = es.id_sport AND '.
		'autres._etat = "active" '.
	'WHERE '.
		'es.id_ecole = '.$ecole['id'].' AND '.
		'es._etat = "active" '.
	'GROUP BY '.
		's.id, s.sport, s.sexe, s.quota_inscription, es.quota_equipes, es.quota_max, es.quota_reserves, es.id '.
		'ORDER BY '.
			's.sport ASC')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$sports = $sports->fetchAll(PDO::FETCH_ASSOC);


$sports_ajout = $pdo->query($sports_ajout_sql = 'SELECT '.
		's.sport, '.
		's.id, '.
		's.sexe, '.
		's.quota_inscription, '.
		'SUM(es.quota_max) AS quota_sum '.
	'FROM sports AS s '.
	'LEFT JOIN ecoles_sports AS es ON '.
		'es.id_ecole <> es.id_ecole AND '.
		'es.id_sport = s.id AND '.
		'es._etat = "active" '.
	'WHERE '.
		's.id NOT IN ('.
		'SELECT '.
			'es2.id_sport '.
		'FROM ecoles_sports AS es2 '.
		'WHERE '.
			'es2.id_ecole = '.$ecole['id'].' AND '.
			'es2._etat = "active") AND '.
		's._etat = "active" '.
	'GROUP BY '.
		's.id '.
	'ORDER BY '.
		's.sport ASC')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$sports_ajout = $sports_ajout->fetchAll(PDO::FETCH_ASSOC);


$utilisateurs = $pdo->query($utilisateurs_sql = 'SELECT '.
		'id, '.
		'nom, '.
		'prenom '.
	'FROM utilisateurs '.
	'WHERE '.
		'_etat = "active" AND 
		id NOT IN (
			SELECT id_utilisateur '.
			'FROM droits_ecoles '.
			'WHERE '.
				'id_ecole = '.$ecole['id'].' AND '.
				'_etat = "active")')
	->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);


$droits = $pdo->query($droits_sql = 'SELECT '.
		'de.id, '.
		'u.id AS uid, '.
		'u.nom, '.
		'u.prenom '.
	'FROM droits_ecoles AS de '.
	'JOIN utilisateurs AS u ON '.
		'de.id_utilisateur = u.id AND '.
		'u._etat = "active" '.
	'WHERE '.
		'de.id_ecole = '.$ecole['id'].' AND '.
		'de._etat = "active"')
	->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);


$paiements = $pdo->query($paiements_sql = 'SELECT '.
		'p.id AS pid, '.
		'p.* '.
	'FROM paiements AS p '.
	'WHERE '.
		'p.id_ecole = '.$ecole['id'].' AND '.
		'p._etat = "active" '.
	'ORDER BY '.
		'p._date DESC')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$paiements = $paiements->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);


$retards = $pdo->query($retards_sql = 'SELECT '.
		'id AS pid, '.
		'prenom, '.
		'nom, '.
		'date_inscription, '.
		'hors_malus '.
	'FROM participants '.
	'WHERE '.
		'id_ecole = '.$ecole['id'].' AND '.
		'_etat = "active" AND '.
		'date_inscription > "'.APP_DATE_MALUS.'" '.
	'ORDER BY '.
		'nom ASC, '.
		'prenom ASC')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$retards = $retards->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);


if (!empty($_POST['change_retard']) &&
	!empty($retards[$_POST['change_retard']])) {
	$ref = pdoRevision('participants', $_POST['change_retard']);
	$pdo->exec('UPDATE participants SET '.
			'_auteur = '.(int) $_SESSION['user']['id'].', '.
			'_ref = '.(int) $ref.', '.
			'_date = NOW(), '.
			'_message = "Changement de l\'excuse lié au retard", '.
			//-------------//
			'hors_malus = 1 - hors_malus '.
		'WHERE '.
			'_etat = "active" AND '.
			'id = '.(int) $_POST['change_retard'].' AND '.
			'id_ecole = '.$ecole['id']);

	$change_retard = true;
}

if (!empty($_POST['maj_sports']) &&
	!empty($_POST['quotas']) &&
	!empty($_POST['reserves']) &&
	!empty($_POST['equipes']) &&
	!empty($_POST['sports']) &&
	is_array($_POST['sports']) &&
	is_array($_POST['equipes']) &&
	is_array($_POST['quotas']) &&
	is_array($_POST['reserves']) &&
	array_keys($_POST['quotas']) == array_keys($_POST['sports']) &&
	array_keys($_POST['equipes']) == array_keys($_POST['sports']) &&
	array_keys($_POST['reserves']) == array_keys($_POST['sports'])) {

	$sports_id = [];
	foreach ($sports as $sport) 
		$sports_id[$sport['id']] = $sport;

	$modif = $pdo->prepare('UPDATE ecoles_sports SET '.
			'_auteur = '.(int) $_SESSION['user']['id'].', '.
			'_ref = :ref, '.
			'_date = NOW(), '.
			'_message = "Modification des quotas d\'un sport associé à une école", '.
			//-------------//
			'quota_max = :quota_max, '.
			'quota_equipes = :quota_equipes, '.
			'quota_reserves = :quota_reserves '.
		'WHERE '.
			'_etat = "active" AND '.
			'id_sport = :id_sport AND '.
			'id_ecole = '.$ecole['id']);

	
	$suppr = $pdo->prepare('UPDATE ecoles_sports SET '.
			'_auteur = '.(int) $_SESSION['user']['id'].', '.
			'_ref = :ref, '.
			'_date = NOW(), '.
			'_message = "Suppression d\'un sport associé à une école", '.
			//-------------//
			'_etat = "desactive" '.
		'WHERE '.
			'_etat = "active" AND '.
			'id_sport = :id_sport AND '.
			'id_ecole = '.$ecole['id']);

	$erreur_quotas_sports = false;
	foreach ($_POST['sports'] as $i => $id) { 

		if (!empty($sports_id[$id]) && 
			intval($_POST['equipes'][$i]) && (
				intval($_POST['reserves'][$i]) &&
				$_POST['reserves'][$i] >= 0 ||
				$_POST['reserves'][$i] == 0) &&
			$_POST['equipes'][$i] >= $sports_id[$id]['equipes'] && (
				intval($_POST['quotas'][$i]) ||
				$_POST['quotas'][$i] == 0) && (
				$_POST['quotas'][$i] >= (int) $sports_id[$id]['inscriptions'] ||
					$_POST['quotas'][$i] == 0 &&
					(int) $sports_id[$id]['inscriptions'] == 0)) {

			$ref = pdoRevision('ecoles_sports', null, '_etat = "active" AND id_ecole = '.$ecole['id'].' AND id_sport = '.$id);
			
			if ($_POST['quotas'][$i] == 0) {
				$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
				$suppr->execute(array(
					':ref' => $ref,
					//------------//
					':id_sport' => $id));
				$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
			}

			else {
				$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
				$modif->execute(array(
					':ref' => $ref,
					//-------------//
					':id_sport' => $id,
					':quota_equipes' 	=> (int) $_POST['equipes'][$i],
					':quota_max' 		=> (int) $_POST['quotas'][$i],
					':quota_reserves' 	=> (int) $_POST['reserves'][$i]));
				$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
			}
		}

		else
			$erreur_quotas_sports = true;
	}

}

$ecoles_cible = $pdo->query('SELECT e.id, e.nom, count(es.id) = 0 as selected '.
	'FROM ecoles e '.
		'LEFT JOIN ecoles_sports es on e.id = es.id_ecole and es._etat = "active" '.
	'where e._etat = "active" AND e.id != '.$ecole['id'].' '.
	'group by e.id') or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$ecoles_cible = $ecoles_cible->fetchAll(PDO::FETCH_ASSOC);

if (!empty($_POST['clone_quotas']) &&
	!empty($_POST['clone_ecoles']) &&
	is_array($_POST['clone_ecoles']) && 
	!empty($_POST['clone_sports']) &&
	is_array($_POST['clone_sports'])){
	$input_clone_ecoles = [];
	foreach ($_POST['clone_ecoles'] as $ecole_id) {
		$input_clone_ecoles[] = (int) $ecole_id;
	}
	$input_clone_sports = [];
	foreach ($_POST['clone_sports'] as $sport_id) {
		$input_clone_sports[] = (int) $sport_id;
	}

	// On enlève les écoles qui ont déjà des équipes
	$sanitized = $pdo->prepare('SELECT id_ecole, id_sport '.
		'FROM ecoles_sports es '.
		'WHERE es.id_ecole IN (?) '.
		  'AND es.id_sport IN (?) '.
		  'AND 0 = (SELECT COUNT(*) FROM equipes WHERE es.id = equipes.id_ecole_sport AND _etat = "active") '.
		  'AND es._etat = "active"');
	$sanitized->execute([
		implode(',', $input_clone_ecoles), 
		implode(',', $input_clone_sports)
	]);
	$clone_ecoles = [];
	$clone_sports = [];
	while ($row = $sanitized->fetch(PDO::FETCH_ASSOC)) {
		$clone_ecoles[] = $row['id_ecole'];
		$clone_sports[] = $row['id_sport'];
	}
	
	$pdo->beginTransaction();
	$clone_delete = $pdo->prepare('UPDATE ecoles_sports '.
		'SET _etat="desactive", _message = CONCAT("Mis à jour avec les donnés de ", ?, " ", _message) '.
		'WHERE id_ecole IN (?) AND id_sport IN (?) AND _etat="active";');
	$clone_delete->execute([
		$ecole['nom'],
		implode(',', $clone_ecoles),
		implode(',', $clone_sports)
	]);

	$clone_clone = $pdo->prepare('INSERT INTO ecoles_sports (id_ecole, id_sport, quota_max, quota_reserves, quota_equipes, _date, _auteur, _etat, _message) '.
		'SELECT e.id                      			 as id_ecole, '.
			's.id                      				 as id_sport, '.
			'q.quota_max, '.
			'q.quota_reserves, '.
			'q.quota_equipes, '.
			'NOW()                     				 as _date, '.
			'?			                  			 as _auteur, '.
			'"active"                  				 as _etat, '.
			'CONCAT("Clone des quotas de ", ?)		 as _message '.
		'FROM ecoles as e '.
			'CROSS JOIN sports s '.
			'JOIN ecoles_sports as q on q.id_sport = s.id '.
		'WHERE q.id_ecole = ? AND q.id_sport IN (?) AND NOT EXISTS(SELECT * FROM ecoles_sports as es where s.id = es.id_sport AND e.id = es.id_ecole AND es._etat = "active") AND q._etat = "active"');
	$clone_clone->execute([
		$_SESSION['user']['id'],
		$ecole['nom'],
		$ecole['id'],
		implode(',', $clone_sports)
	]);

	if($pdo->commit()){
		$erreur_clone = (count($clone_ecoles) != count($input_clone_ecoles) || 
						 count($clone_sports) != count($input_clone_sports)) ? 
						 'missing' : false;
	} else {
		$erreur_clone = true;
	}
}


if (!empty($_POST['add_sport']) &&
	!empty($_POST['sport']) &&
	!empty($_POST['quota-max']) &&
	intval($_POST['quota-max']) &&
	!empty($_POST['quota-equipes']) &&
	intval($_POST['quota-equipes']) &&
	isset($_POST['quota-reserves']) && (
		intval($_POST['quota-reserves']) &&
		$_POST['quota-reserves'] > 0 ||
		$_POST['quota-reserves'] == 0) &&
	$_POST['quota-max'] > 0 &&
	$_POST['quota-equipes'] > 0) {

	$sports_ajout_id = [];
	foreach ($sports_ajout as $sport_ajout) 
		$sports_ajout_id[] = $sport_ajout['id'];

	$erreur_quotas_sports = isset($erreur_quotas_sports) ?
		$erreur_quotas_sports :
		!in_array($_POST['sport'], $sports_ajout_id);


	if (in_array($_POST['sport'], $sports_ajout_id))
		$pdo->exec('INSERT INTO ecoles_sports SET '.
			'_auteur = '.(int) $_SESSION['user']['id'].', '.
			'_date = NOW(), '.
			'_message = "Ajout d\'un sport associé à une école", '.
			'_etat = "active", '.
			//-------------//
			'id_ecole = '.$ecole['id'].', '.
			'id_sport = '.$_POST['sport'].', '.
			'quota_equipes = '.$_POST['quota-equipes'].', '.
			'quota_max = '.$_POST['quota-max'].', '.
			'quota_reserves = '.$_POST['quota-reserves']);

}


if (!empty($_POST['add_paiement']) &&
	isset($_POST['montant']) &&
	isset($_POST['commentaire']) &&
	isset($_POST['type']) && (
		empty($_POST['type']) ||
		in_array($_POST['type'], ['cb', 'especes', 'cheque', 'virement'])) &&
	is_numeric($_POST['montant'])) {

	$pdo->exec('INSERT INTO paiements SET '.
		'_auteur = '.(int) $_SESSION['user']['id'].', '.
		'_date = NOW(), '.
		'_message = "Ajout d\'un paiement d\'école", '.
		'_etat = "active", '.
		//-------------//
		'id_ecole = '.$ecole['id'].', '.
		'montant = '.(float) $_POST['montant'].', '.
		'etat = "paye", '.
		'saisie = "manuelle", '.
		'commentaire = "'.secure($_POST['commentaire']).'", '.
		'type = '.(empty($_POST['type']) ? 'NULL' : '"'.$_POST['type'].'"'));
	
	$ajout_paiement = true;
}


if (!empty($_POST['add_droit']) &&
	!empty($_POST['utilisateur']) &&
	in_array($_POST['utilisateur'], array_keys($utilisateurs))) {
	
	$pdo->exec('INSERT INTO droits_ecoles SET '.
		'_auteur = '.(int) $_SESSION['user']['id'].', '.
		'_date = NOW(), '.
		'_message = "Ajout d\'un droit à une école", '.
		'_etat = "active", '.
		//-------------//
		'id_ecole = '.$ecole['id'].', '.
		'id_utilisateur = '.$_POST['utilisateur']);

	$add_droit = true;
}


if (!empty($_POST['del_droit']) &&
	in_array($_POST['del_droit'], array_keys($droits))) {
	
	$ref = pdoRevision('droits_ecoles', (int) $_POST['del_droit']);
	$pdo->exec('set FOREIGN_KEY_CHECKS = 0');
	$pdo->exec('UPDATE droits_ecoles SET '.
			'_auteur = '.(int) $_SESSION['user']['id'].', '.
			'_date = NOW(), '.
			'_ref = '.(int) $ref.', '.
			'_message = "Suppression d\'un droit à une école", '.
			'_etat = "desactive" '.
		'WHERE '.
			'id = '.$_POST['del_droit']);
	$pdo->exec('set FOREIGN_KEY_CHECKS = 1');

	$delete_droit = true;
}


if (!empty($_POST['del_paiement']) &&
	in_array($_POST['del_paiement'], array_keys($paiements))) {
	
	$ref = pdoRevision('paiements', (int) $_POST['del_paiement']);
	$pdo->exec('set FOREIGN_KEY_CHECKS = 0');
	$pdo->exec('UPDATE paiements SET '.
			'_auteur = '.(int) $_SESSION['user']['id'].', '.
			'_date = NOW(), '.
			'_ref = '.(int) $ref.', '.
			'_message = "Suppression d\'un paiement", '.
			'_etat = "desactive" '.
		'WHERE '.
			'id = '.$_POST['del_paiement']);
	$pdo->exec('set FOREIGN_KEY_CHECKS = 1');

	$delete_paiement = true;
}


if (isset($add_droit) || 
	isset($delete_droit)) {
	$utilisateurs = $pdo->query($utilisateurs_sql)
		->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);

	$droits = $pdo->query($droits_sql)
		->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);
}


if (isset($ajout_paiement) || 
	isset($delete_paiement)) {
	$paiements = $pdo->query($paiements_sql)
		->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);
}


if (isset($erreur_quotas_sports) && 
	!$erreur_quotas_sports) {

	$sports = $pdo->query($sports_sql)
		or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
	$sports = $sports->fetchAll(PDO::FETCH_ASSOC);


	$sports_ajout = $pdo->query($sports_ajout_sql)
		or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
	$sports_ajout = $sports_ajout->fetchAll(PDO::FETCH_ASSOC);

}

if (isset($change_retard)) {
	$retards = $pdo->query($retards_sql)
		->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);
}

$montant_inscriptions = $pdo->query('SELECT '.
		'SUM(CASE WHEN t.id IS NULL THEN 0 ELSE t.tarif END) AS montant '.
	'FROM participants AS p '.
	'LEFT JOIN tarifs_ecoles AS te ON '.
		'te.id = p.id_tarif_ecole AND '.
		'te._etat = "active" '.
	'LEFT JOIN tarifs AS t ON '.
		'te.id_tarif = t.id AND '.
		't._etat = "active" '.
	'WHERE '.
		'p.id_ecole = '.$ecole['id'].' AND '.
		'p._etat = "active"')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$montant_inscriptions = $montant_inscriptions->fetch(PDO::FETCH_ASSOC);


$montant_recharges = $pdo->query('SELECT '.
		'SUM(recharge) AS montant '.
	'FROM participants '.
	'WHERE '.
		'id_ecole = '.$ecole['id'].' AND '.
		'_etat = "active"')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$montant_recharges = $montant_recharges->fetch(PDO::FETCH_ASSOC);


$montant_paye = $pdo->query('SELECT '.
		'SUM(montant) AS montant '.
	'FROM paiements '.
	'WHERE '.
		'id_ecole = '.$ecole['id'].' AND '.
		'etat = "paye" AND '.
		'_etat = "active"')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$montant_paye = $montant_paye->fetch(PDO::FETCH_ASSOC);


$inscriptions_enretard = $pdo->query('SELECT '.
		'COUNT(p.id) AS nbretards, '.
		'SUM(CASE WHEN t.id IS NULL THEN 0 ELSE t.tarif END) AS montant '.
	'FROM participants AS p '.
	'LEFT JOIN tarifs_ecoles AS te ON '.
		'te.id = p.id_tarif_ecole AND '.
		'te._etat = "active" '.
	'LEFT JOIN tarifs AS t ON '.
		'te.id_tarif = t.id AND '.
		't._etat = "active" '.
	'WHERE '.
		'p.hors_malus = 0 AND '.
		'p.id_ecole = '.$ecole['id'].' AND '.
		'p.date_inscription > "'.APP_DATE_MALUS.'" AND '.
		'p._etat = "active"')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$inscriptions_enretard = $inscriptions_enretard->fetch(PDO::FETCH_ASSOC);



//Inclusion du bon fichier de template
require DIR.'templates/admin/ecoles/edition.php';
