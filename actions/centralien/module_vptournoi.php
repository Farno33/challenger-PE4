<?php

$vpTournois = $pdo->query('SELECT id, groupe_multiple FROM sports WHERE _etat = "active" AND ' .
	'IF(groupe_multiple != 0, groupe_multiple = (SELECT groupe_multiple FROM sports WHERE id = ' . $options['vptournoi'] . '), id = ' . $options['vptournoi'] . ') ' .
	'ORDER BY sport ASC')->fetchAll(PDO::FETCH_ASSOC|PDO::FETCH_UNIQUE);

if (
	empty($args[2][0]) ||
	is_numeric($args[2][0])
) {
	if (count($vpTournois) == 1)
		$id_sport = $options['vptournoi']; // Ici on affiche pas la liste des tournois comme on sais à quel sport il est attaché, on lui donne directement la liste des phases de *son* sport
	else if (is_numeric($args[2][0]) && in_array($args[2][0], array_keys($vpTournois)))
		$id_sport = $args[2][0];	// Ici il a déjà choisi quel tournoi prendre
	else if (count($vpTournois) > 1) // Si on en arrive ici, il n'as pas choisi son tournoi, on lui affiche une liste
		die(require DIR . 'actions/admin/tournois/action_liste.php');
	else
		die(require DIR . 'templates/_error.php');
	// TODO : Réaranger toute les URL vers tournoi pour renvoyer vers l'ID sport pour pas peter les couilles
	die(require DIR . 'actions/admin/tournois/action_tournoi.php');
} else if (preg_match('`^phase_([1-9][0-9]*)$`', $args[2][0])) {
	// Là il a choisi une phase, donc le parse est très proche de l'original... on a juste à check que la phase est bien dans le sport du VP => fait dans action_phase.php
	$id_phase = preg_replace('/^phase_/', '', $args[2][0]);
	die(require DIR . 'actions/admin/tournois/action_phase.php');
} else {
	// Pour l'instant on limite l'accès à cette partie du module donc le reste ne peut être qu'erreurs
	die(require DIR . 'templates/_error.php');
}
