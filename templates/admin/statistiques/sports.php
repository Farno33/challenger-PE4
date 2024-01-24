<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* templates/admin/statistiques/sports.php *****************/
/* Template des stats sur les sports ***********************/
/* *********************************************************/
/* Dernière modification : le 23/01/15 *********************/
/* *********************************************************/


//Inclusion de l'entête de page
require DIR.'templates/admin/_header_admin.php';

?>
			
				<h2>Statistiques sur les sports</h2>

				<a class="excel" href="?excel">Télécharger en XLSX</a>
				<center>
				<table style="width:auto">
					<thead>
						<tr>
							<th>Ecole <br /><br /><small style="font-weight:normal">Les cas où il y a plusieurs équipes <br />dans un sport collectif associé à une école<br /> sont affichées en violet</small></th>

							<?php
							
							$nbSports = 0;
							$indiv = null;
							$nbCo = 0;
							$nbIndiv = 0;

							foreach ($sports as $id => $sport) {
								$nbSports++;
								$nbCo += $sport['individuel'] ? 0 : 1;
								$nbIndiv += $sport['individuel'] ? 1 : 0;

								if ($indiv === null) {
									$indiv = $sport['individuel'];
								}

								else if ($indiv != $sport['individuel']) {
									$indiv = $sport['individuel'];
									echo '<th style="width:0px !important"></th>';
									$nbSports++;
								}

							 ?>

							<th class="vertical"><span><?php echo stripslashes($sport['sport']).' '.printSexe($sport['sexe']); ?></span></th>

							<?php } ?>

							<th></th>
							<th class="vertical" style="background:#000; color:#FFF; font-weight:bold;"><span>TOTAUX Equipes</span></th>
							<th class="vertical" style="background:#000; color:#FFF; font-weight:bold;"><span>TOTAUX Sportifs</span></th>
						</tr>

						<tr>
							<th></th>

							<?php if ($nbCo) { ?>
							<th colspan="<?php echo $nbCo; ?>">Collectifs</th>
							<?php } if ($nbCo && $nbIndiv) { ?>
							<th style="width:0px !important"></th>
							<?php } if ($nbIndiv) { ?>
							<th colspan="<?php echo $nbIndiv; ?>">Individuels</th>
							<?php } ?>
							<th></th>
							<th></th>
							<th></th>
						</tr>
					</thead>

					<tbody>

						<?php

						$nbEcoles = 0;
						$totaux = [];
						$totauxEq = [];

						foreach ($ecoles as $ecole) {

						$nbEcoles++;

						?>

						<tr class="form">
							<td><center><?php echo stripslashes($ecole['nom']); ?></center></td>
							
							<?php

							$indiv = null;
							$nbEquipes = 0;
							$nbSportifs = 0;
							foreach ($sports as $id => $sport) {

								if (!isset($totaux[$id])) {
									$totaux[$id] = 0;
									$totauxEq[$id] = 0;
								}

								if (!empty($ecole['sports'][$id])) {
									foreach ($ecole['sports'][$id] as $equipe) {
										$totaux[$id] += $equipe['spnb'];
										$nbSportifs += $equipe['spnb'];
									}

									if (!$sport['individuel']) {
										$totauxEq[$id] += count($ecole['sports'][$id]);
										$nbEquipes += count($ecole['sports'][$id]);
									}
								}

								if ($indiv === null) {
									$indiv = $sport['individuel'];
								}

								else if ($indiv != $sport['individuel']) {
									$indiv = $sport['individuel'];
									echo '<th style="width:0px !important"></th>';
								}


							?>

							<td class="vertical"<?php echo !empty($ecole['sports'][$id]) ? ' style="background-color:'.(count($ecole['sports'][$id]) > 1 ? '#FDF': '#DFD').'"' : ''; ?>>
								<center><b><?php echo !empty($ecole['sports'][$id]) ? ($sport['individuel'] ? 
									array_sum(array_map(function($equipe) {
										return $equipe['spnb'];
										}, $ecole['sports'][$id])) : 
									implode('<br />', array_map(function($equipe) {
										return $equipe['spnb'];
										}, $ecole['sports'][$id]))) : ''; ?></b></center>
							</td>

							<?php } ?>

							<th></th>
							<td<?php echo $nbEquipes ? ' style="background-color:#88F"' : ''; ?>><center><b><?php echo $nbEquipes ? $nbEquipes : ''; ?></b></center></td>
							<td<?php echo $nbSportifs ? ' style="background-color:#DDF"' : ''; ?>><center><b><?php echo $nbSportifs ? $nbSportifs : ''; ?></b></center></td>
						</tr>

						<?php } if (!$nbEcoles) { ?>

						<tr class="vide">
							<td colspan="<?php echo 1 + $nbSports + 3; ?>">Aucune école</td>
						</tr>

						<?php } else { ?>

						<tr>
							<th colspan="<?php echo 1 + $nbSports + 3; ?>"></th>
						</tr>

						<tr class="form">
							<th>TOTAUX Equipes</th>

							<?php 
							$indiv = null;
							$totalEquipes = 0;
							foreach ($sports as $id => $sport) { 
								if ($indiv === null) {
									$indiv = $sport['individuel'];
								}

								else if ($indiv != $sport['individuel']) {
									$indiv = $sport['individuel'];
									echo '<th style="width:0px !important"></th>';
								}

								if ($sport['individuel']) 
									echo '<td></td>';

								else {
									$totalEquipes += $totauxEq[$id];

								?>

							<td<?php if (!empty($totauxEq[$id])) echo ' style="background-color:#F88"'; ?>>
								<center><b><?php echo empty($totauxEq[$id]) ? '' : $totauxEq[$id]; ?></b></center>
							</td>

							<?php } } ?>

							<th></th>	
							<td colspan="2" style="background-color:#888;"><center><b><?php echo $totalEquipes; ?></b></center></td>
							
						</tr>

						<tr class="form">
							<th>TOTAUX Sportifs</th>

							<?php 

							$indiv = null;
							$totalSportifs = 0;
							foreach ($sports as $id => $sport) { 
								if ($indiv === null) {
									$indiv = $sport['individuel'];
								}

								else if ($indiv != $sport['individuel']) {
									$indiv = $sport['individuel'];
									echo '<th style="width:0px !important"></th>';
								}

								$totalSportifs += $totaux[$id];

								?>

							<td<?php if (!empty($totaux[$id])) echo ' style="background-color:#FDD"'; ?>>
								<center><b><?php echo empty($totaux[$id]) ? '' : $totaux[$id]; ?></b></center>
							</td>

							<?php } ?>

							<th></th>
							<td colspan="2" style="background-color:#DDD;"><center><b><?php echo $totalSportifs; ?></b></center></td>
							
						</tr>

						<?php } ?>

					</tbody>
				</table>
				</center>


<?php

//Inclusion du pied de page
require DIR.'templates/_footer.php';
