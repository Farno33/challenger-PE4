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
require DIR.'templates/_header.php';

$cql = 'T|s:sports:
S|s.individuel
B|s.individuel|s.tournoi_initie';

?>
			
					<h2>
						Liste des Tournois
					</h2>

				<table class="table-small">
					<thead>
						<tr>
							<th>Sport</th>
							<th style="width:60px">Sexe</th>
							<th style="width:60px"><small>Individuel</small></th>
							<th style="width:50px"><small>Ecoles</small></th>
							<th style="width:50px"><small>Equipes</small></th>
							<th style="width:50px"><small>Sportifs</small></th>
							<th style="width:50px"><small>Initié</small></th>
							<th style="width:50px"><small>Phases</small></th>
							<th style="width:50px"><small>Concurrents</small></th>
						</tr>
					</thead>

					<tbody>

						<?php if (empty($sports)) { ?>

						<tr class="vide">
							<td colspan="9">Aucun sport</td>
						</tr>

						<?php } 

						$previousIndividuel = null;
						foreach ($sports as $sport) { 

							if ($previousIndividuel !== $sport['individuel'] && 
								$previousIndividuel !== null) echo '<tr><th colspan="9"></th></tr>';

							$previousIndividuel = $sport['individuel'];

						?>

						<tr class="form clickme" onclick="window.location.href = '<?php url('score/tournois/'.$sport['id']); ?>';">
							<td><div><?php echo stripslashes($sport['sport']); ?></div></td>
							<td>
								<input type="checkbox" <?php if ($sport['sexe'] != 'f') echo 'checked '; ?>/>
								<label class="sexe<?php if ($sport['sexe'] == 'm') echo ' sexe-m'; ?>"></label>
							</td>
							<td>
								<input type="checkbox" <?php if ($sport['individuel']) echo 'checked '; ?>/>
								<label></label>
							</td>
							<td><div><center><b><?php echo $sport['nb_ecoles']; ?></b></center></div></td>
							<td><div><center><b><?php echo $sport['nb_equipes']; ?></b></center></div></td>
							<td><div><center><b><?php echo $sport['nb_sportifs']; ?></b></center></div></td>
							<td>
								<input type="checkbox" <?php echo $sport['tournoi_initie'] === null ? 'disabled ' : ($sport['tournoi_initie'] ? 'checked ' : ''); ?>/>
								<label></label>
							</td>
							<td><div><center><b><?php echo $sport['tournoi_initie'] === null ? '-' : $sport['nb_phases']; ?></b></center></div></td>
							<td><div><center><b><?php echo $sport['tournoi_initie'] === null ? '-' : $sport['nb_concurrents']; ?></b></center></div></td>
						</tr>

						<?php } ?>

					</tbody>
						

					</tbody>
				</table>
		

<?php 

//Inclusion du pied de page
require DIR.'templates/_footer.php';
