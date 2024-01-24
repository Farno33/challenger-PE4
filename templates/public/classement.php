<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* templates/public/classement.php *************************/
/* Template du tableau des classements *********************/
/* *********************************************************/
/* Dernière modification : le 17/02/14 *********************/
/* *********************************************************/


//Inclusion de l'entête de page
require DIR.'templates/_header_nomenu.php';

?>
			
		<style>
				.subnav li, 
				.subnav li ul { 
					width:200px !important;
				}
				</style>
			
				
				<nav class="subnav">
					<h2>Classements</h2>

					<ul>
						<li><a href="?p=tableau">Tableau Genéral</a></li>
						<li>
							<a href="?p=sports">Classement des sports</a>
							
						</li>
					</ul>
				</nav>

				<?php if (empty($_GET['p']) || $_GET['p'] == 'tableau') { ?>

				<h3>Tableau général</h3>

				<table class="table-small">
					<thead>
						<tr>
							<th>Ecole</th>
							<th style="width:120px">Sports</th>
							<th style="width:80px">Pompom</th>
							<th style="width:80px">Fairplay</th>
							<th style="width:80px">DD</th>
							<th style="width:80px">Total</th>
							<th>Classement</th>
						</tr>
					</thead>

					<tbody>

						<?php if (count($ecoles) && $ecoles[array_keys($ecoles)[0]]['total']) { ?>

						<tr>
							<td colspan="7" style="background:#CCC; padding-bottom:0px"><h4 style="margin:0 0 5px">Podium</h4></td>
						</tr>

						<?php } ?>

						<?php

						$p = 0;
						$i = 0;
						$previous = -1;

						$prevKey = -1;
						foreach ($ecoles as $key => $ecole) {
							if (isset($ecoles[$prevKey]) && $ecoles[$prevKey]['total'] == $ecole['total']) {
								$ecoles[$prevKey]['exaequo'] = true;
								$ecoles[$key]['exaequo'] = true;
							}

							$prevKey = $key;
						}

						foreach ($ecoles as $eid => $ecole) {
							$isECL = preg_match('`centrale (.*)lyon`i', $ecole['nom']);
							$isVieuxCons = preg_match('`vieux? (.*)cons?`i', $ecole['nom']);

							if (!$isECL && $ecole['equipes'] == 0)
								continue;

							if (!$isECL)
								$p++;

						?>

						<?php if ($previous && $ecole['total'] == 0) {  ?>

						<tr>
							<td colspan="7" style="background:#CCC; padding-bottom:0px"><h4 style="margin:0 0 5px">Ecoles sans points</h4></td>
						</tr>

						<?php } else if ($i < 4 && $previous > $ecole['total'] && $p >= 4) { ?>

						<tr>
							<td colspan="7" style="background:#CCC; padding-bottom:0px"><h4 style="margin:0 0 5px">Hors podium</h4></td>
						</tr>

						<?php } 


							if ($i == 0 || $previous > $ecole['total'])
								$i = $p;

							$previous = $ecole['total'];

						?>

						<tr>
							<td><?php if ($ecole['total'] && $i <= 3 || $isECL || $isVieuxCons) echo '<img src="'.
								url('assets/images/actions/'.($isECL || $isVieuxCons ? 'griffes' : ($i == 1 ? 'Gold' : 
										($i == 2 ? 'Argent' : 'Bronze'))).'.png', false, false).
									'" alt="" style="height:16px" /> ';
									echo stripslashes($ecole['nom']); ?></td>
							<td><center><?php if ($ecole['points']) { ?><b style="color:red"><?php echo round($ecole['points']); ?></b> <a style="color:gray" href="?p=sports&e=<?php echo $eid; ?>">(détails)</a><?php } ?></center></td>
							<td><center<?php if ($ecole['pompom']) echo ' style="color:#06B; font-weight:bold;"'; ?>><?php echo $ecole['pompom'] ? round($ecole['pompom']) : ''; ?></center></td>
							<td><center<?php if ($ecole['fairplay']) echo ' style="color:orange; font-weight:bold;"'; ?>><?php echo $ecole['fairplay'] ? round($ecole['fairplay']) : ''; ?></center></td>
							<td><center<?php if ($ecole['dd']) echo ' style="color:green; font-weight:bold;"'; ?>><?php echo $ecole['dd'] ? round($ecole['dd']) : ''; ?></center></td>
							<td><center><b><?php echo round($ecole['total']); ?></b></center></td>
							<td><center><b><?php echo !$isECL && !$ecole['total'] ? '' : ($isECL ? '<i>Non classé</i>' : printClassement($i, !empty($ecole['exaequo']))); ?></b></center></td>
						</tr>

						<?php }

						if (!count($ecoles)) { ?> 

						<tr class="vide">
							<td colspan="7">Aucune école</td>
						</tr>

						<?php }  ?>

					</tbody>
				</table>



				<?php } else { ?>

				<h3>Classement des sports</h3>

				<center>
					<form method="get">
						<input type="hidden" name="p" value="sports" />
						<fieldset>
							<select name="e" style="width:300px" onchange="$(this).parent().parent().submit();">
								<option value="" <?php if (!isset($_GET['e']) || !in_array($_GET['e'], array_keys($ecoles)))
									echo 'selected'; ?>>Toutes les écoles</option>

								<?php 

								function cmp($a, $b) {
								    return strcmp($a['nom'], $b['nom']);
								}

								uasort($ecoles, 'cmp');
								
								foreach ($ecoles as $eid => $ecole) { 
									if (empty($ecole['equipes']))
										continue;

								?>

								<option value="<?php echo $eid; ?>" <?php if (!empty($_GET['e']) && $_GET['e'] == $eid) 
									echo ' selected'; ?>><?php echo stripslashes($ecole['nom']); ?></option>

								<?php } ?>

							</select>
							<input type="submit" class="success" value="Afficher" style="margin-bottom:0px !important" />
						</fieldset>
					</form>
				</center><br />
				<br />

				<style>
				.dark { background:#333 !important; color:#FFF;}
				</style>

				<table>
					<thead>
						<tr>
							<th>Sport</th>
							<th style="width:50px">Indiv.</th>
							<th style="width:60px"><small>Coeff.</small></th>
							<th>1er</th>
							<th style="width:60px"><small>Pts</small></th>
							<th>2e</th>
							<th style="width:60px"><small>Pts</small></th>
							<th>3e</th>
							<th style="width:60px"><small>Pts</small></th>
							<th>3e-Ex</th>
							<th style="width:60px"><small>Pts</small></th>
						</tr>
					</thead>

					<tbody>

						<?php 

						$count = 0;
						$previousIndividuel = null;
						foreach ($classements as $classement) {
							$id_ecole_1 = isset($concurrents[$classement['id_concurrent1']]) && isset($ecoles[$concurrents[$classement['id_concurrent1']]['eid']]) ? 
								$concurrents[$classement['id_concurrent1']]['eid'] : null;

							$id_ecole_2 = isset($concurrents[$classement['id_concurrent2']]) && isset($ecoles[$concurrents[$classement['id_concurrent2']]['eid']]) ? 
								$concurrents[$classement['id_concurrent2']]['eid'] : null;

							$id_ecole_3 = isset($concurrents[$classement['id_concurrent3']]) && isset($ecoles[$concurrents[$classement['id_concurrent3']]['eid']]) ? 
								$concurrents[$classement['id_concurrent3']]['eid'] : null;
							
							$id_ecole_3ex = isset($concurrents[$classement['id_concurrent3ex']]) && isset($ecoles[$concurrents[$classement['id_concurrent3ex']]['eid']]) ? 
								$concurrents[$classement['id_concurrent3ex']]['eid'] : null;

						if (!empty($_GET['e']) && 
							$id_ecole_1 != $_GET['e'] && 
							$id_ecole_2 != $_GET['e'] && 
							$id_ecole_3 != $_GET['e'] && 
							$id_ecole_3ex != $_GET['e'])
							continue;

						if ($previousIndividuel !== $classement['individuel'] && 
							$previousIndividuel !== null) echo '<tr><th colspan="11"></th></tr>';

						$previousIndividuel = $classement['individuel'];
						$count++;

						?>

						<tr>
							<td><?php echo stripslashes($classement['sport']).' '.printSexe($classement['sexe']); ?></td>
							<td style="padding:0; width:50px">
								<input type="checkbox" <?php if ($classement['individuel']) echo 'checked '; ?> />
								<label></label>
							</td>
							<td><center><?php echo $classement['coeff']; ?></center></td>

							<?php if (!empty($id_ecole_1)) { ?>
							<td<?php if (!empty($_GET['e']) && $id_ecole_1 == $_GET['e']) echo ' class="dark"'; ?>>
								<img src="<?php url('assets/images/actions/Gold.png'); ?>" alt="" />
								<b><a href="?p=sports&e=<?php echo $id_ecole_1; ?>"><?php echo stripslashes($ecoles[$id_ecole_1]['nom']); ?></a></b>

								<br /><small>
								<?php 

								if (empty($concurrents[$classement['id_concurrent1']]))
									echo '';
								else if ($classement['individuel']) 
									echo stripslashes('<b>'.$concurrents[$classement['id_concurrent1']]['nom'].' '.$concurrents[$classement['id_concurrent1']]['prenom'].'</b>'); 
								else 
									echo stripslashes('Equipe : <b>'.$concurrents[$classement['id_concurrent1']]['label'].'</b>'); 
								?>
								</small>
							</td>
							<td<?php if (!empty($_GET['e']) && $id_ecole_1 == $_GET['e']) echo ' class="dark"'; ?>>
								<center><?php echo round($classement['coeff'] * 
								($p = (!$classement['ex_12'] ? 
									APP_POINTS_1ER :
									(!$classement['ex_23'] ? 
										(APP_POINTS_1ER + APP_POINTS_2E) / 2 : 
										(APP_POINTS_1ER + APP_POINTS_2E + APP_POINTS_3E) / ($classement['ex_3'] ? 4 : 3)))), 2).'<br /><small>'.round($p, 2).'</small>'; ?></center></td>
							<?php } else { ?><td></td><td></td><?php } ?>

							<?php if (!empty($id_ecole_2)) { ?>
							<td<?php if (!empty($_GET['e']) && $id_ecole_2 == $_GET['e']) echo ' class="dark"'; ?>>
								<img src="<?php url('assets/images/actions/'.($classement['ex_12'] ? 'Gold' : 'Argent').'.png'); ?>" alt="" />
								<b><a href="?p=sports&e=<?php echo $id_ecole_2; ?>"><?php echo stripslashes($ecoles[$id_ecole_2]['nom']); ?></a></b>

								<br /><small>
								<?php 

								if (empty($concurrents[$classement['id_concurrent2']]))
									echo '';
								else if ($classement['individuel']) 
									echo stripslashes('<b>'.$concurrents[$classement['id_concurrent2']]['nom'].' '.$concurrents[$classement['id_concurrent2']]['prenom'].'</b>'); 
								else 
									echo stripslashes('Equipe : <b>'.$concurrents[$classement['id_concurrent2']]['label'].'</b>'); 
								?>
								</small>
							</td>
							<td<?php if (!empty($_GET['e']) && $id_ecole_2 == $_GET['e']) echo ' class="dark"'; ?>>
								<center><?php echo round($classement['coeff'] * 
								($p = (!$classement['ex_12'] ? 
									(!$classement['ex_23'] ? 
										APP_POINTS_2E : 
										(APP_POINTS_2E + APP_POINTS_3E) / ($classement['ex_3'] ? 3 : 2)) : 
									(!$classement['ex_23'] ? 
										(APP_POINTS_1ER + APP_POINTS_2E) / 2 : 
										(APP_POINTS_1ER + APP_POINTS_2E + APP_POINTS_3E) / ($classement['ex_3'] ? 4 : 3)))), 2).'<br /><small>'.round($p, 2).'</small>'; ?></center></td>
							<?php } else { ?><td></td><td></td><?php } ?>

							<?php if (!empty($id_ecole_3)) { ?>
							<td<?php if (!empty($_GET['e']) && $id_ecole_3 == $_GET['e']) echo ' class="dark"'; ?>>
								<img src="<?php url('assets/images/actions/'.
									($classement['ex_12'] ? ($classement['ex_23'] ? 'Gold' : 'Bronze') :  ($classement['ex_23'] ? 'Argent' : 'Bronze')).
									'.png'); ?>" alt="" />
								<b><a href="?p=sports&e=<?php echo $id_ecole_3; ?>"><?php echo stripslashes($ecoles[$id_ecole_3]['nom']); ?></a></b>

								<br /><small>
								<?php 

								if (empty($concurrents[$classement['id_concurrent3']]))
									echo '';
								else if ($classement['individuel']) 
									echo stripslashes('<b>'.$concurrents[$classement['id_concurrent3']]['nom'].' '.$concurrents[$classement['id_concurrent3']]['prenom'].'</b>'); 
								else 
									echo stripslashes('Equipe : <b>'.$concurrents[$classement['id_concurrent3']]['label'].'</b>'); 
								?>
								</small>
							</td>
							<td<?php if (!empty($_GET['e']) && $id_ecole_3 == $_GET['e']) echo ' class="dark"'; ?>>
								<center><?php echo round($classement['coeff'] * 
								($p = (!$classement['ex_23'] ? 
									APP_POINTS_3E / ($classement['ex_3'] ? 2 : 1) : 
									(!$classement['ex_12'] ? 
										(APP_POINTS_2E + APP_POINTS_3E) / ($classement['ex_3'] ? 3 : 2) : 
										(APP_POINTS_1ER + APP_POINTS_2E + APP_POINTS_3E) / ($classement['ex_3'] ? 4 : 3)))), 2).'<br /><small>'.round($p, 2).'</small>'; ?></center></td>
							<?php } else { ?><td></td><td></td><?php } ?>

							<?php if ($classement['ex_3'] && !empty($id_ecole_3ex)) { ?>
							<td<?php if (!empty($_GET['e']) && $id_ecole_3ex == $_GET['e']) echo ' class="dark"'; ?>>
								<img src="<?php url('assets/images/actions/'.($classement['ex_12'] && $classement['ex_23'] ? 'Gold' : ($classement['ex_23'] ? 'Argent' : 'Bronze')).'.png'); ?>" alt="" />
								<b><a href="?p=sports&e=<?php echo $id_ecole_3ex; ?>"><?php echo stripslashes($ecoles[$id_ecole_3ex]['nom']); ?></a></b>

								<br /><small>
								<?php 

								if (empty($concurrents[$classement['id_concurrent3ex']]))
									echo '';
								else if ($classement['individuel']) 
									echo stripslashes('<b>'.$concurrents[$classement['id_concurrent3ex']]['nom'].' '.$concurrents[$classement['id_concurrent3ex']]['prenom'].'</b>'); 
								else 
									echo stripslashes('Equipe : <b>'.$concurrents[$classement['id_concurrent3ex']]['label'].'</b>'); 
								?>
								</small>
							</td>
							<td<?php if (!empty($_GET['e']) && $id_ecole_3ex== $_GET['e']) echo ' class="dark"'; ?>>
								<center><?php echo round($classement['coeff'] * 
								($p = (!$classement['ex_23'] ? 
									APP_POINTS_3E / 2 : 
									(!$classement['ex_12'] ? 
										(APP_POINTS_2E + APP_POINTS_3E) / 3 :
										(APP_POINTS_1ER + APP_POINTS_2E + APP_POINTS_3E) / 4))), 2).'<br /><small>'.round($p, 2).'</small>'; ?></center></td>
							<?php } else { ?><td></td><td></td><?php } ?>

						</tr>

						<?php } if (!$count) { ?> 

						<tr class="vide">
							<td colspan="11">Aucun classement</td>
						</tr>

						<?php }  ?>

					</tbody>
				</table>

				<div class="alerte alerte-info">
					<div class="alerte-contenu">
						<b>Comment sont calculés les points ?</b>
						<br />
						<br />
						Pour chaque sport, le 1er obtient <?php echo APP_POINTS_1ER; ?> points, le second <?php echo APP_POINTS_2E; ?> et le troisième <?php echo APP_POINTS_3E; ?>. En cas d'ex-aequo, les points à distribuer sont sommés et divisés de manière égale. <i>(Si le 2e et le 3e sont ex-aequo alors chacun aura (<?php echo APP_POINTS_2E; ?>+<?php echo APP_POINTS_3E; ?>)/2 = <?php echo (APP_POINTS_2E + APP_POINTS_3E) / 2; ?> points)</i>.
						Ces points sont alors multipliés par un coefficient correspondant au nombre d'équipes en lice dans le cas des sports collectifs, et au nombre de sportifs dans le cas des sports individuels.
						L'ensemble des points "sportifs" sont ensuite sommés aux points supplémentaires (Développement Durable, Pompom, ...)<br /> 
						<br />
						L'Ecole Centrale de Lyon, organisatrice du Challenge, n'est pas classée.
					</div>
				</div>

				<?php } ?>


<?php

//Inclusion du pied de page
require DIR.'templates/_footer.php';
