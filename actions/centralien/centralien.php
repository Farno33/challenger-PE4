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

/*
 * Ce module à besoin de tarifs associés à l'école, notemment au moins un non sportif
 * Sans ça, c'est les bugs assurés
 */


if (
	empty($_SESSION['user']) ||
	empty($user) ||
	empty($user['cas'])
)
	die(header('location:' . url('accueil', false, false)));


$data_ecl = $pdo->query('SELECT ' .
	'id, ' .
	'as_code ' .
	'FROM ecoles AS e ' .
	'WHERE ' .
	'e.nom LIKE "%centrale%" AND ' .
	'e.nom LIKE "%lyon%" AND ' .
	'e._etat = "active" ' .
	'LIMIT 1')
	->fetch(PDO::FETCH_ASSOC);
$id_ecl = empty($data_ecl['id']) ? 'NULL' : $data_ecl['id'];


if (empty($id_ecl))
	die(header('location:' . url('profil', false, false)));

$equipes = $pdo->query($equipes_sql = 'SELECT ' .
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
	'es.id_ecole = ' . $id_ecl)
	->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);

$tarifs = $pdo->query('SELECT ' .
	't.id, ' .
	't.tarif, ' .
	't.sportif, ' .
	't.description, ' .
	't.id_sport_special, ' .
	'te.id AS id_tarif_ecole '.
	'FROM tarifs AS t ' .
	'JOIN tarifs_ecoles AS te ON ' .
	'te.id_ecole = ' . $id_ecl . ' AND ' .
	'te.id_tarif = t.id AND ' .
	'te._etat = "active" ' .
	'WHERE ' .
	't._etat = "active"')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$tarifs = $tarifs->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);
/**
 * @var array[] $tarifs Tarifs[id (int), tarif (int), sportif (bool), description (string), id_sport_special (int), id_tarif_ecole (int)]
 */

$tarifs_ecoles = $pdo->query('SELECT ' .
	'te.id, ' .
	'te.id_tarif ' .
	'FROM tarifs_ecoles AS te ' .
	'WHERE ' .
	'te.id_ecole = ' . $id_ecl . ' AND ' .
	'te._etat = "active"')
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$tarifs_ecoles = $tarifs_ecoles->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);
/** @var array[] $tarifs_ecoles $tarifs_ecoles[id_tarif_ecole]['id_tarif'] => (int) tarifs.id */

$tarifs_equipes = [];
$sports_speciaux = [];
$tarif_sportif = null;
$tarif_non_sportif = null;
foreach ($tarifs as $tid => $tarif) {
	if (!empty($tarif['id_sport_special']))
		$sports_speciaux[$tarif['id_sport_special']] = $tid;
	else if ($tarif['sportif'])
		$tarif_sportif = $tid;
	else
		$tarif_non_sportif = $tid;
}
foreach ($equipes as $eid => $equipe) {
	if (!empty($sports_speciaux[$equipe['sid']]))
		$tarifs_equipes[$eid] = $sports_speciaux[$equipe['sid']];
	else
		$tarifs_equipes[$eid] = $tarif_sportif;
}

$sports_quota = $pdo->query("select sports.id, sports.sport, sports.quota_inscription,count(sportifs.id) as nb_sportifs
from sportifs
join equipes on sportifs.id_equipe = equipes.id
join ecoles_sports as esport on equipes.id_ecole_sport = esport.id
join sports on sports.id = esport.id_sport
where sportifs.`_etat`='active'
group by esport.id_sport")
    ->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);

$options = $pdo->query('SELECT ' .
	'c.id, ' .
	'p.id AS id_participant, ' .
	'p.uid, ' .
	'c.vptournoi, ' .
	'c.soiree, ' .
	'c.pfvendredi, ' .
	'c.pfsamedi, ' .
	//'c.vegetarien, '.
	'c.tshirt, ' .
	'c.gourde, ' .
	'c.tombola, ' .
	'p.sportif, ' .
	'p.licence, ' .
	'p.sexe, ' .
	'p._message, ' .
	'p.telephone, ' .
	'p.id_tarif_ecole ' .
	'FROM centraliens AS c ' .
	'LEFT JOIN participants AS p ON ' .
	'p.id = c.id_participant AND ' .
	'p._etat = "active" ' .
	'WHERE ' .
	'c.id_utilisateur = ' . (int) $_SESSION['user']['id'] . ' AND ' .
	'c._etat = "active"');

