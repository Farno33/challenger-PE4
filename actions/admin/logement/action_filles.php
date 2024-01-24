<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/admin/logement/action_filles.php ****************/
/* Liste des filles pouvant être logées ********************/
/* *********************************************************/
/* Dernière modification : le 16/02/15 *********************/
/* *********************************************************/

if (isset($_GET['filter']) &&
	empty($_GET['ecole']))
	$_GET['ecole'] = '';


$now = new DateTime();
$finPhase1 = new DateTime(APP_FIN_PHASE1);
$finPhase2 = new DateTime(APP_DATE_MALUS);
$finMalus = new DateTime(APP_FIN_MALUS);
$finInscrip = new DateTime(APP_FIN_INSCRIP);

$phase_actuelle = $now < $finPhase1 ? 'phase1' : (
	$now < $finPhase2 ? 'phase2' : (
		$now < $finMalus ? 'malus' : (
			$now < $finInscrip ? 'modif' : null)));


if (isset($_GET['maj']) &&
	isset($_POST['chambre']) &&
	!empty($_POST['pid'])) {

	$existe = $pdo->query('SELECT '.
			'p.id, '.
			'cp.id AS cpid, '.
			'e.format_long '.
		'FROM participants AS p '.
		'JOIN ecoles AS e ON '.
			'e.id = p.id_ecole '.
		'LEFT JOIN chambres_participants AS cp ON '.
			'cp.id_participant = p.id AND '.
			'cp._etat = "active" '.
		'WHERE '.
			'p.id = '.(int) $_POST['pid'].' AND '.
			'p._etat = "active"')
		or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
	$existe = $existe->fetch(PDO::FETCH_ASSOC);

	if (empty($existe))
		die;

	if (!empty($_POST['chambre'])) {
		$format = !empty($existe['format_long']) ? 'long' : 'court';
		$existeChambre = $pdo->query('SELECT '.
				'id '.
			'FROM chambres '.
			'WHERE '.
				'('.
					'format LIKE "long%" OR '.
					'format LIKE "'.$format.'%") AND '.
				'numero = "'.secure($_POST['chambre']).'" AND '.
				'_etat = "active"')
			or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
		$existeChambre = $existeChambre->fetch(PDO::FETCH_ASSOC);

		if (empty($existeChambre))
			die;

		//Changement de chambre
		if (!empty($existe['cpid'])) {
			$ref = pdoRevision('chambres_participants', $existe['cpid']);
			$pdo->exec('UPDATE chambres_participants SET '.
				'_auteur = '.(int) $_SESSION['user']['id'].', '.
				'_date = NOW(), '.
				'_ref = '.(int) $ref.', '.
				'_message = "Changement de chambre et suppression état responsable", '.
				//------------//
				'id_chambre = '.(int) $existeChambre['id'].', '.
				'respo = 0 '.
				'WHERE '.
					'id = '.(int) $existe['cpid']);
		} 

		//Ajout dans une chambre
		else {
			$pdo->exec('INSERT INTO chambres_participants SET '.
				'_auteur = '.(int) $_SESSION['user']['id'].', '.
				'_date = NOW(), '.
				'_message = "Ajout d\'un participant dans une chambre", '.
				//------------//
				'id_participant = '.(int) $_POST['pid'].', '.
				'respo = 0, '.
				'id_chambre = '.(int) $existeChambre['id']);
		}
	} 

	//Suppression du participant d'une chambre
	else if (!empty($existe['cpid'])) {
		$ref = pdoRevision('chambres_participants', $existe['cpid']);
		$pdo->exec('UPDATE chambres_participants SET '.
			'_auteur = '.(int) $_SESSION['user']['id'].', '.
			'_date = NOW(), '.
			'_ref = '.(int) $ref.', '.
			'_etat = "desactive", '.
			'_message = "Suppression d\'un participant d\'une chambre" '.
			//------------//
			'WHERE '.
				'id = '.(int) $existe['cpid']);
	}

	die;
}


