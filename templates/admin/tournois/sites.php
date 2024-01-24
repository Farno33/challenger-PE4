<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* templates/admin/droits/liste.php ************************/
/* Template de la liste des organisateurs ******************/
/* *********************************************************/
/* Dernière modification : le 24/11/14 *********************/
/* *********************************************************/

$cql = "T|s:sites:";

//Inclusion de l'entête de page
require DIR.'templates/admin/_header_admin.php';

?>
			
				<form method="post" action="<?php url('admin/module/competition/extract'); ?>">
				<h2>
					Liste des Sites
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
						if (!empty($modify)) echo 'Le site a bien été édité';
						else if (!empty($delete)) echo 'Le site a bien été supprimé';
						else if (!empty($add)) echo 'Le site a bien été ajouté';
						else echo 'Une erreur s\'est produite';
						?>
					</div>
				</div>

				<?php } ?>


				<form method="post">
					<table class="table-small">
						<thead>
							<tr class="form">
								<td><input type="text" name="nom[]" value="" placeholder="Nom..." /></td>
								<td><textarea name="description[]" placeholder="Description..."></textarea></td>
								<td><input type="number" step="any" min="0" max="90" name="latitude[]" value="" placeholder="Latitude..." /></td>
								<td><input type="number" step="any" min="-180" max="180" name="longitude[]" value="" placeholder="Longitude..." /></td>
								
								<td class="actions">
									<button type="submit" name="add">
										<img src="<?php url('assets/images/actions/add.png'); ?>" alt="Add" />
									</button>
									<input type="hidden" name="id[]" />
								</td>
							</tr>

							<tr>
								<th>Nom</th>
								<th>Description</th>
								<th>Latitude</th>
								<th>Longitude</th>
								<th class="actions">Actions</th>
							</tr>
						</thead>

						<tbody>

							<?php if (!count($sites)) { ?> 

							<tr class="vide">
								<td colspan="5">Aucun site</td>
							</tr>

							<?php } foreach ($sites as $site) { ?>

							<tr class="form">
								<td><input type="text" name="nom[]" value="<?php echo stripslashes($site['nom']); ?>" /></td>
								<td><textarea name="description[]"><?php echo stripslashes($site['description']); ?></textarea></td>
								<td><input type="number" step="any" min="0" max="90" name="latitude[]" value="<?php echo stripslashes($site['latitude']); ?>" /></td>
								<td><input type="number" step="any" min="-180" max="180" name="longitude[]" value="<?php echo stripslashes($site['longitude']); ?>" /></td>
								
								<td class="actions" style="text-align:left; padding-left:10px !important">
									<button type="submit" name="edit" value="<?php echo stripslashes($site['id']); ?>">
										<img src="<?php url('assets/images/actions/edit.png'); ?>" alt="Edit" />
									</button>									
									<?php if (empty($site['nb_matchs'])) { ?>
									<button type="submit" name="delete" value="<?php echo stripslashes($site['id']); ?>" />
										<img src="<?php url('assets/images/actions/delete.png'); ?>" alt="Delete" />
									</button>
									<?php } ?>
									<input type="hidden" name="id[]" value="<?php echo stripslashes($site['id']); ?>" />
								</td>
							</tr>

							<?php } ?>

						</tbody>
					</table>
				</form>

				<script type="text/javascript">
				$(function() {
					$speed =  <?php echo APP_SPEED_ERROR; ?>;

			    	$analysis = function(elem, event, force) {
			            if (event.keyCode == 13 || force) {
			                event.preventDefault();
			              	$parent = elem.parent().parent();
			              	$first = $parent.children('td:first');
			  				$nom = $first.children('input');
			  				$description = $first.next().children('textarea');
			  				$latitude = $first.next().next().children('input');
			  				$longitude = $first.next().next().next().children('input');
			  				$erreur = false;

			                if (!$nom.val().trim()) {
			                	$erreur = true;
			                	$nom.addClass('form-error').removeClass('form-error', $speed).focus();
			                }

			                //Vérification sur latitude/longitude

			                if (!$erreur)
			                	$parent.children('.actions').children('button:first-of-type').unbind('click').click();   
			           
			            }
			        };

					$('td input[type=text], td input[type=number], td input[type=checkbox], td input[type=password], td select, td.actions button:first-of-type').bind('keypress', function(event) {
						$analysis($(this), event, false) });
					$('td.actions button:first-of-type').bind('click', function(event) {
						$analysis($(this), event, true) });	
				});
				</script>

<?php

//Inclusion du pied de page
require DIR.'templates/_footer.php';