if ($options) {
	$options = $options->fetch(PDO::FETCH_ASSOC);

	if ($options == false)
		$options = []; // sinon sur le premier login ⇒ Deprecated: Automatic conversion of false to array is deprecated in /var/www/html/actions/centralien/centralien.php on line 155

}
/**
 * @var array|mixed $options Options[
 * 	- id (int) *id centralien*
 *  - id_participant (int)
 *  - uid (int)	*utilité ?*
 *  - vptournoi (null|int) *id du sport dont il est vp*
 *  - soiree (bool)
 *  - pfvendredi (bool)
 *  - pfsamedi (bool)
 *  - tshirt(bool)
 *  - gourde (bool)
 *  - tombola (int)
 *  - sportif (bool) *(depuis la table participants)*
 *  - licence (string) *(depuis la table participants)*
 *  - sexe (char) *(depuis la table participants)*
 * 	- _message (string) *(depuis la table participants)*
 *  - id_tarif_ecole (int) *id du tarif*
 * ]
 */
if (empty($options['sexe']) || str_contains($options['_message'], 'sexe incertain')) {
	$options['sexe'] = "";
}

if (empty($options['licence'])) {
	if (!empty($data_ecl['as_code'])) {
		$licence = $pdo->query('SELECT ' .
			'licence ' .
			'FROM licences ' .
			'WHERE ' .
			'LOCATE(as_code, "' . secure($data_ecl['as_code']) . '") > 0 AND ' .
			'nom LIKE "' . secure($user['nom']) . '" AND ' .
			'prenom LIKE "' . secure($user['prenom']) . '" ' .
			'LIMIT 1')
			->fetch(PDO::FETCH_ASSOC);
		$options['licence'] = empty($licence) ? '' : $licence['licence'];
	} else $options['licence'] = '';
}
//Création du participant
if (empty($options['id_participant'])) {
	//Attention ce participant n'a aucun statut
	$options['nom'] = ucname(secure($user['nom']));
	$options['prenom'] = ucname(secure($user['prenom']));
	$options['telephone'] = getPhone(secure($user['telephone']));
	$options['email'] = strtolower(secure($user['email']));
	$options['sportif'] = 0;
	$options['licence'] = secure(formatLicence($options['licence']));
	$options['id_tarif_ecole'] = (empty($tarif_non_sportif) ? 0 : (int) $tarifs[$tarif_non_sportif]['id_tarif_ecole']);

	$pdo->exec('INSERT INTO participants SET ' .
		'_auteur = ' . (int) $_SESSION['user']['id'] . ', ' .
		'_date = NOW(), ' .
		'_message = "Ajout automatique d\'un participant centralien.'.(empty($options['sexe']) ? ' Attention sexe incertain' : '').'", ' .
		'_etat = "active", ' .
		//-------------//
		'nom = "' . $options['nom'] . '", ' .
		'prenom = "' . $options['prenom'] . '", ' .
		'sexe = "' . (empty($options['sexe']) ? 'h' : $options['sexe']) . '", ' .
		'telephone = "' . $options['telephone'] . '", ' .
		'email = "' . $options['email'] . '", ' .
		'licence = "' . $options['licence'] . '", ' .
		'sportif = 0, ' .
		'pompom = 0, ' .
		'fanfaron = 0, ' .
		'cameraman = 0, ' .
		'id_tarif_ecole = ' . $options['id_tarif_ecole'] . ', ' .
		'recharge = 0, ' .
		'id_ecole = ' . (int) $id_ecl . ', ' .
		'logeur = "-", ' .
		'date_inscription = NOW()');

	$options['id_participant'] = $pdo->lastInsertId();

	if (
		empty($options['id']) &&
		!empty($options['id_participant'])
	) {
		$pdo->exec('INSERT INTO centraliens SET ' .
			'_auteur = ' . (int) $_SESSION['user']['id'] . ', ' .
			'_date = NOW(), ' .
			'_message = "Ajout automatique des options de centralien", ' .
			'_etat = "active", ' .
			//---------------//
			'id_participant = ' . (int) $options['id_participant'] . ', ' .
			'id_utilisateur  = ' . (int) $_SESSION['user']['id'] . ', ' .
			'soiree = 0, ' .
			'pfvendredi = 0, ' .
			'pfsamedi = 0, ' .
			//'vegetarien = 0, '.
			'tshirt = 0, '.
			'gourde = 0') or die(print_r($pdo->errorInfo()));
	} else if (!empty($options['id_participant'])) {
		$ref = pdoRevision('centraliens', $options['id']);
		$pdo->exec('UPDATE centraliens SET ' .
			'_auteur = ' . (int) $_SESSION['user']['id'] . ', ' .
			'_ref = ' . (int) $ref . ', ' .
			'_date = NOW(), ' .
			'_message = "Lien automatique avec le participant", ' .
			//-------------//
			'id_participant = ' . (int) $options['id_participant'] . ' ' .
			'WHERE ' .
			'id = ' . $options['id']);
	}
}

