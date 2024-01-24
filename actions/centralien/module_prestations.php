<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/public/logout.php *******************************/
/* Gére la déconnexion dans les différents modules *********/
/* *********************************************************/
/* Dernière modification : le 20/11/14 *********************/
/* *********************************************************/


//On ne fait pas attention ici aux contraintes participants
//En particulier téléphone (capitaine), sports spéciaux, logeur, ...
//On prend juste le premier tarif accessible

$possibleEdition = time() <= strtotime(APP_FIN_CENTRALIENS);


$nb_pf_max = APP_CENTRALIENS_NB_PFOOD;

$nb_pf = $pdo->query("SELECT COUNT(*) AS nb " .
	"FROM centraliens " .
	"WHERE pfsamedi = 1 AND _etat = 'active'")
	->fetchAll();

$nb_pf = $nb_pf[0]['nb'];
$possible_packsfull = $nb_pf <= $nb_pf_max;


$nb_gourdes_max = APP_CENTRALIENS_NB_GOURDE;

$nb_gourdes = $pdo->query("SELECT COUNT(*) AS nb " .
	"FROM centraliens " .
	"WHERE gourde = 1 AND _etat = 'active'")
	->fetchAll();

$nb_gourdes = $nb_gourdes[0]['nb'];
$possible_gourde = $nb_gourdes <= $nb_gourdes_max;

$nb_tshirt_max = APP_CENTRALIENS_NB_TSHIRT;

$nb_tshirt = $pdo->query("SELECT COUNT(*) AS nb " .
	"FROM centraliens " .
	"WHERE tshirt = 1 AND _etat = 'active'")
	->fetchAll();

$nb_tshirt = $nb_tshirt[0]['nb'];
$possible_tshirt = $nb_tshirt <= $nb_tshirt_max;

/**
 * Génère un nom d'équipe de maniere totalement arbitraire/aléatoire/je ne sais pas ce que je fais
 *
 * @param string $sid ID du sport de l'équipe à créer
 * @return string label pour cette magnifique nouvelle équipe
 */
