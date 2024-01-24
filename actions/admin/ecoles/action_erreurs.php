<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/admin/ecoles/action_edition.php *****************/
/* Edition d'une école *************************************/
/* *********************************************************/
/* Dernière modification : le 16/02/15 *********************/
/* *********************************************************/

if (!empty($_GET['switch'])) {
    $pdo->exec('UPDATE erreurs SET '.
            'etat = (CASE WHEN etat = "specifiee" THEN "corrigee" ELSE "specifiee" END) '.
        'WHERE '.
            'id = '.(int) $_GET['switch']);
    die(header('location:'.url('admin/module/ecoles/erreurs', false, false)));
}

$erreurs = $pdo->query('SELECT '.
        'er.id AS erid, '.
        'p.id, '.
        'p.prenom, '.
        'p.id_ecole, '.
        'p.nom, '.
        'er._date AS date, '.
        'er.message, '.
        'er.etat '.
    'FROM erreurs AS er '.
    'JOIN participants AS p ON '.
        'p._etat = "active" AND '.
        'p.id = er.id_participant '.
    'ORDER BY '.
        'er._date DESC')
    ->fetchAll(PDO::FETCH_ASSOC);

$access = !empty($_SESSION['user']) && 
    !empty($_SESSION['user']['privileges']) &&
    in_array('ecoles', $_SESSION['user']['privileges']);

foreach ($erreurs as $k => $erreur) {
    $erreurs[$k]['cle'] = url($erreur['id'].'/'.substr(sha1(APP_SEED.$erreur['id_ecole'].'_'.$erreur['id']), 0, 20), false, false);
    $erreurs[$k]['access'] = $access ||
        !empty($_SESSION['user']) &&
        !empty($_SESSION['user']['ecoles']) &&
        in_array($erreur['id_ecole'], $_SESSION['user']['ecoles']);
}

//Inclusion du bon fichier de template
require DIR.'templates/admin/ecoles/erreurs.php';
