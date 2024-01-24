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

$cql = "T|u:utilisateurs:|c:contacts:
F|u.nom|u.prenom|c.poste|c.photo";

//Inclusion de l'entête de page
require DIR.'templates/admin/_header_admin.php';

?>
			
				<form method="post" action="<?php url('admin/module/competition/extract'); ?>">
				<h2>
					Liste des Contacts
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
						if (!empty($modify)) echo 'Le contact a bien été édité';
						else if (!empty($delete)) echo 'Le contact a bien été supprimé';
						else if (!empty($add)) echo 'Le contact a bien été ajouté';
						else echo 'Une erreur s\'est produite';
						?>
					</div>
				</div>

				<?php } ?>


				<form method="post">
					<table class="table-small">
						<thead>
							<tr class="form">
								<td>
									<select name="login[]">
										<option value="" selected>Responsable...</option>
										<?php 
										foreach ($admins as $id => $admin) { 
											if (in_array($id, array_keys($contacts)))
												continue;
										?>
										<option value="<?php echo $admin['login']; ?>"><?php echo stripslashes($admin['nom'].' '.$admin['prenom']); ?></option>
										<?php } ?>
									</select>
								</td>
								<td><input type="text" name="poste[]" value="" placeholder="Poste..." /></td>
								<td><input type="text" name="photo[]" value="" placeholder="Photo..." /></td>
								<td class="actions">
									<button type="submit" name="add">
										<img src="<?php url('assets/images/actions/add.png'); ?>" alt="Add" />
									</button>
								</td>
							</tr>

							<tr>
								<th>Nom / Prénom</th>
								<th>Poste</th>
								<th>Photo</th>
								<th class="actions">Actions</th>
							</tr>
						</thead>

						<tbody>

							<?php 

							if (!count($contacts)) { ?> 

							<tr class="vide">
								<td colspan="4">Aucun contact</td>
							</tr>

							<?php } foreach ($contacts as $uid => $contact) {
								$utilisateur = $admins[$uid]; ?>

							<tr class="form">
								<td>
									<input type="hidden" name="login[]" value="<?php echo stripslashes($utilisateur['login']); ?>" />
									<div><?php echo stripslashes($utilisateur['nom'].' '.$utilisateur['prenom']); ?></div>
								</td>
								<td><input type="text" name="poste[]" value="<?php echo stripslashes($contact['poste']); ?>" /></td>
								<td><input type="text" name="photo[]" value="<?php echo stripslashes($contact['photo']); ?>" /></td>
								<td class="actions">
									<button type="submit" name="edit" value="<?php echo stripslashes($utilisateur['login']); ?>">
										<img src="<?php url('assets/images/actions/edit.png'); ?>" alt="Edit" />
									</button>									
									<button type="submit" name="delete" value="<?php echo stripslashes($utilisateur['login']); ?>" />
										<img src="<?php url('assets/images/actions/delete.png'); ?>" alt="Delete" />
									</button>
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
			  				$login = $first.children('input').length ? $first.children('input') : $first.find('select option:selected').first();
			  				$poste = $first.next().children('input');
			  				$photo = $first.next().next().children('input');
			  				$erreur = false;

			  				console.log($login);

			                if (!$login.val().trim()) {
			                	$erreur = true;
			                	$login.addClass('form-error').removeClass('form-error', $speed).focus();
			                }

			                if (!$poste.val().trim()) {
			                	$erreur = true;
			                	$poste.addClass('form-error').removeClass('form-error', $speed).focus();
			                }

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