$sports = $pdo->query($sports_sql = 'SELECT ' .
    'id, ' .
    'sport, ' .
    'sexe, ' .
    'individuel, ' .
    'groupe_multiple ' .
    'FROM sports ' .
    'WHERE ' .
    '_etat = "active" AND (' .
    'sexe = "' . secure(empty($options['sexe']) ? 'm' : $options['sexe']) . '" OR ' .
    'sexe = "m")')
    ->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);

if (empty($options['id_participant'])) unset($moduleCentralien['Equipes']);
else {
    $mes_equipes = $pdo->query($mes_equipes_sql = 'SELECT ' .
        'sp.id_equipe, ' .
        'sp.id AS spid, ' .
        's.id AS sid, ' .
        's.sport, ' .
        's.sexe, ' .
        'eq.label, ' .
        'eq.id_capitaine AS cap, ' .
		'eq._message, ' .
        's.individuel ' .
        'FROM sportifs AS sp ' .
        'JOIN equipes AS eq ON ' .
        'eq.id = sp.id_equipe AND ' .
        'eq._etat = "active" ' .
        'JOIN ecoles_sports AS es ON ' .
        'es.id = eq.id_ecole_sport AND ' .
        'es._etat = "active" ' .
        'JOIN sports AS s ON ' .
        's.id = es.id_sport AND ' .
        's._etat = "active" ' .
        'WHERE ' .
        'sp._etat = "active" AND ' .
        'sp.id_participant = ' . $options['id_participant'])
        ->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);
}


 /**
 * Dissout une équipe en ajustant les tarifs pour chacuns des *anciens* coéquipiers
 *
 * ⚠️ Attention ne vérifie pas les authorisations
 * @see delete_equipier()
 * @param int $eq ID de l'équipe à dissoudre
 */
function delete_equipe(int $eq): void
{
	global $pdo;

    $ref = pdoRevision('equipes', (int)$eq);
    $pdo->exec('UPDATE equipes SET ' .
        '_auteur = ' . (int)$_SESSION['user']['id'] . ', ' .
        '_ref = ' . (int)$ref . ', ' .
        '_date = NOW(), ' .
        '_message = "Le capitaine a dissout l\'équipe", ' .
        //-------------//
        '_etat = "desactive" ' .
        'WHERE ' .
        'id = ' . (int)$eq);
    
    $equipiers = $pdo->query('SELECT  ' .
        'id, ' .
        'id_participant, ' .
        '(SELECT COUNT(*) FROM sportifs as sp where sp.id_participant = sportifs.id_participant AND sp._etat = "active") AS nb ' .
        'FROM sportifs  ' .
        'WHERE  ' .
        'id_equipe = ' . (int)$eq . ' AND ' .
        '_etat = "active"')
        ->fetchAll(PDO::FETCH_ASSOC);
		
    foreach ($equipiers as $equipier) {
        delete_equipier($equipier);
    }
}

