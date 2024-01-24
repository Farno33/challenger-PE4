<?php

if (empty($id))
        die(json_encode(['as' => [], 'licences' => []]));

$as = $pdo->query('SELECT as_code '.
    'FROM ecoles '.
    'WHERE '.
        '_etat = "active" AND '.
        'id = '.(int) $id)
    ->fetch(PDO::FETCH_ASSOC);

if (empty($as))
    die(json_encode(['as' => [], 'licences' => []]));

$as = explode(',', $as['as_code']);
$as = array_map('trim', $as);
$as = array_filter($as);

$licences = [];

$where = '0 ';
foreach ($as as $code) 
    $where .= 'OR as_code = "'.$code.'" ';

$licences_ = $pdo->query('SELECT '.
        'licence, '.
        'nom, '.
        'prenom, '.
        '_date '.
    'FROM licences '.
    'WHERE '.$where.
    'ORDER BY licence ASC')
    ->fetchAll(PDO::FETCH_ASSOC);


if (!isConnected()) {
    foreach ($licences_ as $licence)
        $licences[] = ['licence' => $licence['licence'], 'nom' => $licence['nom'], 'prenom' => $licence['prenom']];
    
    die(json_encode(['as' => $as, 'licences' => $licences]));
}


$last = null;
$now = new DateTime();

foreach ($licences_ as $licence) {
    $date = new DateTime($licence['_date']);
    if ($last === null ||
        $last > $date)
        $last = $date;
}

if ($last !== null && 
    $now->diff($last)->format('%a') < 1) {
    foreach ($licences_ as $licence)
        $licences[] = ['licence' => $licence['licence'], 'nom' => $licence['nom'], 'prenom' => $licence['prenom']];
    
    die(json_encode(['as' => $as, 'licences' => $licences]));
}


$pdo->exec('DELETE FROM licences WHERE '.$where);


//Trie alphabétique
sort($as);

foreach ($as as $code) {
    $data = http_post('http://www.sport-u-licences.com/sport-u/resultat.php', ['NUMAS' => $code, 'SPORT' => 'tous']);

    preg_match_all('/<tr>'.str_repeat('\s*<td[^>]*>((?:<td.+?<\/td|.)*?)<\/td>', 8).'/si', $data, $matches);


    unset($matches[0]);
    foreach ($matches as $key => $match) {
        $matches[$key] = array_map('strip_tags', $match);
    }

    list(, $lics, $ecoles, $types, $noms, $prenoms, $dates, $ia, $rc) = $matches;

    //Duplication de la fiche client dans le cas où il y a plusieurs licences
    foreach ($lics as $key => $lic_str) {
        $lic_str = explode(' ', $lic_str);
        if (count($lic_str) > 1) {
            foreach ($lic_str as $lic) {
                $lics[] = $lic;
                $ecoles[] = $ecoles[$key];
                $types[] = $types[$key];
                $noms[] = $noms[$key];
                $prenoms[] = $prenoms[$key];
                $dates[] = $dates[$key];
                $ia[] = $ia[$key];
                $rc[] = $rc[$key];
            }
        }

        if (count($lic_str) > 1 ||
            !preg_match('/^(Oui|Non)/', $ia[$key])) {
            unset($lics[$key]);
            unset($ecoles[$key]);
            unset($types[$key]);
            unset($noms[$key]);
            unset($prenoms[$key]);
            unset($dates[$key]);
            unset($ia[$key]);
            unset($rc[$key]);
        }
    }
    

    //On trie dans l'ordre (attention à ne pas casser le lien entre les données)
    //(d'où utilisation de asort et non pas sort)
    asort($lics);

    $insert = $pdo->prepare('INSERT INTO licences SET '.
        'as_code = :as, '.
        'licence = :licence, '.
        'ecole = :ecole, '.
        'type = :type, '.
        'nom = :nom, '.
        'prenom = :prenom, '.
        'inscription = :inscription, '.
        'ia = :ia, '.
        'rc = :rc, '.
        '_date = NOW()');

    foreach ($lics as $key => $lic) {
        $inscrip = explode(' ', $dates[$key]);
        $inscrip[0] = explode('/', $inscrip[0]);
        $inscrip = sprintf("%d/%02d/%02d %s:00",
            ($inscrip[0][2] % 2000) + 2000,
            $inscrip[0][1],
            $inscrip[0][0],
            $inscrip[1]);

        $insert->execute(array(
            'as'            => $code, 
            'licence'       => $lics[$key],
            'ecole'         => html_entity_decode($ecoles[$key]),
            'type'          => $types[$key],
            'nom'           => ucname(html_entity_decode($noms[$key])),
            'prenom'        => ucname(html_entity_decode($prenoms[$key])),
            'inscription'   => $inscrip,
            'ia'            => $ia[$key],
            'rc'            => $rc[$key]
        ));

        $licences[] = ['licence' => $lics[$key], 'nom' => ucname(html_entity_decode($noms[$key])), 'prenom' => ucname(html_entity_decode($prenoms[$key]))];
    }
}

die(json_encode(['as' => $as, 'licences' => $licences]));