<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/admin/logement/action_chambres_batiment.php *****/
/* Liste des chambres dans le batiment concerné ************/
/* *********************************************************/
/* Dernière modification : le 19/01/15 *********************/
/* *********************************************************/

if (isset($_GET['ajax'])) {

	$format = null;
	if (!empty($_POST['format'])) {
		if ($_POST['format'] == 'court') $format = 'court';
		else if (strpos($_POST['format'], 'long') === 0) $format = 'long';
	}


	header('Content-Type: application/json', true);
	if (empty($format))
		die(json_encode([]));

	$filtre = !empty($_POST['filtre']) ? trim($_POST['filtre']) : '';

	$filles = $pdo->query('SELECT '.
			'p.id AS pid, '.
			'p.id, '.
			'p.prenom, '.
			'p.nom, '.
			's.sport, '.
			's.sexe, '.
			'e.nom AS enom '.
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
		'WHERE '.
			't.logement = 1 AND '.
			'p.sexe = "f" AND '.
			($format == 'court' ? 'e.format_long = 0 AND ' : '').
			'p.id NOT IN (SELECT cp.id_participant FROM chambres_participants AS cp WHERE cp._etat = "active") AND ('.
				'CONCAT(p.prenom, " ", p.nom) LIKE "%'.secure($filtre).'%" OR '.
				'CONCAT(p.nom, " ", p.prenom) LIKE "%'.secure($filtre).'%") AND '.
			'p._etat = "active" '.
		'ORDER BY '.
			'p.nom ASC, '.
			'p.prenom ASC, '.
			'e.nom ASC '.
		'LIMIT 20')
		or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
	$filles = $filles->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);

	die(json_encode($filles));
}

if (isset($_GET['add']) &&
	!empty($_POST['chambre']) &&
	!empty($_POST['pid']) &&
	intval($_POST['pid'])) {

	$existe = $pdo->query('SELECT '.
			'c.id, '.
			'c.etat, '.
			'c.format, '.
			'c.places, '.
			'COUNT(cp.id_participant) AS cp '.
		'FROM chambres AS c '.
		'LEFT JOIN chambres_participants AS cp ON '.
			'cp.id_chambre = c.id AND '.
			'cp._etat = "active" '.
		'WHERE '.
			'numero = "'.secure($_POST['chambre']).'" AND '.
			'c._etat = "active" '.
		'GROUP BY '.
			'c.id')
		or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
	$existe = $existe->fetch(PDO::FETCH_ASSOC);

	if (empty($existe) ||
		!in_array($existe['etat'], array('amies', 'amiesplus', 'lachee')) ||
		!in_array($existe['format'], array('court', 'long_soiree', 'long_petitdej')) ||
		$existe['places'] - $existe['cp'] <= 0)
		die('toto');


	$existep = $pdo->query('SELECT '.
			'p.id, '.
			'COUNT(cp.id_chambre) AS cc '.
		'FROM participants AS p '.
		'JOIN tarifs_ecoles AS te ON '.
			'p.id_tarif_ecole = te.id AND '.
			'te._etat = "active" '.
		'JOIN tarifs AS t ON '.
			't.id = te.id_tarif AND '.
			't._etat = "active" '.
		'JOIN ecoles AS e ON '.
			'e.id = p.id_ecole AND '.
			'e._etat = "active" '.
		'LEFT JOIN chambres_participants AS cp ON '.
			'cp.id_participant = p.id AND '.
			'cp._etat = "active" '.
		'WHERE '.
			'p.id = '.(int) $_POST['pid'].' AND '.
			'p.sexe = "f" AND '.
			't.logement = 1 '.
			($existe['format'] == 'court' ? ' AND e.format_long = 0 ' : '').
		'GROUP BY '.
			'p.id')
		or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
	$existep = $existep->fetch(PDO::FETCH_ASSOC);

	if (empty($existep) ||
		$existe['cc'] > 0)
		die('titi');

	$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
	$pdo->exec('INSERT INTO chambres_participants SET '.
		'_auteur = '.(int) $_SESSION['user']['id'].', '.
		'_date = NOW(), '.
		'_message = "Ajout d\'un participant dans une chambre", '.
		//------------//
		'id_participant = '.(int) $existep['id'].', '.
		'id_chambre = '.(int) $existe['id']);
	$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

	die('tutu');
}

