<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/ecoles/recapitulatif.php ************************/
/* Récap de l'école ****************************************/
/* *********************************************************/
/* Dernière modification : le 16/02/14 *********************/
/* *********************************************************/


$id = $args[1][0];
if (!(!empty($_SESSION['user']) && (
		!empty($_SESSION['user']['privileges']) &&
		in_array('ecoles', $_SESSION['user']['privileges']) ||
		!empty($_SESSION['user']['ecoles']) &&
		in_array($id, $_SESSION['user']['ecoles']))))
	die(header('location:'.url('accueil', false, false)));


$ecole = $pdo->query('SELECT '.
		'e.*, '.
		'u.login AS ulogin, '.
		'u.nom AS unom, '.
		'u.prenom AS uprenom, '.
		'u.email AS uemail, '.
		'c.photo AS uphoto, '.
		'c.poste AS uposte, '.
		'u.cas AS ucas, '.
		'u.telephone AS utelephone, '.
		'i.token, '.
		'i.width, '.
		'i.height, '.
		'(SELECT COUNT(p1.id) FROM participants AS p1 WHERE p1.id_ecole = e.id AND p1._etat = "active") AS nb_inscriptions, '.
		'(SELECT COUNT(p2.id) FROM participants AS p2 WHERE p2.id_ecole = e.id AND p2._etat = "active" AND p2.sportif = 1) AS nb_sportif, '.
		'(SELECT COUNT(p3.id) FROM participants AS p3 WHERE p3.id_ecole = e.id AND p3._etat = "active" AND p3.pompom = 1) AS nb_pompom, '.
		'(SELECT COUNT(p4.id) FROM participants AS p4 WHERE p4.id_ecole = e.id AND p4._etat = "active" AND p4.fanfaron = 1) AS nb_fanfaron, '.
		'(SELECT COUNT(p5.id) FROM participants AS p5 WHERE p5.id_ecole = e.id AND p5._etat = "active" AND p5.cameraman = 1) AS nb_cameraman, '.
		'(SELECT COUNT(p6.id) FROM participants AS p6 WHERE p6.id_ecole = e.id AND p6._etat = "active" AND p6.pompom = 1 AND p6.sportif = 0) AS nb_pompom_nonsportif, '.
		'(SELECT COUNT(p7.id) FROM participants AS p7 WHERE p7.id_ecole = e.id AND p7._etat = "active" AND p7.fanfaron = 1 AND p7.sportif = 0) AS nb_fanfaron_nonsportif, '.
		'(SELECT COUNT(p10.id) FROM participants AS p10 WHERE p10.id_ecole = e.id AND p10._etat = "active" AND p10.cameraman = 1 AND p10.sportif = 0) AS nb_cameraman_nonsportif, '.
		'(SELECT COUNT(p8.id) FROM participants AS p8 JOIN tarifs_ecoles AS te8 ON te8.id = p8.id_tarif_ecole AND te8._etat = "active" JOIN tarifs AS t8 ON t8.id = te8.id_tarif AND t8.logement = 1 AND t8._etat = "active" WHERE p8.id_ecole = e.id AND p8.sexe = "f" AND p8._etat = "active") AS nb_filles_logees, '.
		'(SELECT COUNT(p9.id) FROM participants AS p9 JOIN tarifs_ecoles AS te9 ON te9.id = p9.id_tarif_ecole AND te9._etat = "active" JOIN tarifs AS t9 ON t9.id = te9.id_tarif AND t9.logement = 1 AND t9._etat = "active" WHERE p9.id_ecole = e.id AND p9.sexe = "h" AND p9._etat = "active") AS nb_garcons_loges '.
	'FROM ecoles AS e '.
	'LEFT JOIN images AS i ON '.
		'i.id = e.id_image AND '.
		'i._etat = "active" '.
	'LEFT JOIN utilisateurs AS u ON '.
		'u.id = e.id_respo AND '.
		'u._etat = "active" '.
	'LEFT JOIN contacts AS c ON '.
		'c.id_utilisateur = u.id AND '.
		'c._etat = "active" '.
	'WHERE '.
		'e._etat = "active" AND '.
		'e.id = '.(int) $id)
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$ecole = $ecole->fetch(PDO::FETCH_ASSOC);