/** Supprime une personne d'une équipe et ajuste son tarif
 *
 * ⚠️ Attention ne vérifie pas les authorisations
 * @see delete_equipe()
 * @param array $equipier [id *(sportif)*, id_participant, nb *(d'équipe avant suppression)*]
 */
function delete_equipier(array $equipier): void
{
	global $pdo;
	global $tarifs;
    global $tarif_non_sportif;
    global $tarifs_equipes;

    $ref = pdoRevision('sportifs', $equipier['id']);
    $pdo->exec('UPDATE sportifs SET ' .
        '_auteur = ' . (int)$_SESSION['user']['id'] . ', ' .
        '_ref = ' . (int)$ref . ', ' .
        '_date = NOW(), ' .
        '_message = "Ce sportif a été supprimé de son équipe", ' .
        //-------------//
        '_etat = "desactive" ' .
        'WHERE ' .
        'id = ' . $equipier['id']);

    if ($equipier['nb'] == 1) { // C'était sa dernière équipe
        $ref = pdoRevision('participants', $equipier['id_participant']);
        $pdo->exec('UPDATE participants SET ' .
            '_auteur = ' . (int)$_SESSION['user']['id'] . ', ' .
            '_ref = ' . (int)$ref . ', ' .
            '_date = NOW(), ' .
            '_message = "Ce participant a été supprimé de sa dernière équipe : retour au tarif non-sportif", ' .
            //-------------//
            'sportif = 0, ' .
            'id_tarif_ecole = ' . $tarifs[$tarif_non_sportif]['id_tarif_ecole'] . ' ' .
            'WHERE ' .
            'id = ' . $equipier['id_participant']);

		$ref = pdoRevision('centraliens', null, '_etat = "active" AND id_participant = ' . $equipier['id_participant']);
		$pdo->exec('UPDATE centraliens SET ' .
			'_auteur = ' . (int)$_SESSION['user']['id'] . ', ' .
			'_ref = ' . (int)$ref . ', ' .
			'_date = NOW(), ' .
			'_message = "Ce centralien a été supprimé de sa dernière équipe : il n\'as plus le droit au packfoods", ' .
			//-------------//
			'pfsamedi = 0 ' .
			'WHERE ' .
			'_etat = "active" AND ' .
			'id_participant = ' . $equipier['id_participant']);
    } else {
        $equipes_equipier = $pdo->query('SELECT ' .
            'id_equipe ' .
            'FROM sportifs ' .
            'WHERE _etat = "active" AND id_participant = ' . $equipier['id_participant'])
			->fetchAll(PDO::FETCH_ASSOC);
        $tarif_selected = $tarif_non_sportif;
        foreach ($equipes_equipier as $equipe_equipier) {
            if ($tarifs[$tarifs_equipes[$equipe_equipier['id_equipe']]]['tarif'] > $tarifs[$tarif_selected]['tarif'])
                $tarif_selected = $tarifs_equipes[$equipe_equipier['id_equipe']];
        }

        $ref = pdoRevision('participants', $equipier['id_participant']);
        $pdo->exec('UPDATE participants SET ' .
            '_auteur = ' . (int)$_SESSION['user']['id'] . ', ' .
            '_ref = ' . (int)$ref . ', ' .
            '_date = NOW(), ' .
            '_message = "Ce participant a été supprimé d\'une de ses équipes : revision du tarif", ' .
            //-------------//
            'id_tarif_ecole = ' . $tarifs[$tarif_selected]['id_tarif_ecole'] . ' ' .
            'WHERE ' .
            'id = ' . $equipier['id_participant']);
    }
}

if(empty($options['vptournoi'])) unset($modulesCentralien['vptournoi']);

//Le module sélectionné existe-t-il
$moduleCentralien = $args[1][0];
if (
	!empty($moduleCentralien) &&
	!in_array($moduleCentralien, array_keys($modulesCentralien))
)
	die(require DIR . 'templates/_error.php');


if (!empty($moduleCentralien))
	die(require DIR . 'actions/centralien/module_' . $moduleCentralien . '.php');
	

require DIR . 'templates/centralien/accueil.php';