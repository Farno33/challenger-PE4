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

require DIR . 'templates/centralien/_header_centralien.php';

// (new DateTime(APP_FIN_PHASE1))->format('d F H:i') malheuresement pas i18n (et en nglois en plus quelle honte)
?>
<h2>Prestations Centraliennes</h2>

<form class="form-table" method="post" <?php if (!$possibleEdition) echo ' onsubmit="alert(\'Trop tard !\'); return false;"'; ?>>
	<fieldset>
		<h3>
			Options<br />
			<small style="color:gray; font-weight:normal; font-size:65%">Date limite d'édition : <?php echo printDateTime(APP_FIN_CENTRALIENS, false); ?></small>
		</h3>

		<label for="form-soiree">
			<span data-image="<?php url('assets/images/presta/soiree.png'); ?>" data-question="La soirée du Samedi, c’est la plus grosse soirée de l’année : 2000 personnes entre le foyer et un chapiteau monté pour l’occasion. <br /><br />Les équipes du bar et du bar // serviront toute la soirée. Pour vous faire vibrer, des artistes professionnels seront invités sur scène aux côtés de DBS. La soirée commencera dès 21h.">Soirée</span>
			<input <?php if (!$possibleEdition) echo ' disabled'; ?> name="soiree" id="form-soiree" type="checkbox" <?php if (!empty($options['soiree'])) echo 'checked'; ?> />
			<label for="form-soiree"></label>

			<small>Le prix de la soirée est de <b><?php echo APP_CENTRALIENS_PRIX_SOIREE; ?>€</b></small>
		</label>

		<label for="form-pfsamedi">
			<span>Pack-foods</span>
			<?php if ($possible_packsfull && $options['sportif']) { ?>
				<input <?php if (!$possibleEdition) echo ' disabled'; ?> name="pfsamedi" id="form-pfsamedi" type="checkbox" <?php if (!empty($options['pfsamedi'])) echo 'checked'; ?> />
				<label for="form-pfsamedi" class="package"></label>
				<small>
					En tant que sportif, un sandwich pour le samedi midi est compris dans votre participation. Pour <b><?php echo APP_CENTRALIENS_PRIX_PFOOD; ?>€</b> de plus tu as accès au full package : petit dejeuner le samedi matin + sandwich du samedi midi + brunch le dimanche (il en reste <?php echo $nb_pf_max - $nb_pf; ?>)
				</small>
			<?php } else if (!$options['sportif']) { ?>
				<b> Les pack-foods sont réservés aux sportifs. (Inscriptions en bas de la page)</b>
			<?php } else { ?>
				<b> En tant que sportif, un packfood est compris dans votre participation (samedi midi). Il n'y a plus de full package</b>
			<?php } ?>

		</label>
		<label for="form-tshirt">
			<span data-image="<?php url('assets/images/presta/tshirt.png'); ?>" data-question="Le T-shirt participant, c’est un T-shirt de sport en tissu respirant (pas du simple coton) aux couleurs du Challenge. En plus d’être un T-shirt de qualité, c’est un bon souvenir à garder de l’événement.">T-Shirt</span>
			<?php if ($possible_tshirt && $options['sportif'] == 0) { ?>
				<input <?php if (!$possibleEdition) echo ' disabled'; ?> name="tshirt" id="form-tshirt" type="checkbox" <?php if (!empty($options['tshirt'])) echo 'checked'; ?> />
				<label for="form-tshirt"></label>
				<small>À récupérer lors du paiement (+<b><?php echo APP_CENTRALIENS_PRIX_TSHIRT; ?>€</b>). Compris dans la prestation pour les sportifs. Il en reste : <?php echo $nb_tshirt_max - $nb_tshirt; ?></small>
			<?php } else if ($options['sportif']) { ?>
				<b>En tant que sportif, un t-shirt est compris dans votre participation</b>
			<?php } else { ?>
				<b>Il n'y a plus de t-shirt disponible pour les non-sportifs</b>
			<?php } ?>
		</label>

		<label for="form-gourde">
			<span data-image="<?php url('assets/images/presta/gourde.png'); ?>" data-question="Afin de réduire la consommation d'eau et de bouteilles en plastique, le Challenge propose à ses participants une gourde aux couleurs du Challenge et réutilisable en dehors de l'événement.">Gourde</span>
			<?php if ($possible_gourde) { ?>
				<input <?php if (!$possibleEdition) echo ' disabled'; ?> name="gourde" id="form-gourde" type="checkbox" <?php if (!empty($options['gourde'])) echo 'checked'; ?> />
				<label for="form-gourde"></label>
				<small>À récupérer lors du paiement (+<b><?php echo APP_CENTRALIENS_PRIX_GOURDE; ?>€</b>). Il en reste : <?php echo $nb_gourdes_max - $nb_gourdes; ?> </small>
			<?php } else { ?>
				<b>Il n'y a plus de gourde disponible</b>
			<?php } ?>
		</label>

		<label for="form-tombola">
			<span data-image="<?php url('assets/images/presta/tombola.png'); ?>" data-question="Prends un ou plusieurs tickets pour participer à notre tombola et gagner un des nombreux lots mis en jeu ! Tu pourras notamment repartir avec ton poids en cacahuètes et participer à toutes sortes de jeux et d'activités le vendredi soir avec ces tickets !">Tombola</span>
			<input <?php if (!$possibleEdition) echo ' disabled'; ?> name="tombola" id="form-tombola" type="number" step="1" min="0" value="<?php if (!empty($options['tombola'])) echo (int) $options['tombola']; ?>" />
			<small>Les tickets sont à <b>2€</b> l'unité, <b>4€</b> les trois, <b>6€</b> les cinq et <b>10€</b> les dix. </small>
		</label>

		<label>
			<span>Total</span>
			<div><b name="price">0 €</b><?php
										if (
											!empty($options['id_tarif_ecole']) &&
											$tarifs[$tarifs_ecoles[$options['id_tarif_ecole']]['id_tarif']]['tarif'] > 0
										)
											echo ' (Sport : +' . $tarifs[$tarifs_ecoles[$options['id_tarif_ecole']]['id_tarif']]['tarif'] . ' €)'; ?></div>
		</label>

		<center name="savesubmit" style="height: 0; overflow: hidden; animation: 0.5s;">
			<?php if ($possibleEdition) { ?>
				<input name="save" type="submit" value="Sauvegarder mes options" onclick="window.onbeforeunload = null; return true;"/>
			<?php } ?>
		</center>
	</fieldset>
