<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* templates/admin/ecoles/tarification.php *****************/
/* Template de la gestion des tarifs ***********************/
/* *********************************************************/
/* Dernière modification : le 21/11/14 *********************/
/* *********************************************************/

$cql = 'T|t:tarifs:
TL|s:sports:|e:ecoles:
F|t.sportif|t.for_pompom|t.for_cameraman|t.for_fanfaron|t.nom|t.logement|t.tarif|s.sport|s.sexe|e.nom
C|t.ecole_lyonnaise:'.(int) !empty($_GET['e']).'|t.format_long:'.(int) !empty($_GET['l']);

//Inclusion de l'entête de page
require DIR.'templates/admin/_header_admin.php';

?>
				<style>
				.subnav li, 
				.subnav li ul { width:200px !important;}
				</style>
				
				<form method="post" action="<?php url('admin/module/competition/extract'); ?>">
				<nav class="subnav">
					<h2>
						Liste des Tarifications
						<?php if (isset($_GET['e']) && isset($_GET['l'])) { ?><input type="submit" value="CQL" /><?php } ?>
					</h2>

					<input type="hidden" name="cql" value="<?php echo $cql; ?>" />
	 					
					<ul>
						<li><a href="<?php url('admin/module/ecoles/tarification'); ?>">Sports spéciaux</a></li>
						<li>
							<span>Ecoles Non-Lyonnaises</span>
							<ul>
								<li><a href="?e=0&l=0">Format court</a></li>
								<li><a href="?e=0&l=1">Format long</a></li>
							</ul>
						<li>
							<span>Ecoles Lyonnaises</span>
							<ul>
								<li><a href="?e=1&l=0">Format court</a></li>
								<li><a href="?e=1&l=1">Format long</a></li>
							</ul>
						</li>
					</ul>
				</nav>
				</form>

				<?php
				if (isset($add) ||
					isset($modify) ||
					!empty($delete)) {
				?>

				<div class="alerte alerte-<?php echo !empty($add) || !empty($modify) || !empty($delete) ? 'success' : 'erreur'; ?>">
					<div class="alerte-contenu">
						<?php
						if (!empty($modify)) echo 'Le tarif a bien été édité';
						else if (!empty($delete)) echo 'Le tarif a bien été supprimé';
						else if (!empty($add)) echo 'Le tarif a bien été ajouté';
						?>
					</div>
				</div>

				<?php } 
				if (isset($_GET['e']) && 
					isset($_GET['l']) &&
					in_array($_GET['e'], ['0', '1']) &&
					in_array($_GET['l'], ['0', '1'])) { 
				?>

				<form method="post">
				<h3>Tarifs des <u>Ecoles <?php if (!$_GET['e']) echo 'Non-'; ?>Lyonnaises</u> / <u>Format <?php echo $_GET['l'] ? 'Long' : 'Court'; ?></u></h3>
				<table>
					<thead>
						<tr class="form">
							<td>
								<input type="checkbox" name="sportif[]" id="form-sportif" value="0" />
								<label for="form-sportif"></label>
							</td>
							<td>
								<select name="for_pompom[]" data-extra="pompom">
									<option value="yes">Oui</option>
									<option value="or" selected>&nbsp;</option>
									<option value="no">Non</option>
								</select>
							</td>
							<td>
								<select name="for_cameraman[]" data-extra="video">
									<option value="yes">Oui</option>
									<option value="or" selected>&nbsp;</option>
									<option value="no">Non</option>
								</select>
							</td>
							<td>
								<select name="for_fanfaron[]" data-extra="fanfaron">
									<option value="yes">Oui</option>
									<option value="or" selected>&nbsp;</option>
									<option value="no">Non</option>
								</select>
							</td>
							<td><input type="text" name="nom[]" placeholder="Nom..." /></td>
							<td><textarea name="description[]" placeholder="Description..."></textarea></td>
							<td>
								<input type="checkbox" name="logement[]" id="form-logement" value="0" />
								<label class="package" for="form-logement"></label>
							</td>
							<td><input type="number" step="any" min="0" name="montant[]" placeholder="Montant..." /></td>
							<td>
								<select name="special[]">
									<option value="" selected>&nbsp;</option>

									<?php foreach ($sports as $sid => $sport) { ?>

									<option value="<?php echo $sid; ?>"><?php echo stripslashes($sport['sport']).' '.strip_tags(printSexe($sport['sexe'])); ?></option>

									<?php } ?>

								</select>
							</td>
							<td>
								<select name="ecole_for[]">
									<option value="" selected>Toutes</option>

									<?php 
									foreach ($ecoles as $eid => $ecole) { 
										if ($ecole['ecole_lyonnaise'] != $_GET['e'])
											continue; 
										?>

									<option value="<?php echo $eid; ?>"><?php echo stripslashes($ecole['nom']); ?></option>

									<?php } ?>

								</select>
							</td>
							<td class="actions">
								<button type="submit" name="add">
									<img src="<?php url('assets/images/actions/add.png'); ?>" alt="Add" />
								</button>

								<input type="hidden" name="ecole_lyonnaise[]" value="<?php echo $_GET['e']; ?>" /> 
								<input type="hidden" name="long[]" value="<?php echo $_GET['l']; ?>" /> 
								<input type="hidden" name="id[]" />
							</td>
						</tr>


						<tr>
							<th style="width:70px"><small>Sportif</small></th>
							<th style="width:70px"><small>P.</small></th>
							<th style="width:70px"><small>C.</small></th>
							<th style="width:70px"><small>F.</small></th>
							<th>Nom</th>
							<th>Description</th>
							<th>Logement</th>
							<th>Montant</th>
							<th style="width:110px"><small>Sport Spécial...</small></th>
							<th style="width:110px"><small>...Pour Ecole</small></th>
							<th class="actions">Actions</th>
						</tr>
					</thead>

					<tbody>

						<?php if (empty($tarifs_groups[$_GET['e'].'_'.$_GET['l']])) { ?> 

						<tr class="vide">
							<td colspan="11">Aucun tarif</td>
						</tr>

						<?php } else foreach ($tarifs_groups[$_GET['e'].'_'.$_GET['l']] as $tarif) { ?>

						<tr class="form">
							<td>
								<input type="checkbox" name="sportif[]" id="form-sportif-<?php echo $tarif['id']; ?>" value="<?php echo $tarif['id']; ?>" <?php if ($tarif['sportif']) echo 'checked '; ?>/>
								<label for="form-sportif-<?php echo $tarif['id']; ?>"></label>
							</td>

							<td>
								<select name="for_pompom[]" data-extra="pompom">
									<option value="yes"<?php if ($tarif['for_pompom'] == 'yes') echo ' selected'; ?>>Oui</option>
									<option value="or"<?php if (!in_array($tarif['for_pompom'], ['yes', 'no'])) echo ' selected'; ?>>&nbsp;</option>
									<option value="no"<?php if ($tarif['for_pompom'] == 'no') echo ' selected'; ?>>Non</option>
								</select>
							</td>
							<td>
								<select name="for_cameraman[]" data-extra="video">
									<option value="yes"<?php if ($tarif['for_cameraman'] == 'yes') echo ' selected'; ?>>Oui</option>
									<option value="or"<?php if (!in_array($tarif['for_cameraman'], ['yes', 'no'])) echo ' selected'; ?>>&nbsp;</option>
									<option value="no"<?php if ($tarif['for_cameraman'] == 'no') echo ' selected'; ?>>Non</option>
								</select>
							</td>
							<td>
								<select name="for_fanfaron[]" data-extra="fanfaron">
									<option value="yes"<?php if ($tarif['for_fanfaron'] == 'yes') echo ' selected'; ?>>Oui</option>
									<option value="or"<?php if (!in_array($tarif['for_fanfaron'], ['yes', 'no'])) echo ' selected'; ?>>&nbsp;</option>
									<option value="no"<?php if ($tarif['for_fanfaron'] == 'no') echo ' selected'; ?>>Non</option>
								</select>
							</td>

							<td><input type="text" name="nom[]" value="<?php echo stripslashes($tarif['nom']); ?>" /></td>
							<td><textarea name="description[]"><?php echo stripslashes($tarif['description']); ?></textarea></td>
							<td>
								<input type="checkbox" name="logement[]" id="form-logement-<?php echo $tarif['id']; ?>" value="<?php echo $tarif['id']; ?>" <?php if (!empty($tarif['logement'])) echo 'checked '; ?>/>
								<label class="package" for="form-logement-<?php echo $tarif['id']; ?>"></label>
							</td>
							<td><input type="number" step="any" min="0" name="montant[]" value="<?php echo sprintf('%.2f', (float) $tarif['tarif']); ?>" /></td>
							<td>
								<select name="special[]">
									<option value="">&nbsp;</option>

									<?php foreach ($sports as $sid => $sport) { ?>

									<option value="<?php echo $sid; ?>"<?php if ($tarif['id_sport_special'] == $sid) echo ' selected'; ?>><?php echo stripslashes($sport['sport']).' '.strip_tags(printSexe($sport['sexe'])); ?></option>

									<?php } ?>

								</select>
							</td>
							<td>
								<select name="ecole_for[]">
									<option value="">Toutes</option>

									<?php
									foreach ($ecoles as $eid => $ecole) {
										if ($ecole['ecole_lyonnaise'] != $tarif['ecole_lyonnaise'])
											continue; 
									 ?>

									<option value="<?php echo $eid; ?>"<?php if ($tarif['id_ecole_for_special'] == $eid) echo ' selected'; ?>><?php echo stripslashes($ecole['nom']); ?></option>

									<?php } ?>

								</select>
							</td>
							<td class="actions content" style="text-align:left">
																
								<button type="submit" name="listing" value="<?php echo stripslashes($tarif['id']); ?>">
									<img src="<?php url('assets/images/actions/list.png'); ?>" alt="Listing" />
								</button>

								<?php //if (empty($tarif['teid'])) { ?>

								<button type="submit" name="edit" value="<?php echo stripslashes($tarif['id']); ?>">
									<img src="<?php url('assets/images/actions/edit.png'); ?>" alt="Edit" />
								</button>

								<button type="submit" name="delete" value="<?php echo stripslashes($tarif['id']); ?>" />
									<img src="<?php url('assets/images/actions/delete.png'); ?>" alt="Delete" />
								</button>

								<?php //} ?>

								<input type="hidden" name="ecole_lyonnaise[]" value="<?php echo $_GET['e']; ?>" /> 
								<input type="hidden" name="long[]" value="<?php echo $_GET['l']; ?>" /> 
								<input type="hidden" name="id[]" value="<?php echo stripslashes($tarif['id']); ?>" />
							</td>
						</tr>

						<?php } ?>

					</tbody>
				</table>
				</form>



				<script type="text/javascript">
				$(function() {
					$speed =  <?php echo APP_SPEED_ERROR; ?>;

					$('select[data-extra]').on('change', function() {
						$(this).parent().removeClass('extra-'+$(this).data('extra')+' extra-'+$(this).data('extra')+'-or').addClass($(this).val() != 'no' ? 'extra-'+$(this).data('extra') + ($(this).val() == 'or' ? '-or' : '') : '');
					}).each(function() {
						$(this).change();
					});

					$analysis = function(elem, event, force) {
			            if (event.keyCode == 13 || force) {
			                event.preventDefault();
			              	$parent = elem.parent().parent();
			              	$first = $parent.children('td:first');
			  				$sportif = $first.children('input');
			  				$for_pompom = $first.next().children('select');
			  				$for_cameraman = $first.next().next().children('select');
			  				$for_fanfaron = $first.next().next().next().children('select');
			  				$nom = $first.next().next().next().next().children('input');
			  				$description = $first.next().next().next().next().next().children('textarea');
			  				$logement = $first.next().next().next().next().next().next().children('textarea');
			  				$montant = $first.next().next().next().next().next().next().next().children('input');
			  				$special = $first.next().next().next().next().next().next().next().next().children('select');
			  				$ecole_for = $first.next().next().next().next().next().next().next().next().next().children('select');

			                if ($.inArray($for_pompom.val(), ['yes', 'or', 'no']) < 0)
			                	$for_pompom.addClass('form-error').removeClass('form-error', $speed).focus();

			                if ($.inArray($for_cameraman.val(), ['yes', 'or', 'no']) < 0)
			                	$for_cameraman.addClass('form-error').removeClass('form-error', $speed).focus();

			                if ($.inArray($for_fanfaron.val(), ['yes', 'or', 'no']) < 0)
			                	$for_fanfaron.addClass('form-error').removeClass('form-error', $speed).focus();

			                if (!$nom.val().trim())
			                	$nom.addClass('form-error').removeClass('form-error', $speed).focus();

			                if (!$description.val().trim())
			                	$description.addClass('form-error').removeClass('form-error', $speed).focus();

			                if (!$.isNumeric($montant.val()) ||
			                	$montant.val() < 0)
			                	$montant.addClass('form-error').removeClass('form-error', $speed).focus();

			                if ($special.val() && (
				                	!$.isNumeric($special.val()) ||
				                	$special.val() < 0 ||
				                	Math.floor($special.val()) != $special.val()))
			                	$special.addClass('form-error').removeClass('form-error', $speed).focus();

			                if ($ecole_for.val() && (
				                	!$.isNumeric($ecole_for.val()) ||
				                	$ecole_for.val() < 0 ||
				                	Math.floor($ecole_for.val()) != $ecole_for.val()))
			                	$ecole_for.addClass('form-error').removeClass('form-error', $speed).focus();

			                if ($.inArray($for_pompom.val(), ['yes', 'or', 'no']) >= 0 &&
			                	$.inArray($for_cameraman.val(), ['yes', 'or', 'no']) >= 0 &&
			                	$.inArray($for_fanfaron.val(), ['yes', 'or', 'no']) >= 0 &&
			                	$nom.val().trim() &&
			                	$description.val().trim() &&
			                	$.isNumeric($montant.val()) &&
			                	$montant.val() >= 0 && (
			                		!$special.val() || 
				                	$.isNumeric($special.val()) &&
				                	$special.val() >= 0 &&
				                	Math.floor($special.val()) == $special.val()) && (
			                		!$ecole_for.val() || 
				                	$.isNumeric($ecole_for.val()) &&
				                	$ecole_for.val() >= 0 &&
				                	Math.floor($ecole_for.val()) == $ecole_for.val()))
			                	$parent.children('.actions').children('button:first-of-type').unbind('click').click();   
			           
			            }
			        };

					$('td input[type=text], td input[type=number], td input[type=checkbox], td select, td.actions button:first-of-type').bind('keypress', function(event) {
						$analysis($(this), event, false) });
					$('td.actions button:first-of-type').bind('click', function(event) {
						$analysis($(this), event, true) });	

					document.onselectstart = function() { return false; };


			   	});
				</script>

				<?php } else { ?>

				<div class="alerte alerte-attention">
					<div class="alerte-contenu">
						Cette page récapitule la portée des sports spéciaux tels qu'ils sont définis par les tarifs sur les sous-onglets de ce présent module.
						<b>Un sport spécial n'est sélectionnable que si un tarif y donnant accès est sélectionné</b>, il faut donc faire attention à ne pas choisir la portée trop rapidement.
					</div>
				</div>


				<table class="table-small">
					<thead>
						<tr>
							<th>Sport</th>
							<th>Portée</th>
						</tr>
					</thead>

					<tbody>

					<?php if (empty($sports_speciaux)) { ?>
					<tr class="vide">
						<td colspan="2">Aucun sport spécial</td>
					</tr>
					<?php } foreach ($sports_speciaux as $sport) {
						$portee = array_unique(explode(',', $sport['ecoles_for_special']));
					 ?>
						<tr>
							<td><?php echo stripslashes($sport['sport']).' '.printSexe($sport['sexe']); ?></td>
							<td><?php if (in_array('0', $portee)) echo '<b>Toutes les écoles</b>';
							else {
								$ecs = [];
								foreach ($portee as $eid) {
									if (empty($eid) || empty($ecoles[$eid]))
										continue; 

									$ecs[] = $ecoles[$eid]['nom'];
								} 

								echo implode(', ', $ecs); 
							} ?></td>
						</tr>
					<?php } ?>
					</tbody>
				</table>

				<?php } ?>

<?php

//Inclusion du pied de page
require DIR.'templates/_footer.php';