$quotas = $pdo->query('SELECT '.
		'quota, '.
		'valeur, '.
		'id '.
	'FROM quotas_ecoles '.
	'WHERE '.
		'id_ecole = '.(int) $id.' AND '.
		'_etat = "active"')
	->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);


foreach ($quotas as $quota => $valeur)
	$quotas[$quota] = $valeur['valeur'];


$quotas_reserves = $pdo->query('SELECT '.
		'es.id_sport, '.
		's.sport, '.
		's.sexe, '.
		'es.quota_reserves, '.
		'(SELECT COUNT(p.id) FROM participants AS p JOIN sportifs AS sp ON sp.id_participant = p.id AND sp._etat = "active" JOIN equipes AS eq ON eq._etat = "active" AND eq.id = sp.id_equipe WHERE p.id_ecole = es.id_ecole AND p._etat = "active" AND eq.id_ecole_sport = es.id) AS sportifs '.
	'FROM ecoles_sports AS es '.
	'JOIN sports AS s ON '.
		's._etat = "active" AND '.
		's.id = es.id_sport '.
	'WHERE '.
		'es.id_ecole = '.$ecole['id'].' AND '.
		'es.quota_reserves > 0 AND '.
		'es._etat = "active"')
	->fetchAll(PDO::FETCH_ASSOC);

if (isset($quotas['total'])) {
	$places_reservees = 0;

	foreach ($quotas_reserves as $quota_reserves) {
		if ($quota_reserves['sportifs'] < $quota_reserves['quota_reserves']) {
			$quotas['total'] -= $quota_reserves['quota_reserves'];
			$places_reservees += $quota_reserves['quota_reserves'];
		}
	}
}


if (empty($ecole) ||
	$ecole['etat_inscription'] == 'fermee' && (
		empty($_SESSION['user']['privileges']) ||
		!in_array('ecoles', $_SESSION['user']['privileges']))) {
	die(header('location:'.url('', false, false)));
}



$inscription_on = isset($quotas['total']);
$sportif_on = isset($quotas['sportif']); 
$nonsportif_on = isset($quotas['nonsportif']); 
$logement_on = isset($quotas['logement']);
$filles_on = isset($quotas['filles_logees']); 
$garcons_on = isset($quotas['garcons_loges']); 
$pompom_on = isset($quotas['pompom']); 
$pompom_nonsportif_on = isset($quotas['pompom_nonsportif']); 
$cameraman_on = isset($quotas['cameraman']); 
$cameraman_nonsportif_on = isset($quotas['cameraman_nonsportif']); 
$fanfaron_on = isset($quotas['fanfaron']); 
$fanfaron_nonsportif_on = isset($quotas['fanfaron_nonsportif']); 



$sans_sport = $pdo->query('SELECT '.
		'p.id, '.
		'p.nom, '.
		'p.prenom, '.
		'p.licence, '.
		'p.sexe, '.
		't.logement '.
	'FROM participants AS p '.
	'JOIN tarifs_ecoles AS te ON '.
		'te.id = p.id_tarif_ecole AND '.
		'te._etat = "active" '.
	'JOIN tarifs AS t ON '.
		't.id = te.id_tarif AND '.
		't._etat = "active" '.
	'WHERE '.
		'p._etat = "active" AND '.
		'p.sportif = 1 AND '.
		'p.id_ecole = '.$ecole['id'].' AND '.
		'p.id NOT IN (SELECT '.
				'ps.id '.
			'FROM sportifs AS ss '.
			'JOIN participants AS ps ON '.
				'ps.id = ss.id_participant AND '.
				'ps._etat = "active" '.
			'WHERE '.
				'ss._etat = "active" AND '.
				'ps.id_ecole = p.id_ecole) '.
	'ORDER BY '.
		'p.nom ASC, '.
		'p.prenom ASC')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$sans_sport = $sans_sport->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);


