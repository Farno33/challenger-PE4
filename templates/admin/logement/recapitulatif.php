<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* templates/admin/logement/recapitulatif.php **************/
/* Template des pour le recapitulatif **********************/
/* *********************************************************/
/* Dernière modification : le 16/02/15 *********************/
/* *********************************************************/


//Inclusion de l'entête de page
require DIR.'templates/admin/_header_admin.php';

?>
				<style>
				td small { color:red;}
				</style>

				<h2>Récapitulatif pour le Logement</h2>
				
				<?php $placesDispo = 0; ?>
				<table class="table-small">
					<thead>
						<tr>
							<th>Total</th>
							<th style="width:160px"></th>

							<?php foreach(str_split('UVTXABC') as $batiment) { ?>

							<th><?php echo $batiment; ?></th>

							<?php } ?>

						</tr>
					</thead>

					<tbody>
						<tr>
							<td><?php echo $total['nb_chambres']; ?></td>
							<th>Chambres</th>

							<?php foreach(str_split('UVTXABC') as $batiment) { ?>

							<td><?php echo $batiments[$batiment]['nb_chambres']; ?></td>

							<?php } ?>

						</tr>

						<tr>
							<td><?php echo ($s = $total['nb_lachee'] + $total['nb_amies'] + $total['nb_amiesplus']).' <small>'.round($s / $total['nb_chambres'] * 100).'%</small>'; ?></td>
							<th>Disponibles</th>

							<?php foreach(str_split('UVTXABC') as $batiment) { ?>

							<td><?php echo ($s = $batiments[$batiment]['nb_lachee'] + $batiments[$batiment]['nb_amies'] + $batiments[$batiment]['nb_amiesplus']).
								' <small>'.round($s / $total['nb_chambres'] * 100).'%</small>'; ?></td>

							<?php } ?>

						</tr>

						<tr>
							<td><?php echo ($s = $total['nb_lachee']) . ' <small>'.round($s / $total['nb_chambres'] * 100).'%</small>'; ?></td>
							<th>"Lachées"</th>

							<?php foreach(str_split('UVTXABC') as $batiment) { ?>

							<td><?php echo ($s = $batiments[$batiment]['nb_lachee']) . ' <small>'.round($s / $total['nb_chambres'] * 100).'%</small>'; ?></td>

							<?php } ?>

						</tr>

						<tr>
							<td><?php echo ($s = $total['nb_amies']) . ' <small>'.round($s / $total['nb_chambres'] * 100).'%</small>'; ?></td>
							<th>"Pour amies"</th>

							<?php foreach(str_split('UVTXABC') as $batiment) { ?>

							<td><?php echo ($s = $batiments[$batiment]['nb_amies']) . ' <small>'.round($s / $total['nb_chambres'] * 100).'%</small>'; ?></td>

							<?php } ?>

						</tr>

						<tr>
							<td><?php echo ($s = $total['nb_amiesplus']) . ' <small>'.round($s / $total['nb_chambres'] * 100).'%</small>'; ?></td>
							<th>"Avec amies"</th>

							<?php foreach(str_split('UVTXABC') as $batiment) { ?>

							<td><?php echo ($s = $batiments[$batiment]['nb_amiesplus']) . ' <small>'.round($s / $total['nb_chambres'] * 100).'%</small>'; ?></td>

							<?php } ?>

						</tr>
						
						<tr><th colspan="<?php echo 2+strlen('UVTXABC'); ?>"></th></tr>

						<tr>
							<td colspan="<?php echo 2 + strlen('UVTXABC'); ?>">
								<center>Il y a <b><?php echo $total['nb_places'] - $total['nb_filles']; ?></b> places restantes
									sur les <b><?php echo $total['nb_places']; ?></b> disponibles.</center>
							</td>
						</tr>

					</tbody>
				</table>


				<table class="table-small">
					<thead>
						<tr>
							<td colspan="<?php echo 2 + strlen('UVTXABC'); ?>"><center>Max théorique : 200 personnes par bâtiment</center></td>
						</tr>
						
						<tr>
							<th>Total</th>
							<th style="width:160px"></th>

							<?php foreach(str_split('UVTXABC') as $batiment) { ?>

							<th><?php echo $batiment; ?></th>

							<?php } ?>

						</tr>
					</thead>

					<tbody>
						<tr>
							<td><?php echo ($s = $total['nb_chambres'] + $total['nb_filles']).' <small>'.round($s / strlen('UVTXABC') / 200 * 100).'%</small>'; ?></td>
							<th>Nb pers.</th>

							<?php foreach(str_split('UVTXABC') as $batiment) { ?>

							<td><?php echo ($s = $batiments[$batiment]['nb_chambres'] + $batiments[$batiment]['nb_filles']).' <small>'.round($s / 200 * 100).'%</small>'; ?></td>

							<?php } ?>

						</tr>

						<tr>
							<td><?php echo ($s = $total['nb_chambres'] + $total['nb_filles'] - $total['nb_lachee'] - $total['nb_amies']). /* on ne met pas amiesplus */
								' <small>'.round($s / strlen('UVTXABC') / 200 * 100).'%</small>'; ?></td>
							<th>Nb pers. <small>(strass)</small></th>

							<?php foreach(str_split('UVTXABC') as $batiment) { ?>

							<td><?php echo ($s = $batiments[$batiment]['nb_chambres'] + $batiments[$batiment]['nb_filles'] - $batiments[$batiment]['nb_lachee'] - $batiments[$batiment]['nb_amies']).
							' <small>'.round($s / 200 * 100).'%</small>'; ?></td>

							<?php } ?>

						</tr>

						<tr><th colspan="<?php echo 2+strlen('UVTXABC'); ?>"><small>Nb pers. (strass) = Nb chambres + Nb filles - Nb "lachees" - Nb "pour amies"</small></th></tr>

						<tr>
							<td><?php echo ($s = $total['nb_places']).' <small>'.round($s / strlen('UVTXABC') / 200 * 100).'%</small>'; ?></td>
							<th>Places</th>

							<?php foreach(str_split('UVTXABC') as $batiment) { ?>

							<td><?php echo ($s = (int) $batiments[$batiment]['nb_places']).' <small>'.round($s / 200 * 100).'%</small>'; ?></td>

							<?php } ?>

						</tr>

						<tr>
							<td><?php echo ($s = $total['nb_filles']).' <small>'.round($s / strlen('UVTXABC') / 200 * 100).'%</small>'; ?></td>
							<th>Nb filles</th>

							<?php foreach(str_split('UVTXABC') as $batiment) { ?>

							<td><?php echo ($s = $batiments[$batiment]['nb_filles']).' <small>'.round($s / 200 * 100).'%</small>'; ?></td>

							<?php } ?>

						</tr>

						<tr>
							<td><?php echo ($s = $total['nb_places'] - $total['nb_filles']).' <small>'.round($s / strlen('UVTXABC') / 200 * 100).'%</small>'; ?></td>
							<th>Places restantes</th>

							<?php foreach(str_split('UVTXABC') as $batiment) { ?>

							<td><?php echo ($s = $batiments[$batiment]['nb_places'] - $batiments[$batiment]['nb_filles']).' <small>'.round($s / 200 * 100).'%</small>'; ?></td>

							<?php } ?>

						</tr>

						<tr><th colspan="<?php echo 2+strlen('UVTXABC'); ?>"></th></tr>

						<tr>
							<td colspan="<?php echo 2 + strlen('UVTXABC'); ?>"><center>Il y a <b><?php echo $total['nb_filles']; ?></b> filles logées 
								sur <b><?php echo $nb_filles['tot']; ?></b>, soit <b><?php echo round($total['nb_filles'] / max($nb_filles['tot'], 1) * 100); ?></b>%.</center></td>
						</tr>

					</tbody>
				</table>

<?php

//Inclusion du pied de page
require DIR.'templates/_footer.php';
