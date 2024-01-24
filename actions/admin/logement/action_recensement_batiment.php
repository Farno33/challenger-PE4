<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/admin/logement/action_recensement_batiment.php **/
/* Liste des chambres dans le batiment concerné ************/
/* *********************************************************/
/* Dernière modification : le 19/01/15 *********************/
/* *********************************************************/



if (isset($_GET['maj']) &&
	!empty($_POST['chambre']) &&
	is_string($_POST['chambre']) &&
	strlen($_POST['chambre']) == 4 &&
	isset($_POST['nom']) &&
	isset($_POST['prenom']) &&
	isset($_POST['surnom']) &&
	isset($_POST['email']) &&
	isset($_POST['lit']) &&
	isset($_POST['telephone']) &&
	isset($_POST['etat']) &&
	isset($_POST['format']) &&
	isset($_POST['commentaire']) &&
	isset($_POST['places']) && (
		$_POST['places'] == '0' ||
		intval($_POST['places'])) &&
	in_array($_POST['etat'], array_keys($labelsEtatChambre)) &&
	in_array($_POST['format'], array_keys($labelsFormatChambre))) {

	$bracelet = !empty($_POST['bracelet']);


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

	if (!empty($existe) && 
		$existe['cp'] && (
			$existe['etat'] != $_POST['etat'] ||
			$existe['format'] != $_POST['format'] ||
			$existe['places'] != $_POST['places']))
		die(json_encode(array(
			'error' => 1,
			'etat' => empty($existe['etat']) ? 'noncontacte' : $existe['etat'],
			'format' => empty($existe['format']) ? 'nonrenseigne' : $existe['format'])));


	if (!empty($existe)) {
		$ref = pdoRevision('chambres', $existe['id']);
		$pdo->exec('UPDATE chambres SET '.
			'_auteur = '.(int) $_SESSION['user']['id'].', '.
			'_ref = '.(int) $ref.', '.
			'_date = NOW(), '.
			'_message = "Modification des données de la chambre", '.
				'nom = "'.secure($_POST['nom']).'", '.
				'prenom = "'.secure($_POST['prenom']).'", '.
				'surnom = "'.secure($_POST['surnom']).'", '.
				'telephone = "'.secure(getPhone($_POST['telephone'])).'", '.
				'email  = "'.secure($_POST['email']).'", '.
				'places = '.(int) $_POST['places'].', '.
				'etat = "'.secure($_POST['etat']).'", '.
				'format = "'.secure($_POST['format']).'", '.
				'lit_camp = '.abs((int) $_POST['lit']).', '.
				'bracelet = '.($bracelet ? '1' : '0').', '.
				'commentaire = "'.secure($_POST['commentaire']).'" '.
			'WHERE '.
				'id = '.$existe['id']) or die(print_r($pdo->errorInfo())); 
	}

	else
		$pdo->exec('INSERT INTO chambres SET '.
			'_auteur = '.(int) $_SESSION['user']['id'].', '.
			'_date = NOW(), '.
			'_message = "Ajout de la chambre", '.
			'_etat = "active", '.
			//------------------//
			'numero = "'.secure($_POST['chambre']).'", '.
			'nom = "'.secure(ucname($_POST['nom'])).'", '.
			'prenom = "'.secure(ucname($_POST['prenom'])).'", '.
			'surnom = "'.secure($_POST['surnom']).'", '.
			'telephone = "'.secure(getPhone($_POST['telephone'])).'", '.
			'email = "'.secure($_POST['email']).'", '.
			'places = '.(int) $_POST['places'].', '.
			'etat = "'.secure($_POST['etat']).'", '.
			'format = "'.secure($_POST['format']).'", '.
			'lit_camp = '.abs((int) $_POST['lit']).', '.
			'bracelet = '.($bracelet ? '1' : '0').', '.
			'commentaire = "'.secure($_POST['commentaire']).'"');

	die(json_encode(array(
		'error' => 0, 
		'nom' => ucname($_POST['nom']),
		'prenom' => ucname($_POST['prenom']), 
		'telephone' => getPhone($_POST['telephone']))));
}