if (isset($_GET['maj']) &&
	isset($_POST['telephone']) &&
	!empty($_POST['pid'])) {

	$existe = $pdo->query('SELECT '.
			'p.id '.
		'FROM participants AS p '.
		'WHERE '.
			'p.id = '.(int) $_POST['pid'].' AND '.
			'p._etat = "active"')
		or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
	$existe = $existe->fetch(PDO::FETCH_ASSOC);

	if (empty($existe))
		die;

	$ref = pdoRevision('participants', $existe['id']);
	$pdo->exec('UPDATE participants SET '.
			'_auteur = '.(int) $_SESSION['user']['id'].', '.
			'_date = NOW(), '.
			'_ref = '.(int) $ref.', '.
			'_message = "Modification du téléphone", '.
			//-----------//
			'telephone = "'.secure(getPhone($_POST['telephone'])).'" '.
		'WHERE '.
			'id = '.(int) $_POST['pid']);

	die(getPhone($_POST['telephone']));
}


if (!empty($_POST['add_other']) && 
	!empty($_POST['chambre']) &&
	!empty($_POST['other']) && 
	is_string($_POST['chambre']) &&
	preg_match('`[UVTXABC][0-9]{3}`', $_POST['chambre'])) {

	$existe = $pdo->query('SELECT '.
			'p.id, '.
			'cp.id AS cpid '.
		'FROM participants AS p '.
		'LEFT JOIN chambres_participants AS cp ON '.
			'cp.id_participant = p.id AND '.
			'cp._etat = "active" '.
		'WHERE '.
			'p.id = '.(int) $_POST['other'].' AND '.
			'p._etat = "active"')
		or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
	$existe = $existe->fetch(PDO::FETCH_ASSOC);

	$chambre = $pdo->query('SELECT '.
			'id '.
		'FROM chambres WHERE '.
			'numero = "'.secure($_POST['chambre']).'" AND '.
			'_etat = "active"')
		or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
	$chambre = $chambre->fetch(PDO::FETCH_ASSOC);

	if (empty($existe) ||
		!empty($existe['cpid']) ||
		empty($chambre))
		$error = true;

	else {
		$pdo->exec($s='INSERT INTO chambres_participants SET '.
			'_auteur = '.(int) $_SESSION['user']['id'].', '.
			'_date = NOW(), '.
			'_message = "Ajout d\'un autre participant dans une chambre", '.
			//------------//
			'id_participant = '.(int) $_POST['other'].', '.
			'id_chambre = '.(int) $chambre['id']);
	}
}


if (isset($_GET['ajax']) &&
	!empty($_POST['format']) &&
	in_array($_POST['format'], ['court', 'long'])) {

	$filtre = !empty($_POST['filtre']) ? trim($_POST['filtre']) : '';
	$sienne = !empty($_POST['chambre']) ? $_POST['chambre'] : '';
	$format = !empty($_POST['chambre']) ? $_POST['chambre'] : '';

	$chambres_ = $pdo->query('SELECT '.
			'c.id, '.
			'c.numero, '.
			'c.nom, '.
			'c.prenom, '.
			'c.surnom, '.
			'c.etat, '.
			'c.places, '.
			'COUNT(cp.id_participant) AS filles '.
		'FROM chambres AS c '.
		'LEFT JOIN chambres_participants AS cp ON '.
			'cp.id_chambre = c.id AND '.
			'cp._etat = "active" '.
		'WHERE '.
			'(c.etat = "lachee" OR c.etat = "amies" OR c.etat = "amiesplus") AND ('.
				'c.format LIKE "long%" OR '.
				'c.format LIKE "'.secure($_POST['format']).'%") AND '.
			'c._etat = "active" '.
		'GROUP BY '.
			'c.id '.
		'ORDER BY '.
			'c.numero')
		or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
	$chambres_ = $chambres_->fetchAll(PDO::FETCH_ASSOC);

	$chambres = [];
	if (empty($filtre) && 
		empty($_POST['other']))
		$chambres[] = array(
			'html' => '(Sans chambre)',
			'numero' => '',
			'color' => 'black',
			'bgColor' => 'transparent');

	foreach ($chambres_ as $chambre) {
		
		$numero = $chambre['numero'];
		$places = $chambre['places'];
		$places -= $chambre['filles'];

		if (!empty($filtre) && 
			strpos($numero, strtoupper(preg_replace('/\s+/', '', $filtre))) === false &&
			!search_filtres(strtolower(removeAccents($chambre['prenom'].' '.$chambre['surnom'].' '.$chambre['nom'])), 
					make_filtres($filtre)) ||
			$places <= 0 && $sienne != $numero)
			continue;

		$chambres[] = array(
			'html' => $numero.($numero == $sienne ? ' <span>(La sienne)</span>' : '').'<br />'.
				'<small><small class="lui">'.stripslashes($chambre['prenom'].' '.$chambre['surnom'].' '.$chambre['nom']).'</small><br />'.
				$labelsEtatChambre[$chambre['etat']].' / '.$places.'p</small>',
			'numero' => $numero,
			'bgColor' => colorChambre($numero),
			'color' => colorContrast(colorChambre($numero)));
	}

	header('Content-Type: application/json', true);
	echo json_encode($chambres);
	exit;
}


