<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* templates/public/signature.php **************************/
/* Template pour la vérification des données + signature ***/
/* *********************************************************/
/* Dernière modification : le 16/01/16 *********************/
/* *********************************************************/


//Inclusion de l'entête de page
require DIR.'templates/public/_header_signature.php';

$uid = $participant['uid'];

?>	
				<style type="text/css">
				@media(max-width: 800px) {
					div.bloc {
						padding:0px !important;
						margin:0px !important;
					}
				}
				</style>

				<?php if ($allow && !empty($_POST['signal'])) { ?>

				<div class="alerte alerte-attention">
					<div class="alerte-contenu">
						De manière succinte, décris les erreurs présentes dans les données renseignées.<br />
						Ton responsable et l'équipe organisatrice du Challenge seront avertis, mais il te faudra cependant revenir sur cette page pour vérifier les mises à jour afin de finaliser l'inscription.
					</div>
				</div>

				<form method="post">

					<fieldset class="signature">
					<div>
						<h3>Remarques sur les données</h3>

						<textarea name="remarques" style="width:100%"><?php echo empty($participant['erreur']) ? "" : stripslashes($participant['erreur']); ?></textarea>
					</div>
					</fieldset>

					<center>
						<input type="submit" name="main" value="Annuler" />
						<input type="submit" name="error" class="success" value="Soumettre ces remarques" />
					</center>
				</form>

				<?php } else if ($allow && !empty($_POST['valid'])) { ?>

				<div class="alerte alerte-attention">
					<div class="alerte-contenu">
						Il est crucial que tu acceptes le réglement intérieur afin de finaliser ton inscription.<br />
						Sans ton accord, ta participation au Challenge est compromise.
					</div>
				</div>

				
					<?php 
					$fichierPrefixe = DIR.'templates/public/reglement_';
					$lang = file_exists($fichierPrefixe.$participant['lang'].'.php') ? $participant['lang'] : 'fr';
					?>
					
					<div class="iframe">
						<center>
					<?php 
					echo file_get_contents($fichierPrefixe.$lang.'.php');
				
					?>
				</center>
					</div>
					
					
				
				<?php if ($participant['cameraman']) {?>
					<div class="iframe">
						<center>
					<?php 
						echo file_get_contents(DIR.'templates/public/reglement_cameraman.php');
					?>
				</center>
				</div>
				<?php }?>

				<form method="post">
					<center>
						<button type="submit" name="signature" class="button success" value="1">
							<?php if ($participant['lang'] == 'en') { ?>By clicking this button, I certify that I've read <br />
							and accepted the present Regulation for Challenge <br />
							and so I finalize my inscription<?php } else { ?>
							En cliquant sur ce bouton, j'atteste avoir lu et accepté<br />
							le présent réglement intérieur du Challenge, <br />
							et ainsi je finalise mon inscription<?php } ?>
						</button>
					</center>
				</form>

				<?php } else { ?>

				<?php if ($participant['signature']) { ?>
				<div class="alerte alerte-success">
					<div class="alerte-contenu">
						<b>Bonjour cher/chère participant(e) au Challenge !</b><br />
						Tout est bon pour toi ! Tu as vérifié tes données et signé le règlement intérieur
						<?php echo printDateTime($participant['signature']); ?>.<br />
						Tu peux accéder au règlement en cliquant sur <a href="<?php url('reglement'); ?>">ce lien</a>.
					</div>
				</div>

				<?php } else if (!empty($participant['erreur'])) { ?> 

				<div class="alerte alerte-attention">
					<div class="alerte-contenu">
						<b>Bonjour cher/chère participant(e) au Challenge !</b><br />
						Tu as constaté précédemment qu'il y avait des erreurs dans les données renseignées.
						Pour finaliser ton inscription tu es invité(e) à modifier tes remarques ou bien à valider définitivement les données si celles-ci ont été mises à jour.<br />
						Laisse quelques jours passer pour que les responsables modifient les fausses données.<br />
						<br />
						Pour information voici tes remarques : <br />
						<i><?php echo nl2br(strip_tags($participant['erreur'])); ?></i>
					</div>
				</div>

				<?php } else { ?> 

				<div class="alerte alerte-info">
					<div class="alerte-contenu">
						<b>Bonjour cher/chère participant(e) au Challenge !</b><br />
						Tu as été inscrit(e) par un responsable de ton école, mais pour finaliser ton inscription il est nécessaire que tu vérifies et valides les données renseignées. 
					</div>
				</div>

				<?php } ?>

				<fieldset class="signature">
					<div>
						<h3>Données personnelles</h3>

						<div class="bloc">
							<label>
								<span>Nom</span>
								<?php echo stripslashes($participant['nom']); ?>
							</label>

							<label>
								<span>Prénom</span>
								<?php echo stripslashes($participant['prenom']); ?>
							</label>

							<label>
								<span>Sexe</span>
								<input type="checkbox" disabled <?php if ($participant['sexe'] == 'h') echo 'checked '; ?>/>
								<label class="sexe"></label>
							</label>

							<label>
								<span>Téléphone</span>
								<?php echo stripslashes($participant['telephone']); ?>
							</label>

							<label>
								<span>Email</span>
								<?php echo stripslashes($participant['email']); ?>
							</label>
						</div>

						<div class="bloc">
							<label>
								<span>Ecole</span>
								<?php echo stripslashes($participant['enom']); ?>
							</label>

							<label>
								<span>Sportif</span>
								<input type="checkbox" disabled <?php if ($participant['sportif']) echo 'checked '; ?>/>
								<label class="nodisabled"></label>
							</label>

							<?php if ($participant['pompom']) { ?> 

							<label>
								<span>Pompom</span>
								<input type="checkbox" disabled checked />
								<label class="extra-pompom"></label>
							</label>

							<?php } if ($participant['cameraman']) { ?> 

							<label>
								<span>Cameraman</span>
								<input type="checkbox" disabled checked />
								<label class="extra-cameraman"></label>
							</label>

							<?php } if ($participant['fanfaron']) { ?> 

							<label>
								<span>Fanfaron</span>
								<input type="checkbox" disabled checked />
								<label class="extra-fanfaron"></label>
							</label>

							<?php } ?>


						</div>
					</div>
				</fieldset>

				<?php if ($participant['sportif']) { ?>

				<fieldset class="signature">
					<h3>Données en tant que sportif</h3>

					<div class="bloc">
						<label>
							<span>Licence</span>
							<?php echo stripslashes($participant['licence']); ?>
						</label>

						<?php foreach ($sportifs as $sportif) {	?>

						<label>
							<span>Sport</span>
							<?php echo stripslashes($sportif['sport']).' '.printSexe($sportif['sexe']); ?>
						</label>

						<label>
							<span>Capitaine</span>
							<input type="checkbox" disabled <?php if ($id_participant == $sportif['id_capitaine']) echo 'checked '; ?>/>
							<label class="nodisabled"></label>
						</label>

						<?php } ?>

					</div>

					<?php if (empty($sportifs)) { ?>

					<div class="alerte alerte-erreur" style="width:auto; margin-top:-1em;">
						<div class="alerte-contenu">
							Tu n'es inscrit dans aucune équipe. Alerte rapidement ton responsable puisqu'il t'est impossible de continuer sans cela.
						</div>
					</div>

					<?php } ?>

				</fieldset>

				<?php } ?>

				<fieldset class="signature">
					<h3>Données sur le tarif et le logement</h3>

					<div class="bloc">
						<label>
							<span>Tarif</span>
							<?php echo stripslashes($participant['tnom']); ?>
							<small style="margin-left:100px; margin-top:-5px">
								<?php echo stripslashes($participant['description']).
										' ('.printMoney($participant['tarif']).')'; ?></small>
						</label>

						<label>
							<span>Gourde (Oui=4€,Non=0€)</span>
							<?php echo printMoney($participant['recharge']); ?>
						</label>

						<label>
							<span>Total</span>
							<?php echo printMoney($participant['recharge']+$participant['tarif']); ?>
						</label>
					</div>

					<div class="bloc">
						<label>
							<span>Logement</span>
							<input type="checkbox" disabled <?php if ($participant['logement']) echo 'checked '; ?>/>
							<label class="notdisabled"></label>
						</label>

						<?php if (!$participant['logement'] && !$participant['ecole_lyonnaise']) { ?>

						<label>
							<span>Logeur</span>
							<?php echo stripslashes($participant['logeur']); ?>
						</label>

						<?php } ?>
					</div>
				</fieldset>

				<?php if ($allow) { ?>

				<form method="post">
					<center>
						<input type="submit" name="signal" class="delete" value="Il y a des erreurs" />
						<input type="submit" name="valid" class="success" value="Tout est correct" />
					</center>
				</form>

				<?php } ?>

				<?php } ?>

<?php

//Inclusion du pied de page
require DIR.'templates/_footer.php';