$batiment = $args[2][0];
$proprios_ = $pdo->query('SELECT '.
		'c.id AS _id, '.
		'c.id, '.
		'c.places, '.
		'c.numero, '.
		'c.bracelet, '.
		'c.lit_camp, '.
		'c.nom, '.
		'c.prenom, '.
		'c.surnom, '.
		'c.email, '.
		'c.telephone, '.
		'c.commentaire, '.
		'c.etat, '.
		'c.format '.
	'FROM chambres AS c '.
	'WHERE '.
		'SUBSTR(c.numero, 1, 1) = "'.$batiment.'" AND '.
		'c._etat = "active"')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$proprios_ = $proprios_->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);


$proprios = array();
foreach ($proprios_ as $proprio)
	$proprios[$proprio['numero']] = $proprio;



//Historique de l'école
if (!empty($_POST['listing']) &&
	in_array($_POST['listing'], array_keys($proprios_))) {

	$history = "SELECT T2.*, T1.lvl, T3.nom AS _auteur_nom, T3.prenom AS _auteur_prenom FROM ".
		"(SELECT ".
	        "@r AS _id, ".
	        "(SELECT @r := _ref FROM chambres WHERE id = _id) AS parent, ".
	        "@l := @l + 1 AS lvl ".
	    "FROM ".
	        "(SELECT @r := ".$_POST['listing'].", @l := 0) vars, ".
	        "chambres m ".
	    "WHERE @r <> 0) T1 ".
		"JOIN chambres T2 ON T1._id = T2.id ".
		"JOIN utilisateurs T3 ON T3.id = T2._auteur ".
		"ORDER BY T1.lvl ASC";
	$history = $pdo->query($history)->fetchAll(PDO::FETCH_ASSOC);
	$titre = 'Historique de "'.stripslashes($proprios_[$_POST['listing']]['numero']).'"';
	die(require DIR.'templates/admin/historique.php');
}


//Téléchargement du fichier XLSX concerné
if (isset($_GET['excel'])) {
	$proprietaires = [];

	$etages = in_array($batiment, array('A', 'B', 'C')) ? 4 : 6;	
	foreach (range(0, $etages) as $etage) {
		$chambres = $etage == 0 ? 
			($etages == 6 ? 8 : 20) : 
			($etages == 6 ? 16 : 17);

		foreach (range(1, $chambres) as $numero) {
			$chambre = sprintf('%s%d%02d', $batiment, $etage, $numero);
			$color = colorChambre($chambre);
			$proprio = isset($proprios[$chambre]) 
				? $proprios[$chambre] 
				: [
					'nom' => '',
					'prenom' => '',
					'surnom' => '', 
					'telephone' => '', 
					'email' => '', 
					'places' => 0, 
					'etat' => 'noncontacte', 
					'format' => 'nonrenseigne', 
					'lit_camp' => 0, 
					'bracelet' => '', 
					'commentaire' => ''];
			$proprio['numero'] = $chambre;
			$proprio['bracelet'] = !empty($proprio['bracelet']) ? 'Oui' : 'Non';
			$proprietaires[] = $proprio;
		}
	}

	$titre = 'Liste Recensement : Batiment '.$batiment;
	$fichier = 'liste_recensement_'.$batiment;
	$items = $proprietaires;
	$labels = [
		'Chambre' => 'numero',
		'Nom' => 'nom',
		'Prénom' => 'prenom',
		'Surnom' => 'surnom',
		'Téléphone' => 'telephone',
		'Email' => 'email',
		'Places' => 'places',
		'Etat' => 'etat',
		'Format' => 'format',
		'Lit de camp' => 'lit_camp',
		'Bracelet' => 'bracelet',
		'Commentaire' => 'commentaire',
	];
	exportXLSX($items, $fichier, $titre, $labels);

}

//Inclusion du bon fichier de template
require DIR.'templates/admin/logement/recensement_batiment.php';