function generer_nom_equipe(string $sid): string
{
	global $sports;
	$animaux = array("Accenteur", "Aigle", "Aigrette", "Alligator", "Alouette", "Alpaga", "Anaconda", "Anoa", "Antilope", "Ara", "Argali", "Autour", "Autruche", "Avocette", "Babiroussa", "Babouin", "Baleine", "Banteng", "Beira", "Belette", "Bergeronnette", "Bernache", "Bison", "Blaireau", "Blesbok", "Boa", "Boeuf", "Bongo", "Bonobo", "Bouquetin", "Bouvreuil", "Bruant", "Bubale", "Buffle", "Busard", "Buse", "Cacatoès", "Caille", "Caïman", "Calopsitte", "Campagnol", "Canard", "Capucin", "Capybara", "Caracal", "Carcajou", "Caribou", "Casoar", "Castor", "Céphalophe", "Cerf", "Chacal", "Chameau", "Chamois", "Chardonneret", "Chauve-souris", "Chèvre", "Chevreuil", "Chien", "Chimpanzé", "Choucas", "Chouette", "Cigogne", "Civette", "Coati", "Cobe", "Cobra", "Colibri", "Condor", "Corbeau", "Corneille", "Coucou", "Couleuvre", "Coyote", "Crocodile", "Cygne", "Daim", "Dauphin", "Dhole", "Diable", "Dik-dik", "Dingo", "Douc", "Dragon", "Drill", "Dromadaire", "Dugong", "Ecureuil", "Elan", "Eland", "Eléphant", "Emeu", "Engoulevent", "Entelle", "Epervier", "Etourneau", "Faisan", "Faucon", "Fauvette", "Fennec", "Flamant", "Fossa", "Fou", "Fouine", "Foulque", "Fourmilier", "Furet", "Gallinule", "Gaur", "Gazelle", "Geai", "Gelada", "Genette", "Gibbon", "Girafe", "Glouton", "Gnou", "Goéland", "Goral", "Gorille", "Grand", "Grande", "Grèbe", "Grimpereau", "Grison", "Grive", "Grizzli", "Grue", "Grosbec", "Guanaco", "Guépard", "Guêpier", "Guib", "Gypaète", "Hamster", "Harfang", "Hérisson", "Hermine", "Héron", "Hibou", "Hippopotame", "Hippotrague", "Hirondelle", "Huppe", "Hyène", "Ibis", "Impala", "Isard", "Jaguar", "Kangourou", "Kiwi", "Koala", "Lama", "Lamantin", "Langur", "Lapin", "Lemming", "Léopard", "Lièvre", "Linotte", "Lion", "Loriot", "Loup", "Loutre", "Lycaon", "Macaque", "Évoli", "Maki", "Manchot", "Mandrill", "Mangabey", "Mangouste", "Mara", "Marabout", "Markhor", "Marmotte", "Marsouin", "Martin", "Martinet", "Martre", "Merle", "Mésange", "Milan", "Moineau", "Morse", "Mouffette", "Mulot", "Musaraigne", "Narval", "Nasique", "Nyala", "Ocelot", "Oie", "Okapi", "Orang-outan", "Orignal", "Ornithorynque", "Orque", "Orvet", "Otarie", "Otocyon", "Ouistiti", "Ours", "Outarde", "Palombe", "Panda", "Pangolin", "Paon", "Pélican", "Pikachu", "Perruche", "Petit", "Petit-duc", "Phacochère", "Phoque", "Pic", "Pie", "Pigeon", "Pingouin", "Pinson", "Porc-épic", "Potamochère", "Puku", "Puma", "Putois", "Pygargue", "Python", "Ragondin", "Raphicère", "Rat", "Ratel", "Raton", "Renard", "Renne", "Requin", "Rorqual", "Rhinocéros", "Roitelet", "Rossignol", "Rouge-gorge", "Rougequeue", "Sanglier", "Sarcelle", "Sassabi", "Serin", "Serval", "Siamang", "Singe", "Sitatunga", "Sittelle", "Spatule", "Springbok", "Suricate", "Tamanoir", "Tamarin-lion", "Tapir", "Tarsier", "Taupe", "Tigre", "Topi", "Tortue", "Toucan", "Tourterelle", "Unau", "Urial", "Urubu", "Vanneau", "Varan", "Vautour", "Verdier", "Vipère", "Vigogne", "Vison", "Wallaby", "Wapiti", "Wombat", "Yak", "Zèbre", "Zibeline", "Zorille");
	$couleurs_n = array("orange", "jaune", "rose", "rouge", "marron", "azur");
	$couleurs_m = array("bleu", "vert", "violet", "noir", "gris", "blanc");
	$couleurs_f = array("bleue", "verte", "violette", "noire", "grise", "blanche");
	$couleurs = array_merge($couleurs_n, $sports[$sid]['sexe'] == 'f' ? $couleurs_f : $couleurs_m);
	$centralios = array("du caps", "de la STR", "de la pougne", "du conchiage", "du davis", "de la fouque", "du dancefloor", "du shotgun", "des BE", "du stade");

	return $animaux[rand(0, count($animaux) - 1)] . ($sports[$sid]['individuel'] ? ' ' : 's ') . $couleurs[rand(0, count($couleurs) - 1)] . ($sports[$sid]['individuel'] ? ' ' : 's ') . ($sports[$sid]['sexe'] == 'f' ? 'déesse' . ($sports[$sid]['individuel'] ? ' ' : 's ') : 'dieu' . ($sports[$sid]['individuel'] ? ' ' : 'x ')) . $centralios[rand(0, count($centralios) - 1)] . '-' . $sports[$sid]['sport'];
}

/*
$rankingsql = 'SELECT * FROM sportifs WHERE id_participant = '.(int) $options['id_participant'].'GROUP BY ranking';
$valeur_ranking = $pdo->query($rankingsql);
echo $valeur_ranking;*/

if (isset($_GET['ajax'])) {
	function specialDie()
	{
		ob_end_clean();
	}

	register_shutdown_function('specialDie');

	$id = $id_ecl;

	ob_start();
	require DIR . 'actions/import/licences.php';
	die;
}

