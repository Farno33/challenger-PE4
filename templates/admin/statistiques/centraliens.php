<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* templates/admin/statistiques/ecoles.php *****************/
/* Template des stats sur les Ecoles ***********************/
/* *********************************************************/
/* Dernière modification : le 23/01/15 *********************/
/* *********************************************************/


//Inclusion de l'entête de page
require DIR.'templates/admin/_header_admin.php';

?>
			
				<h2>Données sur les Centraliens</h2>
				<a class="excel" href="?excel">Télécharger en XLSX</a>

				<style>
				tr:not(.empty), tr:not(.empty) td { background:var(--soft-red) !important; }
				tr.paye, tr.paye td { background:var(--soft-as-hell-green) !important; }
				</style>

				<table class="table-small" style="width:1000px">
					<thead>
						<tr>
							<th>Nom</th>
							<th>Prénom</th>
							<th style="width:60px"><small>Soirée</small></th>
							<!--<th style="width:60px"><small>PF&nbsp;Ven.</small></th>
							<th style="width:60px"><small>Vegetarien</small></th>-->
							<th style="width:60px"><small>Full package</small></th>
							<th style="width:60px"><small>T-Shirt</small></th>
							<th style="width:60px"><small>Gourde</small></th>
							<th style="width:80px"><small>Tombola</small></th>
							<th style="width:70px"><small>Supplément</small></th>
							<th style="width:100px"><small>Total</small></th>
							<th style="width:60px">Payé</th>
						</tr>
					</thead>

					<tbody>

						<?php foreach ($centraliens as $cid => $centralien) { ?>

						<tr class="clickme centralien<?php if ($centralien['paye']) echo ' paye'; ?>">
							<td><?php echo stripslashes($centralien['nom']); ?></td>
							<td><?php echo stripslashes($centralien['prenom']); ?></td>
							<td style="padding:0px">													
								<input type="checkbox" <?php if ($centralien['soiree']) echo 'checked '; ?>/>
								<label></label>
							</td>
							<td style="padding:0px">													
								<input type="checkbox" <?php if ($centralien['pfsamedi']) echo 'checked '; ?>/>
								<label></label>
							</td>
							<!--<td style="padding:0px">													
								<input type="checkbox" <?//php if ($centralien['pfvendredi']) echo 'checked '; ?>/>
								<label></label>
							</td>
							<td style="padding:0px">													
								<input type="checkbox" <?//php if ($centralien['vegetarien']) echo 'checked '; ?>/>
								<label></label>
							</td>-->
							<td style="padding:0px" <?php if ((!empty($centralien['tarif']) && $centralien['tarif'] != 0) || $centralien['tshirt']) echo ' title='.($centralien['tshirt'] ? 'Acheté' : 'Sportif'); ?>>													
								<input type="checkbox" <?php if ((!empty($centralien['tarif']) && $centralien['tarif'] != 0) || $centralien['tshirt']) echo ' checked '; ?>/>
								<label></label>
							</td>
							<td style="padding:0px">													
								<input type="checkbox" <?php if ($centralien['gourde']) echo 'checked '; ?>/>
								<label></label>
							</td>
							<td>													
								<center><?php echo $centralien['tombola'] ? ((int) $centralien['tombola'].'='.printMoney($centralien['tombola_p'])) : ''; ?></center>
							</td>
							<td title="<?php echo $centralien['tnom'] ? $centralien['tnom'] : ''; ?>">													
								<center><?php echo $centralien['tarif'] ? printMoney($centralien['tarif']) : ''; ?></center>
							</td>
							<td>													
								<center><b><?php echo printMoney($centralien['total']); ?></b></center>
							</td>
							<td style="padding:0px">													
								<input type="checkbox" id="c<?php echo $cid; ?>" data-cid="<?php echo $cid; ?>" <?php if ($centralien['paye']) echo 'checked '; ?>/>
								<label for="c<?php echo $cid; ?>"></label>
							</td>
						</tr>

						<?php } if (!count($centraliens)) { ?> 

						<tr class="vide">
							<td colspan="10">Aucun centralien</td>
						</tr>

						<?php } else { ?>

						<tr>
							<th colspan="10"></th>
						</tr>

						<tr class="empty">
							<td></td>
							<td></td>
							<td><center><?php echo $soirees; ?></center></td>
							<!--<td><center><?//php echo $pfvendredis; ?></center></td>
							<td><center><?//php echo $vegetariens; ?></center></td>-->
							<td><center><?php echo $pfsamedis; ?></center></td>
							<td><center><?php echo $tshirts; ?></center></td>
							<td><center><?php echo $gourdes; ?></center></td>
							<td><center><?php echo $tombolas."=".printMoney($tombolas_p); ?></center></td>
							<td><center><?php echo printMoney($supplements); ?></center></td>
							<td><center><b><?php echo printMoney($totaux); ?></b></center></td>
							<td title="payé / total payant (+inscriptions vides)"><center><b id="payes"><?php echo $payes; ?></b> / <b><?php echo count($centraliens).' (+'.$insciptionVide.')'; ?></b></center></td>
						</tr>

						<?php } ?>

					</tbody>
				</table>

				<script type="text/javascript">
				$(function() {
					$('tr.centralien td:last-of-type input[type=checkbox]').change(function() {
						if ($(this).is(':checked'))
							$(this).parent().parent().addClass('paye');
						else
							$(this).parent().parent().removeClass('paye');

						$('#payes').html(parseInt($('#payes').html()) + ($(this).is(':checked') ? 1 : -1));
						$.ajax({
							url: "<?php url('admin/module/statistiques/centraliens'); ?>",
						  	method: "POST",
						  	cache: false,
							dataType: "json",
							data: {cid: $(this).data('cid')}
						});
					});
				});
				</script>


<?php

//Inclusion du pied de page
require DIR.'templates/_footer.php';
