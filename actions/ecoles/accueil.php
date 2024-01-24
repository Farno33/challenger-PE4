<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/ecoles/accueil.php ******************************/
/* Accueil de l'administration *****************************/
/* *********************************************************/
/* Dernière modification : le 27/11/14 *********************/
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


$quotas_reserves = $pdo->query('SELECT '.
		'es.id_sport, '.
		'es.quota_reserves, '.
		'(SELECT COUNT(p.id) FROM participants AS p JOIN sportifs AS sp ON sp.id_participant = p.id AND sp._etat = "active" JOIN equipes AS eq ON eq._etat = "active" AND eq.id = sp.id_equipe WHERE p.id_ecole = es.id_ecole AND p._etat = "active" AND eq.id_ecole_sport = es.id) AS sportifs '.
	'FROM ecoles_sports AS es WHERE '.
		'es.id_ecole = '.$ecole['id'].' AND '.
		'es.quota_reserves > 0 AND '.
		'es._etat = "active"')
	->fetchAll(PDO::FETCH_ASSOC);


foreach ($quotas as $quota => $valeur)
	$quotas[$quota] = $valeur['valeur'];


if (isset($quotas['total'])) {
	$places_reservees = 0;

	foreach ($quotas_reserves as $quota_reserves) {
		if (intval($quota_reserves['quota_reserves']) &&
			$quota_reserves['sportifs'] < $quota_reserves['quota_reserves']) {
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



if ($ecole['etat_inscription'] == 'validee' && (
		empty($_SESSION['user']['privileges']) ||
		!in_array('ecoles', $_SESSION['user']['privileges'])))
	die(header('location:'.url('ecoles/'.$id.'/recapitulatif', false, false)));


if ((!empty($_POST['save']) || !empty($_POST['continue'])) &&
	isset($_POST['adresse']) &&
	isset($_POST['code_postal']) && 
	isset($_POST['ville']) &&
	isset($_POST['email_ecole']) &&
	isset($_POST['telephone_ecole']) &&
	!empty($_POST['nom_respo']) &&
	!empty($_POST['prenom_respo']) &&
	!empty($_POST['email_respo']) &&
	!empty($_POST['telephone_respo']) &&
	!empty($_POST['nom_corespo']) &&
	!empty($_POST['prenom_corespo']) &&
	!empty($_POST['email_corespo']) &&
	!empty($_POST['telephone_corespo'])) {

	$ref = pdoRevision('ecoles', $ecole['id']);
	$pdo->exec('UPDATE ecoles SET '.
			'_auteur = '.(int) $_SESSION['user']['id'].', '.
			'_ref = '.(int) $ref.', '.
			'_date = NOW(), '.
			'_message = "Modification des données sur les responsables", '.
			//---------------------//
			'adresse = "'.secure($_POST['adresse']).'", '.
			'code_postal = "'.secure($_POST['code_postal']).'", '.
			'ville = "'.secure($_POST['ville']).'", '.
			'email_ecole = "'.secure($_POST['email_ecole']).'", '.
			'telephone_ecole = "'.secure($_POST['telephone_ecole']).'", '.
			'nom_respo = "'.secure($_POST['nom_respo']).'", '.
			'prenom_respo = "'.secure($_POST['prenom_respo']).'", '.
			'email_respo = "'.secure($_POST['email_respo']).'", '.
			'telephone_respo = "'.secure($_POST['telephone_respo']).'", '.
			'nom_corespo = "'.secure($_POST['nom_corespo']).'", '.
			'prenom_corespo = "'.secure($_POST['prenom_corespo']).'", '.
			'email_corespo = "'.secure($_POST['email_corespo']).'", '.
			'telephone_corespo = "'.secure($_POST['telephone_corespo']).'" '.
		'WHERE '.
			'id = '.$ecole['id']);
	

	$_POST['id'] = $ecole['id'];
	foreach ($ecole as $label => $value)
		if (!isset($_POST[$label]))
			$_POST[$label] = $value;

	$ecole = $_POST;
	$erreur_maj = false;

	if (!empty($_POST['continue']))
		header('location:'.url('ecoles/'.$ecole['id'].'/participants', false, false));


} else if (!empty($_POST['save']) || !empty($_POST['continue']))
	$erreur_maj = 'champs';


//Inclusion du bon fichier de template
require DIR.'templates/ecoles/accueil.php';