$participants = $pdo->query('SELECT '.
		'p.id, '.
		'p.nom, '.
		'p.prenom, '.
		'p.email, '.
		'p.telephone, '.
		'p.licence, '.
		'p.sexe, '.
		't.nom AS tarif, '.
		'p.recharge AS recharge, '.
		't.logement, '.
		'p.logeur, '.
		'p.recharge + t.tarif AS montant, '.
		'CASE WHEN p.date_inscription > "'.APP_DATE_MALUS.'" THEN 1 ELSE 0 END AS retard, '.
		'p.hors_malus '.
	'FROM participants AS p '.
	'JOIN tarifs_ecoles AS te ON '.
		'te.id = p.id_tarif_ecole AND '.
		'te._etat = "active" '.
	'JOIN tarifs AS t ON '.
		't.id = te.id_tarif AND '.
		't._etat = "active" '.
	'WHERE '.
		'p._etat = "active" AND '.
		'p.id_ecole = '.$ecole['id'].' '.
	'ORDER BY '.
		'p.nom ASC, '.
		'p.prenom ASC')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$participants = $participants->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);


$pompoms = $pdo->query('SELECT '.
		'p.id, '.
		'p.nom, '.
		'p.prenom, '.
		'p.sportif, '.
		'p.sexe, '.
		't.logement '.
	'FROM participants AS p '.
	'JOIN tarifs_ecoles AS te ON '.
		'te.id = p.id_tarif_ecole AND '.
		'te._etat = "active" '.
	'JOIN tarifs AS t ON '.
		't.id = te.id_tarif AND '.
		't._etat = "active" '.
	'WHERE '.
		'p._etat = "active" AND '.
		'p.pompom = 1 AND '.
		'p.id_ecole = '.$ecole['id'].' '.
	'ORDER BY '.
		'p.nom ASC, '.
		'p.prenom ASC')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$pompoms = $pompoms->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);


$fanfarons = $pdo->query('SELECT '.
		'p.id, '.
		'p.nom, '.
		'p.prenom, '.
		'p.sportif, '.
		'p.sexe, '.
		't.logement '.
	'FROM participants AS p '.
	'JOIN tarifs_ecoles AS te ON '.
		'te.id = p.id_tarif_ecole AND '.
		'te._etat = "active" '.
	'JOIN tarifs AS t ON '.
		't.id = te.id_tarif AND '.
		't._etat = "active" '.
	'WHERE '.
		'p._etat = "active" AND '.
		'p.fanfaron = 1 AND '.
		'p.id_ecole = '.$ecole['id'].' '.
	'ORDER BY '.
		'p.nom ASC, '.
		'p.prenom ASC')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$fanfarons = $fanfarons->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);


$cameramans = $pdo->query('SELECT '.
		'p.id, '.
		'p.nom, '.
		'p.prenom, '.
		'p.sportif, '.
		'p.sexe, '.
		't.logement '.
	'FROM participants AS p '.
	'JOIN tarifs_ecoles AS te ON '.
		'te.id = p.id_tarif_ecole AND '.
		'te._etat = "active" '.
	'JOIN tarifs AS t ON '.
		't.id = te.id_tarif AND '.
		't._etat = "active" '.
	'WHERE '.
		'p._etat = "active" AND '.
		'p.cameraman = 1 AND '.
		'p.id_ecole = '.$ecole['id'].' '.
	'ORDER BY '.
		'p.nom ASC, '.
		'p.prenom ASC')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$cameramans = $cameramans->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);


