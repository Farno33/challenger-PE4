<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/ecoles/participants.php *************************/
/* Inscription des participants ****************************/
/* *********************************************************/
/* Dernière modification : le 08/12/14 *********************/
/* *********************************************************/


$id = $args[1][0];
if (!(!empty($_SESSION['user']) && (
		!empty($_SESSION['user']['privileges']) &&
		in_array('ecoles', $_SESSION['user']['privileges']) ||
		!empty($_SESSION['user']['ecoles']) &&
		in_array($id, $_SESSION['user']['ecoles']))))
	die(header('location:'.url('accueil', false, false)));


if (!empty($_POST['masse']))
	die(header('location:'.url('ecoles/'.$id.'/import', false, false)));

if (!empty($_SESSION['user']['privileges']) &&
	in_array('ecoles', $_SESSION['user']['privileges']))
	$accesAdmin = true;


$now = new DateTime();
$finPhase1 = new DateTime(APP_FIN_PHASE1);
$finPhase2 = new DateTime(APP_DATE_MALUS);
$finMalus = new DateTime(APP_FIN_MALUS);
$finInscrip = new DateTime(APP_FIN_INSCRIP);

$phase_actuelle = $now < $finPhase1 ? 'phase1' : (
	$now < $finPhase2 ? 'phase2' : (
		$now < $finMalus ? 'malus' : (
			$now < $finInscrip ? 'modif' : null)));


$pdo->exec('DELETE FROM participants WHERE _etat = "temp" AND _auteur = '.(int) $_SESSION['user']['id']);
$pdo->exec('DELETE FROM equipes WHERE _etat = "temp" AND _auteur = '.(int) $_SESSION['user']['id']);
$pdo->exec('DELETE FROM sportifs WHERE _etat = "temp" AND _auteur = '.(int) $_SESSION['user']['id']);


$ecole = $pdo->query($request_ecole = 'SELECT '.
		'e.*, '.
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
		!in_array('ecoles', $_SESSION['user']['privileges'])))
	die(header('location:'.url('', false, false)));


if (!in_array($ecole['etat_inscription'], ['ouverte', 'limitee']) && (
		empty($_SESSION['user']['privileges']) ||
		!in_array('ecoles', $_SESSION['user']['privileges'])))
	die(header('location:'.url('ecoles/'.$ecole['id'].'/recapitulatif', false, false)));


if (!empty($_POST['del_participant']) &&
	!empty($_POST['id']))
	$_POST['delete'] = $_POST['id'];


$tarifs = $pdo->query('SELECT '.
		'te.id, '.
		't.sportif, '.
		't.for_pompom, '.
		't.for_cameraman, '.
		't.for_fanfaron, '.
		't.tarif, '.
		't.nom, '.
		't.description, '.
		'CASE WHEN t.id_ecole_for_special = '.$ecole['id'].' OR t.id_ecole_for_special IS NULL THEN t.id_sport_special ELSE NULL END AS id_sport_special, '.
		's.sport, '.
		's.sexe, '.
		't.logement '.
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
$tarifs = $tarifs->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);





//On veut éditer un participant, on récupère donc sa fiche
if (!empty($_GET['edit']) || 
	!empty($_POST['edit']) ||
	!empty($_POST['delete']) ||
	!empty($_POST['listing'])) {

	$pid = (int) (!empty($_POST['edit'])
		? $_POST['edit']
		: (!empty($_POST['delete']) 
			? $_POST['delete'] 
			: (!empty($_POST['listing']) 
				? $_POST['listing']
				: $_GET['edit'])));

	$found = $pdo->query($s='SELECT '.
			'p.id, '.
			'p.nom, '.
			'p.prenom, '.
			'p.sexe, '.
			'p.telephone, '.
			'p.email, '.
			'p.sportif, '.
			'p.pompom, '.
			'p.cameraman, '.
			'p.fanfaron, '.
			'p.licence, '.
			'p.logeur, '.
			't.logement, '.
			'p.id_tarif_ecole, '.
			'p.date_inscription, '.
			'p.recharge '.
		'FROM participants AS p '.
		'JOIN tarifs_ecoles AS te ON '.
			'te.id = p.id_tarif_ecole AND '.
			'te._etat = "active" '.
		'JOIN tarifs AS t ON '.
			't.id = te.id_tarif AND '.
			't._etat = "active" '.
		'WHERE '.
			'p._etat = "active" AND '.
			'p.id_ecole = '.$ecole['id'].' AND '.
			'p.id = '.(int) $pid);
	$found = $found->fetch(PDO::FETCH_ASSOC);

	$found_sports = $pdo->query('SELECT '.
			'es.id, '.
			'sp.id AS spid, '.
			's.sport, '.
			's.sexe, '.
			'eq.label, '.
			'CASE WHEN eq.id_capitaine = sp.id_participant THEN 1 ELSE 0 END AS is_capitaine '.
		'FROM sportifs AS sp '.
		'JOIN equipes AS eq ON '.
			'eq.id = sp.id_equipe AND '.
			'eq._etat = "active" '.
		'JOIN ecoles_sports AS es ON '.
			'es.id = eq.id_ecole_sport AND '.
			'es._etat = "active" '.
		'JOIN sports AS s ON '.
			's.id = es.id_sport AND '.
			's._etat = "active" '.
		'WHERE '.
			'sp._etat = "active" AND '.
			'sp.id_participant = '.(int) $pid)
		->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);

	if (!empty($found)) {
		$inscrip = new DateTime($found['date_inscription']);
		$found['phase'] = $inscrip < $finPhase1 ? 'phase1' : (
			$inscrip < $finPhase2 ? 'phase2' : (
				$inscrip < $finMalus ? 'malus' : null));
	}

	$is_capitaine = false;
	foreach ($found_sports as $es => $val) {
		if ($val['is_capitaine'])
			$is_capitaine = true;
	}

	if (!empty($_POST['edit']) ||
		!empty($_GET['edit']))
		$participant_edit = $found;
}

