<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* templates/admin/configurations/liste.php ****************/
/* Template de la liste du module des constantes ***********/
/* *********************************************************/
/* Dernière modification : le 23/11/14 *********************/
/* *********************************************************/


//Inclusion de l'entête de page
require DIR.'templates/admin/_header_admin.php';

?>
			
				<h2>
					Liste des Tâches
					
					<?php if (empty($serviceOk)) { ?>
					<a style="font-size:14px" class="excel_big">Service inactif</a>
					<?php } else { ?>
					<a style="font-size:14px" class="excel">Service actif</a>
					<?php } ?>
				</h2>

				<?php
				if (isset($add) ||
					isset($modify) ||
					!empty($delete)) {
				?>

				<div class="alerte alerte-<?php echo !empty($add) || !empty($modify) || !empty($delete) ? 'success' : 'erreur'; ?>">
					<div class="alerte-contenu">
						<?php
						if (!empty($modify)) echo 'La tâche a bien été éditée';
						else if (!empty($delete)) echo 'La tâche a bien été supprimée';
						else if (!empty($add)) echo 'La tâche a bien été ajoutée';
						else echo 'Une erreur s\'est produite, veuillez réessayer.';
						?>
					</div>
				</div>

				<?php } ?>


				<form method="post">
					<table>
						<thead>
							<tr class="form">
								<td><input type="text" name="nom[]" value="" placeholder="Nom..." /></td>
								<td><input type="text" name="periodicite[]" value="" placeholder="Périodicité..." /></td>
								<td><input type="text" name="script[]" value="" placeholder="Script..." /></td>
								<td>
									<input type="checkbox" name="active[]" id="form-active" checked value="1" />
									<label for="form-active"></label>
								</td>
								<td></td>
								<td class="actions">
									<button type="submit" name="add">
										<img src="<?php url('assets/images/actions/add.png'); ?>" alt="Add" />
									</button>
									<input type="hidden" name="id[]" />
								</td>
							</tr>


							<tr>
								<th>Nom</th>
								<th>Périodicité</th>
								<th>Script</th>
								<th>Activée</th>
								<th><small>Dern. exéc.</small></th>
								<th class="actions">Actions</th>
							</tr>
						</thead>

						<tbody>

							<?php if (!count($taches)) { ?> 

							<tr class="vide">
								<td colspan="6">Aucune tâche</td>
							</tr>

							<?php } foreach ($taches as $tache) { ?>

							<tr class="form">
								<td><input type="text" name="nom[]" value="<?php echo stripslashes($tache['nom']); ?>" /></td>
								<td><input type="text" name="periodicite[]" value="<?php echo stripslashes($tache['periodicite']); ?>" /></td>
								<td><input type="text" name="script[]" value="<?php echo stripslashes($tache['script']); ?>" /></td>
								<td>
									<input type="checkbox" name="active[]" id="form-active-<?php echo $tache['id']; ?>" <?php if (!empty($tache['active'])) echo 'checked '; ?>value="<?php echo $tache['id']; ?>" />
									<label for="form-active-<?php echo $tache['id']; ?>"></label>
								</td>
								<td class="content">
									<?php echo empty($tache['execution']) ? '<i>Aucune</i>' : printDateTime($tache['execution']); ?>
								</td>
								<td class="actions">
									<button type="submit" name="edit" value="<?php echo stripslashes($tache['id']); ?>">
										<img src="<?php url('assets/images/actions/edit.png'); ?>" alt="Edit" />
									</button>
																		
									<button type="submit" name="delete" value="<?php echo stripslashes($tache['id']); ?>" />
										<img src="<?php url('assets/images/actions/delete.png'); ?>" alt="Delete" />
									</button>

									<input type="hidden" name="id[]" value="<?php echo stripslashes($tache['id']); ?>" />
								</td>
							</tr>

							<?php } ?>

						</tbody>
					</table>
				</form>

				<center><pre style="text-align:left; width:300px">
*    *    *    *    *    *
-    -    -    -    -    -
|    |    |    |    |    |
|    |    |    |    |    + year [optional]
|    |    |    |    +----- day of week (0 - 7) (Sunday=0 or 7)
|    |    |    +---------- month (1 - 12)
|    |    +--------------- day of month (1 - 31)
|    +-------------------- hour (0 - 23)
+------------------------- min (0 - 59)


* : every
n : digit
a-b : range
*/m : every m digit
				</pre></center>

				<script type="text/javascript">
				$(function() {
					$speed =  <?php echo APP_SPEED_ERROR; ?>;

			    	$analysis = function(elem, event, force) {
			            if (event.keyCode == 13 || force) {
			                event.preventDefault();
			              	$parent = elem.parent().parent();
			              	$first = $parent.children('td:first');
			  				$nom = $first.children('input');
			  				$periodicite = $first.next().children('input');
			  				$script = $first.next().next().children('input');

			                if (!$nom.val().trim())
			                	$nom.addClass('form-error').removeClass('form-error', $speed).focus();

			                if (!$periodicite.val().trim())
			                	$periodicite.addClass('form-error').removeClass('form-error', $speed).focus();

			                if (!$script.val().trim())
			                	$script.addClass('form-error').removeClass('form-error', $speed).focus();

			                if ($nom.val().trim() &&
			                	$periodicite.val().trim() &&
			                	$script.val().trim())
			                	$parent.children('.actions').children('button:first-of-type').unbind('click').click();   
			           
			            }
			        };

					$('td input[type=text], td input[type=number], td select, td.actions button:first-of-type').bind('keypress', function(event) {
						$analysis($(this), event, false) });
					$('td.actions button:first-of-type').bind('click', function(event) {
						$analysis($(this), event, true) });	
				});
				</script>

<?php

//Inclusion du pied de page
require DIR.'templates/_footer.php';
