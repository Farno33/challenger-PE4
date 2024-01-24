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

$cql = "T|u:utilisateurs:
F|u.login|u.cas|u.nom|u.prenom|u.telephone|u.email|u.responsable
B|u.responsable:DESC|u.login
S|u.responsable";

//Inclusion de l'entête de page
require DIR.'templates/admin/_header_admin.php';

?>
			
				<form method="post" action="<?php url('admin/module/competition/extract'); ?>">
				<h2>
					Liste des Utilisateurs
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
						if (!empty($modify)) echo 'L\'utilisateur a bien été édité'.(!empty($changePass) ? '.<br /> <b>Le mot-de-passe est identique au nouveau login</b>, il est conseillé de le changer directement' : (empty($cas) ? ', le mot-de-passe est inchangé' : ''));
						else if (!empty($delete)) echo 'L\'utilisateur a bien été supprimé';
						else if (!empty($add)) echo 'L\'utilisateur a bien été ajouté'.(empty($cas) ? '.<br /><b>Le mot-de-passe est identique au login</b>, il est conseillé de le changer directement' : '');
						
						else if (!empty($cas)) echo 'Impossible de récupérer les données pour se connecter au CAS'; 
						else echo 'Une erreur s\'est produite (le login existe déjà, ou le login est invalide)';
						?>
					</div>
				</div>

				<?php } ?>


				<form method="post">
					<table class="table-small">
						<thead>
							<tr class="form">
								<td><input type="text" name="login[]" value="" placeholder="Login..." /></td>
								<td>
									<input type="checkbox" id="form-cas" name="cas[]" value="0" />
									<label for="form-cas"></label>
								</td>
								<td><input type="text" name="nom[]" value="" placeholder="Nom..." /></td>
								<td><input type="text" name="prenom[]" value="" placeholder="Prénom..." /></td>
								<td><input type="text" name="telephone[]" value="" placeholder="Téléphone..." /></td>
								<td><input type="text" name="email[]" value="" placeholder="Email..." /></td>
								<td>
									<input type="checkbox" id="form-responsable" name="responsable[]" value="0" />
									<label for="form-responsable"></label>
								</td>
								<td class="actions">
									<button type="submit" name="add">
										<img src="<?php url('assets/images/actions/add.png'); ?>" alt="Add" />
									</button>
									<input type="hidden" name="id[]" />
								</td>
							</tr>

							<tr>
								<th>Login</th>
								<th style="width:60px">CAS</th>
								<th>Nom</th>
								<th>Prénom</th>
								<th>Téléphone</th>
								<th>Email</th>
								<th style="width:60px"><small>Responsable</small></th>
								<th class="actions">Actions</th>
							</tr>
						</thead>

						<tbody>

							<?php 

							$previousRespo = null;
							if (!count($admins)) { ?> 

							<tr class="vide">
								<td colspan="8">Aucun utilisateurs</td>
							</tr>

							<?php } foreach ($admins as $admin) { 

							if ($previousRespo !== null && $previousRespo != $admin['responsable'])
								echo '<tr><th colspan="8"></th></tr>';

							$previousRespo = $admin['responsable'];

							?>

							<tr class="form">
								<td><input type="text" name="login[]" value="<?php echo stripslashes($admin['login']); ?>" /></td>
								<td>
									<input type="checkbox" id="form-cas-<?php echo $admin['id']; ?>" name="cas[]" value="<?php echo $admin['id']; ?>" <?php if ($admin['cas']) echo 'checked '; ?>/>
									<label for="form-cas-<?php echo $admin['id']; ?>"></label>
								</td>
								<td><input type="text" name="nom[]" value="<?php echo stripslashes($admin['nom']); ?>" /></td>
								<td><input type="text" name="prenom[]" value="<?php echo stripslashes($admin['prenom']); ?>" /></td>
								<td><input type="text" name="telephone[]" value="<?php echo stripslashes($admin['telephone']); ?>" /></td>
								<td><input type="text" name="email[]" value="<?php echo stripslashes($admin['email']); ?>" /></td>
								<td>
									<input type="checkbox" id="form-responsable-<?php echo $admin['id']; ?>" name="responsable[]" value="<?php echo $admin['id']; ?>" <?php if ($admin['responsable']) echo 'checked '; ?>/>
									<label for="form-responsable-<?php echo $admin['id']; ?>"></label>
								</td>
								<td class="actions">
									<button type="submit" name="edit" value="<?php echo stripslashes($admin['id']); ?>">
										<img src="<?php url('assets/images/actions/edit.png'); ?>" alt="Edit" />
									</button>									
									<button type="submit" name="delete" value="<?php echo stripslashes($admin['id']); ?>" />
										<img src="<?php url('assets/images/actions/delete.png'); ?>" alt="Delete" />
									</button>
									<input type="hidden" name="id[]" value="<?php echo stripslashes($admin['id']); ?>" />
								</td>
							</tr>

							<?php } ?>

						</tbody>
					</table>
				</form>

				<script type="text/javascript">
				$(function() {
					$toggleNPE = function(elem) {
						$parent = elem.parent().parent();
						$first = $parent.children('td:first');
		  				$nom = $first.next().next().children('input');
		  				$prenom = $first.next().next().next().children('input');
		  				$email = $first.next().next().next().next().next().children('input');

		  				if (elem.is(':checked')) {
		  					$nom.val('').attr('disabled', true);
		  					$prenom.val('').attr('disabled', true);
		  					$email.val('').attr('disabled', true);
		  				} else {
		  					$nom.attr('disabled', false);
		  					$prenom.attr('disabled', false);
		  					$email.attr('disabled', false);
		  				}
					};

					$speed =  <?php echo APP_SPEED_ERROR; ?>;

			    	$analysis = function(elem, event, force) {
			            if (event.keyCode == 13 || force) {
			                event.preventDefault();
			              	$parent = elem.parent().parent();
			              	$first = $parent.children('td:first');
			  				$login = $first.children('input');
			  				$cas = $first.next().children('input');
			  				$nom = $first.next().next().children('input');
			  				$prenom = $first.next().next().next().children('input');
			  				$telephone = $first.next().next().next().next().children('input');
			  				$email = $first.next().next().next().next().next().children('input');
			  				$responsable = $first.next().next().next().next().next().next().children('input');
			  				$erreur = false;

			                if (!$login.val().trim()) {
			                	$erreur = true;
			                	$login.addClass('form-error').removeClass('form-error', $speed).focus();
			                }

			                if (!$cas.is(':checked') && !$nom.val().trim()) {
			                	$erreur = true;
			                	$nom.addClass('form-error').removeClass('form-error', $speed).focus();
			                }

			                if (!$cas.is(':checked') && !$prenom.val().trim()) {
			                	$erreur = true;
			                	$prenom.addClass('form-error').removeClass('form-error', $speed).focus();
			                }

			                //Mettre vérification de l'email
			                if (!$cas.is(':checked') && !$email.val().trim()) {
			                	$erreur = true;
			                	$email.addClass('form-error').removeClass('form-error', $speed).focus();
			                }

			                //Mettre vérification du téléphone ??

			                if (!$erreur) {
			                	$parent.data('keep', true).parent().parent().find('tr:not(:first-of-type)').each(function() {
			                		if (!$(this).data('keep'))
			                			$(this).find('input').removeAttr('name');
			                	});
			                	$parent.children('.actions').children('button:first-of-type').unbind('click').click();   
			                }
			           
			            }
			        };

					$('td input[type=text], td input[type=number], td input[type=checkbox], td input[type=password], td select, td.actions button:first-of-type').bind('keypress', function(event) {
						$analysis($(this), event, false) });
					$('td.actions button:first-of-type').bind('click', function(event) {
						$analysis($(this), event, true) });	
					
					$('td input[type=checkbox][name="cas[]"]').bind('change', function() {
						$toggleNPE($(this));
					});
				});
				</script>

<?php

//Inclusion du pied de page
require DIR.'templates/_footer.php';