//Historique du participant
if (!empty($_POST['listing']) &&
	!empty($found)) {

	$history = "SELECT T2.*, T1.lvl, T3.nom AS _auteur_nom, T3.prenom AS _auteur_prenom FROM ".
		"(SELECT ".
	        "@r AS _id, ".
	        "(SELECT @r := _ref FROM participants WHERE id = _id) AS parent, ".
	        "@l := @l + 1 AS lvl ".
	    "FROM ".
	        "(SELECT @r := ".$pid.", @l := 0) vars, ".
	        "participants m ".
	    "WHERE @r <> 0) T1 ".
		"JOIN participants T2 ON T1._id = T2.id ".
		"JOIN utilisateurs T3 ON T3.id = T2._auteur ".
		"ORDER BY T1.lvl ASC";
	$history = $pdo->query($history)->fetchAll(PDO::FETCH_ASSOC);
	$titre = 'Historique de "'.stripslashes($found['nom'].' '.$found['prenom']).'"';
	die(require DIR.'templates/ecoles/historique.php');
}


//On supprime un participant
if (!empty($_POST['delete']) &&
	!empty($found) &&
	(!empty($found['phase']) && //null est mise de côté
		$found['phase'] == $phase_actuelle ||
	!empty($accesAdmin)) &&
	empty($is_capitaine) &&
	isset($found_sports)) {

	$pdo->exec('set FOREIGN_KEY_CHECKS = 0');

	$chambres = $pdo->query('SELECT '.
			'cp.id '.
		'FROM chambres_participants AS cp '.
		'JOIN chambres AS c ON '.
			'c.id = cp.id_chambre AND '.
			'c._etat = "active" '.
		'WHERE '.
			'cp._etat = "active" AND '.
			'cp.id_participant = '.$found['id'])
		->fetchAll(PDO::FETCH_ASSOC);

	$erreurs = $pdo->query('SELECT '.
			'er.id '.
		'FROM erreurs AS er '.
		'WHERE '.
			'er._etat = "active" AND '.
			'er.id_participant = '.$found['id'])
		->fetchAll(PDO::FETCH_ASSOC);
	
	$del_chambre = $pdo->prepare('UPDATE chambres_participants SET '.
		'_auteur = '.(int) $_SESSION['user']['id'].', '.
		'_ref = :ref, '.
		'_date = NOW(), '.
		'_message = "Suppression du lien chambre-participant", '.
		'_etat = "desactive" '.
	'WHERE '.
		'id = :id');

	foreach ($chambres as $chambre) {
		$ref = pdoRevision('chambres_participants', $chambre['id']);
		$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
		$del_chambre->execute([
			':ref' => $ref, 
			':id' => $chambre['id']]);
		$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
	}

	$del_erreur = $pdo->prepare('UPDATE erreurs SET '.
		'_auteur = '.(int) $_SESSION['user']['id'].', '.
		'_ref = :ref, '.
		'_date = NOW(), '.
		'_message = "Suppression de l\'erreur", '.
		'_etat = "desactive" '.
	'WHERE '.
		'id = :id');

	foreach ($erreurs as $erreur) {
		$ref = pdoRevision('erreurs', $erreur['id']);
		$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
		$del_erreur->execute([
			':ref' => $ref, 
			':id' => $erreur['id']]);
		$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
	}

	$del_sportif = $pdo->prepare('UPDATE sportifs SET '.
		'_auteur = '.(int) $_SESSION['user']['id'].', '.
		'_ref = :ref, '.
		'_date = NOW(), '.
		'_message = "Suppression du sportif", '.
		'_etat = "desactive" '.
	'WHERE '.
		'id = :id');

	foreach ($found_sports as $found_sport) {
		$ref = pdoRevision('sportifs', $found_sport['spid']);
		$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
		$del_sportif->execute([
			':ref' => $ref, 
			':id' => $found_sport['spid']]);
		$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
	}

	$ref = pdoRevision('participants', (int) $_POST['delete']);
	$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
	$pdo->exec('UPDATE participants SET '.
			'_auteur = '.(int) $_SESSION['user']['id'].', '.
			'_ref = '.(int) $ref.', '.
			'_date = NOW(), '.
			'_message = "Suppression du participant", '.
			'_etat = "desactive" '.
		'WHERE '.
			'id_ecole = '.$ecole['id'].' AND '.
			'id = '.(int) $_POST['delete']);
	$pdo->exec('set FOREIGN_KEY_CHECKS = 1');


	//Faut-il mettre nul à classements_indiv ???

	$delete = true;
	$ecole = $pdo->query($request_ecole);
	$ecole = $ecole->fetch(PDO::FETCH_ASSOC);
}