if (
	$possibleEdition &&
	!empty($options['id_participant']) &&
	!empty($_POST['change']) &&
	isset($_POST['licence']) &&
	isset($_POST['sexe']) &&
	isset($_POST['telephone'])
) {
	$ref = pdoRevision('participants', $options['id_participant']);
	$options['licence'] = secure(formatLicence($_POST['licence']));
	$options['sexe'] = secure(getSexe($_POST['sexe']));
	$options['telephone'] = secure(getTelephone($_POST['telephone']));

	$pdo->exec('UPDATE participants SET ' .
		'_auteur = ' . (int) $_SESSION['user']['id'] . ', ' .
		'_ref = ' . (int) $ref . ', ' .
		'_date = NOW(), ' .
		'_message = "Modification de la licence, du sexe et/ou du telephone", ' .
		//-------------//
		'licence = "' . $options['licence'] . '", ' .
		'sexe = "' . $options['sexe'] . '", ' .
		'telephone = "' . $options['telephone'] . '" ' .
		'WHERE ' .
		'id = ' . $options['id_participant']);
}

/*
if ($possibleEdition &&
!empty($options['id_participant']) &&
!empty($_POST['validate'])) {
$ref = pdoRevision('sportifs', $options['id_participant']);
//$options['ranking'] = htmlspecialchars($_POST['ranking']);
$pdo->exec('UPDATE sportifs SET '.
'_auteur = '.(int) $_SESSION['user']['id'].', '.
'_ref = '.(int) $ref.', '.
'_date = NOW(), '.
'_message = "Modification du classement", '.
//-------------//
//'ranking = "'.secure($_POST['ranking']).'" '.
'WHERE '.
'id_participant = '.$options['id_participant']);
}
*/

if (
	$possibleEdition &&
	!empty($_POST['save'])
) {
	$options['soiree'] = (int) !empty($_POST['soiree']);
	//$options['pfvendredi'] = (int) !empty($_POST['pfvendredi']);
	//$options['vegetarien'] = (int) !empty($_POST['vegetarien']);
	$nb_pf -= $options['pfsamedi'];
	$options['pfsamedi'] = $possible_packsfull ? (int) !empty($_POST['pfsamedi']) : ($options['pfsamedi'] && !empty($_POST['pfsamedi']));
	$nb_pf += $options['pfsamedi'];
	$possible_packsfull = $nb_pf <= $nb_pf_max;

	$nb_gourdes -= $options['gourde'];
	$options['gourde'] = $possible_gourde ? (int) !empty($_POST['gourde']) : ($options['gourde'] && !empty($_POST['gourde']));
	$nb_gourdes += $options['gourde'];
	$possible_gourde = $nb_gourdes <= $nb_gourdes_max;

	$nb_tshirt -= $options['tshirt'];
	$options['tshirt'] = $possible_tshirt ? (int) !empty($_POST['tshirt']) : ($options['tshirt'] && !empty($_POST['tshirt']));
	$nb_tshirt += $options['tshirt'];
	$possible_tshirt = $nb_tshirt <= $nb_tshirt_max;

	$options['tombola'] = isset($_POST['tombola']) ? max(0, (int) $_POST['tombola']) : 0;

	if (empty($options['id'])) {
		$pdo->exec('INSERT INTO centraliens SET ' .
			'_auteur = ' . (int) $_SESSION['user']['id'] . ', ' .
			'_date = NOW(), ' .
			'_message = "Ajout des options de centralien", ' .
			'_etat = "active", ' .
			//---------------//
			'id_utilisateur  = ' . (int) $_SESSION['user']['id'] . ', ' .
			'soiree = ' . $options['soiree'] . ', ' .
			//'pfvendredi = '.$options['pfvendredi'].', '.
			'pfsamedi = ' . (int)($options['pfsamedi'] && $options['sportif']) . ', ' .
			//'vegetarien = '.$options['vegetarien'].', '.
			'tshirt = ' . (int)($options['tshirt'] && !$options['sportif']) . ', ' .
			'gourde = ' . $options['gourde'] . ', ' .
			'tombola = ' . $options['tombola']) or die(print_r($pdo->errorInfo()));
	} else {
		$ref = pdoRevision('centraliens', $options['id']);
		$pdo->exec('UPDATE centraliens SET ' .
			'_auteur = ' . (int) $_SESSION['user']['id'] . ', ' .
			'_ref = ' . (int) $ref . ', ' .
			'_date = NOW(), ' .
			'_message = "Mise à jour des options de centralien", ' .
			//-------------//
			'soiree = ' . $options['soiree'] . ', ' .
			//'pfvendredi = '.$options['pfvendredi'].', '.
			'pfsamedi = ' . (int)($options['pfsamedi'] && $options['sportif']) . ', ' .
			//'vegetarien = '.$options['vegetarien'].', '.
			'tshirt = ' . (int)($options['tshirt'] && !$options['sportif']) . ', ' .	// oui je pourrais mettre un XOR mais c'est plus clair comme ça
			'gourde = ' . $options['gourde'] . ', ' .
			'tombola = ' . $options['tombola'] . ' ' .
			'WHERE ' .
			'id = ' . $options['id']);
	}
}