$equipes_sportifs_ = $pdo->query('SELECT '.
		's.id, '.
		'e.id AS eid, '.
		'e.label, '.
		's.sport, '.
		's.sexe, '.
		'e.id_capitaine AS cid, '.
		'p.nom AS pnom, '.
		'p.prenom AS pprenom, '.
		'p.sexe AS psexe, '.
		'p.licence AS plicence, '.
		'p.id AS pid, '.
		't.nom AS ptarif, '.
		't.logement AS plogement, '.
		'u.nom AS unom, '.
		'u.prenom AS uprenom '.
	'FROM equipes AS e '.
	'JOIN ecoles_sports AS es ON '.
		'es.id = e.id_ecole_sport AND '.
		'es._etat = "active" '.
	'JOIN sports AS s ON '.
		's.id = es.id_sport AND '.
		's._etat = "active" '.
	'LEFT JOIN ('.
		'sportifs AS sp '.
		'JOIN participants AS p ON '.
			'p.id = sp.id_participant AND '.
			'p._etat = "active" '.
		'JOIN tarifs_ecoles AS te ON '.
			'te.id = p.id_tarif_ecole AND '.
			'te._etat = "active" '.
		'JOIN tarifs AS t ON '.
			't.id = te.id_tarif AND '.
			't._etat = "active") ON '.
		'sp.id_equipe = e.id AND '.
		'sp._etat = "active" '.
	'LEFT JOIN utilisateurs AS u ON '.
		'u.id = s.id_respo AND '.
		'u._etat = "active" '.
	'WHERE '.
		'es.id_ecole = '.$ecole['id'].' AND '.
		'e._etat = "active" '.
	'ORDER BY '.
		's.sport ASC, '.
		's.sexe ASC, '.
		'e.label ASC, '.
		'p.nom ASC, '.
		'p.prenom ASC')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$equipes_sportifs_ = $equipes_sportifs_->fetchAll(PDO::FETCH_ASSOC);


$equipes_sportifs = [];
$sportifs_sports = [];
foreach ($equipes_sportifs_ as $data) {
	$equipes_sportifs[$data['id']][$data['eid']][] = $data;

	if (!empty($data['pid']))
		$sportifs_sports[$data['pid']][] = $data;
}


$paiements = $pdo->query('SELECT '.
		'p.* '.
	'FROM paiements AS p '.
	'WHERE '.
		'p._etat = "active" AND '.
		'p.id_ecole = '.$ecole['id'].' '.
	'ORDER BY '.
		'p._date DESC')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$paiements = $paiements->fetchAll(PDO::FETCH_ASSOC);


$montant_inscriptions = $pdo->query('SELECT '.
		'SUM(tarif) AS montant '.
	'FROM participants AS p '.
	'JOIN tarifs_ecoles AS te ON '.
		'te.id = p.id_tarif_ecole AND '.
		'te._etat = "active" '.	
	'LEFT JOIN tarifs AS t ON '.
		'te.id_tarif = t.id AND '.
		't._etat = "active" '.
	'WHERE '.
		'p._etat = "active" AND '.
		'p.id_ecole = '.$ecole['id'])
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$montant_inscriptions = $montant_inscriptions->fetch(PDO::FETCH_ASSOC);


$montant_recharges = $pdo->query('SELECT '.
		'SUM(recharge) AS montant '.
	'FROM participants AS p '.
	'WHERE '.
		'p._etat = "active" AND '.
		'p.id_ecole = '.$ecole['id'])
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$montant_recharges = $montant_recharges->fetch(PDO::FETCH_ASSOC);


$montant_paye = $pdo->query('SELECT '.
		'SUM(montant) AS montant '.
	'FROM paiements '.
	'WHERE '.
		'_etat = "active" AND '.
		'id_ecole = '.$ecole['id'].' AND '.
		'etat = "paye"')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$montant_paye = $montant_paye->fetch(PDO::FETCH_ASSOC);


$inscriptions_enretard = $pdo->query('SELECT '.
		'COUNT(p.id) AS nbretards, '.
		'SUM(tarif) AS montant '.
	'FROM participants AS p '.
	'JOIN tarifs_ecoles AS te ON '.
		'te.id = p.id_tarif_ecole AND '.
		'te._etat = "active" '.
	'LEFT JOIN tarifs AS t ON '.
		't.id = te.id_tarif AND '.
		't._etat = "active" '.
	'WHERE '.
		'p.hors_malus = 0 AND '.
		'p._etat = "active" AND '.
		'p.id_ecole = '.$ecole['id'].' AND '.
		'date_inscription > "'.APP_DATE_MALUS.'"')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$inscriptions_enretard = $inscriptions_enretard->fetch(PDO::FETCH_ASSOC);