</form>

<div id="modal-question" class="modal"></div>
<style type="text/css">
	.modal img {
		display: block;
		margin: 0px auto 30px;
		max-width: 100%;
	}

	#simplemodal-container {
		height: 500px;
		max-height: calc(100% - 50px);
	}

	@media (max-width: 800px) {
		.modal img {
			float: none;
			margin-bottom: 10px;
			width: 100%;
		}

		#simplemodal-container {
			width: 90% !important;
			left: 5% !important;
			height: 400px;
			top: 20px;
		}
	}
</style>



<script type="text/javascript">
	/*
	$(function() {
		$('span[data-question]').click(function(e) {
			e.preventDefault();
			e.stopPropagation();
			$("#modal-question").html(
				($(this).data('image') ? '<img src="' + $(this).data('image') + '" />' : '') +
				$(this).data('question')).modal();
		}).css('cursor', 'pointer').append(' <img src="<?php //url('assets/images/actions/question.png'); 
														?>" />');
	});*/
</script>

<?php if (!empty($options['id_participant'])) { ?>
	<form class="form-table" method="post" action="#sports" <?php if (!$possibleEdition) echo ' onsubmit="alert(\'Trop tard !\'); return false;"'; ?>>
		<fieldset>
			<h3>
				<a name="sports"></a>Sports<br />
				<small style="color:gray; font-weight:normal; font-size:65%">Date limite d'édition : <?php echo printDateTime(APP_FIN_CENTRALIENS, false); ?></small>
			</h3>

			<?php if ($possibleEdition) { ?>

				<label for="form-null">
					<span>Licence<span style="color: red; display: inline;">*</span></span>
					<input type="text" placeholder="MG1E XXXXXX ou, ssi vous n'avez pas de licence (cf. texte en dessous), la mention 'Certificat médical ou questionnaire'" name="licence" id="form-licence" class="one_input" value="<?php echo empty($options['licence']) ? '' : $options['licence']; ?>" pattern="^((([A-Za-z0-9]{4})[^0-9A-Za-z]?([0-9]{6}),?)*$)|(^[^,0-9]*)$" />
					<small>Vous pouvez chercher votre numéro de licence <a href="http://www.sport-u-licences.com/sport-u/">sur le site de la FFSU</a>. Si vous n'avez pas de licence, vous pouvez completer <a href="https://sport-u.com/wp-content/uploads/2022/06/20220613_Questionnaire-de-sante-22-23.pdf">le questionnaire de la FFSU</a> ou fournir un certificat médical, indiquez alors "Certificat médical ou questionnaire" dans ce champ</small>
				</label>

				<label for="form-null">
					<span>Sexe<span style="color: red; display: inline;">*</span></span>
					<div class="fourthree_input">
						<input required type="radio" id="femme" name="sexe" value="f" <?php echo $options['sexe'] == 'f' ? 'checked' : ''; ?>>
						<label for="femme">Femme</label>
						<input required type="radio" id="homme" name="sexe" value="h" <?php echo $options['sexe'] == 'h' ? 'checked' : ''; ?>>
						<label for="homme">Homme</label>
					</div>
					<span>Téléphone</span>
					<input class="fourthree_input" type="tel" name="telephone" placeholder="Pour sports indiv. et capitaines" pattern="(^((((\+)|(00))33 ?0? ?)|0)[1-9] ?([0-9]{2}( |-)?){4}$)|(((\+)|(00))(?!33)(?!0)[0-9]*)" value="<?php echo empty($options['telephone']) ? '' : $options['telephone']; ?>">
				</label>

				<center>
					<input type="submit" value="Changer" name="change" />
				</center>
				<?php }
			if (!empty($options['licence']) && !empty($options['sexe'])) {
				if ($possibleEdition) { ?>
					<?php if (count($equipes) > 0) { ?>
						<label for="form-null">
							<span>Rejoindre</span>
							<select name="equipe" class="fourtwo_input">
								<option value=""></option>
								<?php

								$vc = [];
								$co = [];
								$indiv = [];
								$ver = [];
								foreach ($equipes as $eid => $equipe) {
									if (strpos($equipe["_message"], "locked") !== false) {
										$ver[$eid] = $equipe;
									} else if (strpos($equipe['label'], 'VC') !== false) {
										$vc[$eid] = $equipe;
									} else if (empty($equipe['individuel'])) {
										$co[$eid] = $equipe;
									} else {
										$indiv[$eid] = $equipe;
									}
								}

								if (count($vc)) { ?>

									<optgroup label="Vieux Cons">

										<?php foreach ($vc as $eid => $equipe) { ?>
											<option value="<?php echo $eid; ?>"><?php echo stripslashes($equipe['sport']) . ' ' .
																					printSexe($equipe['sexe']) . (empty($equipe['individuel']) ? ' / ' . stripslashes($equipe['label']) : ''); ?></option>
										<?php } ?>

									</optgroup>

								<?php }

								if (count($co)) { ?>

									<optgroup label="Sports collectifs">

										<?php foreach ($co as $eid => $equipe) { ?>
											<option value="<?php echo $eid; ?>"><?php echo stripslashes($equipe['sport']) . ' ' .
																					printSexe($equipe['sexe']) . (empty($equipe['individuel']) ? ' / ' . stripslashes($equipe['label']) : ''); ?></option>
										<?php } ?>

									</optgroup>

								<?php }
								if (count($indiv)) { /* // Wesh des équipes de sport indiv ? c'est juste un coup à avoir deux joueurs sur le même slot de competition... ?> 
									<optgroup label="Sports individuels (veillez à être joignable par les autres de ce groupe si vous n'avez pas de N° de téléphone)">

										<?php foreach ($indiv as $eid => $equipe) { ?>
											<option value="<?php echo $eid; ?>"><?php echo stripslashes($equipe['sport']) . ' ' .
																					printSexe($equipe['sexe']) . (empty($equipe['individuel']) ? ' / ' . stripslashes($equipe['label']) : ''); ?></option>
										<?php } ?>

									</optgroup>

										<?php */
								}
								if (count($ver)) { ?>
									<optgroup label="Équipes verrouillés" disabled>

										<?php foreach ($ver as $eid => $equipe) { ?>
											<option><?php echo stripslashes($equipe['sport']) . ' ' .
														printSexe($equipe['sexe']) . (empty($equipe['individuel']) ? ' / ' . stripslashes($equipe['label']) : ''); ?></option>
										<?php } ?>

									</optgroup>

								<?php } ?>

							</select>
							<div class="four_input">
								<input type="submit" value="Rejoindre" name="add" />
							</div>
							<small>Certains sports nécessitent un supplément (raid, tennis et ski)</small> <?php // TODO: Automatiser 
																											?>
						</label>

						<!-- DEPRECATED
						<label for="form-null">
							<span>Classement</span>
							<input type="text" name="ranking" id="form-ranking" class="fourtwo_input" value="<?//php if (!empty($options['ranking'])) echo (string) $options['ranking']; ?>" />
							<div class="four_input">
								<input type="submit" value="Valider" name="validate" /> 
							</div>
							<small>Merci d'indiquer un classement (pour le badminton, tennis, tennis de table) ou un temps de référence (pour la natation) si tu es classé(e), afin de faciliter le travail du VP Tournoi de ton sport.</small>
						</label>	
						-->

					<?php }
					if (count($sports) > 0 && !empty($options['telephone'])) { ?>
						<label for="form-null">
							<span>Nouvelle équipe ou sports individuels</span>
							<select name="sport" class="fourthree_input">
								<option value=""></option>
								<?php
								$co = [];
								$indiv = [];
								foreach ($sports as $sid => $sport) {
									if ($sport['individuel']) {
										$indiv[$sid] = $sport;
									} else {
										$co[$sid] = $sport;
									}
								}

								if (count($indiv)) { ?>

									<optgroup label="Sports individuels">
										<?php foreach ($indiv as $sid => $sport) { ?>
											<option value="<?php echo $sid; ?>"><?php echo $sport['sport'] . ' ' . printSexe($sport['sexe']); ?></option>
										<?php } ?>
									</optgroup>
								<?php }
								if (count($co)) { ?>
									<optgroup label="Sports collectifs">
										<?php foreach ($co as $sid => $sport) { ?>
											<option value="<?php echo $sid; ?>"><?php echo $sport['sport'] . ' ' . printSexe($sport['sexe']); ?></option>
										<?php } ?>
									</optgroup>
								<?php } ?>

							</select>
							<input type="text" name="label" id="form-label" class="fourthree_input" title="Nom de l'equipe (VC dans le nom pour une équipe de vieux cons)" placeholder="Nom de l'equipe (VC dans le nom pour une équipe de vieux cons)" />
							<div class="four_input">
								<input type="submit" value="Créer" name="add" />
							</div>
							<small>Certains sports nécessitent un supplément (raid, tennis et ski)</small> <?php // TODO: Automatiser 
																											?>
						</label>

					<?php } ?>
				<?php } ?>

				<?php if (count($mes_equipes)) { ?>

					<table class="table-small">
						<tr>
							<th>Sport</th>
							<th>Equipe</th>
							<th style="width:70px"><small>Supplément</small></th>
							<th class="actions">Actions</th>
						</tr>

						<?php foreach ($mes_equipes as $eid => $equipe) { ?>

							<tr class="form">
								<td class="content"><?php echo stripslashes($equipe['sport']) . ' ' . printSexe($equipe['sexe']); ?></td>
								<td class="content"><?php echo stripslashes($equipe['label']); ?> (<a href="<?php url('centralien/equipes'); ?>#e<?php echo $eid; ?>">Voir</a>)</td>
								<td class="content"><?php
													if ($tarifs[$tarifs_equipes[$eid]]['tarif'] > 0)
														echo printMoney($tarifs[$tarifs_equipes[$eid]]['tarif']); ?></td>
								<td class="content">
									<center>
										<?php if ($possibleEdition) { ?>
											<button type="submit" name="delete" value="<?php echo stripslashes($eid); ?>" <?php if ($equipe['cap'] == $options['id_participant'] && !$equipe['individuel']) echo ('onClick="return confirm(\'Êtes vous sûr ? Vous êtes le capitaine de cette équipe, si vous confirmez, votre équipe sera dissout !\')"'); ?> />
											<img src="<?php url('assets/images/actions/delete.png'); ?>" alt="Delete" />
											</button>
										<?php } ?>
									</center>
								</td>
							</tr>

						<?php } ?>

					</table>

				<?php } ?>

		</fieldset>
	</form>

<?php } else { ?>

	</fieldset>
	</form>

<?php }
		} ?>


