<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* templates/admin/classement/tableau.php ******************/
/* Template du tableau des classements *********************/
/* *********************************************************/
/* Dernière modification : le 17/02/14 *********************/
/* *********************************************************/

//Inclusion de l'entête de page
require DIR.'templates/admin/_header_admin.php';

?>
			
				<h2>
					Envoi d'un message<br />

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


				<form method="post" class="form-table" onsubmit="return checkForm()" action="<?php url('admin/module/communication/ecrire'); ?>">
					<fieldset>
						<h4 style="margin-top:0">Paramètres généraux</h4>
						
						<label for="form-type">
							<span>Type</span>
							<input type="checkbox" name="type" id="form-type" <?php echo !empty($exists) && $exists['type'] == 'sms' ? 'checked' : ''; ?> />
							<label for="form-type" class="type-message"></label>
						</label>

						<label for="form-null">
							<span>Retardé</span>
							<input type="checkbox" name="retard" id="form-retard" />
							<label class="two_input" for="form-retard"></label>

							<input class="four_input" type="date" name="date" disabled placeholder="jj/mm/aaaa" />
							<input class="four_input" type="time" name="time" disabled placeholder="hh:mm" />
						</label>

						<label for="form-to">
							<span>Destinataire</span>
							<input type="text" name="to" id="form-to" value="<?php echo !empty($exists) ? ($exists['type'] == 'email' ? $exists['email'] : $exists['telephone']) : ''; ?>" <?php
								if (!empty($exists['id_participant'])) { ?> data-email="<?php echo $exists['email']; ?>" data-telephone="<?php echo $exists['telephone']; ?>" <?php } ?> />
							<input type="hidden" name="id" value="<?php if (!empty($exists) && !empty($exists['id_participant'])) echo $exists['id_participant']; ?>" id="form-id" />
						</label>

						<label for="form-null">
							<span>Modèle</span>
							<select id="form-modele-email" onchange="selectModele(this)">
								<option selected disabled></option>

								<?php foreach ($modeles as $mid => $modele) { 
									if ($modele['type'] != 'email')
										continue; ?>
								<option value="<?php echo $mid; ?>"><?php echo stripslashes($modele['nom']); ?></option>

								<?php } ?>
							</select>
							<select id="form-modele-sms" style="display:none" onchange="selectModele(this)">
								<option selected disabled></option>

								<?php foreach ($modeles as $mid => $modele) { 
									if ($modele['type'] != 'sms')
										continue; ?>
								<option value="<?php echo $mid; ?>"><?php echo stripslashes($modele['nom']); ?></option>

								<?php } ?>
							</select>
						</label>
					</fieldset>

					<fieldset>
						<h4 style="margin-top:0">Contenu du message</h4>

						<label for="form-titre">
							<span>Titre</span>
							<input type="text" id="form-titre" name="titre" value="<?php
								if (!empty($exists) && $exists['type'] == 'email' && isset($exists['titre']))
									echo 'Re: '.str_replace('"', '&quot;', $exists['titre']); ?>" />
						</label>

						<label for="form-email">
							<span>Contenu</span>
							<textarea style="height:30em !important" id="form-email" name="email"><?php
								if (!empty($exists) && $exists['type'] == 'email' && isset($exists['message']))
									echo '<p>&nbsp;</p><blockquote class="challenger_quote" style="border-left:1px solid gray; padding-left:10px">'.$exists['message'].'</blockquote>'; ?></textarea>
						</label>

						<label for="form-sms" style="display:none">
							<span>Contenu</span>
							<textarea id="form-sms" name="sms"></textarea>
						</label>
					</fieldset>

					<center><input type="submit" name="send" value="Envoyer le message" class="success" /></center>
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
				@media (max-width: 800px) {
					.mce-tinymce.mce-container {
						width:100%;
					} 
				}
				</style>

				<script type="text/javascript">
				checkForm = function() {
					var isSms = $('#form-type').is(':checked');
					return true;
				};

				selectModele = function(select) {
					var modele = $(select).val();
					$(select).children('option').attr('selected', false);
					$(select).children('option[disabled]').attr('selected', true);

					$.ajax({
						url: "<?php url('admin/module/communication/publipostage?modele'); ?>",
						method: "POST",
						cache: false,
						dataType: "json",
						data: {
							type: $('#form-type').is(':checked') ? 'sms' : 'email',
							modele: modele
						},
						success: function(data) {
							if (typeof data.modele === 'string') {
								if ($('#form-type').is(':checked')) { //SMS
									$('#form-sms').val(data.modele);
									tinymce.get('form-sms').setContent(data.modele, {format : 'raw'});
								} else {
									$('#form-titre').val(data.titre);
									$('#form-email').val(data.modele);
									tinymce.get('form-titre').setContent(data.titre, {format : 'raw'});
									tinymce.get('form-email').setContent(data.modele, {format : 'raw'});
								}
							}
						}
					});
				};

				var edited = false;

				function changeType(elem) {
					if ($(elem).is(':checked')) { //SMS
						if ($('#form-to').data('telephone'))
							$('#form-to').val($('#form-to').data('telephone'));

						$('label[for=form-sms]').css('display', 'block');
						$('label[for=form-titre]').css('display', 'none');
						$('label[for=form-email]').css('display', 'none');
						$('#form-modele-sms').css('display', 'inline-block');
						$('#form-modele-email').css('display', 'none');
					} else {
						if ($('#form-to').data('email'))
							$('#form-to').val($('#form-to').data('email'));

						$('label[for=form-sms]').css('display', 'none');
						$('label[for=form-titre]').css('display', 'block');
						$('label[for=form-email]').css('display', 'block');
						$('#form-modele-sms').css('display', 'none');
						$('#form-modele-email').css('display', 'inline-block');
					}
				}

				$(function() {
					var canSearch = false;
		    		var onlyOnEnter = false;
					$("#form-to").autocomplete({
				        source: function( request, response ) {
							var $me = this.element;
							$.ajax({
								url: "<?php url('admin/module/communication/conversations?ajax'); ?>",
							  	method: "POST",
							  	cache: false,
								dataType: "json",
								data:{filtre: request.term},
								success: function(data) {
									response(data);
								}
							});
						},
				        minLength:2,
				        select: function(e, ui) {
				            e.preventDefault();
				            $('#form-id').val(ui.item.id);
				            $('#form-to').val($('#form-type').is(':checked') ? ui.item.telephone : ui.item.email)
				            	.data('email', ui.item.email)
				            	.data('telephone', ui.item.telephone);
				        },
				        focus: function(e, ui) {
	            			return false;  
				    	},
				        search: function (e, ui) {
				        	var canTempSearch = canSearch;
				        	canSearch = false;
				        	return !onlyOnEnter || onlyOnEnter && canTempSearch;
				        }
				    }).bind('keyup', function(e) {
				    	if (e.keyCode == 13) {
				    		canSearch = true;
				    		$(this).autocomplete("search", $(this).val());
				    	}
				    }).focus(function(){
				    	if (!onlyOnEnter)
				    		$(this).autocomplete("search");        
			        }).blur(function() {
			        });
	                            
	                $.ui.autocomplete.prototype._renderItem = function(ul, item) {
	                    return $("<li>")
	                    	.append("<a>" + (item.exte ? "<i>(Exté)</i> " : '') + item.prenom + ' ' + item.nom + "<br /><small" + (item.id && item.id == $('#form-id').val() ? ' class="lui"' : '')+">" + 
	                    		item.email + (item.email && item.telephone ? " / " : "") + item.telephone + "</small></a>")
	                    	.appendTo(ul);
	       			};

	       			$('#form-to').on('change', function() {
	       				if ($('#form-id').val()) {
	       					$(this).removeData('email');
	       					$(this).removeData('telephone');
	       					$('#form-id').val('');
	       				}
	       			});


					$('#form-type').on('click', function() {
						changeType(this);
					});

					$('#form-retard').on('click', function() {
						if ($(this).is(':checked')) { 
							$('input[name=date]').attr('disabled', false);
							$('input[name=time]').attr('disabled', false);
						} else {
							$('input[name=date]').attr('disabled', true);
							$('input[name=time]').attr('disabled', true);
						}
					});

					$('#form-titre, #form-sms, #form-email').on('change', function() {
						edited = true;
					});

					//Toutes les 30 secondes on regarde s'il y a eu des modifs si oui alors 
					//on fait une très simple requête pour éviter la déconnexion
					setInterval(function() {
						if (edited) {
							edited = false;
							$.ajax({
								url: "<?php url('admin/module/communication/publipostage'); ?>",
							  	method: "POST",
							  	cache: false,
								data:{connected: true}
							});
						}
					}, 30000);

					changeType($('#form-type'));

				});
				</script>
				<script type="text/javascript" src="<?php url('assets/js/tinymce.communication.js'); ?>"></script>

		

<?php 

//Inclusion du pied de page
require DIR.'templates/_footer.php';