$tarifs = $pdo->query('SELECT '.
		't.sportif AS _group, '.
		't.sportif, '.
		't.id, '.
		't.tarif, '.
		't.nom, '.
		't.description, '.
		'CASE WHEN t.id_ecole_for_special = '.$ecole['id'].' OR t.id_ecole_for_special IS NULL THEN s.id ELSE NULL END AS id_sport_special, '.
		's.sport, '.
		's.sexe, '.
		't.logement, '.
		't.for_pompom, '.
		't.for_cameraman, '.
		't.for_fanfaron '.
	'FROM tarifs AS t '.
	'JOIN tarifs_ecoles AS te ON '.
		'te.id_ecole = '.$ecole['id'].' AND '.
		'te.id_tarif = t.id AND '.
		'te._etat = "active" '.
	'LEFT JOIN sports AS s ON '.
		's.id = t.id_sport_special AND '.
		's._etat = "active" AND '.
		'(t.id_ecole_for_special = '.$ecole['id'].' OR t.id_ecole_for_special IS NULL) '.
	'WHERE '.
		't._etat = "active" '.
	'ORDER BY '.
		'sportif ASC, '.
		'nom ASC')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$tarifs_groupes = $tarifs->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_GROUP);
$tarifs = [];
foreach ($tarifs_groupes as $tarifs_groupe)
	$tarifs = array_merge($tarifs, $tarifs_groupe);


$asCodes = array_map('strtoupper', array_map('trim', explode(',', $ecole['as_code'])));
$erreurs = $pdo->query('SELECT '.
		'p.nom, '.
		'p.prenom, '.
		'p.sexe, '.
		'p.sportif, '.
		'er.message, '.
		'er.etat, '.
		'er._date '.
	'FROM erreurs AS er '.
	'JOIN participants AS p ON '.
		'p.id = er.id_participant AND '.
		'p._etat = "active" '.
	'WHERE '.
		'p.id_ecole = '.$ecole['id'].' AND '.
		'er._etat = "active" '.
	'ORDER BY '.
		'er._date DESC')
	->fetchAll(PDO::FETCH_ASSOC);

$licences_sql = $pdo->query('SELECT '.
		'licence, '.
		'nom, '.
		'prenom '.
	'FROM licences '.
	'WHERE '.
		implode(' OR ', array_map(function($as) {
			return 'as_code LIKE "'.secure($as).'"';
		}, $asCodes)))
	->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);

$emails = [];
$telephones = [];
$licences = [];
$noms_prenoms = [];
$domaines = [];



foreach ($participants as $id => $participant) {
	$email = getEmail($participant['email']);
	$domaine = explode('@', $email);
	$nom_clean = clean($participant['nom']);
	$prenom_clean = clean($participant['prenom']);
	$nom_prenom = $nom_clean.' '.$prenom_clean;
	$prenom_nom = $prenom_clean.' '.$nom_clean;
	$telephone = getTelephone($participant['telephone']);
	$licence = getLicences(trim($participant['licence']), $asCodes);

	if (empty($emails[$email]))
		$emails[$email] = [];

	if (!empty($telephone)) {
		if (empty($telephones[$telephone]))
			$telephones[$telephone] = [];

		$telephones[$telephone][] = $id;
	}

	if (!empty($domaine[1])) {
		if (empty($domaines[$domaine[1]]))
			$domaines[$domaine[1]] = [];

		$domaines[$domaine[1]][] = $id;
	}

	foreach ($licence as $licence_item) {
		if (!empty($licence_item)) {
			if (empty($licences[$licence_item]))
				$licences[$licence_item] = [];

			$licences[$licence_item][] = $id;
		}
	}

	if (empty($noms_prenoms[$nom_prenom]))
		$noms_prenoms[$nom_prenom] = [];

	$emails[$email][] = $id;
	$noms_prenoms[$nom_prenom][] = $id;
	$noms_prenoms[$prenom_nom][] = $id;
	$participants[$id]['domaine'] = empty($domaine[1]) ? '' : $domaine[1];
	$participants[$id]['nom_prenom'] = $nom_prenom;
	$participants[$id]['prenom_nom'] = $prenom_nom;
}

$domaine_ecole = '';
$domaine_max = 0;
$excluded_domaines = [
	'gmail.com'];
foreach ($domaines as $domaine => $ids) {
	if (count($ids) > $domaine_max &&
		!in_array($domaine, $excluded_domaines)) {
		$domaine_ecole = $domaine;
		$domaine_max = count($ids);
	}
}