if (count($mes_equipes)) {
	$groupe = array_keys($mes_equipes)[0];
	$groupe = empty($equipes[$groupe]) ? null : $equipes[$groupe]['groupe_multiple'];
	$mes_sports = array_column($mes_equipes, 'sid');

	if (!empty($groupe)) {
		foreach ($equipes as $k => $equipe) {
			if ($equipe['groupe_multiple'] != $groupe || in_array($equipe['sid'], $mes_sports))
				unset($equipes[$k]);
		}
		foreach ($sports as $k => $sport) {
			if ($sport['groupe_multiple'] != $groupe || in_array($k, $mes_sports))
				unset($sports[$k]);
		}
	} else {
		$equipes = [];
		$sports = [];
	}

	foreach ($mes_equipes as $eid => $equipe) {
		unset($equipes[$eid]);
	}
}


foreach ($equipes as $k => $equipe) { // On enleve si le sexe n'est pas bon (on ne le fait pas dans sport car j'ai déjà fait le tri en SQL)
	if (
		$equipe['sexe'] != 'm' &&
		!empty($options['sexe']) &&
		$equipe['sexe'] != $options['sexe']
	)
		unset($equipes[$k]);
}

foreach ($equipes as $k => $equipe) { // On enleve s'il n'y a pas les quotas
	if ($sports_quota[$equipe['sid']]['quota_inscription'] != '')
		if ($sports_quota[$equipe['sid']]['quota_inscription'] <= $sports_quota[$equipe['sid']]['nb_sportifs'])
			unset($equipes[$k]);
}

foreach ($sports as $k => $sport) { // On enleve s'il n'y a pas les quotas
	if (empty($sports_quota[$k]))
		unset($sports[$k]);
	else if ($sports_quota[$k]['quota_inscription'] != '')
		if ($sports_quota[$k]['quota_inscription'] <= $sports_quota[$k]['nb_sportifs'])
			unset($sports[$k]);
}


if (
	$possibleEdition &&
	!empty($options['id_participant']) &&
	!empty($_POST['sport']) &&
	!empty($_POST['add']) &&
	!empty($sports[$_POST['sport']]) &&
	!empty($options['id_participant'])
) {
	$sid = $_POST['sport'];

	$pdo->exec('INSERT INTO equipes SET ' .
		'_auteur = ' . (int) $_SESSION['user']['id'] . ', ' .
		'_date = NOW(), ' .
		'_message = "Création d\'une équipe par un centralien", ' .
		//-------------//
		'id_ecole_sport = (SELECT id FROM ecoles_sports WHERE id_ecole = ' . (int) $id_ecl . ' AND id_sport = ' . (int) $sid . ' AND _etat="active" LIMIT 1), ' .
		'id_capitaine = ' . (int) $options['id_participant'] . ', ' .
		'label = "' . (empty($_POST['label']) ?
			generer_nom_equipe($sid) :
			secure($_POST['label'])) . '"')
		or die(print_r($pdo->errorInfo()));

	$_POST['equipe'] = $pdo->lastInsertId(); // ⇒ on rejoins tout de suite l'équipe créée

	$equipes[$_POST['equipe']] = $pdo->query('SELECT ' .
		'eq.id, ' .
		'eq.label, ' .
		'eq._message, ' .
		's.sport, ' .
		's.sexe, ' .
		's.individuel, ' .
		's.groupe_multiple, ' .
		's.id AS sid ' .
		'FROM equipes AS eq ' .
		'JOIN ecoles_sports AS es ON ' .
		'es.id = eq.id_ecole_sport AND ' .
		'es._etat = "active" ' .
		'JOIN sports AS s ON ' .
		's.id = es.id_sport AND ' .
		's._etat = "active" ' .
		'WHERE ' .
		'eq._etat = "active" AND ' .
		'eq.id = ' . (int) $_POST['equipe'] . ' ' .
		'LIMIT 1')
		->fetch(PDO::FETCH_ASSOC);

	if (!empty($sports_speciaux[$equipes[$_POST['equipe']]['sid']]))
		$tarifs_equipes[$_POST['equipe']] = $sports_speciaux[$equipes[$_POST['equipe']]['sid']];
	else
		$tarifs_equipes[$_POST['equipe']] = $tarif_sportif;
}