$ecoles = $pdo->query('SELECT '.
		'e.id, '.
		'e.format_long, '.
		'e.nom '.
	'FROM ecoles AS e '.
	'WHERE '.
		'e._etat = "active" '.
	'ORDER BY '.
		'e.nom ASC')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$ecoles = $ecoles->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);


if (isset($_GET['ecole'])) {

	if (!empty($_GET['del']) &&
		intval($_GET['del'])) {
		$existe = $pdo->query('SELECT '.
				'p.id, '.
				'cp.id AS cpid '.
			'FROM participants AS p '.
			'LEFT JOIN chambres_participants AS cp ON '.
				'cp.id_participant = p.id AND '.
				'cp._etat = "active" '.
			'WHERE '.
				'p.id = '.(int) $_GET['del'].' AND '.
				'p._etat = "active"')
			or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
		$existe = $existe->fetch(PDO::FETCH_ASSOC);

		if (!empty($existe['cpid'])) {
			$ref = pdoRevision('chambres_participants', $existe['cpid']);
			$pdo->exec('UPDATE chambres_participants SET '.
				'_auteur = '.(int) $_SESSION['user']['id'].', '.
				'_date = NOW(), '.
				'_ref = '.(int) $ref.', '.
				'_etat = "desactive", '.
				'_message = "Suppression d\'un autre participant d\'une chambre" '.
				//------------//
				'WHERE '.
					'id = '.(int) $existe['cpid']);
		}
	}


	$filles = $pdo->query('SELECT '.
			'e.id AS eid, '.
			'e.format_long, '.
			'e.nom AS enom, '.
			'p.date_inscription, '.
			'p.id, '.
			'p.nom, '.
			'p.pompom, '.
			'p.fanfaron, '.
			'p.cameraman, '.
			'p.prenom, '.
			's.id AS sid, '.
			's.sport, '.
			's.sexe, '.
			'p.telephone, '.
			'c.id AS cid, '.
			'c.numero, '.
			'cp.respo '.
		'FROM ecoles AS e '.
		'LEFT JOIN (
			participants AS p '.
			'JOIN tarifs_ecoles AS te ON '.
				'te.id = p.id_tarif_ecole AND '.
				'te._etat = "active" '.
			'JOIN tarifs AS t ON '.
				't.id = te.id_tarif AND '.
				't.logement = 1 AND '.
				't._etat = "active" '.
			'LEFT JOIN (sportifs AS sp '.
				'JOIN equipes AS eq ON '.
					'eq.id = sp.id_equipe AND '.
					'eq._etat = "active" '.
				'JOIN ecoles_sports AS es ON '.
					'es.id = eq.id_ecole_sport AND '.
					'es._etat = "active" '.
				'JOIN sports AS s ON '.
					's.id = es.id_sport AND '.
					's._etat = "active") ON '.
				'sp.id_participant = p.id AND '.
				'sp._etat = "active" '.
			'LEFT JOIN (chambres_participants AS cp '.
				'JOIN chambres AS c ON '.
					'c.id = cp.id_chambre AND '.
					'c._etat = "active") ON '.
				'cp.id_participant = p.id AND '.
				'cp._etat = "active") ON '.
			'e.id = p.id_ecole AND '.
			'p._etat = "active" AND  '.
			'p.sexe = "f" '.
		'WHERE '.
			(!empty($_GET['ecole']) && in_array($_GET['ecole'], array_keys($ecoles)) ? 'e.id = '.(int) $_GET['ecole'].' AND ' : '').
			//Avec le filtre on perd le caractère LEFT JOIN de la jointure puisque cela met de côté les résultats NULL
			(!empty($_GET['filter']) ? '('.
				'CONCAT(p.prenom, " ", p.nom) LIKE "%'.secure($_GET['filter']).'%" OR '.
				'CONCAT(p.nom, " ", p.prenom) LIKE "%'.secure($_GET['filter']).'%" OR '.
				'c.numero LIKE "%'.secure($_GET['filter']).'%") AND ' : '').
			'e._etat = "active" '.
		'ORDER BY '.
			'e.nom ASC, '.
			's.sport ASC, '.
			'p.nom ASC, '.
			'p.prenom ASC')
		or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
	$filles = $filles->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_GROUP);


	//Suppression des doublons (dans le cas de plusieurs sports)
	$ids = [];
	foreach ($filles as $eid => $group) {
		foreach ($group as $key => $fille) {
			if (empty($fille['id']))
				continue;

			if (in_array($fille['id'], $ids))
				unset($filles[$eid][$key]);

			else
				$ids[] = $fille['id'];
		}
	}


	$labels_phases = [
		'phase1' => '<b>Phase 1</b> : jusqu\'au '.$finPhase1->format('d/m/y H:i'),
		'phase2' => '<b>Phase 2</b> : jusqu\'au '.$finPhase2->format('d/m/y H:i'),
		'malus' => 	'<b>Phase malus</b> : jusqu\'au '.$finMalus->format('d/m/y H:i'),
		'modif' => 	'<b>Phase modification</b> : jusqu\'au '.$finInscrip->format('d/m/y H:i'),
		'end' => '<b>Phase spéciale</b> : à partir du '.$finInscrip->add(new DateInterval('PT1S'))->format('d/m/y H:i')];


	foreach ($filles as $eid => $group) {
		$phases = [
			'phase1' => [],
			'phase2' => [],
			'malus' => [],
			'modif' => [],
			'end' => []];

		foreach ($group as $k => $fille) {
			$inscrip = new DateTime($fille['date_inscription']);
			
			if ($inscrip < $finPhase1) 			$phases['phase1'][$k] = array_merge($fille, ['phase' => 'phase1']);
			else if ($inscrip < $finPhase2) 	$phases['phase2'][$k] = array_merge($fille, ['phase' => 'phase2']);
			else if ($inscrip < $finMalus)		$phases['malus'][$k] = array_merge($fille, ['phase' => 'malus']);
			else if ($inscrip < $finInscrip) 	$phases['modif'][$k] = array_merge($fille, ['phase' => 'modif']);
			else 								$phases['end'][$k] = array_merge($fille, ['phase' => 'end']);
		}

		$filles[$eid] = $phases['phase1'] + $phases['phase2'] + $phases['malus'] + $phases['modif'] + $phases['end'];
	}


	$other_loges = $pdo->query('SELECT '.
			'e.id AS eid, '.
			'e.nom AS enom, '.
			'e.format_long, '.
			'p.id, '.
			'p.nom, '.
			'p.prenom, '.
			'p.sexe, '.
			'p.fanfaron, '.
			'p.pompom, '.
			'p.cameraman, '.
			't.logement, '.
			's.id AS sid, '.
			's.sport, '.
			's.sexe AS ssexe, '.
			'p.telephone, '.
			'c.id AS cid, '.
			'c.numero, '.
			'cp.respo '.
		'FROM ecoles AS e '.
		'LEFT JOIN (participants AS p '.
			'JOIN tarifs_ecoles AS te ON '.
				'te.id = p.id_tarif_ecole AND '.
				'te._etat = "active" '.
			'JOIN tarifs AS t ON '.
				't.id = te.id_tarif AND '.
				't._etat = "active" '.
			'LEFT JOIN (sportifs AS sp '.
				'JOIN equipes AS eq ON '.
					'eq.id = sp.id_equipe AND '.
					'eq._etat = "active" '.
				'JOIN ecoles_sports AS es ON '.
					'es.id = eq.id_ecole_sport AND '.
					'es._etat = "active" '.
				'JOIN sports AS s ON '.
					's.id = es.id_sport AND '.
					's._etat = "active") ON '.
				'sp.id_participant = p.id AND '.
				'sp._etat = "active" '.
			'JOIN chambres_participants AS cp ON '.
				'cp.id_participant = p.id AND '.
				'cp._etat = "active" '.
			'JOIN chambres AS c ON '.
				'c.id = cp.id_chambre AND '.
				'c._etat = "active") ON '.
			'e.id = p.id_ecole AND '.
			'p._etat = "active" AND  '.
			'(p.sexe = "f" AND t.logement = 0 OR p.sexe = "h") '.
		'WHERE '.
			(!empty($_GET['ecole']) && in_array($_GET['ecole'], array_keys($ecoles)) ? 'e.id = '.(int) $_GET['ecole'].' AND ' : '').
			//Avec le filtre on perd le caractère LEFT JOIN de la jointure puisque cela met de côté les résultats NULL
			(!empty($_GET['filter']) ? '('.
				'CONCAT(p.prenom, " ", p.nom) LIKE "%'.secure($_GET['filter']).'%" OR '.
				'CONCAT(p.nom, " ", p.prenom) LIKE "%'.secure($_GET['filter']).'%" OR '.
				'c.numero LIKE "%'.secure($_GET['filter']).'%") AND ' : '').
			'e._etat = "active" '.
		'ORDER BY '.
			'e.nom ASC, '.
			'p.nom ASC, '.
			'p.prenom ASC, '.
			's.sport ASC')
		or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
	$other_loges = $other_loges->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_GROUP);


	//Suppression des doublons (dans le cas de plusieurs sports)
	$ids = [];
	foreach ($other_loges as $eid => $group) {
		foreach ($group as $key => $other) {
			if (empty($other['id']))
				continue;

			if (in_array($other['id'], $ids))
				unset($other_loges[$eid][$key]);

			else
				$ids[] = $other['id'];
		}
	}
}