if (isset($_GET['delete']) &&
	!empty($_POST['id'])) {

	$existe = $pdo->query('SELECT '.
			'id '.
		'FROM chambres_participants '.
		'WHERE '.
			'id_participant = '.(int) $_POST['id'].' AND '.
			'_etat = "active"')
		or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
	$existe = $existe->fetch(PDO::FETCH_ASSOC);

	if (empty($existe))
		die;

	$ref = pdoRevision('chambres_participants', $existe['id']);
	$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
	$pdo->exec('UPDATE chambres_participants SET '.
		'_auteur = '.(int) $_SESSION['user']['id'].', '.
		'_date = NOW(), '.
		'_ref = '.(int) $ref.', '.
		'_etat = "desactive", '.
		'_message = "Suppression d\'un participant d\'une chambre" '.
		//------------//
		'WHERE '.
			'id = '.(int) $existe['id']);
	$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

	die;
}


if (isset($_GET['respo']) &&
	!empty($_POST['id'])) {

	$existe = $pdo->query('SELECT '.
			'cp1.id, '.
			'cp1.respo, '.
			'cp2.id AS rid '.
		'FROM chambres_participants AS cp1 '.
		'LEFT JOIN chambres_participants AS cp2 ON '.
			'cp2.id_chambre = cp1.id_chambre AND '.
			'cp2._etat = "active" AND '.
			'cp2.respo = 1 '.
		'WHERE '.
			'cp1.id_participant = '.(int) $_POST['id'].' AND '.
			'cp1._etat = "active"')
		or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
	$existe = $existe->fetch(PDO::FETCH_ASSOC);

	if (empty($existe))
		die;

	if ($existe['rid'] != $existe['id']) {
		$ref = pdoRevision('chambres_participants', $existe['rid']);
		$pdo->exec('UPDATE chambres_participants SET '.
				'_auteur = '.(int) $_SESSION['user']['id'].', '.
				'_date = NOW(), '.
				'_ref = '.(int) $ref.', '.
				'_message = "Suppression du statut de responsable de chambre", '.
				//------------//
				'respo = 0 '.
			'WHERE '.
				'id = '.(int) $existe['rid']);
	}

	$ref = pdoRevision('chambres_participants', $existe['id']);
	$pdo->exec('UPDATE chambres_participants SET '.
			'_auteur = '.(int) $_SESSION['user']['id'].', '.
			'_date = NOW(), '.
			'_ref = '.(int) $ref.', '.
			'_message = "Changement du responsable de chambre", '.
			//------------//
			'respo = 1 - respo '.
		'WHERE '.
			'id = '.(int) $existe['id']);

	die;
}


if (isset($_GET['maj']) &&
	!empty($_POST['chambre']) &&
	is_string($_POST['chambre']) &&
	strlen($_POST['chambre']) == 4 &&
	in_array($_POST['clef'], array_keys($labelsEtatClef)) &&
	isset($_POST['lit'])) {

	$existe = $pdo->query('SELECT '.
			'c.id, '.
			'c.etat, '.
			'c.places, '.
			'COUNT(cp.id_participant) AS cp '.
		'FROM chambres AS c '.
		'LEFT JOIN chambres_participants AS cp ON '.
			'cp.id_chambre = c.id AND '.
			'cp._etat = "active" '.
		'WHERE '.
			'numero = "'.secure($_POST['chambre']).'" AND '.
			'c._etat = "active" '.
		'GROUP BY '.
			'c.id')
		or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
	$existe = $existe->fetch(PDO::FETCH_ASSOC);

	if (empty($existe) ||
		!in_array($existe['etat'], array('amies', 'amiesplus', 'lachee')))
		die;


	$ref = pdoRevision('chambres', $existe['id']);
	$pdo->exec('UPDATE chambres SET '.
		'_auteur = '.(int) $_SESSION['user']['id'].', '.
		'_ref = '.(int) $ref.', '.
		'_date = NOW(), '.
		'_message = "Modification de l\'état des clefs/lit de camp", '.
		//---------------//
		'etat_clef = "'.secure($_POST['clef']).'", '.
		'lit_camp = '.abs((int) $_POST['lit']).' '.
	'WHERE '.
		'id = '.$existe['id']); 

	die;
}