if (!empty($_POST['add_participant']))
	$add = true;

if (!empty($_POST['edit_participant']))
	$modify = true;


$cons = 'SELECT '.
	'p.id, '.
	'p.*, '.
	't.logement, ';
if (substr($ecole['nom'],0,3) == 'ECL' && $ecole['nom'] != 'ECL Individuel')
{
	$cons = $cons.'(SELECT eq.id '.
					'FROM equipes AS eq '.
					'WHERE '.
						'eq.id_capitaine = p.id AND '.
						'eq._etat = "active") AS equipe, '.
			
					'(SELECT eq.id_ecole_sport '.
					'FROM equipes AS eq '.
					'WHERE '.
						'eq.id_capitaine = p.id AND '.
						'eq._etat = "active") AS ecole_sport, ';
}
$cons = $cons.'CASE WHEN (SELECT COUNT(eq.id) '.
		'FROM equipes AS eq '.
		'JOIN ecoles_sports AS es ON '.
			'es.id = eq.id_ecole_sport AND '.
			'es._etat = "active" '.
		'JOIN sports AS s ON '.
			's.id = es.id_sport AND '.
			's._etat = "active" '.
		'WHERE '.
			'eq.id_capitaine = p.id AND '.
			'eq._etat = "active") > 0 THEN 1 ELSE 0 END AS is_capitaine '.	
	'FROM participants AS p '.
	'JOIN tarifs_ecoles AS te ON '.
	'te.id = p.id_tarif_ecole AND '.
	'te._etat = "active" '.
	'JOIN tarifs AS t ON '.
	't.id = te.id_tarif AND '.
	't._etat = "active" '.
	'WHERE '.
	'p.id_ecole = '.$ecole['id'].' AND '.
	'p._etat = "active" '.
	'ORDER BY '.
	'nom ASC, '.
	'prenom ASC';
$participants = $pdo->query($cons) or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$participants = $participants->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);


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

$places_inscription = (empty($quotas['total']) ? 0 : max(0, (int) $quotas['total']) - $ecole['nb_inscriptions']);
$places_sportif = (empty($quotas['sportif']) ? 0 : (int) $quotas['sportif']) - $ecole['nb_sportif']; 
$places_nonsportif = (empty($quotas['nonsportif']) ? 0 : (int) $quotas['nonsportif']) - ($ecole['nb_inscriptions'] - $ecole['nb_sportif']); 
$places_filles_logees = (empty($quotas['filles_logees']) ? 0 : (int) $quotas['filles_logees']) - $ecole['nb_filles_logees']; 
$places_garcons_loges = (empty($quotas['garcons_loges']) ? 0 : (int) $quotas['garcons_loges']) - $ecole['nb_garcons_loges']; 
$places_logement = (empty($quotas['logement']) ? 0 : (int) $quotas['logement']) - $ecole['nb_garcons_loges'] - $ecole['nb_filles_logees']; 
$places_pompom = (empty($quotas['pompom']) ? 0 : (int) $quotas['pompom']) - $ecole['nb_pompom']; 
$places_cameraman = (empty($quotas['cameraman']) ? 0 : (int) $quotas['cameraman']) - $ecole['nb_cameraman']; 
$places_fanfaron = (empty($quotas['fanfaron']) ? 0 : (int) $quotas['fanfaron']) - $ecole['nb_fanfaron']; 
$places_pompom_nonsportif = (empty($quotas['pompom_nonsportif']) ? 0 : (int) $quotas['pompom_nonsportif']) - $ecole['nb_pompom_nonsportif']; 
$places_cameraman_nonsportif = (empty($quotas['cameraman_nonsportif']) ? 0 : (int) $quotas['cameraman_nonsportif']) - $ecole['nb_cameraman_nonsportif']; 
$places_fanfaron_nonsportif = (empty($quotas['fanfaron_nonsportif']) ? 0 : (int) $quotas['fanfaron_nonsportif']) - $ecole['nb_fanfaron_nonsportif']; 


