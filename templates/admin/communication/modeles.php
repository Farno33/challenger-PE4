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

$cql = "T|m:modeles:";

//Inclusion de l'entête de page
require DIR.'templates/admin/_header_admin.php';

?>
			
				<?php if (!empty($modele)) { ?>

				<h2>
					Edition du modèle <i><?php echo stripslashes($modele['nom']); ?></i><br />

					<?php if (empty($mailOk)) { ?>
					<a style="font-size:14px;display:inline-block" class="excel_big">Emails inactifs</a>
					<?php } else { ?>
					<a style="font-size:14px;display:inline-block" class="excel">Emails actifs</a>
					<?php } ?>

					<?php if (empty($smsOk)) { ?>
					<a style="font-size:14px;display:inline-block" class="excel_big">SMS inactifs</a>
					<?php } else { ?>
					<a style="font-size:14px;display:inline-block" class="excel">SMS actifs</a>
					<?php } ?>
				</h2>

				<form method="post" class="form-table" onsubmit="return checkForm(event)">
					<fieldset>
						<h4 style="margin-top:0">Paramètres généraux</h4>
						
						<label for="form-type">
							<span>Type</span>
							<input type="checkbox" name="type" id="form-type" <?php if ($modele['type'] == 'sms') echo 'checked'; ?> />
							<label for="form-type" class="type-message"></label>
						</label>

						<label for="form-nom">
							<span>Nom</span>
							<input type="text" name="nom" id="form-nom" value="<?php echo stripslashes($modele['nom']); ?>" />
						</label>
					</fieldset>

					<fieldset>
						<h4 style="margin-top:0">Contenu du message</h4>

						<label for="form-titre"<?php if ($modele['type'] == 'sms') echo ' style="display:none"'; ?>>
							<span>Titre</span>
							<input type="text" id="form-titre" name="titre" value="<?php if ($modele['type'] == 'email') echo str_replace('"', '&quot;', stripslashes($modele['titre'])); ?>" />
						</label>

						<label for="form-email"<?php if ($modele['type'] == 'sms') echo ' style="display:none"'; ?>>
							<span>Contenu</span>
							<textarea style="height:30em !important" id="form-email" name="email"><?php 
								if ($modele['type'] == 'email') echo stripslashes($modele['modele']); ?></textarea>
						</label>

						<label for="form-sms"<?php if ($modele['type'] == 'email') echo ' style="display:none"'; ?>>
							<span>Contenu</span>
							<textarea id="form-sms" name="sms"><?php 
								if ($modele['type'] == 'sms') echo stripslashes($modele['modele']); ?></textarea>
						</label>
					</fieldset>

					<center><input type="submit" name="save" value="Editer le modèle" class="success" /></center>
				</form>

				<style type="text/css">
				.mce-tinymce.mce-container {
					display:inline-block;
					width:calc(100% - 160px);
				}
				.mce-charactercount {
				margin: 2px 0 2px 2px;
				padding: 8px;
				}
				.mce-fullscreen {
					width:100% !important;
				}
				</style>

				<script type="text/javascript">
				$speed =  <?php echo APP_SPEED_ERROR; ?>;

				checkForm = function(e) {
					if ($('#form-nom').val().trim() == '') {
			            $('#form-nom').addClass('form-error').removeClass('form-error', $speed).focus();
						e.preventDefault();
						return false;
					}

					return true;
				};

				$(function() {
					$('#form-type').on('click', function() {
						if ($(this).is(':checked')) { //SMS
							$('label[for=form-sms]').css('display', 'block');
							$('label[for=form-titre]').css('display', 'none');
							$('label[for=form-email]').css('display', 'none');
							$('#form-modele-sms').css('display', 'inline-block');
							$('#form-modele-email').css('display', 'none');
						} else {
							$('label[for=form-sms]').css('display', 'none');
							$('label[for=form-titre]').css('display', 'block');
							$('label[for=form-email]').css('display', 'block');
							$('#form-modele-sms').css('display', 'none');
							$('#form-modele-email').css('display', 'inline-block');
						}
					});

					var edited = false;
					$('#form-titre, #form-sms, #form-email').on('change', function() {
						edited = true;
					});

					//Toutes les 30 secondes on regarde s'il y a eu des modifs si oui alors 
					//on fait une très simple requête pour éviter la déconnexion
					setInterval(function() {
						if (edited) {
							edited = false;
							$.ajax({
								url: "<?php url('admin/module/communication/modeles'); ?>",
							  	method: "POST",
							  	cache: false,
								data:{connected: true}
							});
						}
					}, 30000);
				});
				</script>
				<script type="text/javascript" src="<?php url('assets/js/tinymce.communication.js'); ?>"></script>

				<?php } else { ?>

				<form method="post" action="<?php url('admin/module/competition/extract'); ?>">
				<h2>
					Liste des Modèles
					<input type="submit" value="CQL" /><br />

					<?php if (empty($mailOk)) { ?>
					<a style="font-size:14px;display:inline-block" class="excel_big">Emails inactifs</a>
					<?php } else { ?>
					<a style="font-size:14px;display:inline-block" class="excel">Emails actifs</a>
					<?php } ?>

					<?php if (empty($smsOk)) { ?>
					<a style="font-size:14px;display:inline-block" class="excel_big">SMS inactifs</a>
					<?php } else { ?>
					<a style="font-size:14px;display:inline-block" class="excel">SMS actifs</a>
					<?php } ?>
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
						if (!empty($modify)) echo 'Le modèle a bien été édité';
						else if (!empty($delete)) echo 'Le modèle a bien été supprimé';
						else if (!empty($add)) echo 'Le modèle a bien été ajouté';
						else echo 'Une erreur s\'est produite';
						?>
					</div>
				</div>

				<?php } ?>


				<form method="post" action="<?php url('admin/module/communication/modeles'); ?>">
					<table class="table-small">
						<thead>
							<tr class="form">
								<td>
									<input type="checkbox" name="type[]" id="form-type" value="0" />
									<label class="type-message" for="form-type" />
								</td>
								<td><input type="text" name="nom[]" value="" placeholder="Nom..." /></td>
								
								<td class="actions">
									<button type="submit" name="add">
										<img src="<?php url('assets/images/actions/add.png'); ?>" alt="Add" />
									</button>
									<input type="hidden" name="id[]" />
								</td>
							</tr>

							<tr>
								<th>Type</th>
								<th>Nom</th>
								<th class="actions">Actions</th>
							</tr>
						</thead>

						<tbody>

							<?php if (!count($modeles)) { ?> 

							<tr class="vide">
								<td colspan="3">Aucun modèle</td>
							</tr>

							<?php } 

							$type = null;
							foreach ($modeles as $mid => $modele) { 
								if ($type !== null && 
									$type != $modele['type'])
									echo '<tr><th colspan="3"></th></tr>';

								$type = $modele['type'];
							?>

							<tr class="form clickme" onclick="window.location.href = '<?php url('admin/module/communication/modele_'.$mid); ?>';">
								<td>
									<input type="checkbox" name="type[]" value="<?php echo $mid; ?>" <?php if ($modele['type'] == 'sms') echo ' checked'; ?> />
									<label class="type-message" />
								</td>
								<td class="content"><?php echo stripslashes($modele['nom']); ?></td>
								
								<td class="actions">									
									<button onclick="event.stopPropagation();" 	type="submit" name="delete" value="<?php echo stripslashes($mid); ?>" />
										<img src="<?php url('assets/images/actions/delete.png'); ?>" alt="Delete" />
									</button>
									<input type="hidden" name="id[]" value="<?php echo stripslashes($mid); ?>" />
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
			  				$type = $first.children('input');
			  				$nom = $first.next().children('input');
			  				$erreur = false;

			  				console.log($nom);
			                if (typeof $nom.val() !== 'undefined' && !$nom.val().trim()) {
			                	$erreur = true;
			                	$nom.addClass('form-error').removeClass('form-error', $speed).focus();
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

				<?php } ?>

<?php

//Inclusion du pied de page
require DIR.'templates/_footer.php';