if (!empty($_GET['ecole']) &&
	in_array($_GET['ecole'], array_keys($ecoles))) {

	$others = $pdo->query('SELECT '.
			'p.id, '.
			'p.nom, '.
			'p.prenom '.
		'FROM participants AS p '.
		'JOIN tarifs_ecoles AS te ON '.
			'te.id = p.id_tarif_ecole AND '.
			'te._etat = "active" '.
		'JOIN tarifs AS t ON '.
			't.id = te.id_tarif AND '.
			't._etat = "active" '.
		'JOIN ecoles AS e ON '.
			'e.id = p.id_ecole AND '.
			'e._etat = "active" '.
		'WHERE '.
			'(p.sexe = "f" AND t.logement = 0 OR p.sexe = "h") AND '.
			'p.id_ecole = '.(int) $_GET['ecole'].' AND '.
			'p.id NOT IN (SELECT '.
					'cp.id_participant '.
				'FROM chambres_participants AS cp WHERE cp._etat = "active") AND '.
			'p._etat = "active" '.
		'ORDER BY '.
			'p.nom ASC, '.
			'p.prenom ASC')
		or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
	$others = $others->fetchAll(PDO::FETCH_ASSOC);
}


//Inclusion du bon fichier de template
require DIR.'templates/admin/logement/filles.php';
