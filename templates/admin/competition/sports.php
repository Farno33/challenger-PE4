<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* templates/admin/competition/sports.php ******************/
/* Template des sports de la compétition *******************/
/* *********************************************************/
/* Dernière modification : le 13/12/14 *********************/
/* *********************************************************/

$cql = 'T|s:sports:|u:utilisateurs:
F|s.sport|s.sexe|s.quota_inscription|u.nom|u.prenom|s.groupe_multiple|s.individuel';


//Inclusion de l'entête de page
require DIR.'templates/admin/_header_admin.php';

?>
				
				<form method="post" action="<?php url('admin/module/competition/extract'); ?>">
					<h2>
						Liste des Sports 
						<input type="submit" value="CQL" />
					</h2>
					<input type="hidden" name="cql" value="<?php echo $cql; ?>" />
				</form>

				<?php
				if (isset($add) ||
					isset($modify) ||
					!empty($delete)) {
				?>

				<div class="alerte alerte-<?php echo !empty($add) || !empty($modify) || !empty($delete) ? 'success' : 'erreur'; ?>">
					<div class="alerte-contenu">
						<?php
						if (!empty($modify)) echo 'Le sport a bien été édité';
						else if (!empty($delete)) echo 'Le sport a bien été supprimé';
						else if (!empty($add)) echo 'Le sport a bien été ajouté';
						else echo 'Des équipes existent pour ce sport, impossible de supprimer';
						?>
					</div>
				</div>

				<?php } ?>

				<form method="post">
					<table class="table-small">
						<thead>
							<tr class="form">
								<td><input type="text" name="sport[]" placeholder="Sport..." /></td>
								<td>
									<select name="sexe[]" data-sexe="true">
										<option value="m">Mixte</option>
										<option value="h">Masculin</option>
										<option value="f">Féminin</option>
									</select>
								</td>
								<td><input type="number" min="0" data-sum="0" name="inscriptions[]" placeholder="Quota Inscriptions..." /></td>
								<td>
									<select name="respo[]">
										<option value="" selected>Respo...</option>

										<?php foreach ($respos as $id => $respo) { ?>

										<option value="<?php echo $id; ?>"><?php 
											echo stripslashes(strtoupper($respo['nom']).' '.$respo['prenom']); ?></option>

										<?php } ?>

									</select>
								</td>
								<td>
									<select name="groupe[]" data-groupe="true">
										<option value="0">Non lié</option>
										
										<?php for ($i = 1; $i <= $groupeMax + 1; $i++) { ?>

										<option value="<?php echo $i; ?>">Groupe <?php echo $i; ?></option>

										<?php } ?>

									</select>
								</td>
								<td>
									<input type="checkbox" name="individuel[]" id="form-individuel-0" value="0" />
									<label for="form-individuel-0"></label>
								</td>
								<td class="actions">
									<button type="submit" name="add">
										<img src="<?php url('assets/images/actions/add.png'); ?>" alt="Add" />
									</button>
									<input type="hidden" name="id[]" />
								</td>
							</tr>

							<tr>
								<th>Sport</th>
								<th style="width:90px">Sexe</th>
								<th style="width:100px"><small>Q. Inscriptions</small></th>
								<th>Responsable</th>
								<th style="width:90px">Multiples</th>
								<th style="width:60px"><small>Individuel</small></th>
								<th>Actions</th>
							</tr>
						</thead>

						<tbody>

							<?php if (!count($sports)) { ?> 

							<tr class="vide">
								<td colspan="7">Aucun sport</td>
							</tr>

							<?php } foreach ($sports as $sid => $sport) { ?>

							<tr class="form">
								<td><input type="text" name="sport[]" value="<?php echo stripslashes($sport['sport']); ?>" /></td>
								<td>
									<select name="sexe[]" data-sexe="true">
										<option value="m"<?php if ($sport['sexe'] == 'm') echo ' selected'; ?>>Mixte</option>
										<option value="h"<?php if ($sport['sexe'] == 'h') echo ' selected'; ?>>Masculin</option>
										<option value="f"<?php if ($sport['sexe'] == 'f') echo ' selected'; ?>>Féminin</option>
									</select>
								</td>
								<td><input type="number" min="<?php echo (int) $sport['nb_inscriptions']; ?>" data-sum="<?php echo (int) $sport['nb_inscriptions']; ?>" name="inscriptions[]" value="<?php echo $sport['quota_inscription']; ?>"  /></td>
								<td>
									<select name="respo[]">										
										<?php foreach ($respos as $id => $respo) { ?>

										<option <?php if ($id == $sport['id_respo']) echo 'selected '; ?>value="<?php echo $id; ?>"><?php 
											echo stripslashes(strtoupper($respo['nom']).' '.$respo['prenom']); ?></option>

										<?php } ?>

									</select>
								</td>
								<td>
									<select name="groupe[]" data-groupe="true">
										<option value="0"<?php if ($sport['groupe_multiple'] == 0) echo ' selected'; ?>>Non lié</option>
										
										<?php for ($i = 1; $i <= $groupeMax + (in_array($sid, $sportsGroupes[$groupeMax]) && count($sportsGroupes[$groupeMax]) == 1 ? 0 : 1); $i++) { ?>

										<option value="<?php echo $i; ?>"<?php if ($sport['groupe_multiple'] == $i) echo ' selected'; ?>>Groupe <?php echo $i; ?></option>

										<?php } ?>

									</select>
								</td>
								<td>
									<input type="checkbox" name="individuel[]" id="form-individuel-<?php echo $sport['id']; ?>" value="<?php echo $sport['id']; ?>" <?php if ($sport['individuel']) echo 'checked'; ?> />
									<label for="form-individuel-<?php echo $sport['id']; ?>"></label>
								</td>
								<td class="actions content" style="text-align:left">
									<button type="submit" name="listing" value="<?php echo (int) $sid; ?>">
										<img src="<?php url('assets/images/actions/list.png'); ?>" alt="Listing" />
									</button>

									<button type="submit" name="edit" value="<?php echo (int) $sid; ?>">
										<img src="<?php url('assets/images/actions/edit.png'); ?>" alt="Edit" />
									</button>

									<?php if (empty($sport['nb_ecoles'])) { ?>

									<button type="submit" name="delete" value="<?php echo (int) $sid; ?>" />
										<img src="<?php url('assets/images/actions/delete.png'); ?>" alt="Delete" />
									</button>

									<?php } ?>

									<input type="hidden" name="id[]" value="<?php echo (int) $sid; ?>" />
								</td>
							</tr>

							<?php } ?>

						</tbody>
					</table>
				</form>

				<script type="text/javascript">
				$(function() {
					$speed =  <?php echo APP_SPEED_ERROR; ?>;
					$couleurs = ['#f4ad7b', '#5E7AA9', '#C262A0', '#C6E774', '#F4F17B', '#ED7782', '#52A196', '#7163B0', '#8A5CAB', '#7ACF68', '#F4DD7B', '#F4C97B'];

					$('select[data-sexe]').on('change', function() {
						$(this).parent().removeClass('sexe-h sexe-f sexe-m').addClass('sexe-'+$(this).val());
					}).each(function() {
						$(this).change();
					});

					$('select[data-groupe]').on('change', function() {
						$color = parseInt($(this).val()) > 0 ? $couleurs[$(this).val() % $couleurs.length] : 'none';
							$(this).parent().css('background-color', $color);
							$(this).css('background-color', $color);
					}).each(function() {
						$(this).change();
					});


			    	$analysis = function(elem, event, force) {
			            if (event.keyCode == 13 || force) {
			                event.preventDefault();
			              	$parent = elem.parent().parent();
			              	$first = $parent.children('td:first');
			              	$sport = $first.children('input');
			              	$sexe = $first.next().children('select');
			              	$inscrip = $first.next().next().children('input');
			              	$respo = $first.next().next().next().children('select');
			              	$erreur = false;

			                if (!$sport.val()) {
			                	$erreur = true;
			                	$sport.addClass('form-error').removeClass('form-error', $speed).focus();
			               	}

			               	if ($.inArray($sexe.val(), ['h', 'f', 'm']) < 0) {
			                	$erreur = true;
			                	$sexe.addClass('form-error').removeClass('form-error', $speed).focus();
			               	}

			               	if (!$.isNumeric($respo.val()) ||
			                	$respo.val() <= 0 ||
			                	Math.floor($respo.val()) != $respo.val()) {
			                	$erreur = true;
			                	$respo.addClass('form-error').removeClass('form-error', $speed).focus();
			               	}

			               	if ($inscrip.val() && (
				               		!$.isNumeric($inscrip.val()) ||
				                	$inscrip.val() < parseInt($inscrip.data('sum')) ||
				                	Math.floor($inscrip.val()) != $inscrip.val())) {
			                	$erreur = true;
			                	$inscrip.addClass('form-error').removeClass('form-error', $speed).focus();
			               	}
			                
			                if (!$erreur)
			                	$parent.children('.actions').children('button:first-of-type').unbind('click').click();   
			           
			            }
			        };

					$('td input[type=text], td input[type=number], td input[type=checkbox], td select, td.actions button:first-of-type').bind('keypress', function(event) {
						$analysis($(this), event, false) });
					$('td.actions button:first-of-type').bind('click', function(event) {
						$analysis($(this), event, true) });	
				});
				</script>


<?php

//Inclusion du pied de page
require DIR.'templates/_footer.php';