$batiment = str_replace('_', '', $args[2][0]);
$proprios_ = $pdo->query('SELECT '.
		'c.id, '.
		'c.numero, '.
		'c.places, '.
		'c.nom, '.
		'c.prenom, '.
		'c.surnom, '.
		'c.email, '.
		'c.telephone, '.
		'c.etat, '.
		'c.format, '.
		'c.etat_clef, '.
		'c.lit_camp, '.
		'GROUP_CONCAT(cp.id_participant, ",") AS filles, '.
		'(SELECT cp2.id_participant '.
			'FROM chambres_participants AS cp2 '.
			'WHERE '.
				'cp2.id_chambre = c.id AND '.
				'cp2.respo = 1 AND '.
				'cp2._etat = "active" '.
			'LIMIT 1) AS respo '.
	'FROM chambres AS c '.
	'LEFT JOIN chambres_participants AS cp ON '.
		'cp.id_chambre = c.id AND '.
		'cp._etat = "active" '.
	'WHERE '.
		'SUBSTR(c.numero, 1, 1) = "'.$batiment.'" AND ('.
			'c.etat = "lachee" OR '.
			'c.etat = "amiesplus" OR '.
			'c.etat = "amies") AND '.
		'c._etat = "active" '.
	'GROUP BY '.
		'c.id')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$proprios_ = $proprios_->fetchAll(PDO::FETCH_ASSOC);


$participants = $pdo->query('SELECT '.
		'p.id AS pid, '.
		'p.id, '.
		'p.nom, '.
		'e.nom AS enom, '.
		'p.prenom, '.
		's.id AS sid, '.
		's.sport, '.
		's.sexe, '.
		'p.telephone '.
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
	'WHERE '.
		'p._etat = "active" '.
	'ORDER BY '.
		'e.nom ASC, '.
		's.sport ASC, '.
		'p.nom ASC, '.
		'p.prenom ASC')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$participants = $participants->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);



$proprios = array();
$nbmaxfilles = 0;
foreach ($proprios_ as $proprio) {
	$proprios[$proprio['numero']] = $proprio;
	$nbmaxfilles = max($nbmaxfilles, $proprio['places']);
}



//Téléchargement du fichier XLSX concerné
if (isset($_GET['excel'])) {
	$proprietaires = [];

	$nbfilles = max(1, $nbmaxfilles);
	$etages = in_array($batiment, array('A', 'B', 'C')) ? 4 : 6;	
	foreach (range(0, $etages) as $etage) {
		$chambres = $etage == 0 ? 
			($etages == 6 ? 8 : 20) : 
			($etages == 6 ? 16 : 17);

		foreach (range(1, $chambres) as $numero) {
			$chambre = sprintf('%s%d%02d', $batiment, $etage, $numero);
			$color = colorChambre($chambre);
			$proprio = isset($proprios[$chambre]) ? $proprios[$chambre] : null;
			
			if (empty($proprio))
				continue;

			$proprio['numero'] = $chambre;
			$proprio['filles'] = explode(',', $proprio['filles']);
			$proprio['filles'] = array_values(array_filter($proprio['filles']));

			for ($i = 0; $i < $nbfilles; $i++) {
				$fille = !empty($proprio['filles'][$i]) ? $participants[$proprio['filles'][$i]] : null;
				$proprio['fille_'.$i.'_ecole'] = empty($fille) ? '' : $fille['enom'];
				$proprio['fille_'.$i.'_np'] = empty($fille) ? '' : $fille['nom'].' '.$fille['prenom'];
			}

			$proprietaires[] = $proprio;
		}
	}

	$titre = 'Liste Chambres : Batiment '.$batiment;
	$fichier = 'liste_chambres_'.$batiment;
	$items = $proprietaires;
	$labels = [
		'Chambre' => 'numero',
		'Nom' => 'nom',
		'Prénom' => 'prenom',
		'Surnom' => 'surnom',
		'Etat' => 'etat',
		'Format' => 'format'];

	for ($i = 0; $i < $nbfilles; $i++) {
		$labels['Ecole '.($i+1)] = 'fille_'.$i.'_ecole';
		$labels['Fille '.($i+1)] = 'fille_'.$i.'_np';
	}

	$labels = array_merge($labels, [
		'Clefs' => 'etat_clef', 
		'Lit de Camp' => 'lit_camp']);

	exportXLSX($items, $fichier, $titre, $labels);

}


//Inclusion du bon fichier de template
require DIR.'templates/admin/logement/chambres_batiment.php';
