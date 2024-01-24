<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* templates/ecoles/accueil.php ****************************/
/* Template de l'accueil pour les écoles *******************/
/* *********************************************************/
/* Dernière modification : le 11/12/14 *********************/
/* *********************************************************/


if ($admin_actif)
	require DIR.'templates/admin/_header_admin.php';

else
	require DIR.'templates/_header_nomenu.php';

?>

				<nav class="subnav">
					<h2>Mon profil</h2>
					<ul>
						<li><a href="?p=coords">Coordonnées</a></li>
						<li><a href="?p=connexions">Connexions</a></li>
						<li><a href="?p=actions">Actions</a></li>
						<li><a href="?p=modifications">Modifications</a></li>
					</ul>
				</nav>

				<form class="form-table" method="post">
					
					<?php if (empty($_GET['p']) || $_GET['p'] == 'coords') { ?>

					<fieldset>
						<h3>Coordonnées</h3>
					
						<label for="form-login">
							<span>Identifiant</span>
							<input class="disabled" type="text" disabled name="login" id="form-login" value="<?php echo stripslashes($user['login']); ?>" />
						</label>

						<label>
							<span>Prénom / Nom</span>
							<input class="disabled two_input" disabled type="text" value="<?php echo stripslashes($user['prenom']); ?>" />
							<input class="disabled two_input" disabled type="text" value="<?php echo stripslashes($user['nom']); ?>" />
						</label>
					
						<label for="form-email">
							<span>Email</span>
							<input class="disabled" disabled type="text" name="email" id="form-email" value="<?php echo stripslashes($user['email']); ?>" />
						</label>
					
						<label for="form-telephone">
							<span>Téléphone</span>
							<input class="disabled" disabled type="text" name="telephone" id="form-telephone" value="<?php echo stripslashes($user['telephone']); ?>" />
						</label>

						<?php if (empty($user['cas'])) { ?>

						<label for="form-old">
							<span>Mot-de-passe</span>
							
							<?php if (!empty($user['pass'])) { ?>

							<input class="<?php echo !empty($user['cas']) && !empty($user['pass']) ? 'four' : 'three'; ?>_input" id="form-old" type="password" name="old" value="" placeholder="Mot-de-passe actuel" />
							
							<?php } ?>

							<input class="<?php echo !empty($user['cas']) && !empty($user['pass']) ? (!empty($user['pass']) ? 'four' : 'three') : (!empty($user['pass']) ? 'three' : 'two'); ?>_input" id="form-password" type="password" name="password" value="" placeholder="Nouveau mot-de-passe" />
							<input class="<?php echo !empty($user['cas']) && !empty($user['pass']) ? (!empty($user['pass']) ? 'four' : 'three') : (!empty($user['pass']) ? 'three' : 'two'); ?>_input" type="password" name="password_bis" value="" placeholder="Répéter mot-de-passe" />
							
							<?php if (!empty($user['cas']) && !empty($user['pass'])) { ?>

							<input class="<?php echo !empty($user['pass']) ? 'four' : 'three'; ?>_input" type="submit" name="delete" value="Supprimer" />

							<?php } ?>

						</label>

						<?php } ?>

						<?php if (!empty($error)) { ?>

						<div class="alerte alerte-erreur">
							<div class="alerte-contenu">
								Les mots-de-passe doivent être identiques.
							</div>
						</div>

						<?php } else if (!empty($need)) { ?>

						<div class="alerte alerte-erreur">
							<div class="alerte-contenu">
								Le mot-de-passe actuel n'est pas correct.
							</div>
						</div>

						<?php } else if (!empty($save) || !empty($delete)) { ?>
						
						<div class="alerte alerte-success">
							<div class="alerte-contenu">
								Le mot-de-passe a bien été mis à jour.
							</div>
						</div>

						<?php } ?>

						<?php if (empty($user['cas'])) { ?>

						<center>
							<input type="submit" class="success" name="save" value="Mettre à jour" />
						</center>

						<?php } ?>
						
					</fieldset>

					<?php } else if ($_GET['p'] == 'connexions') { ?>

					<fieldset>
						<h3>Historique de connexions</h3>

						<table class="table">
							<thead>
								<tr>
									<th>Date</th>
									<th>Méthode</th>
									<th>Lieu</th>
									<th>OS</th>
									<th>Navigateur</th>
								</tr>
							</thead>

							<tbody>

								<?php 

								foreach ($connexions as $connexion) { 
									$agent = $uaparser->parse(stripslashes($connexion['agent']));	
									$lieu = !empty($connexion['geoip']) ? json_decode($connexion['geoip']) : $connexion['ip'];
									$lieu = ($lieu === false || empty($lieu->city) ? $connexion['ip'] : $lieu->city).
											(!empty($lieu->country_code) ? ' <small>'.$lieu->country_code.'</small>' : '');

									if ($lieu == '127.0.0.1' || $lieu == '::1')
										$lieu = '<small>LOCALHOST</small>';
								?>

								<tr>
									<td><?php echo !empty($_SESSION['user']['active']) && $connexion['id'] == $_SESSION['user']['active'] ? '<b style="color:green">Actuelle</b>' : printDateTime($connexion['connexion']); ?>
										<small>(<?php echo printInterval($connexion['connexion'], $connexion['dernier']); ?>)</small></td>
									<td><center><?php echo printMethodeConnexion($connexion['methode']); ?></b></center></td>
									<td><?php echo $lieu; ?></div></td>
									<td><?php echo $agent->os->family.' <small>'.$agent->os->toVersionString.'</small>'; ?></td>
									<td><?php echo $agent->ua->family.' <small>'.$agent->ua->toVersionString.'</small>'; ?></td>
								</tr>

								<?php } ?>

							</tbody>
						</table>
					</fieldset>

					<?php } else if ($_GET['p'] == 'actions') { ?>

					<fieldset>
						<h3>Historique des actions</h3>

						<table class="table">
							<thead>
								<tr>
									<th style="width:160px">Date</th>
									<th>Groupe</th>
									<th style="width:100px">ID &gt; <i>Ref</i></th>
									<th>Action</th>
									<th>Message</th>
									<th>État</th>
								</tr>
							</thead>

							<tbody>

								<?php foreach ($datas as $data) { ?>

								<tr>
									<td><?php echo printDateTime($data['_date']); ?></td>
									<td><center><b><?php echo $data['groupe']; ?></b></center></td>
									<td><small><?php echo $data['id'].(!empty($data['_ref']) ? ' &gt; <i>'.$data['_ref'] : '').'</i>' ?></small></td>
									<td><center><?php echo printActionItem($data['_etat'], $data['_ref']); ?></center></td>
									<td style="padding:0px"><textarea readonly><?php echo stripslashes($data['_message']); ?></textarea></td>
									<td><center><?php echo printEtatItem($data['_etat']); ?></center></td>
								</tr>

								<?php } if (!count($datas)) { ?>

								<tr class="vide">
									<td colspan="6">Aucune action</td>
								</tr>

								<?php } ?>

							</tbody>
						</table>
					</fieldset>

					<?php } else if ($_GET['p'] == 'modifications') { ?>

					<fieldset>
						<h3>Suivi des modifications</h3>

						<table class="table">
							<thead>
								<tr>
									<th>ID</th>
									<th>Date</th>
									<th>Auteur</th>
									<th>Action</th>
									<th>Message</th>
									<th>État</th>
								</tr>
							</thead>

							<tbody>

								<?php foreach ($history as $revision) { ?>

								<tr>
									<td><small><?php echo $revision['id']; ?></small></td>
									<td><?php echo printDateTime($revision['_date']); ?></td>
									<td><center><b><?php echo $revision['_auteur_nom'].' '.$revision['_auteur_prenom']; ?></b></center></td>
									<td><center><?php echo printActionItem($revision['_etat'], $revision['_ref']); ?></center></td>
									<td style="padding:0px"><textarea readonly style="border:0"><?php echo stripslashes($revision['_message']); ?></textarea></td>
									<td><center><?php echo printEtatItem($revision['_etat']); ?></center></td>
								</tr>

								<?php } ?>

							</tbody>
						</table>
					</fieldset>

					<?php } ?>

				</form>

				<script type="text/javascript">
				$(function() {
					$speed =  <?php echo APP_SPEED_ERROR; ?>;
			    	
			    	$analysisData = function(elem, event, force) {
			            if (event.keyCode == 13 || force) {
			              	$field_coords = elem.children('fieldset').first();
			              	$field_respo = $field_coords.next().children('.bloc').first();
			              	$field_corespo = $field_coords.next().children('.bloc').first().next();
			  				$first_respo = $field_respo.children('label').first();
			  				$first_corespo = $field_corespo.children('label').first();
			  				$nom_respo = $first_respo.children('input');
			  				$prenom_respo = $first_respo.next().children('input');
			  				$email_respo = $first_respo.next().next().children('input');
			  				$telephone_respo = $first_respo.next().next().next().children('input');
			  				$nom_corespo = $first_corespo.children('input');
			  				$prenom_corespo = $first_corespo.next().children('input');
			  				$email_corespo = $first_corespo.next().next().children('input');
			  				$telephone_corespo = $first_corespo.next().next().next().children('input');
			  				
			                if (!$nom_respo.val().trim())
			                	$nom_respo.addClass('form-error').removeClass('form-error', $speed).focus();

			                if (!$prenom_respo.val().trim())
			                	$prenom_respo.addClass('form-error').removeClass('form-error', $speed).focus();

			                if (!$email_respo.val().trim())
			                	$email_respo.addClass('form-error').removeClass('form-error', $speed).focus();

			                if (!$telephone_respo.val().trim())
			                	$telephone_respo.addClass('form-error').removeClass('form-error', $speed).focus();

			                if (!$nom_corespo.val().trim())
			                	$nom_corespo.addClass('form-error').removeClass('form-error', $speed).focus();

			                if (!$prenom_corespo.val().trim())
			                	$prenom_corespo.addClass('form-error').removeClass('form-error', $speed).focus();

			                if (!$email_corespo.val().trim())
			                	$email_corespo.addClass('form-error').removeClass('form-error', $speed).focus();

			                if (!$telephone_corespo.val().trim())
			                	$telephone_corespo.addClass('form-error').removeClass('form-error', $speed).focus();

			                if (!($nom_respo.val().trim() &&
			                	$prenom_respo.val().trim() &&
			                	$email_respo.val().trim() &&
			                	$telephone_respo.val().trim() &&
			                	$nom_corespo.val().trim() &&
			                	$prenom_corespo.val().trim() && 
			                	$email_corespo.val().trim() &&
			                	$telephone_corespo.val().trim()))
			                	event.preventDefault();
			           
			            }
			        };

			        $('form.form-table').first().bind('submit', function(event) { $analysisData($(this), event, true); });
				});
				</script>

<?php

//Inclusion du pied de page
require DIR.'templates/_footer.php';