//Téléchargement du fichier XLSX concerné
if (isset($_GET['excel']) &&
	in_array($_GET['excel'], ['sanssport', 'multisports', 'fanfarons', 'pompoms', 'cameramans', 
		'sportifs', 'participants', 'paiements', 'err_soumises', 'err_np', 'err_emails', 'err_phones'])) {

	if ($_GET['excel'] == 'pompoms') {
		foreach ($pompoms as $k => $pompom) {
			$pompoms[$k]['psportif'] = $pompom['sportif'] ? 'Oui' : 'Non';
			$pompoms[$k]['psexe'] = $pompom['sexe'] == 'h' ? 'Homme' : 'Femme';
			$pompoms[$k]['plogement'] = $pompom['logement'] ? 'Full package' : 'Light package';
		}

		$titre = 'Liste des Pompoms';
		$fichier = 'liste_pompoms';
		$items = $pompoms;
		$labels = [
			'Nom' => 'nom',
			'Prénom' => 'prenom',
			'Sportif' => 'psportif',
			'Sexe' => 'psexe',
			'Logement' => 'plogement',
		];
	} else if ($_GET['excel'] == 'fanfarons') {
		foreach ($fanfarons as $k => $fanfaron) {
			$fanfarons[$k]['psportif'] = $fanfaron['sportif'] ? 'Oui' : 'Non';
			$fanfarons[$k]['psexe'] = $fanfaron['sexe'] == 'h' ? 'Homme' : 'Femme';
			$fanfarons[$k]['plogement'] = $fanfaron['logement'] ? 'Full package' : 'Light package';
		}

		$titre = 'Liste des Fanfarons';
		$fichier = 'liste_fanfarons';
		$items = $fanfarons;
		$labels = [
			'Nom' => 'nom',
			'Prénom' => 'prenom',
			'Sportif' => 'psportif',
			'Sexe' => 'psexe',
			'Logement' => 'plogement',
		];
	} else if ($_GET['excel'] == 'cameramans') {
		foreach ($cameramans as $k => $cameraman) {
			$cameramans[$k]['psportif'] = $cameraman['sportif'] ? 'Oui' : 'Non';
			$cameramans[$k]['psexe'] = $cameraman['sexe'] == 'h' ? 'Homme' : 'Femme';
			$cameramans[$k]['plogement'] = $cameraman['logement'] ? 'Full package' : 'Light package';
		}

		$titre = 'Liste des Cameramans';
		$fichier = 'liste_cameramans';
		$items = $cameramans;
		$labels = [
			'Nom' => 'nom',
			'Prénom' => 'prenom',
			'Sportif' => 'psportif',
			'Sexe' => 'psexe',
			'Logement' => 'plogement',
		];
	} else if ($_GET['excel'] == 'multisports') {
		$multi_sports = [];
		foreach ($sportifs_sports as $sportif_sports) { 
			if (count($sportif_sports) <= 1)
				continue;

			$multi_sports = array_merge($multi_sports, $sportif_sports);
		}

		foreach ($multi_sports as $k => $sportif) {
			$multi_sports[$k]['pcapitaine'] = $sportif['cid'] == $sportif['pid'] ? 'Oui' : '';
			$multi_sports[$k]['psport'] = $sportif['sport'].' '.strip_tags(printSexe($sportif['sexe']));
		}

		$titre = 'Liste des Multi-sportifs';
		$fichier = 'liste_multi-sportifs';
		$items = $multi_sports;
		$labels = [
			'Nom' => 'pnom',
			'Prénom' => 'pprenom',
			'Licence' => 'plicence',
			'Capitaine' => 'pcapitaine',
			'Sport' => 'psport',
			'Equipe' => 'label',
		];

	} else if ($_GET['excel'] == 'sanssport') {
		foreach ($sans_sport as $k => $participant) {
			$sans_sport[$k]['psexe'] = $participant['sexe'] == 'h' ? 'Homme' : 'Femme';
			$sans_sport[$k]['plogement'] = $participant['logement'] ? 'Full package' : 'Light package';
		}

		$titre = 'Liste des Sportifs sans Sport';
		$fichier = 'liste_sportifs-sans-sport';
		$items = $sans_sport;
		$labels = [
			'Nom' => 'nom',
			'Prénom' => 'prenom',
			'Licence' => 'licence',
			'Sexe' => 'psexe',
			'Logement' => 'plogement',
		];
	} else if ($_GET['excel'] == 'sportifs' && 
		!empty($_GET['sport']) && 
		in_array($_GET['sport'], array_keys($equipes_sportifs))) {
		$sportifs = [];
		$data = array_keys($equipes_sportifs[$_GET['sport']])[0];
		$data = $equipes_sportifs[$_GET['sport']][$data][0];

		foreach ($equipes_sportifs[$_GET['sport']] as $equipe) {
			foreach ($equipe as $sportif) {
				if (empty($sportif['pid'])) 
					continue;

				$sportif['pcapitaine'] = $sportif['cid'] == $sportif['pid'] ? 'Oui' : '';
				$sportif['psexe'] = $sportif['psexe'] == 'h' ? 'Homme' : 'Femme';
				$sportif['plogement'] = $sportif['plogement'] ? 'Full package' : 'Light package';
				$sportifs[] = $sportif;
			}
		}

		$titre = 'Liste des Sportifs ('.$data['sport'].' '.strip_tags(printSexe($data['sexe'])).')';
		$fichier = 'liste_sportifs_'.onlyLetters(utf8_decode(unsecure($data['sport']).' '.strip_tags(printSexe($data['sexe']))));
		$items = $sportifs;
		$labels = [
			'Equipe' => 'label',
			'Capitaine' => 'pcapitaine',
			'Nom' => 'pnom',
			'Prénom' => 'pprenom',
			'Sexe' => 'psexe',
			'Licence' => 'plicence', 
			'Tarif' => 'ptarif', 
			'Logement' => 'plogement'
		];
	} else if ($_GET['excel'] == 'participants') {
		foreach ($participants as $k => $participant) {
			$participants[$k]['pretard'] = !empty($participant['retard']) ? 'Oui' : ''; 
			$participants[$k]['plogement'] = !empty($participant['logement']) ? 'Full package' : 'Light package'; 
		}

		$titre = 'Liste des Participants';
		$fichier = 'liste_participants';
		$items = $participants;
		$labels = [
			'Nom' => 'nom',
			'Prénom' => 'prenom',
			'Email' => 'email',
			'Tarif' => 'tarif', 
			'Gourde' => 'recharge',
			'Montant' => 'montant',
			'Retard' => 'pretard',
			'Logement' => 'plogement',
		];
	} else if ($_GET['excel'] == 'paiements') {
		foreach ($paiements as $k => $paiement) {
			$paiements[$k]['pmontant'] = printMoney($paiement['montant']);
		}

		$titre = 'Liste des Paiements';
		$fichier = 'liste_paiements';
		$items = $paiements;
		$labels = [
			'Date' => '_date',
			'Type' => 'type',
			'Montant' => 'pmontant',
			'Etat' => 'etat',
		];
	} else if ($_GET['excel'] == 'err_soumises') {
		foreach ($erreurs as $k => $erreur) {
			$erreurs[$k]['psexe'] = $erreur['sexe'] == 'h' ? 'Homme' : 'Femme';
			$erreurs[$k]['psportif'] = !empty($erreur['sportif']) ? 'Oui' : 'Non';
		}
		$titre = 'Liste des Erreurs soumises';
		$fichier = 'liste_erreurs_soumises';
		$items = $erreurs;
		$labels = [
			'Date' => '_date',
			'Nom' => 'nom',
			'Prénom' => 'prenom',
			'Sexe' => 'psexe',
			'Sportif' => 'psportif',
			'Message' => 'message',
			'Etat' => 'etat'
		];
	} 

	/*else if ($_GET['excel'] == 'err_np') {

	} else if ($_GET['excel'] == 'err_emails') {

	} else if ($_GET['excel'] == 'err_phones') {

	}*/

	if (isset($items))
		exportXLSX($items, $fichier, $titre, $labels);
} 





//Inclusion du bon fichier de template
require DIR.'templates/ecoles/recapitulatif.php';
