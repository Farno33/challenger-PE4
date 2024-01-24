<?php


$id = $args[1][0];
if (!(!empty($_SESSION['user']) && (
        !empty($_SESSION['user']['privileges']) &&
        in_array('ecoles', $_SESSION['user']['privileges']) ||
        !empty($_SESSION['user']['ecoles']) &&
        in_array($id, $_SESSION['user']['ecoles']))))
    die(header('location:'.url('accueil', false, false)));


//Permet de laisser la session ouverte tant que l'on utilise le tableur
if (!empty($_POST['load']) && $_POST['load'] == 'null')
    die;


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


$ecole = $pdo->query('SELECT '.
        'e.*, '.
        '(SELECT COUNT(p1.id) FROM participants AS p1 WHERE p1.id_ecole = e.id AND (p1._etat = "active" OR p1._etat = "temp" AND p1._auteur = '.(int) $_SESSION['user']['id'].')) AS nb_inscriptions, '.
        '(SELECT COUNT(p2.id) FROM participants AS p2 WHERE p2.id_ecole = e.id AND (p2._etat = "active" OR p2._etat = "temp" AND p2._auteur = '.(int) $_SESSION['user']['id'].') AND p2.sportif = 1) AS nb_sportif, '.
        '(SELECT COUNT(p3.id) FROM participants AS p3 WHERE p3.id_ecole = e.id AND (p3._etat = "active" OR p3._etat = "temp" AND p3._auteur = '.(int) $_SESSION['user']['id'].') AND p3.pompom = 1) AS nb_pompom, '.
        '(SELECT COUNT(p4.id) FROM participants AS p4 WHERE p4.id_ecole = e.id AND (p4._etat = "active" OR p4._etat = "temp" AND p4._auteur = '.(int) $_SESSION['user']['id'].') AND p4.fanfaron = 1) AS nb_fanfaron, '.
        '(SELECT COUNT(p5.id) FROM participants AS p5 WHERE p5.id_ecole = e.id AND (p5._etat = "active" OR p5._etat = "temp" AND p5._auteur = '.(int) $_SESSION['user']['id'].') AND p5.cameraman = 1) AS nb_cameraman, '.
        '(SELECT COUNT(p6.id) FROM participants AS p6 WHERE p6.id_ecole = e.id AND (p6._etat = "active" OR p6._etat = "temp" AND p6._auteur = '.(int) $_SESSION['user']['id'].') AND p6.pompom = 1 AND p6.sportif = 0) AS nb_pompom_nonsportif, '.
        '(SELECT COUNT(p7.id) FROM participants AS p7 WHERE p7.id_ecole = e.id AND (p7._etat = "active" OR p7._etat = "temp" AND p7._auteur = '.(int) $_SESSION['user']['id'].') AND p7.fanfaron = 1 AND p7.sportif = 0) AS nb_fanfaron_nonsportif, '.
        '(SELECT COUNT(p10.id) FROM participants AS p10 WHERE p10.id_ecole = e.id AND (p10._etat = "active" OR p10._etat = "temp" AND p10._auteur = '.(int) $_SESSION['user']['id'].') AND p10.cameraman = 1 AND p10.sportif = 0) AS nb_cameraman_nonsportif, '.
        '(SELECT COUNT(p8.id) FROM participants AS p8 JOIN tarifs_ecoles AS te8 ON te8.id = p8.id_tarif_ecole AND te8._etat = "active" JOIN tarifs AS t8 ON t8.id = te8.id_tarif AND t8.logement = 1 AND t8._etat = "active" WHERE p8.id_ecole = e.id AND p8.sexe = "f" AND (p8._etat = "active" OR p8._etat = "temp" AND p8._auteur = '.(int) $_SESSION['user']['id'].')) AS nb_filles_logees, '.
        '(SELECT COUNT(p9.id) FROM participants AS p9 JOIN tarifs_ecoles AS te9 ON te9.id = p9.id_tarif_ecole AND te9._etat = "active" JOIN tarifs AS t9 ON t9.id = te9.id_tarif AND t9.logement = 1 AND t9._etat = "active" WHERE p9.id_ecole = e.id AND p9.sexe = "h" AND (p9._etat = "active" OR p9._etat = "temp" AND p9._auteur = '.(int) $_SESSION['user']['id'].')) AS nb_garcons_loges '.
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
        'es.quota_reserves, '.
        '(SELECT COUNT(p.id) FROM participants AS p JOIN sportifs AS sp ON sp.id_participant = p.id AND sp._etat = "active" JOIN equipes AS eq ON eq._etat = "active" AND eq.id = sp.id_equipe WHERE p.id_ecole = es.id_ecole AND p._etat = "active" AND eq.id_ecole_sport = es.id) AS sportifs '.
    'FROM ecoles_sports AS es WHERE '.
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

//La vérification pour le droit d'édition en cas d'accès limité est plus bas

if (empty($_POST['load']) ||
    !in_array($_POST['load'], ['sports', 'tarifs', 'licences', 'send', 'confirm']))
    die(require DIR.'templates/_error.php');


if ($_POST['load'] == 'confirm') {
    $pdo->exec('UPDATE participants SET _etat = "active" WHERE _etat = "temp" AND _auteur = '.(int) $_SESSION['user']['id']);
    $pdo->exec('UPDATE equipes SET _etat = "active" WHERE _etat = "temp" AND _auteur = '.(int) $_SESSION['user']['id']);
    $pdo->exec('UPDATE sportifs SET _etat = "active" WHERE _etat = "temp" AND _auteur = '.(int) $_SESSION['user']['id']);
    die(json_encode(''));
}


else if ($_POST['load'] == 'tarifs') {
    $tarifs = $pdo->query('SELECT '.
            'te.id AS id_tarif_ecole, '.
            't.nom, '.
            't.sportif, '.
            't.tarif, '.
            'CASE WHEN t.id_ecole_for_special = '.(int) $id.' OR t.id_ecole_for_special IS NULL THEN s.id ELSE NULL END AS id_sport_special, '.
            's.sport, '.
            's.sexe, '.
            't.for_pompom, '.
            't.for_cameraman, '.
            't.for_fanfaron, '.
            't.logement '.
        'FROM tarifs AS t '.
        'JOIN tarifs_ecoles AS te ON '.
            'te.id_tarif = t.id AND '.
            'te._etat = "active" '.
        'LEFT JOIN sports AS s ON '.
            's.id = t.id_sport_special AND '.
            's._etat = "active" AND '.
            '(t.id_ecole_for_special = '.(int) $id.' OR t.id_ecole_for_special IS NULL) '.
        'WHERE '.
            't.ecole_lyonnaise = '.($ecole['ecole_lyonnaise'] ? '1' : '0').' AND '.
            'te.id_ecole = '.(int) $id.' AND '.
            't._etat = "active"')
        ->fetchAll(PDO::FETCH_ASSOC);

    die(json_encode($tarifs));
}


else if ($_POST['load'] == 'sports') {
    $sports = $pdo->query('SELECT '.
        's.id, '.
        'es.id AS id_ecole_sport, '.
        's.sport, '.
        's.sexe, '.
        'es.quota_max, '.
        'es.quota_equipes, '.
        's.quota_inscription, '.
        '(SELECT COUNT(pe.id) '.
            'FROM participants AS pe '.
            'JOIN sportifs AS spe ON '.
                'spe.id_participant = pe.id AND '.
                'spe._etat = "active" '.
            'JOIN equipes AS eqe ON '.
                'spe.id_equipe = eqe.id AND '.
                'eqe._etat = "active" '.
            'JOIN ecoles_sports AS ese ON '.
                'eqe.id_ecole_sport = ese.id AND '.
                'ese._etat = "active" '.
            'WHERE '.
                'ese.id_sport = s.id AND '.
                'ese.id_ecole = '.(int) $id.' AND '.
                'pe._etat = "active") AS nb_sportifs, '.
        '(SELECT COUNT(p.id) '.
            'FROM participants AS p '.
            'JOIN ecoles AS ec ON '.
                'ec.id = p.id_ecole AND '.
                'ec._etat = "active" '.
            'JOIN sportifs AS sp ON '.
                'sp.id_participant = p.id AND '.
                'sp._etat = "active" '.
            'JOIN equipes AS eq ON '.
                'sp.id_equipe = eq.id AND '.
                'eq._etat = "active" '.
            'JOIN ecoles_sports AS esp ON '.
                'eq.id_ecole_sport = esp.id AND '.
                'esp._etat = "active" '.
            'WHERE '.
                'esp.id_sport = s.id AND '.
                'p._etat = "active") AS nb_inscriptions, '.
        '(SELECT COUNT(eqt.id) '.
            'FROM equipes AS eqt '.
            'JOIN ecoles_sports AS est ON '.
                'est.id = eqt.id_ecole_sport AND '.
                'est.id_ecole = '.(int) $id.' AND '.
                'est._etat = "active" '.
            'WHERE '.
                'est.id_sport = s.id AND '.
                'eqt._etat = "active") AS nb_equipes, '.
        'CASE WHEN (SELECT COUNT(t.id) '.
            'FROM tarifs AS t '.
            'WHERE '.
                't.id_sport_special = s.id AND '.
                't._etat = "active" AND '.
                '(t.id_ecole_for_special = '.(int) $id.' OR t.id_ecole_for_special IS NULL)) > 0 THEN 1 ELSE 0 END AS special '.
    'FROM sports AS s '.
    'JOIN ecoles_sports AS es ON '.
        'es.id_sport = s.id AND '.
        'es._etat = "active" '.
    'WHERE '.
        'es.id_ecole = '.(int) $id.' AND '.
        's._etat = "active"')->fetchAll(PDO::FETCH_ASSOC);

    die(json_encode($sports));
}



else if ($_POST['load'] == 'licences') {
    require DIR.'actions/import/licences.php';
}

//SEND
else {
    if (!empty($_POST['id'])) {
        $found = $pdo->query('SELECT '.
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
                'p.recharge, '.
                'CASE WHEN t.id_ecole_for_special = '.(int) $id.' OR t.id_ecole_for_special IS NULL THEN t.id_sport_special ELSE NULL END AS id_sport_special, '.
                'CASE WHEN (SELECT COUNT(spt.id) '.
                    'FROM sportifs AS spt '.
                    'JOIN equipes AS eqt ON '.
                        'eqt.id = spt.id_equipe AND '.
                        'eqt._etat = "active" '.
                    'JOIN ecoles_sports AS est ON '.
                        'est.id = eqt.id_ecole_sport AND '.
                        'est._etat = "active" '.
                    'JOIN sports AS st ON '.
                        'st.id = est.id_sport AND '.
                        'st._etat = "active" '.
                    'WHERE '.
                        'spt.id_participant = p.id AND '.
                        'spt._etat = "active") > 0 THEN 1 ELSE 0 END AS has_sports, '.
                'CASE WHEN (SELECT COUNT(eqe.id) '.
                    'FROM equipes AS eqe '.
                    'JOIN ecoles_sports AS ese ON '.
                        'ese.id = eqe.id_ecole_sport AND '.
                        'ese._etat = "active" '.
                    'JOIN sports AS se ON '.
                        'se.id = ese.id_sport AND '.
                        'se._etat = "active" '.
                    'WHERE '.
                        'eqe.id_capitaine = p.id AND '.
                        'eqe._etat = "active") > 0 THEN 1 ELSE 0 END AS is_capitaine, '.
                'CASE WHEN (SELECT COUNT(sps.id) '.
                    'FROM sportifs AS sps '.
                    'JOIN equipes AS eqs ON '.
                        'eqs.id = sps.id_equipe AND '.
                        'eqs._etat = "active" '.
                    'JOIN ecoles_sports AS ess ON '.
                        'ess.id = eqs.id_ecole_sport AND '.
                        'ess._etat = "active" '.
                    'JOIN sports AS ss ON '.
                        'ss.id = ess.id_sport AND '.
                        'ss._etat = "active" '.
                    'WHERE '.
                        'sps.id_participant = p.id AND '.
                        'ss.sexe = p.sexe AND '.
                        'sps._etat = "active") > 0 THEN 1 ELSE 0 END AS has_sports_sexe, '.
                'CASE WHEN t.id_sport_special IS NULL THEN 0 '.
                'WHEN (t.id_ecole_for_special IS NULL OR t.id_ecole_for_special = '.(int) $id.') AND '.
                    '(SELECT COUNT(spp.id) '.
                    'FROM sportifs AS spp '.
                    'JOIN equipes AS eqp ON '.
                        'eqp.id = spp.id_equipe AND '.
                        'eqp._etat = "active" '.
                    'JOIN ecoles_sports AS esp ON '.
                        'esp.id = eqp.id_ecole_sport AND '.
                        'esp._etat = "active" '.
                    'JOIN sports AS s_p ON '.
                        's_p.id = esp.id_sport AND '.
                        's_p._etat = "active" '.
                    'WHERE '.
                        'spp.id_participant = p.id AND '.
                        's_p.id = t.id_sport_special AND '.
                        'spp._etat = "active") > 0 THEN 1 ELSE 0 END AS has_sport_special '.
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
                'p.id = '.(int) $_POST['id']);
        $found = $found->fetch(PDO::FETCH_ASSOC);
    }

    if (empty($found) && 
        $ecole['etat_inscription'] == 'limitee' && (
            empty($_SESSION['user']['privileges']) ||
            !in_array('ecoles', $_SESSION['user']['privileges'])))
            die(json_encode(['error' => true, 'message' => 'L\'inscription est limitée, vous ne pouvez pas ajouter de nouveau participant']));


    if (empty($phase_actuelle) && empty($accesAdmin))
            die(json_encode(['error' => true, 'message' => 'Les modifications (et inscriptions) sont closes']));


    $participants = $pdo->query('SELECT '.
            'nom, '.
            'prenom, '.
            'email, '.
            'telephone, '.
            'licence, '.
            'sportif '.
        'FROM participants '.
        'WHERE '.
            '(_etat = "active" OR _etat = "temp" AND _auteur = '.(int) $_SESSION['user']['id'].') AND '.
            (!empty($found) ? 'id <> '.(int) $found['id'].' AND ' : '').
            'id_ecole = '.(int) $id)
        ->fetchAll(PDO::FETCH_ASSOC);

    $tarifs_ = $pdo->query('SELECT '.
            'te.id AS id_tarif_ecole, '.
            'nom, '.
            'logement, '.
            'sportif, '.
            'CASE WHEN id_ecole_for_special = '.(int) $id.' OR id_ecole_for_special IS NULL THEN id_sport_special ELSE NULL END AS id_sport_special, '.
            'for_pompom, '.
            'for_fanfaron, '.
            'for_cameraman '.
        'FROM tarifs AS t '.
        'JOIN tarifs_ecoles AS te ON '.
            'te.id_tarif = t.id AND '.
            'te.id_ecole = '.(int) $id.' AND '.
            'te._etat = "active" '.
        'WHERE '.
            't._etat = "active"')
        ->fetchAll(PDO::FETCH_ASSOC);

    $sports_ = $pdo->query('SELECT '.
            's.id, '.
            'es.id AS id_ecole_sport, '.
            's.sport, '.
            's.sexe, '.
            'es.quota_max, '.
            'es.quota_equipes, '.
            's.quota_inscription, '.
            '(SELECT COUNT(pe.id) '.
                'FROM participants AS pe '.
                'JOIN sportifs AS spe ON '.
                    'spe.id_participant = pe.id AND '.
                    '(spe._etat = "active" OR spe._etat = "temp" AND spe._auteur = '.(int) $_SESSION['user']['id'].') '.
                'JOIN equipes AS eqe ON '.
                    'spe.id_equipe = eqe.id AND '.
                    '(eqe._etat = "active" OR eqe._etat = "temp" AND eqe._auteur = '.(int) $_SESSION['user']['id'].') '.
                'JOIN ecoles_sports AS ese ON '.
                    'eqe.id_ecole_sport = ese.id AND '.
                    'ese._etat = "active" '.
                'WHERE '.
                    'ese.id_sport = s.id AND '.
                    'ese.id_ecole = es.id_ecole AND '.
                    '(pe._etat = "active" OR pe._etat = "temp" AND pe._auteur = '.(int) $_SESSION['user']['id'].')) AS nb_sportifs, '.
            '(SELECT COUNT(p.id) '.
                'FROM participants AS p '.
                'JOIN sportifs AS sp ON '.
                    'sp.id_participant = p.id AND '.
                    '(sp._etat = "active" OR sp._etat = "temp" AND sp._auteur = '.(int) $_SESSION['user']['id'].') '.
                'JOIN equipes AS eq ON '.
                    'sp.id_equipe = eq.id AND '.
                    '(eq._etat = "active" OR eq._etat = "temp" AND eq._auteur = '.(int) $_SESSION['user']['id'].') '.
                'JOIN ecoles_sports AS esp ON '.
                    'eq.id_ecole_sport = esp.id AND '.
                    'esp._etat = "active" '.
                'WHERE '.
                    'esp.id_sport = s.id AND '.
                    '(p._etat = "active" OR p._etat = "temp" AND p._auteur = '.(int) $_SESSION['user']['id'].')) AS nb_inscriptions, '.
            '(SELECT COUNT(eqt.id) '.
                'FROM equipes AS eqt '.
                'JOIN ecoles_sports AS est ON '.
                    'est.id = eqt.id_ecole_sport AND '.
                    'est._etat = "active" '.
                'WHERE '.
                    'est.id_ecole = es.id_ecole AND '.
                    'est.id_sport = s.id AND '.
                    '(eqt._etat = "active" OR eqt._etat = "temp" AND eqt._auteur = '.(int) $_SESSION['user']['id'].')) AS nb_equipes, '.
            '(SELECT eqf.id '.
                'FROM equipes AS eqf '.
                'JOIN ecoles_sports AS esf ON '.
                    'esf.id = eqf.id_ecole_sport AND '.
                    'esf._etat = "active" '.
                'WHERE '.
                    'esf.id_ecole = es.id_ecole AND '.
                    'esf.id_sport = s.id AND '.
                    '(eqf._etat = "active" OR eqf._etat = "temp" AND eqf._auteur = '.(int) $_SESSION['user']['id'].') '.
                'LIMIT 1) AS id_equipe, '.
            'CASE WHEN (SELECT COUNT(t.id) '.
                'FROM tarifs AS t '.
                'WHERE '.
                    't.id_sport_special = s.id AND '.
                    '(t.id_ecole_for_special = '.(int) $id.' OR t.id_ecole_for_special IS NULL) AND '.
                    't._etat = "active") > 0 THEN 1 ELSE 0 END AS special '.
        'FROM sports AS s '.
        'JOIN ecoles_sports AS es ON '.
            'es.id_sport = s.id AND '.
            'es._etat = "active" '.
        'WHERE '.
            'es.id_ecole = '.(int) $id.' AND '.
            's._etat = "active"')
         ->fetchAll(PDO::FETCH_ASSOC);


    $emails = [];
    $licences = [];
    $noms_prenoms = [];

    foreach ($participants as $participant) {
        if (isValidLicence(strtoupper(preg_replace('/\s+/', '', $participant['licence']))))
            $licences[] = strtoupper(preg_replace('/\s+/', '', $participant['licence']));

        $emails[] = trim(strtolower($participant['email']));
        $noms_prenoms[] = strtolower(removeAccents(trim($participant['nom']) . ' ' .
            trim($participant['prenom'])));
    }

    $waitFor = ["nom", "prenom", "sexe", "sportif", "licence", "fanfaron", "pompom", "cameraman", "logement", "tarif", "logeur", "recharge", "sport", "capitaine", "equipe", "telephone", "email"];
    $datas = [];
    foreach ($waitFor as $data)
        $datas[$data] = !isset($_POST[$data]) ? '' : trim($_POST[$data]);
    

    $logement = getLogement($datas['logement']) == '1';
    $fanfaron = getFanfaron($datas['fanfaron']) == '1';
    $pompom = getPompom($datas['pompom']) == '1';
    $cameraman = getCameraman($datas['cameraman']) == '1';
    $sportif = getSportif($datas['sportif']) == '1';
    $capitaine = getCapitaine($datas['capitaine']) == '1';
    $homme = getSexe($datas['sexe']) == 'h';


    //Filtrage des tarifs et sports
    $tarifs = [];
    $sports = [];

    foreach ($tarifs_ as $tarif) {
        if ($tarif['logement'] == '0' && $logement ||
            $tarif['logement'] == '1' && !$logement ||
            $tarif['sportif'] == '1' && !$sportif ||
            $tarif['sportif'] == '0' && $sportif ||
            $tarif['for_fanfaron'] == 'no' && $fanfaron ||
            $tarif['for_cameraman'] == 'no' && $cameraman ||
            $tarif['for_pompom'] == 'no' && $pompom ||
            $tarif['for_fanfaron'] == 'yes' && !$fanfaron ||
            $tarif['for_cameraman'] == 'yes' && !$cameraman ||
            $tarif['for_pompom'] == 'yes' && !$pompom)
            continue; 

        $tarifs[$tarif['id_tarif_ecole']] = $tarif;
    }

    foreach ($sports_ as $sport) {
        if ($sport['special'] == '1' &&
            !empty($datas['tarif']) &&
            !empty($tarifs[$datas['tarif']]) &&
            $tarifs[$datas['tarif']]['id_sport_special'] != $sport['id'] ||
            $sport['sexe'] != 'm' && 
            $sport['sexe'] != getSexe($datas['sexe']))
            continue; 

        $sports[$sport['id_ecole_sport']] = $sport;
    }


    if (!isValidSexe($datas['sexe']))
        die(json_encode(['error' => true, 'field' => 'sexe', 'message' => 'Le champ "sexe" n\'est pas valide']));

    else if (strlen($datas['nom']) == 0)
        die(json_encode(['error' => true, 'field' => 'nom', 'message' => 'Le champ "nom" ne peut être vide']));

    else if (strlen($datas['prenom']) == 0 ||
        in_array(strtolower(removeAccents($datas['nom']) . ' ' . $datas['prenom']), $noms_prenoms))
        die(json_encode(['error' => true, 'field' => 'prenom', 'message' => 'Le champ "prénom" est vide ou le couple (nom, prénom) déjà utilisé']));

    else if (!isValidEmail($datas['email']) || 
        in_array(strtolower($datas['email']), $emails))
        die(json_encode(['error' => true, 'field' => 'email', 'message' => 'L\'email est invalide ou déjà utilisé']));

    else if (!isValidSportif($datas['sportif']))
        die(json_encode(['error' => true, 'field' => 'sportif', 'message' => 'Le champ "sportif" n\'est pas valide']));

    else if ($sportif && (
        strlen($datas['licence']) == 0 || 
        isValidLicence($datas['licence']) &&
        in_array(strtoupper(preg_replace('/\s+/', '', $datas['licence'])), $licences)))
        die(json_encode(['error' => true, 'field' => 'licence', 'message' => 'La licence n\'est pas renseignée (obligatoire pour un sportif) ou déjà utilisée']));

    else if (!isValidFanfaron($datas['fanfaron']))
        die(json_encode(['error' => true, 'field' => 'fanfaron', 'message' => 'Le champ "fanfaron" est invalide']));

    else if (!isValidPompom($datas['pompom']))
        die(json_encode(['error' => true, 'field' => 'pompom', 'message' => 'Le champ "pompom" est invalide']));

    else if (!isValidCameraman($datas['cameraman']) ||
        !$sportif && 
        !$fanfaron && 
        !$pompom && 
        !$cameraman)
        die(json_encode(['error' => true, 'field' => 'cameraman', 'message' => 'Le champ "cameraman" est invalide ou le participant n\'a aucun statut']));

    else if (!isValidLogement($datas['logement']))
        die(json_encode(['error' => true, 'field' => 'logement', 'message' => 'Le champ "logement" est invalide']));
    
    else if (!isValidRecharge($datas['recharge']))
        die(json_encode(['error' => true, 'field' => 'recharge', 'message' => 'Le champ "recharge" est invalide']));

    else if ($sportif &&
        !isValidCapitaine($datas['capitaine']))
        die(json_encode(['error' => true, 'field' => 'capitaine', 'message' => 'Le champ "capitaine" est invalide']));

    else if ($sportif &&
        $capitaine && 
        strlen($datas['telephone']) == 0)
        die(json_encode(['error' => true, 'field' => 'telephone', 'message' => 'Le champ "telephone" ne peut être vide']));

    else if (empty($tarifs[$datas['tarif']]))
        die(json_encode(['error' => true, 'field' => 'tarif', 'message' => 'Le champ "tarif" est invalide']));
    
    else if ($ecole['ecole_lyonnaise'] == '0' &&
        !$logement &&
        strlen($datas['logeur']) == 0)
        die(json_encode(['error' => true, 'field' => 'logeur', 'message' => 'Le champ "logeur" doit être renseigné']));
    
    else if ($sportif && (
            !empty($datas['sport']) &&
            empty($sports[$datas['sport']]) ||
            $capitaine &&
            empty($datas['sport'])))
        die(json_encode(['error' => true, 'field' => 'sport', 'message' => 'Le champ "sport" est invalide']));

    //Le sport n'est accessible qu'à l'ajout
    else if (empty($found) &&
        $sportif && 
        !$capitaine &&
        !empty($datas['sport']) &&
        !empty($sports[$datas['sport']]) && 
        $sports[$datas['sport']]['nb_equipes'] < 1)
        die(json_encode(['error' => true, 'field' => 'sport', 'message' => 'Aucune équipe n\'existe pour ce sport']));

    //Le sport n'est accessible qu'à l'ajout
    // else if (empty($found) &&
    //     $sportif && 
    //     !$capitaine &&
    //     !empty($datas['sport']) &&
    //     !empty($sports[$datas['sport']]) && 
    //     $sports[$datas['sport']]['nb_equipes'] > 1)
    //     die(json_encode(['error' => true, 'field' => 'sport', 'message' => 'Impossible de placer le sportif car il y a plus d\'une équipe pour ce sport']));



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

    $places_inscription = (empty($quotas['total']) ? 0 : (int) $quotas['total']) - $ecole['nb_inscriptions'];
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


    if (!empty($found)) {
        $was_loge = $found['logement'] == '1';
        $was_fanfaron = $found['fanfaron'] == '1';
        $was_pompom = $found['pompom'] == '1';
        $was_cameraman = $found['cameraman'] == '1';
        $was_sportif = $found['sportif'] == '1';
        $was_capitaine = $found['is_capitaine'] == '1';
        $was_homme = $found['sexe'] == 'h';

        $has_sports = $found['has_sports'] == '1';
        $has_sport_special = $found['has_sport_special'] == '1';
        $has_sports_sexe = $found['has_sports_sexe'] == '1';

        //On joue sur les quotas pour assurer l'édition
        if ($inscription_on) $places_inscription++;
        if ($sportif_on && $was_sportif) $places_sportif++;
        if ($nonsportif_on && !$was_sportif) $places_nonsportif++;
        if ($logement_on && $was_loge) $places_logement++;
        if ($filles_on && !$was_homme && $was_loge) $places_filles_logees++;
        if ($garcons_on && $was_homme && $was_loge) $places_garcons_loges++;
        if ($pompom_on && $was_pompom) $places_pompom++;
        if ($cameraman_on && $was_cameraman) $places_cameraman++;
        if ($fanfaron_on && $was_fanfaron) $places_fanfaron++;
        if ($pompom_nonsportif_on && $was_pompom && !$was_sportif) $places_pompom_nonsportif++;
        if ($cameraman_nonsportif_on && $was_cameraman && !$was_sportif) $places_cameraman_nonsportif++;
        if ($fanfaron_nonsportif_on && $was_fanfaron && !$was_sportif) $places_fanfaron_nonsportif++;
    }


    if (!empty($found) &&
        $was_capitaine && !$sportif)
        die(json_encode(['error' => true, 'message' => 'Ce participant est capitaine d\'au moins une équipe']));

    else if (!empty($found) &&
        $was_capitaine && 
        empty($datas['telephone']))
        die(json_encode(['error' => true, 'message' => 'Ce participant est capitaine, il doit avoir un téléphone']));
    
    else if (!empty($found) &&
        $has_sports && !$sportif)
        die(json_encode(['error' => true, 'message' => 'Ce participant est inscrit dans au moins un sport']));
    
    else if (!empty($found) &&
        $has_sports_sexe && (
            $homme && !$was_homme ||
            !$homme && $was_homme))
        die(json_encode(['error' => true, 'message' => 'Ce participant est inscrit dans un sport de l\'autre sexe']));

    else if (!empty($found) &&
        $has_sport_special && 
        !empty($datas['tarif']) &&
        !empty($tarifs[$datas['tarif']]) &&
        $tarifs[$datas['tarif']]['id_sport_special'] != $found['id_sport_special'])
        die(json_encode(['error' => true, 'message' => 'Ce participant est inscrit dans un sport spécial, le tarif actuel ne le permet plus']));

    else if ($inscription_on && $places_inscription <= 0)
        die(json_encode(['error' => true, 'message' => 'Le quota de participants a été atteint']));

    else if ($logement && (
        $logement_on && $places_logement <= 0 ||
        $filles_on && !$homme && $places_filles_logees <= 0 ||
        $garcons_on && $homme && $places_garcons_loges <= 0))
        die(json_encode(['error' => true, 'message' => 'Le quota de logements a été atteint']));

    else if ($sportif && 
        $sportif_on && $places_sportif <= 0)
        die(json_encode(['error' => true, 'message' => 'Le quota de sportifs a été atteint']));

    else if (!$sportif && 
        $nonsportif_on && $places_nonsportif <= 0)
        die(json_encode(['error' => true, 'message' => 'Le quota de non sportifs a été atteint']));

    else if ($pompom_on && $pompom && $places_pompom <= 0 ||
        $pompom_nonsportif_on && $pompom && !$sportif && $places_pompom_nonsportif <= 0)
        die(json_encode(['error' => true, 'message' => 'Le quota de pompoms a été atteint']));

    else if ($cameraman_on && $cameraman && $places_cameraman <= 0 ||
        $cameraman_nonsportif_on && $cameraman && !$sportif && $places_cameraman_nonsportif <= 0)
        die(json_encode(['error' => true, 'message' => 'Le quota de cameramans a été atteint']));

    else if ($fanfaron_on && $fanfaron && $places_fanfaron <= 0 ||
        $fanfaron_nonsportif_on && $fanfaron && !$sportif && $places_fanfaron_nonsportif <= 0)
        die(json_encode(['error' => true, 'message' => 'Le quota de fanfarons a été atteint']));

    else if (empty($found) &&
        $sportif && 
        !empty($datas['sport']) &&
        !empty($sports[$datas['sport']]) &&
        $sports[$datas['sport']]['quota_max'] - $sports[$datas['sport']]['nb_sportifs'] <= 0)
        die(json_encode(['error' => true, 'message' => 'Le quota de sportifs dans ce sport pour cette école a été atteint']));

    else if (empty($found) &&
        $sportif && 
        !empty($datas['sport']) &&
        !empty($sports[$datas['sport']]) &&
        $sports[$datas['sport']]['quota_inscription'] !== null && 
        $sports[$datas['sport']]['quota_inscription'] - $sports[$datas['sport']]['nb_inscriptions'] <= 0)
        die(json_encode(['error' => true, 'message' => 'Le quota d\'inscription pour ce sport a été atteint']));

    else if (empty($found) &&
        $sportif && 
        $capitaine &&
        !empty($datas['sport']) &&
        !empty($sports[$datas['sport']]) &&
        $sports[$datas['sport']]['quota_equipes'] - $sports[$datas['sport']]['nb_equipes'] <= 0)
        die(json_encode(['error' => true, 'message' => 'Le quota d\'équipes pour ce sport a été atteint']));

   

    if (empty($found)) {
        if ((empty($phase_actuelle) || $phase_actuelle == 'modif') && 
            empty($accesAdmin))
            die(json_encode(['error' => true, 'message' => 'Les inscriptions sont closes']));


        $etat = !empty($_POST['temp']) ? 'temp' : 'active';

        $pdo->exec('INSERT INTO participants SET '.
            '_auteur = '.(int) $_SESSION['user']['id'].', '.
            '_date = NOW(), '.
            '_message = "Ajout d\'un participant", '.
            '_etat = "'.$etat.'", '.
            //-------------//
            'nom = "'.ucname(secure($datas['nom'])).'", '.
            'prenom = "'.ucname(secure($datas['prenom'])).'", '.
            'sexe = "'.($homme ? 'h' : 'f').'", '.
            'telephone = "'.getPhone(secure($datas['telephone'])).'", '.
            'email = "'.strtolower(secure($datas['email'])).'", '.
            'licence = "'.getLicence(secure($datas['licence'])).'", '.
            'sportif = '.($sportif ? '1' : '0').', '.
            'pompom = '.($pompom ? '1' : '0').', '.
            'fanfaron = '.($fanfaron ? '1' : '0').', '.
            'cameraman = '.($cameraman ? '1' : '0').', '.
            'id_tarif_ecole = '.abs((int) $datas['tarif']).', '.
            'recharge = '.abs((int) $_POST['recharge']).', '.
            'id_ecole = '.(int) $id.', '.
            'logeur = "'.secure($datas['logeur']).'", '.
            'date_inscription = NOW()');

        $id_participant = $pdo->lastInsertId();

        if ($sportif && 
            !empty($datas['sport'])) {
            $id_equipe = $datas["equipe"];

            if ($capitaine) {
                $pdo->exec('INSERT INTO equipes SET '.
                    '_auteur = '.(int) $_SESSION['user']['id'].', '.
                    '_date = NOW(), '.
                    '_message = "Ajout d\'une équipe", '.
                    '_etat = "'.$etat.'", '.
                    //-----------//
                    'id_capitaine = '.(int) $id_participant.', '.
                    'id_ecole_sport = '.(int) $datas['sport'].', '.
                    'label = "N°'.(int) ($sports[$datas['sport']]['nb_equipes'] + 1).'"') or die(print_r($pdo->errorInfo()));
                
                $id_equipe = $pdo->lastInsertId();
            } 

            $pdo->exec('INSERT INTO sportifs SET '.
                 '_auteur = '.(int) $_SESSION['user']['id'].', '.
                '_date = NOW(), '.
                '_message = "Ajout d\'un sportif", '.
                '_etat = "'.$etat.'", '.
                //-----------//
                'id_participant = '.(int) $id_participant.', '.
                'id_equipe = '.(int) $id_equipe)  or die(print_r($pdo->errorInfo()));
        }
    } 

    else {
        $inscrip = new DateTime($found['date_inscription']);
        $found['phase'] = $inscrip < $finPhase1 ? 'phase1' : (
            $inscrip < $finPhase2 ? 'phase2' : (
                $inscrip < $finMalus ? 'malus' : (
                    $inscrip < $finInscrip ? 'modif' : null)));

        if (empty($phase_actuelle) && empty($accesAdmin))
            die(json_encode(['error' => true, 'message' => 'Les modifications sont closes']));

           
        $ref = pdoRevision('participants', $found['id']);
        $pdo->exec('set FOREIGN_KEY_CHECKS = 0');
        $pdo->exec('UPDATE participants SET '.
                '_auteur = '.(int) $_SESSION['user']['id'].', '.
                '_ref = '.(int) $ref.', '.
                '_date = NOW(), '.
                '_message = "Modification d\'un participant", '.
                //-------------//
                'nom = "'.ucname(secure($datas['nom'])).'", '.
                'prenom = "'.ucname(secure($datas['prenom'])).'", '.
                'telephone = "'.getPhone(secure($datas['telephone'])).'", '.
                'email = "'.strtolower(secure($datas['email'])).'", '.
                'licence = "'.getLicence(secure($datas['licence'])).'", '.
                
                ($phase_actuelle != $found['phase'] && empty($accesAdmin) ? '' :
                    'sexe = "'.($homme ? 'h' : 'f').'", '.
                    'sportif = '.($sportif ? '1' : '0').', '.
                    'pompom = '.($pompom ? '1' : '0').', '.
                    'fanfaron = '.($fanfaron ? '1' : '0').', '.
                    'cameraman = '.($cameraman ? '1' : '0').', '.
                    'id_tarif_ecole = '.abs((int) $datas['tarif']).', ').
                
                'recharge = '.abs((int) $_POST['recharge']).', '.
                'id_ecole = '.(int) $id.', '.
                'logeur = "'.secure($datas['logeur']).'" '.
            'WHERE '.
                'id = '.$found['id']);
        $pdo->exec('set FOREIGN_KEY_CHECKS = 1');

        //Dans le cas d'un changement de sexe, il convient de supprimer le participant de la chambre ou de la tente
    }


    die(json_encode(['error' => false]));
}


//Levensthein, vraiment une bonne idée ?
//Améliorer filtrage des tarifs/sports (licences c'est bon) aussi bien en validation, qu'en autocomplete
//Je veux surtout une similarité entre l'autocomplete et la validation (autocomplete affiche bien mais des tarifs trop éloignés de ce qu'il y a d'écrit)
//Idem pour la reconnaissance des colonnes, des fois c'est foireux je trouve (Ski est transformé en **_ et pour les tarifs je n'en parle même pas...)
//Peut-être faut-il être plus sévère, et avoir une distance nulle ou très basse (1, 2 ???)
//Les mots clés ne sont pas une si bonne idée que cela puisqu'assez trompeur (surtout pout light/full)
//En autocomplete rajouter la gestion des sports spéciaux
//En validation, rajouter la regex pour enlever -?ball
//Pour les emails autant pour le domaine c'est bien autant pour la reconnaissance des noms c'est pas bien 
    //vincnt sera accepté (alors qu'il y a une erreur) alors qu'un prénom composé lancera une erreur


//Pas de confirm, prompt, alert, n'utiliser que les hint
//POssibilité de choisir où mettre une nouvelle colonne ? 