if (
	$possibleEdition &&
	!empty($options['id_participant']) &&
	!empty($_POST['equipe']) &&
	!empty($_POST['add']) &&
	!empty($equipes[$_POST['equipe']]) &&
	strpos($equipes[$_POST['equipe']]["_message"], "locked") === false
) {
	$eids = [$_POST['equipe']];
	foreach ($mes_equipes as $eid => $equipe) {
		array_push($eids, $eid);
	}
	$tarif_selected = $tarif_non_sportif;
	foreach ($eids as $eid) {
		if ($tarifs[$tarifs_equipes[$eid]]['tarif'] > $tarifs[$tarif_selected]['tarif'])
			$tarif_selected = $tarifs_equipes[$eid];
	}


	if (
		empty($options['sportif']) || $tarifs[$tarif_selected]['id_tarif_ecole'] != $options['id_tarif_ecole']
	) {
		$ref = pdoRevision('participants', $options['id_participant']);
		$pdo->exec('UPDATE participants SET ' .
			'_auteur = ' . (int) $_SESSION['user']['id'] . ', ' .
			'_ref = ' . (int) $ref . ', ' .
			'_date = NOW(), ' .
			'_message = "Ajout du caractète sportif du centralien, ou modification du tarif", ' .
			//-------------//
			'sportif = 1, ' .
			'id_tarif_ecole = ' . $tarifs[$tarif_selected]['id_tarif_ecole'] . ' ' .
			'WHERE ' .
			'id = ' . $options['id_participant']);
		$options['sportif'] = $tarif_selected != $tarif_non_sportif;
		if (empty($options['sportif'])) {
			$ref = pdoRevision('centralien', $options['id']);
			$pdo->exec('UPDATE centralien SET ' .
				'_auteur = ' . (int) $_SESSION['user']['id'] . ', ' .
				'_ref = ' . (int) $ref . ', ' .
				'_date = NOW(), ' .
				'_message = "Le centralien deviens sportif : on lui enleve son tshirt", ' .
				//-------------//
				'tshirt = 0, ' .
				'WHERE ' .
				'id = ' . $options['id']);
		}
	}
	$options['id_tarif_ecole'] = $tarifs[$tarif_selected]['id_tarif_ecole'];

	$pdo->exec('INSERT INTO sportifs SET ' .
		'_auteur = ' . (int) $_SESSION['user']['id'] . ', ' .
		'_date = NOW(), ' .
		'_message = "Ajout du centralien dans une équipe", ' .
		'_etat = "active", ' .
		//---------------//
		'id_participant  = ' . $options['id_participant'] . ', ' .
		'id_equipe = ' . (int) $_POST['equipe']) or die(print_r($pdo->errorInfo()));

	$mes_equipes = $pdo->query($mes_equipes_sql)
		->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);

	$groupe = array_keys($mes_equipes)[0];
	$groupe = empty($equipes[$groupe]) ? null : $equipes[$groupe]['groupe_multiple'];
	$mes_sports = array_column($mes_equipes, 'sid');

	if (!empty($groupe)) {
		foreach ($equipes as $k => $equipe) {
			if ($equipe['groupe_multiple'] != $groupe || in_array($equipe['sid'], $mes_sports))
				unset($equipes[$k]);
		}
		foreach ($sports as $k => $sport) {
			if ($sport['groupe_multiple'] != $groupe || in_array($k, $mes_sports))
				unset($sports[$k]);
		}
	} else {
		$equipes = [];
		$sports = [];
	}

	foreach ($mes_equipes as $eid => $equipe) {
		unset($equipes[$eid]);
	}
}


