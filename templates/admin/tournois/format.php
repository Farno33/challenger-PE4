<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* templates/admin/classement/tableau.php ******************/
/* Template du tableau des classements *********************/
/* *********************************************************/
/* Dernière modification : le 17/02/14 *********************/
/* *********************************************************/

//Inclusion de l'entête de page
require DIR.'templates/admin/_header_admin.php';

?>
			
				<h2>Format des poules<br />
					<?php echo stripslashes($sport['sport']).' '.printSexe($sport['sexe']); ?></h2>

				
				<div class="alerte alerte-info">
					<div class="alerte-contenu">
						Le format de la phase de poules pour ce sport n'a pas encore été sélectionné. Choisis l'un des formats possibles suivants, 
						les poules seront alors directement créées et nommées automatiquement, il ne restera plus qu'à placer les équipes.<br />
						<br />
						Le pourcentage est le rapport du nombre d'équipes qualifiées sans repechages sur le nombre d'équipes totales, ainsi plus il est proche de 100 mieux c'est !
						<b>Les formats conseillés sont en bleu.</b>
					</div>
				</div>

				<div class="alerte alerte-success">
					<div class="alerte-contenu">
						Il y a <b><?php echo $nbEquipes; ?></b> équipes, ainsi le nombre d'équipes en phase finale doit être de <b><?php echo $nbEquipesFinales; ?></b>, 
						les phases finales commenceront donc par les <b><?php echo $labelsPhasesFinales[array_keys($labelsPhasesFinales)[$pow == 1 ? 0 : $pow]]; ?></b>. 
					</div>
				</div>

				<style>
				.conseille td { background:#DDF !important;}
				</style>

				<table class="table-small">
					<thead>
						<th>Poules</th>
						<th><small>Equipes / Poule</small></th>
						<th><small>Qualifiées / Poule</small></th>
						<th>Repêchées</th>
						<th>Matchs</th>
						<th><small>Matchs / Poule</small></th>
						<th>Pourcentage</th>
						<th class="actions"></th>
					</thead>

					<tbody>

						<?php foreach ($formats as $nbPoules => $format) { ?>

						<tr<?php if (2 * $format['nb_matchs'] >= $nbEquipes && $format['nb_matchs'] <= 2 * $nbEquipes && $format['pourcentage'] >= 0.5) echo ' class="conseille"'; ?>>
							<td><center><b><?php echo $nbPoules; ?></b></center></td>
							<td><center><?php echo $format['nb_equipes'].($format['nb_equipes'] * $nbPoules < $nbEquipes ? ' ('.($format['nb_equipes']+1).')' : ''); ?></center></td>
							<td><center><?php echo $format['nb_qualifiees']; ?></center></td>
							<td><center><?php echo $format['nb_repechees']; ?></center></td>
							<td><center><?php echo $format['nb_matchs']; ?></center></td>
							<td><center><?php echo ($format['nb_equipes'] * ($format['nb_equipes'] - 1) / 2).
								($format['nb_equipes'] * $nbPoules < $nbEquipes ? ' ('.(($format['nb_equipes'] + 1) * $format['nb_equipes'] / 2).')' : ''); ?></center></td>
							<td><center><?php echo round($format['pourcentage'] * 100); ?>%</center></td>
							<td></td>
						</tr>

						<?php } ?>

					</tbody>
				</table>
		

<?php 

//Inclusion du pied de page
require DIR.'templates/_footer.php';