<script type="text/javascript">
	$(function() {
		$getLicences = function() {
			$.ajax({
				url: "<?php url('centralien?ajax'); ?>",
				cache: false
			});
		};

		$actualisePrix = function(force) {
			var price = 0;
			var allInputs = $(":input");

			var tombola = $('input[name="tombola"]').val();


			if ($('input[name="soiree"]').is(':checked')) price += <?php echo APP_CENTRALIENS_PRIX_SOIREE; ?>;
			if ($('input[name="pfsamedi"]').is(':checked')) price += <?php echo APP_CENTRALIENS_PRIX_PFOOD; ?>;
			//$('div[name="vegetarien"]').css('visibility', 'visible');
			//}
			//else {
			//$('div[name="vegetarien"]').css('visibility', 'hidden');
			//$('input[name="vegetarien"]').prop( "checked", false );
			//}

			if ($('input[name="tshirt"]').is(':checked')) price += <?php echo APP_CENTRALIENS_PRIX_TSHIRT; ?>;
			if ($('input[name="gourde"]').is(':checked')) price += <?php echo APP_CENTRALIENS_PRIX_GOURDE; ?>;

			tombola = parseInt(tombola ? tombola : 0);

			if (!isNaN(tombola)) {

				if (tombola < 3) {
					tombola = tombola * 2;
				} else if (tombola < 5) {
					tombola = 2 * (tombola % 3) + (tombola - tombola % 3) * 4 / 3;
				} else if (tombola < 10) {
					tombola = 2 * (tombola % 5) + (tombola - tombola % 5) * 6 / 5;
				} else if (tombola >= 10) {
					tombola = 2 * (tombola % 10) + (tombola - tombola % 10);
				} else {
					tombola = tombola * 2;
				}
			}

			price += isNaN(tombola) ? 0 : tombola;

			$('b[name="price"]').html(price + '€');

			if (force) {
				$('center[name="savesubmit"]').css('height', '100%');
				window.onbeforeunload = function() {
					return "Vous n'avez pas enregistré vos modifications !";
				}
			}

		};

		$getLicences();
		$actualisePrix();

		$('form:first input').change(function() {
			$actualisePrix(true);
		});
	});
</script>


<?php

//Inclusion du pied de page
require DIR . 'templates/_footer.php';
