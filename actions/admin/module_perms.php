<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Matthieu 'Thamite' MASSARDIER ******************/
/* matthieu.massardier@etu.ec-lyon.fr **********************/
/* *********************************************************/
/* actions/admin/module_perms.php **************************/
/* Supervision du module des Perms *************************/
/* *********************************************************/
/* Dernière modification : le 04/10/23 *********************/
/* *********************************************************/


//Liste des actions possibles
$actionsModule = [
    'crenaux'       => 'Créneaux',
    'inscriptions'  => 'Inscriptions',
];

//On récupère l'action désirée par l'utilisateur
$action = !empty($args[2][0]) ? $args[2][0] : 'crenaux';







//On insére le module concerné
require DIR.'actions/admin/perms/action_'.$action.'.php';