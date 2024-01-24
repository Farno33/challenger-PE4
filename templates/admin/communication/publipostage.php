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
				
				<?php if (empty($nextStep)) { ?>

				<h3><u>Étape 1 :</u> Rédaction du message</h3>
				<br />

				<form method="post" class="form-table" onsubmit="return checkForm()">
					<fieldset>
						<h4 style="margin-top:0">Paramètres généraux</h4>
						
						<label for="form-type">
							<span>Type</span>
							<input type="checkbox" name="type" id="form-type" />
							<label for="form-type" class="type-message"></label>
						</label>

						<label for="form-null">
							<span>Retardé</span>
							<input type="checkbox" name="retard" id="form-retard" />
							<label class="two_input" for="form-retard"></label>

							<input class="four_input" type="date" name="date" disabled placeholder="jj/mm/aaaa" />
							<input class="four_input" type="time" name="time" disabled placeholder="hh:mm" />
						</label>

						<label for="form-null">
							<span>Forcé</span>
							<input type="checkbox" name="force" id="form-force" />
							<label class="two_input" for="form-force"></label>
							<input class="two_input" type="text" name="to" disabled data-email="<?php echo $user['email']; ?>" data-telephone="<?php echo $user['telephone']; ?>" />
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
							<input type="text" id="form-titre" name="titre" />
						</label>

						<label for="form-email">
							<span>Contenu</span>
							<textarea style="height:30em !important" id="form-email" name="email"></textarea>
						</label>

						<label for="form-sms" style="display:none">
							<span>Contenu</span>
							<textarea id="form-sms" name="sms"></textarea>
						</label>
					</fieldset>

					<center><input type="submit" name="next" value="Choix des participants" class="success" /></center>
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

						if ($('#form-force').is(':checked'))
							$('input[name=to]').val($('#form-type').is(':checked') ? $('input[name=to]').data('telephone') : $('input[name=to]').data('email'));
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

					$('#form-force').on('click', function() {
						if ($(this).is(':checked')) { 
							$('input[name=to]').attr('disabled', false);
							$('input[name=to]').val($('#form-type').is(':checked') ? $('input[name=to]').data('telephone') : $('input[name=to]').data('email'));
						} else {
							$('input[name=to]').attr('disabled', true).val('');
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
				});
				</script>
				<script type="text/javascript" src="<?php url('assets/js/tinymce.communication.js'); ?>"></script>

				<?php } else { ?>

				<h3><u>Étape 2 :</u> Choix des destinataires</h3>

				<style type="text/css">
				@media (max-width: 800px) {
					#filters,
					#participants,
					#page {
						width:100% !important;
						margin-right:0px !important;
						margin-left:0px !important;
						float:none !important;
					}
				}
				</style>


				<table id="filters" style="width:250px; float:left; margin-right:20px">
					<thead>
						<tr>
							<th style="width:50px"></th>
							<th>Filtre</th>
						</tr>
					</thead>

					<tbody>
						<tr class="form">
							<td>
								<input type="checkbox" id="filter-sportif" />
								<label for="filter-sportif" />
							</td>
							<td>
								<select id="form-sportif">
									<option value="" selected disabled>Sportif</option>
									<option value="1">Sportif : Oui</option>
									<option value="0">Sportif : Non</option>
								</select>
							</td>
						</tr>

						<tr class="form">
							<td>
								<input type="checkbox" id="filter-capitaine" />
								<label for="filter-capitaine" />
							</td>
							<td>
								<select id="form-capitaine">
									<option value="" selected disabled>Capitaine</option>
									<option value="1">Capitaine : Oui</option>
									<option value="0">Capitaine : Non</option>
								</select>
							</td>
						</tr>

						<tr class="form">
							<td>
								<input type="checkbox" id="filter-logement" />
								<label for="filter-logement" />
							</td>
							<td>
								<select id="form-logement">
									<option value="" selected disabled>Logement</option>
									<option value="1">Logement : Oui</option>
									<option value="0">Logement : Non</option>
								</select>
							</td>
						</tr>

						<tr class="form">
							<td>
								<input type="checkbox" id="filter-sexe" />
								<label for="filter-sexe" />
							</td>
							<td>
								<select id="form-sexe">
									<option value="" selected disabled>Sexe</option>
									<option value="h">Sexe : Homme</option>
									<option value="f">Sexe : Femme</option>
								</select>
							</td>
						</tr>

						<tr class="form">
							<td>
								<input type="checkbox" id="filter-fanfaron" />
								<label for="filter-fanfaron" />
							</td>
							<td>
								<select id="form-fanfaron">
									<option value="" selected disabled>Fanfaron</option>
									<option value="1">Fanfaron : Oui</option>
									<option value="0">Fanfaron : Non</option>
								</select>
							</td>
						</tr>

						<tr class="form">
							<td>
								<input type="checkbox" id="filter-pompom" />
								<label for="filter-pompom" />
							</td>
							<td>
								<select id="form-pompom">
									<option value="" selected disabled>Pompom</option>
									<option value="1">Pompom : Oui</option>
									<option value="0">Pompom : Non</option>
								</select>
							</td>
						</tr>

						<tr class="form">
							<td>
								<input type="checkbox" id="filter-cameraman" />
								<label for="filter-cameraman" />
							</td>
							<td>
								<select id="form-cameraman">
									<option value="" selected disabled>Caméraman</option>
									<option value="1">Caméraman : Oui</option>
									<option value="0">Caméraman : Non</option>
								</select>
							</td>
						</tr>

						<tr class="form">
							<td>
								<input type="checkbox" id="filter-retard" />
								<label for="filter-retard" />
							</td>
							<td>
								<select id="form-retard">
									<option value="" selected disabled>Retard</option>
									<option value="1">Retard : Oui</option>
									<option value="0">Retard : Non</option>
								</select>
							</td>
						</tr>

						<tr class="form">
							<td>
								<input type="checkbox" id="filter-signature" />
								<label for="filter-signature" />
							</td>
							<td>
								<select id="form-signature">
									<option value="" selected disabled>Signature</option>
									<option value="1">Signature : Oui</option>
									<option value="0">Signature : Non</option>
								</select>
							</td>
						</tr>

						<tr>
							<th colspan="2"></th>
						</tr>

						<tr class="form">
							<td>
								<input type="checkbox" id="filter-ecole" />
								<label for="filter-ecole" />
							</td>
							<td>
								<select id="form-ecole">
									<option value="" selected disabled>Choisissez une école</option>

									<?php foreach ($ecoles as $ecole) { ?>
									<option value="<?php echo $ecole['id']; ?>"><?php echo stripslashes($ecole['nom']); ?></option>
									<?php } ?>

								</select>
							</td>
						</tr>

						<tr class="form">
							<td>
								<input type="checkbox" id="filter-sport" />
								<label for="filter-sport" />
							</td>
							<td>
								<select id="form-sport">
									<option value="" selected disabled>Choisissez un sport</option>

									<?php foreach ($sports as $sport) { ?>
									<option value="<?php echo $sport['id']; ?>"><?php echo stripslashes($sport['sport'].' '.strip_tags(printSexe($sport['sexe']))); ?></option>
									<?php } ?>

								</select>
							</td>
						</tr>

						<tr class="form">
							<td>
								<input type="checkbox" id="filter-chambre" />
								<label for="filter-chambre" />
							</td>
							<td>
								<select id="form-chambre">
									<option value="" selected disabled>Choisissez une chambre</option>

									<?php foreach ($chambres as $chambre) { ?>
									<option value="<?php echo $chambre['id']; ?>"><?php echo stripslashes($chambre['numero']); ?></option>
									<?php } ?>

								</select>
							</td>
						</tr>

						<tr>
							<th colspan="2"></th>
						</tr>

						<tr>
							<td colspan="2">
								<center>
									<label><button class="button success" onclick="refresh()">Rafraîchir</button></label><br />
								</cenyet>
							</td>
						</tr>

						<tr>
							<th colspan="2"></th>
						</tr>


						<tr>
							<td colspan="2">
								<center>
									<a href="#" onclick="refresh(true); return false">Sélectionnés</a> : <b id="selected">0</b><br /><br />
									<form method="post" onclick="return sendForm()">
										<input type="hidden" name="titre" value="<?php echo htmlentities($titre); ?>" />
										<input type="hidden" name="contenu" value="<?php echo htmlentities($contenu); ?>" />
										<input type="hidden" name="to" value="<?php echo $to; ?>" />
										<input type="hidden" name="retard" value="<?php echo $date; ?>" />
										<input type="hidden" name="type" value="<?php echo $isSms ? 'sms' : 'email'; ?>" />
										<input type="submit" name="send" value="Envoyer les messages" style="padding:5px !important; " /><br />
									</form>
								</center>
							</td>
						</tr>
					</tbody>
				</table>

				<table id="page" style="width:calc(100% - 270px); margin-bottom:20px; display:none">
					<tbody>
						<tr>
							<td>
								<center>
									<label style="float:left">
										<button class="button" onclick="firstPage()">&lt;&lt;</button>
										<button class="button" onclick="previousPage()">&lt;</button>
									</label>
									Page : <b id="currentPage">-</b> / <span id="totalPages">-</span>
									<label style="float:right">
										<button class="button" onclick="nextPage()">&gt;</button>
										<button class="button" onclick="lastPage()">&gt;&gt;</button>
									</label>
								</center>
							</td>
						</tr>

						<tr class="form">
							<td style="padding-top:10px !important">
								<center>
									<button class="button success" onclick="selectGroup(true, true)">Tous (filtrés)</button>
									<button class="button delete" onclick="selectGroup(false, true)">Aucun (filtrés)</button><br />
									<button class="button" onclick="selectGroup(true, false)">Tous (page)</button>
									<button class="button" onclick="selectGroup(false, false)">Aucun (page)</button>
								</center>
							</td>
						</tr>
					</tbody>
				</table>

				<table id="participants" style="width:calc(100% - 270px)">
					<thead>
						<tr class="form">
							<td style="width:50px">
								<select id="cols">
									<option selected disabled>+</option>
								</select>
							</td>
						</tr>
					</thead>

					<tbody>
					</tbody>
				</table>

				<div class="clearfix"></div>

				<script type="text/javascript">
				$(function() {
					var xhr;
					var participants = [];
					var limit = 100;
					var currentPage = 1;
					var selected = [];
					var init = false;
					var cols = ['nom', 'prenom', 'email', 'telephone'];

					sendForm = function() {
						if (selected.length == 0) {
							alert('Au moins un participant doit être sélectionné');
							return false;
						}

						$('input[name="participants[]"]').remove();
						for (var i in selected) {
							$('input[type=submit]').after('<input type="hidden" name="participants[]" value="' + selected[i] + '" />');
						}

						return true;
					};

					removeFromSelected = function(toDelete) {
						selected = $.grep(selected, function(value) {
						  return value != toDelete;
						});
						
						$('#selected').html(selected.length);
					};

					addToSelected = function(toAdd) {
						if ($.inArray(toAdd, selected) < 0)
							selected.push(toAdd);

						$('#selected').html(selected.length);
					};

					refresh = function(onlySelected) {
						if (xhr)
							xhr.abort();

						init = true;
						$('#participants tbody tr').remove();
						$('#participants tbody').append('<tr>' +
							'<td colspan="' + (cols.length + 1) + '"><center>Chargement en cours...</center></td>' +
							'</tr>');
						$('#currentPage').html('-');
						$('#totalPages').html('-');
						$('#page').css('display', 'none');

						var data = {};

						if (onlySelected === true) {
							data['selected'] = true;
							data['ids'] = selected;
						} else {
							var keys = ['sportif', 'sexe', 'fanfaron', 'cameraman', 'pompom', 'ecole', 'sport', 'capitaine', 'chambre', 'logement', 'retard', 'signature'];
							for (var j in keys) {
								
								if ($('#filter-' + keys[j]).is(':checked') &&
									$('#form-' + keys[j]).val() !== '' &&
									typeof $('#form-' + keys[j]).val() === 'string') {
									data[keys[j]] = $('#form-' + keys[j]).val();
								}
							}
						}

						xhr = $.ajax({
							url: "<?php url('admin/module/communication/publipostage?filter'); ?>",
							method: "POST",
							cache: false,
							dataType: "json",
							data: data,
							success: function(data) {
								participants = data;
								printPage(participants.length ? 1 : 0);
								$('#totalPages').html(Math.ceil(participants.length / limit) + 
									(participants.length ? ' (' + participants.length + ' participants)' : ''));

								if (participants.length)
									$('#page').css('display', 'table');
							}
						});
					};

					printPage = function(page) {
						var i, count = 0;
						var from = (page - 1) * limit + 1;
						var to = from + limit - 1;
						currentPage = page; 
						$('#currentPage').html(currentPage);


						$('#participants tbody tr').remove();

						if (!init)
							$('#participants tbody').append('<tr><td colspan="' + (cols.length + 1) + '"><center><b>Choisissez les filtres pour sélectionner les participants</b></center></td></tr>');

						else if (!participants.length)
							$('#participants tbody').append('<tr class="vide"><td colspan="' + (cols.length + 1) + '">Aucun participant ne correspond à la demande</td></tr>');
						
						for (i in participants) {
							++count;

							if (count < from)
								continue;

							if (count > to)
								break;

							$('#participants tbody').append('<tr data-id="' + participants[i].id + '" class="form' + ($.inArray(participants[i].id, selected) >= 0 ? ' selected' : '') + '">' +
								'<td><input type="checkbox" ' + ($.inArray(participants[i].id, selected) >= 0 ? ' checked' : '') + ' /><label></label></td>' +
								($.inArray('nom', cols) >= 0 ? '<td><div>' + participants[i].nom + '</div></td>' : '') +
								($.inArray('prenom', cols) >= 0 ? '<td><div>' + participants[i].prenom + '</div></td>' : '') +
								($.inArray('sexe', cols) >= 0 ? '<td><input type="checkbox" readonly ' + (participants[i].sexe == 'h' ? 'checked ' : '') + '/><label class="sexe"></label></td>' : '') +
								($.inArray('fanfaron', cols) >= 0 ? '<td><input type="checkbox" readonly ' + (participants[i].fanfaron == '1' ? 'checked ' : '') + '/><label class="extra extra-fanfaron"></label></td>' : '') +
								($.inArray('pompom', cols) >= 0 ? '<td><input type="checkbox" readonly ' + (participants[i].pompom == '1' ? 'checked ' : '') + '/><label class="extra extra-pompom"></label></td>' : '') +
								($.inArray('cameraman', cols) >= 0 ? '<td><input type="checkbox" readonly ' + (participants[i].cameraman == '1' ? 'checked ' : '') + '/><label class="extra extra-video"></label></td>' : '') +
								($.inArray('ecole', cols) >= 0 ? '<td><div>' + participants[i].ecole + '</div></td>' : '') +
								($.inArray('email', cols) >= 0 ? '<td><div>' + participants[i].email + '</div></td>' : '') +
								($.inArray('telephone', cols) >= 0 ? '<td><div>' + participants[i].telephone + '</div></td>' : '') +
								($.inArray('sportif', cols) >= 0 ? '<td><input type="checkbox" readonly ' + (participants[i].sportif == '1' ? 'checked ' : '') + '/><label></label></td>' : '') +
								($.inArray('capitaine', cols) >= 0 ? '<td><input type="checkbox" readonly ' + (participants[i].capitaine == '1' ? 'checked ' : '') + '/><label class="capitaine"></label></td>' : '') +
								($.inArray('sport', cols) >= 0 ? '<td><div>' + participants[i].sport + '</div></td>' : '') +
								($.inArray('equipe', cols) >= 0 ? '<td><div>' + participants[i].equipe + '</div></td>' : '') +
								($.inArray('logement', cols) >= 0 ? '<td><input type="checkbox" readonly ' + (participants[i].logement == '1' ? 'checked ' : '') + '/><label class="package"></label></td>' : '') +
								($.inArray('chambre', cols) >= 0 ? '<td><div>' + participants[i].chambre + '</div></td>' : '') +
								($.inArray('retard', cols) >= 0 ? '<td><input type="checkbox" readonly ' + (participants[i].retard == '1' ? 'checked ' : '') + '/><label></label></td>' : '') +
								($.inArray('signature', cols) >= 0 ? '<td><input type="checkbox" readonly ' + (participants[i].signature == '1' ? 'checked ' : '') + '/><label></label></td>' : '') +
								'</tr>');
						}
					};

					previousPage = function() {
						if (participants.length)
							printPage(Math.max(currentPage - 1, 1));
					};

					nextPage = function() {
						if (participants.length)
							printPage(Math.min(Math.ceil(participants.length / limit), currentPage + 1));
					};

					firstPage = function() {
						if (participants.length)
							printPage(1);
					};

					lastPage = function() {
						if (participants.length)
							printPage(Math.ceil(participants.length / limit));
					};

					selectGroup = function(select, all) {
						var i, count = 0;
						var from = all ? 1 : (currentPage - 1) * limit + 1;
						var to = all ? participants.length : from + limit - 1;

						for (i in participants) {
							++count;

							if (count < from)
								continue;

							if (count > to)
								break;

							if (select)
								addToSelected(participants[i].id);

							else 
								removeFromSelected(participants[i].id);
						}

						if (participants.length)
							printPage(currentPage);
					};

					refreshCols = function() {
						var select = $('#cols');
						var thead = select.parent().parent();
						thead.children('th').remove();
						select.children('option:not(:first)').remove();

						var cols_detailled = {
							nom: 		'Nom',
							prenom: 	'Prénom',
							sexe: 		'Sexe',
							fanfaron: 	'Fanfaron',
							pompom: 	'Pompom',
							cameraman: 	'Caméraman',
							ecole: 		'Ecole',
							email: 		'Email',
							telephone: 	'Téléphone',
							sportif: 	'Sportif',
							capitaine: 	'Capitaine',
							sport: 		'Sport',
							equipe: 	'Equipe',
							logement: 	'Logement',
							chambre: 	'Chambre',
							retard: 	'Retard',
							signature: 	'Signature'};

						for (var i in cols_detailled) {
							if ($.inArray(i, cols) < 0)
								select.append('<option value="' + i + '">' + cols_detailled[i] + '</th>');

							else
								thead.append('<th data-col="' + i + '">' + cols_detailled[i] + '</th>');
						}

						printPage(currentPage);
					};

					$('#participants tbody').delegate('tr','click', function() {
						var check = $(this).find('td:first input[type=checkbox]');
						check.click();
					
						if (check.is(':checked')) {
							$(this).addClass('selected');
							addToSelected($(this).attr('data-id'));
						}

						else {
							$(this).removeClass('selected');
							removeFromSelected($(this).attr('data-id'));
						}
					});

					$('#filters').delegate('input[type="checkbox"]','click', function() {
						if ($(this).is(':checked'))
							$(this).parent().parent().addClass('selected');

						else 
							$(this).parent().parent().removeClass('selected');
					});

					$('#cols').parent().parent().delegate('th','click', function() {
						var toDelete = $(this).attr('data-col');
						cols = $.grep(cols, function(value) {
						  return value != toDelete;
						});

						refreshCols();
					});

					$('#cols').change(function() {
						var sel = $(this).children('option:selected');
						sel.remove();
						cols.push(sel.val());

						refreshCols();
						$(this).children('option:first').removeAttr('disabled').prop('selected', true);
					});

					refreshCols();
				});
				</script>

				<?php } ?>

		

<?php 

//Inclusion du pied de page
require DIR.'templates/_footer.php';