if (
	$possibleEdition &&
	!empty($options['id_participant']) &&
	!empty($_POST['delete']) &&
	!empty($mes_equipes[$_POST['delete']])
) {
	$eq = $_POST['delete'];

	if ($mes_equipes[$eq]['cap'] == $options['id_participant']) {
		delete_equipe($eq);
	} else {
		$equipier = $pdo->query('SELECT  ' .
			'id, ' .
			'id_participant, ' .
			'(SELECT COUNT(*) FROM sportifs as sp where sp.id_participant = sportifs.id_participant AND sp._etat = "active") AS nb ' .
			'FROM sportifs  ' .
			'WHERE  ' .
			'id_equipe = ' . (int) $eq . ' AND ' .
			'id_participant = ' . $options['id_participant'] . ' AND ' .
			'_etat = "active"')
			->fetchAll(PDO::FETCH_ASSOC);

		delete_equipier($equipier[0]);
	}
	unset($mes_equipes[$eq]);

	$tarif_selected = $tarif_non_sportif;
	foreach ($mes_equipes as $eid => $equipe) {
		if ($tarifs[$tarifs_equipes[$eid]]['tarif'] > $tarifs[$tarif_selected]['tarif'])
			$tarif_selected = $tarifs_equipes[$eid];
	}

	$options['id_tarif_ecole'] = $tarifs[$tarif_selected]['id_tarif_ecole'];
	$options['sportif'] = $tarif_selected != $tarif_non_sportif;

	$equipes = $pdo->query($equipes_sql)
		->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);
	$sports = $pdo->query($sports_sql)
		->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);


	if (count($mes_equipes)) {
		$groupe = array_keys($mes_equipes)[0];
		$groupe = empty($equipes[$groupe]) ? null : $equipes[$groupe]['groupe_multiple'];
		$mes_sports = array_column($mes_equipes, 'sid');

		if (!empty($groupe)) {
			foreach ($equipes as $k => $equipe) {
				if ($equipe['groupe_multiple'] != $groupe || in_array($equipe['sid'], $mes_sports))
					unset($equipes[$k]);
			}
			foreach ($sports as $k => $sport) {
				if ($sport['groupe_multiple'] != $groupe || in_array($k, $mes_sports))
					unset($sports[$k]);
			}
		} else {
			$equipes = [];
			$sports = [];
		}

		foreach ($mes_equipes as $eid => $equipe) {
			unset($equipes[$eid]);
		}
	}


	// On renettoie selon le sexe et les quota

	foreach ($equipes as $k => $equipe) {
		if (
			$equipe['sexe'] != 'm' &&
			!empty($options['sexe']) &&
			$equipe['sexe'] != $options['sexe']
		)
			unset($equipes[$k]);
	}

	foreach ($equipes as $k => $equipe) {
		if ($sports_quota[$equipe['sid']]['quota_inscription'] != '')
			if ($sports_quota[$equipe['sid']]['quota_inscription'] <= $sports_quota[$equipe['sid']]['nb_sportifs'])
				unset($equipes[$k]);
	}

	foreach ($sports as $k => $sport) {
		if ($sports_quota[$k]['quota_inscription'] != '')
			if ($sports_quota[$k]['quota_inscription'] <= $sports_quota[$k]['nb_sportifs'])
				unset($sports[$k]);
	}
}

/*var_dump($options);
==> array(14) { ["id"]=> int(3) ["id_participant"]=> int(182) ["uid"]=> string(0) "" ["vptournoi"]=> int(0) ["soiree"]=> int(1) ["pfvendredi"]=> int(0) ["pfsamedi"]=> int(1) ["tshirt"]=> int(1) ["gourde"]=> int(0) ["tombola"]=> int(10) ["sportif"]=> int(0) ["licence"]=> string(8) "azeaeaze" ["sexe"]=> string(1) "h" ["tarif"]=> int(1427) }
*/
require DIR . 'templates/centralien/prestations.php';