// détermination des phases en fonction de la data d'inscription
$phases = [
	'phase1' => [],
	'phase2' => [],
	'malus' => [],
	'modif' => [],
	'end' => []];


$labels_phases = [
	'phase1' => '<b>Phase 1</b> : jusqu\'au '.$finPhase1->format('d/m/y H:i'),
	'phase2' => '<b>Phase 2</b> : jusqu\'au '.$finPhase2->format('d/m/y H:i'),
	'malus' => 	'<b>Phase malus</b> <i>(+'.$ecole['malus'].' %)</i> : jusqu\'au '.$finMalus->format('d/m/y H:i'),
	'modif' => 	'<b>Phase modification</b> : jusqu\'au '.$finInscrip->format('d/m/y H:i'),
	'end' => '<b>Phase spéciale</b> : à partir du '.$finInscrip->add(new DateInterval('PT1S'))->format('d/m/y H:i')];


foreach ($participants as $k => $participant) {
	$inscrip = new DateTime($participant['date_inscription']);
	
	if ($inscrip < $finPhase1) 			$phases['phase1'][$k] = array_merge($participant, ['phase' => 'phase1']);
	else if ($inscrip < $finPhase2) 	$phases['phase2'][$k] = array_merge($participant, ['phase' => 'phase2']);
	else if ($inscrip < $finMalus)		$phases['malus'][$k] = array_merge($participant, ['phase' => 'malus']);
	else if ($inscrip < $finInscrip) 	$phases['modif'][$k] = array_merge($participant, ['phase' => 'modif']);
	else 								$phases['end'][$k] = array_merge($participant, ['phase' => 'end']);
}

$participants = $phases['end'] + $phases['modif'] + $phases['malus'] + $phases['phase2'] + $phases['phase1'];



//Téléchargement du fichier XLSX concerné
if (isset($_GET['excel'])) {

	foreach ($participants as $k => $participant) {
		$participants[$k]['psexe'] = $participant['sexe'] == 'h' ? 'Homme' : 'Femme';
		$participants[$k]['psportif'] = $participant['sportif'] ? 'Oui' : 'Non';
		$participants[$k]['ppompom'] = $participant['pompom'] ? 'Oui' : '';
		$participants[$k]['pcameraman'] = $participant['cameraman'] ? 'Oui' : '';
		$participants[$k]['pfanfaron'] = $participant['fanfaron'] ? 'Oui' : '';
		$participants[$k]['ptarif'] = $tarifs[$participant['id_tarif_ecole']]['nom'];
		$participants[$k]['plogement'] = $tarifs[$participant['id_tarif_ecole']]['logement'] ? 'Full package' : 'Light package';
		$participants[$k]['pmontant'] = printMoney($tarifs[$participant['id_tarif_ecole']]['tarif']);
		$participants[$k]['precharge'] = printMoney($participant['recharge']);
		$participants[$k]['pphase'] = $participant['phase'];
	}

	$titre = 'Liste des participants';
	$fichier = 'liste_participants';
	$items = $participants;
	$labels = [
		'Nom' => 'nom',
		'Prénom' => 'prenom',
		'Sexe' => 'psexe',
		'Sportif' => 'psportif',
		'Licence' => 'licence',
		'Pompom' => 'ppompom',
		'Cameraman' => 'pcameraman',
		'Fanfaron' => 'pfanfaron',
		'Email' => 'email',
		'Téléphone' => 'telephone',
		'Tarif' => 'ptarif',
		'Logement' => 'plogement',
		'Montant' => 'pmontant',
		'Gourde' => 'precharge',
		'Phase' => "pphase",
	];
	exportXLSX($items, $fichier, $titre, $labels);

}



if (!empty($participant_edit) &&
	isset($participants[$participant_edit['id']])) {
	$participant_edit['phase'] = $participants[$participant_edit['id']]['phase'];
}



//Inclusion du bon fichier de template
require DIR.'templates/ecoles/participants.php';
