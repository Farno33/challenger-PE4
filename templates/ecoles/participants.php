<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* templates/ecoles/participants.php ***********************/
/* Template de la gestion des participants *****************/
/* *********************************************************/
/* Dernière modification : le 09/12/14 *********************/
/* *********************************************************/


//Inclusion de l'entête de page
require DIR.'templates/ecoles/_header_ecoles.php';
?>
			
				<h2>Inscription des Participants 
					<a style="display:inline; font-size:14px; vertical-align:middle" class="excel_big" href="?excel">Télécharger en XLSX</a></h2>

				<?php
				if (isset($add) ||
					isset($modify) ||
					isset($delete)) {
				?>

				<div class="alerte alerte-<?php echo isset($add) || isset($modify) || isset($delete) ? 'success' : 'erreur'; ?>">
					<div class="alerte-contenu">
						<?php
						if (!empty($modify)) echo 'Le participant a bien été édité';
						else if (!empty($delete)) echo 'Le participant a bien été supprimé';
						else if (!empty($add)) echo 'Le participant a bien été ajouté';
						?>
					</div>
				</div>

				<?php }

				else if (!empty($accesAdmin)) { 
						?>

				<div class="alerte alerte-attention">
					<div class="alerte-contenu">
						Vous avez les droits d'accès au module "Ecoles" de l'administration, 
						et à ce titre vous avez accès à toutes les fonctionnalités sans prise en compte de l'état d'inscription ou de la phase actuelle.
					</div>
				</div><br />

				<?php } ?>
					
				
				<?php if ((!empty($accesAdmin) || $ecole['etat_inscription'] == 'ouverte') && 
					(isset($quotas['total']) && $places_inscription > 0 || !isset($quotas['total']))) { 

					if (!empty($phase_actuelle) && $phase_actuelle != 'end' || !empty($accesAdmin)) { ?>

					<form method="post">
						<center>
							<input type="submit" name="add" id="ajout_participant" class="success" value="Ajouter un participant" />
							<!--<input type="submit" name="masse" value="Ajouter en masse" />-->
						</center>
					</form>

					<?php } else { ?>

					<div class="alerte alerte-attention">
						<div class="alerte-contenu">
							Les phases d'inscriptions sont closes, il n'est désormais plus possible d'ajouter/modifier/supprimer des participants.
						</div>
					</div>

					<?php } 

				 } else if (empty($accesAdmin) && $ecole['etat_inscription'] == 'limitee') { ?>

				<div class="alerte alerte-attention">
					<div class="alerte-contenu">
						L'inscription est limitée. L'ajout de participants n'est donc plus possible, mais vous pouvez pour autant en éditer.
					</div>
				</div>

				<?php } ?>

				<form method="post">
				<table>
					<thead>
						<tr>
							<th>Nom</th>
							<th>Prénom</th>
							<th style="width:50px"><small>Sexe</small></th>
							<th style="width:50px"><small>Sportif</small></th>
							<th style="width:50px"><small>P.</small></th>
							<th style="width:50px"><small>C.</small></th>
							<th style="width:50px"><small>F.</small></th>
							<th>Email</th>
							<th>Téléphone</th>
							<th style="width:200px">Tarif</th>
							<th style="width:50px"><small>Logement</small></th>
							<th style="width:50px">Montant</th>
							<th style="width:90px">Actions</th>
						</tr>
					</thead>

					<tbody>

						<?php if (!count($participants)) { ?> 

						<tr class="vide">
							<td colspan="13">Aucun participant</td>
						</tr>

						<?php } 

						$phase = null;
						foreach ($participants as $participant) {
							if ($phase === null || $participant['phase'] != $phase) {
								$phase = $participant['phase'];
								echo '<tr class="vide"><td colspan="13" style="background:#CCC">'.$labels_phases[$phase].'</td></tr>';
							}


						 ?>

						<tr class="form clickme">
							<td class="content"><?php echo stripslashes($participant['nom']); ?></td>
							<td class="content"><?php echo stripslashes($participant['prenom']); ?></td>
							<td>
								<input type="checkbox" readonly <?php if ($participant['sexe'] == 'h') echo 'checked '; ?> />
								<label class="sexe"></label>
							</td>
							<td>
								<input type="checkbox" readonly <?php if ($participant['sportif']) echo 'checked '; ?>/>
								<label></label>
							</td>
							<td>
								<input type="checkbox" readonly <?php if ($participant['pompom']) echo 'checked '; ?>/>
								<label class="extra-pompom"></label>
							</td>
							<td>
								<input type="checkbox" readonly <?php if ($participant['cameraman']) echo 'checked '; ?>/>
								<label class="extra-video"></label>
							</td>
							<td>
								<input type="checkbox" readonly <?php if ($participant['fanfaron']) echo 'checked '; ?>/>
								<label class="extra-fanfaron"></label>
							</td>
							<td class="content"><?php echo stripslashes($participant['email']); ?></td>
							<td class="content"><?php echo stripslashes($participant['telephone']); ?></td>
							<td class="content"><?php echo stripslashes($tarifs[$participant['id_tarif_ecole']]['nom']); ?></td>
							<td>
								<input type="checkbox" readonly <?php if ($tarifs[$participant['id_tarif_ecole']]['logement']) echo 'checked '; ?>/>
								<label class="package"></label>
							</td>
							<td>
								<?php $montant = (empty($tarifs[$participant['id_tarif_ecole']]) ? 0 : $tarifs[$participant['id_tarif_ecole']]['tarif']) +
									max(0, (int) $participant['recharge']); ?>
								<center><i><?php echo $montant; ?> €</i></center>
							</td>
							<td class="content">

								<?php if (!empty($phase_actuelle) || !empty($accesAdmin)) { ?>

								<button type="submit" name="edit" value="<?php echo stripslashes($participant['id']); ?>">
									<img src="<?php url('assets/images/actions/edit.png'); ?>" alt="Edit" />
								</button>

								<?php } ?>

								<button type="submit" name="listing" value="<?php echo stripslashes($participant['id']); ?>">
									<img src="<?php url('assets/images/actions/list.png'); ?>" alt="Listing" />
								</button>
																
								<?php if (empty($participant['is_capitaine']) &&
									($participant['phase'] == $phase_actuelle || !empty($accesAdmin))) { ?>

								<button type="submit" name="delete" value="<?php echo stripslashes($participant['id']); ?>" />
									<img src="<?php url('assets/images/actions/delete.png'); ?>" alt="Delete" />
								</button>

								<?php } ?>
							</td>
						</tr>

						<?php } ?>

					</tbody>
				</table>
				</form>


				<div class="alerte alerte-info">
					<div class="alerte-contenu">
						<h3>Quelques données et quotas</h3>
						<br />
						<table style="margin-bottom:0px">
							<tr>
								<td><center><i>Inscriptions : </i> <?php echo '<b>'.$ecole['nb_inscriptions'].'</b>'.($inscription_on ? ' / '.
									($ecole['nb_inscriptions'] >= $quotas['total'] ? '<span class="full">'.(int) $quotas['total'].'</span>' : (int) $quotas['total']).
									($places_reservees > 0 ? ' (+'.$places_reservees.')' : '') : ''); ?></center></td>
								<td><center><i>Sportifs : </i>  <?php echo '<b>'.$ecole['nb_sportif'].'</b>'.($sportif_on ? ' / '.
									($ecole['nb_sportif'] >= $quotas['sportif'] ? '<span class="full">'.(int) $quotas['sportif'].'</span>' : (int) $quotas['sportif']) : ''); ?></center></td>
								<td><center><i>Non sportifs : </i>  <?php echo '<b>'.($ecole['nb_inscriptions'] - $ecole['nb_sportif']).'</b>'.($nonsportif_on ? ' / '.
									($ecole['nb_inscriptions'] - $ecole['nb_sportif'] >= $quotas['nonsportif'] ? '<span class="full">'.(int) $quotas['nonsportif'].'</span>' : (int) $quotas['nonsportif']) : ''); ?></center></td>
							</tr>
							<tr>
								<td><center><i>Participants logés : </i> <?php echo '<b>'.($ecole['nb_filles_logees'] + $ecole['nb_garcons_loges']).'</b> '.($logement_on ? ' / '.
									($ecole['nb_filles_logees'] + $ecole['nb_garcons_loges'] >= $quotas['logement'] ? '<span class="full">'.(int) $quotas['logement'].'</span>' : (int) $quotas['logement']) : ''); ?></center></td>
								<td><center><i>Filles logées : </i> <?php echo '<b>'.$ecole['nb_filles_logees'].'</b> '.($filles_on ? ' / '.
									($ecole['nb_filles_logees'] >= $quotas['filles_logees'] ? '<span class="full">'.(int) $quotas['filles_logees'].'</span>' : (int) $quotas['filles_logees']) : ''); ?></center></td>
								<td><center><i>Garcons logés : </i> <?php echo '<b>'.$ecole['nb_garcons_loges'].'</b> '.($garcons_on ? ' / '.
									($ecole['nb_garcons_loges'] >= $quotas['garcons_loges'] ? '<span class="full">'.(int) $quotas['garcons_loges'].'</span>' : (int) $quotas['garcons_loges']) : ''); ?></center></td>
							</tr>

							<tr>
								<td><center><i>Pompoms : </i> <?php echo '<b>'.$ecole['nb_pompom'].'</b> '.($pompom_on ? ' / '.
									($ecole['nb_pompom'] >= $quotas['pompom'] ? '<span class="full">'.(int) $quotas['pompom'].'</span>' : (int) $quotas['pompom']) : '').
									'<br />dont <b>'.$ecole['nb_pompom_nonsportif'].'</b> '.($pompom_nonsportif_on ? ' / '.
									($ecole['nb_pompom_nonsportif'] >= $quotas['pompom_nonsportif'] ? '<span class="full">'.(int) $quotas['pompom_nonsportif'].'</span>' : (int) $quotas['pompom_nonsportif']) : '').' non sportifs'; ?></center></td>
								<td><center><i>Caméramans : </i> <?php echo '<b>'.$ecole['nb_cameraman'].'</b> '.($cameraman_on ? ' / '.
									($ecole['nb_cameraman'] >= $quotas['cameraman'] ? '<span class="full">'.(int) $quotas['cameraman'].'</span>' : (int) $quotas['cameraman']) : '').
									'<br />dont <b>'.$ecole['nb_cameraman_nonsportif'].'</b> '.($cameraman_nonsportif_on ? ' / '.
									($ecole['nb_cameraman_nonsportif'] >= $quotas['cameraman_nonsportif'] ? '<span class="full">'.(int) $quotas['cameraman_nonsportif'].'</span>' : (int) $quotas['cameraman_nonsportif']) : '').' non sportifs'; ?></center></td>
								<td><center><i>Fanfarons : </i> <?php echo '<b>'.$ecole['nb_fanfaron'].'</b> '.($fanfaron_on ? ' / '.
									($ecole['nb_fanfaron'] >= $quotas['fanfaron'] ? '<span class="full">'.(int) $quotas['fanfaron'].'</span>' : (int) $quotas['fanfaron']) : '').
									'<br />dont <b>'.$ecole['nb_fanfaron_nonsportif'].'</b> '.($fanfaron_nonsportif_on ? ' / '.
									($ecole['nb_fanfaron_nonsportif'] >= $quotas['fanfaron_nonsportif'] ? '<span class="full">'.(int) $quotas['fanfaron_nonsportif'].'</span>' : (int) $quotas['fanfaron_nonsportif']) : '').' non sportifs'; ?></center></td>
							</tr>
						</table>

						<?php if (!empty($quotas_reserves) && isset($quotas['total'])) { ?>

						<table class="table-small" style="margin-top:20px; margin-bottom:0">
							<tr>
								<th>Sport</th>
								<th style="width:100px"><small>Quota réservé</small></th>
								<th style="width:100px"><small>Sportifs</small></th>
								<th style="width:75px"><small>Si atteint</small></th>
							</tr>

							<?php foreach ($quotas_reserves as $quota_reserves) { ?>

							<tr>
								<td><?php echo stripslashes($quota_reserves['sport']).' '.printSexe($quota_reserves['sexe']); ?></td>
								<td><center><?php echo $quota_reserves['quota_reserves']; ?></center></td>
								<td><center><?php echo $quota_reserves['sportifs']; ?></center></td>
								<td><center><b><?php if ($quota_reserves['sportifs'] < $quota_reserves['quota_reserves']) echo '+'.$quota_reserves['quota_reserves']; ?></b></center></td>
							</tr>

							<?php } ?>

						</table>

						<?php } ?>

						<br />
						<br />
						<h3>Légende et informations</h3>
						<div class="double">
							<input type="checkbox" checked />
							<label class="sexe label-margin"></label>
							<input type="checkbox" />
							<label class="sexe label-margin"></label>
						</div>

						<div class="double">
							<input type="checkbox" checked />
							<label class="label-margin"></label>
							<input type="checkbox" />
							<label class="label-margin"></label>
						</div>
						
						<div class="triple">
							<input type="checkbox" checked />
							<label class="extra-pompom label-margin"></label>
							<input type="checkbox" checked />
							<label class="extra-video label-margin"></label>
							<input type="checkbox" checked />
							<label class="extra-fanfaron label-margin"></label>
						</div>
						
						<div class="triple">
							<input type="checkbox" />
							<label class="package label-margin"></label>
							<input type="checkbox" checked />
							<label class="package label-margin"></label>
							<input type="checkbox" checked />
							<label class="capitaine label-margin"></label>
						</div>

						<br />
						<a href="<?php url('ecoles/'.$ecole['id'].'/recapitulatif?p=tarifs'); ?>">Cliquez ici</a> pour voir le détail des tarifs de votre école
					</div>
				</div><br />

				<?php if (!empty($phase_actuelle) && $phase_actuelle != 'modif' || !empty($accesAdmin)) { ?>

				<div id="modal-ajout-participant" class="modal big-modal">
					<form method="post">
						<fieldset>
							<legend>Ajout d'un participant</legend>
							<!--<small>Vous pouvez aussi utiliser <a href="<?//php url('ecoles/'.$ecole['id'].'/import'); ?>">l'outil d'importation en masse</a></small>-->

							<div>
								<label for="form-null" class="needed">
									<span>Nom / Prénom</span>
									<input class="two_input" placeholder="Nom" type="text" name="nom" id="form-nom" value="" />
									<input class="two_input" placeholder="Prénom" type="text" name="prenom" id="form-prenom" value="" />
								</label>

								<label for="form-null" class="needed">
									<span>Sexe</span>
									<input type="checkbox" name="sexe" id="form-sexe" value="sexe" checked />
									<label for="form-sexe" class="sexe"></label>
								</label>

								<label for="form-null" class="needed">
									<span>Email / Téléphone</span>
									<input class="two_input" placeholder="Email" type="text" name="email" id="form-email" value="" />
									<input class="two_input" placeholder="Téléphone" type="text" name="telephone" id="form-telephone" value="" />
								</label>
							</div>
							
							<div>
								<hr />
								<small>Pour retrouvez le détail des tarifs, <a href="<?php url('ecoles/'.$ecole['id'].'/recapitulatif?p=tarifs'); ?>">cliquez-ici</a>.</small>

								<label for="form-null" class="needed">
									<span>Sportif</span>
									<input type="checkbox" name="sportif" id="form-sportif" value="sportif" <?php if (substr($ecole['nom'],0,3) == 'ECL'){echo "checked";} ?>/>
									<label class="two_input" for="form-sportif"></label>
									<input class="two_input" placeholder="Licence (avec le code AS)" type="text" name="licence" id="form-licence" value="" pattern="^(([a-zA-Z0-9]{4})[^0-9a-zA-Z]?([0-9]{6})$)|(^Certificat médical ou questionnaire)$" />
								</label>

								<div class="alerte alerte-attention" id="alerte-certificat-medical" hidden>
									<div class="alerte-contenu">
										La FFSU nous demande certains prérequis pour les candidats sans licence (afin de réaliser un "passeport U", valable le weekend). <br>
										Les candidats souhaitant faire du rugby doivent fournir un certificat médical datant de moins de 1 an. <br>
										Pour les autres, il faut prendre part au questionnaire <a href="https://sport-u.com/wp-content/uploads/2022/06/20220613_Questionnaire-de-sante-22-23.pdf">disponible ici</a>.<br>
										Suivant les reponses :
										<ul>
											<li> non à toutes les questions ⇒ le candidat peut participer sans autres démarches </li>
											<li> oui à une ou plusieurs questions ⇒ le candidat doit fournir un certificat médial de moins de 6 mois</li>
										</ul>
									</div>
								</div>

								<label for="form-null" class="needed" <?php if (substr($ecole['nom'],0,3) == 'ECL'){echo "style='display:none;'";} ?>>
									<span>Extras</span>
									<div class="triple">
										<input type="checkbox" name="pompom" id="form-pompom" value="pompom" />
										<label for="form-pompom" class="extras extra-pompom"></label>

										<input type="checkbox" name="cameraman" id="form-cameraman" value="cameraman" />
										<label for="form-cameraman" class="extras extra-video"></label>

										<input type="checkbox" name="fanfaron" id="form-fanfaron" value="fanfaron" />
										<label for="form-fanfaron" class="extras extra-fanfaron"></label>
									</div>
								</label>

								<label for="form-null" class="needed" <?php if (substr($ecole['nom'],0,3) == 'ECL'){echo "style='display:none;'";} ?>>
									<span>Logement</span>
									<input type="checkbox" name="logement" id="form-logement" value="logement" <?php if (substr($ecole['nom'],0,3) == 'ECL'){echo "checked";} ?>/>
									<label class="two_input package" for="form-logement"></label>
									<input class="two_input" placeholder="Logeur" type="text" name="logeur" id="form-logeur" value="" />
									<small>
										ATTENTION : seules les filles sélectionnant "Full package" pourront dormir en résidence.
									</small>
								</label>

								<label for="form-null" class="needed">
									<span style="text-align: right">Tarif / Gourde</span>
									<select class="two_input" id="form-tarif" name="tarif">
									</select>
										<input class="two_input" placeholder="Gourde (0€ ou 4€)" type="number" min="0" step="4" max="4" id="form-recharge" name="recharge" value="" />
									<small style="padding : 3px"></small>
									<small style="padding : 3px"></small>
								</label>
							</div>

							<div>
								<hr />
								<small>Les sports pourront être renseignés dans <a href="<?php url('ecoles/'.$ecole['id'].'/sportifs'); ?>">l'onglet "Sportifs"</a></small>

								<label for="form-null">
									<span>Sport</span>
									<input type="checkbox" name="capitaine" id="form-capitaine" value="capitaine" onclick="$actualiseCapitaines()" />
									<label id="tick-capitaine" class="two_input capitaine" for="form-capitaine"></label>
									<select class="two_input" id="form-sport" name="sport" onchange="$actualiseCapitaines()"></select>
								</label>
								<label for="form-null" <?php if (!(substr($ecole['nom'],0,3) == 'ECL')){echo "style='display:none;'";} ?>>
									<span id="form-equipe">Equipe</span>
									<select class="two_input" id="form-capitaines" name="capitaine"></select>
								</label>
							</div>

							<hr />
							
							<center>
								<input type="submit" class="success" value="Ajouter le participant" name="add_participant" />
							</center>
						</fieldset>
					</form>
				</div>


				<?php } if (!empty($participant_edit) && 
					(!empty($phase_actuelle) || !empty($accesAdmin))) { ?>

				<div id="modal-edit-participant" class="modal big-modal">
					<form method="post">
						<fieldset data-id="<?php echo $participant_edit['id']; ?>">
							<legend>Edition d'un participant</legend>

							<?php if ($phase_actuelle != $participant_edit['phase'] && empty($accesAdmin)) { ?>

							<div class="alerte alerte-attention">
								<div class="alerte-contenu">
									Ce participant a été inscrit dans une phase précédente, le package (sexe, sportif, extra, logement et tarif) ne peut pas être modifié
								</div>
							</div>

							<?php } ?>

							<div>
								<label for="form-null" class="needed">
									<span>Nom / Prénom</span>
									<input class="two_input" placeholder="Nom" type="text" name="nom" id="form-nom-edit" value="<?php echo stripslashes($participant_edit['nom']); ?>"  />
									<input class="two_input" placeholder="Prénom" type="text" name="prenom" id="form-prenom-edit" value="<?php echo stripslashes($participant_edit['prenom']); ?>"  />
								</label>

								<label for="form-null" class="needed">
									<span>Sexe</span>
									<input <?php if ($phase_actuelle != $participant_edit['phase'] && empty($accesAdmin)) echo 'disabled '; ?>data-last="<?php echo $participant_edit['sexe']; ?>" type="checkbox" name="sexe" id="form-sexe-edit" value="sexe" <?php echo $participant_edit['sexe'] == 'h' ? 'checked ' : ''; ?>/>
									<label for="form-sexe-edit" class="sexe"></label>
								</label>

								<label for="form-null" class="needed">
									<span>Email / Téléphone</span>
									<input class="two_input" placeholder="Email" type="text" name="email" id="form-email-edit" value="<?php echo stripslashes($participant_edit['email']); ?>" />
									<input class="two_input" placeholder="Téléphone" type="text" name="telephone" id="form-telephone-edit" value="<?php echo stripslashes($participant_edit['telephone']); ?>"  />
								</label>
							</div>
							
							<div>
								<hr />
								<small>Pour retrouvez le détail des tarifs, <a href="<?php url('ecoles/'.$ecole['id'].'/recapitulatif?p=tarifs'); ?>">cliquez-ici</a>.</small>

								<label for="form-null" class="needed">
									<span>Sportif</span>
									<input <?php if ($phase_actuelle != $participant_edit['phase'] && empty($accesAdmin)) echo 'disabled '; ?>data-last="<?php echo $participant_edit['sportif'] ? '1' : ''; ?>" type="checkbox" name="sportif" id="form-sportif-edit" value="sportif" <?php echo $participant_edit['sportif'] ? 'checked ': ''; ?>/>
									<label class="two_input" for="form-sportif-edit"></label>
									<input class="two_input" placeholder="Licence (avec le code AS)" type="text" name="licence" id="form-licence-edit" value="<?php echo stripslashes($participant_edit['licence']); ?>" pattern="^(([a-zA-Z0-9]{4})[^0-9a-zA-Z]?([0-9]{6})$)|(^Certificat médical ou questionnaire)$" />
								</label>
								
								<div class="alerte alerte-attention" id="alerte-certificat-medical-edit" hidden>
									<div class="alerte-contenu">
										La FFSU nous demande certains prérequis pour les candidats sans licence (afin de réaliser un "passeport U", valable le weekend). <br>
										Les candidats souhaitant faire du rugby doivent fournir un certificat médical datant de moins de 1 an. <br>
										Pour les autres, il faut prendre part au questionnaire <a href="https://sport-u.com/wp-content/uploads/2022/06/20220613_Questionnaire-de-sante-22-23.pdf">disponible ici</a>.<br>
										Suivant les reponses :
										<ul>
											<li> non à toutes les questions ⇒ le candidat peut participer sans autres démarches </li>
											<li> oui à une ou plusieurs questions ⇒ le candidat doit fournir un certificat médial de moins de 6 mois</li>
										</ul>
									</div>
								</div>

								<label for="form-null" class="needed">
									<span>Extras</span>
									<div class="triple">
										<input <?php if ($phase_actuelle != $participant_edit['phase'] && empty($accesAdmin)) echo 'disabled '; ?>data-last="<?php echo $participant_edit['pompom'] ? '1' : ''; ?>" type="checkbox" name="pompom" id="form-pompom-edit" value="pompom" <?php echo $participant_edit['pompom'] ? 'checked ': ''; ?>/>
										<label for="form-pompom-edit" class="extras extra-pompom"></label>

										<input <?php if ($phase_actuelle != $participant_edit['phase'] && empty($accesAdmin)) echo 'disabled '; ?>data-last="<?php echo $participant_edit['cameraman'] ? '1' : ''; ?>" type="checkbox" name="cameraman" id="form-cameraman-edit" value="cameraman" <?php echo $participant_edit['cameraman'] ? 'checked ': ''; ?>/>
										<label for="form-cameraman-edit" class="extras extra-video"></label>

										<input <?php if ($phase_actuelle != $participant_edit['phase'] && empty($accesAdmin)) echo 'disabled '; ?>data-last="<?php echo $participant_edit['fanfaron'] ? '1' : ''; ?>" type="checkbox" name="fanfaron" id="form-fanfaron-edit" value="fanfaron" <?php echo $participant_edit['fanfaron'] ? 'checked ': ''; ?>/>
										<label for="form-fanfaron-edit" class="extras extra-fanfaron"></label>
									</div>
								</label>

								<label for="form-null" class="needed">
									<span>Logement</span>
									<input <?php if ($phase_actuelle != $participant_edit['phase'] && empty($accesAdmin)) echo 'disabled '; ?>data-last="<?php echo $participant_edit['logement'] ? '1' : ''; ?>" type="checkbox" name="logement" id="form-logement-edit" value="logement" <?php echo $participant_edit['logement'] ? 'checked ': ''; ?>/>
									<label class="two_input package" for="form-logement-edit"></label>
									<input class="two_input" placeholder="Logeur" type="text" name="logeur" id="form-logeur-edit" value="<?php echo stripslashes($participant_edit['logeur']); ?>" />
								</label>

								<label for="form-null" class="needed">
									<span>Tarif / Option Gourde (4€)?</span>
									<select <?php if ($phase_actuelle != $participant_edit['phase'] && empty($accesAdmin)) echo 'disabled data-disabled="true" '; ?>data-last="<?php echo $participant_edit['id_tarif_ecole']; ?>" class="two_input" id="form-tarif-edit" name="tarif">
									</select>
									<input class="two_input" placeholder="Gourde (0€ ou 4€)" type="number" min="0" step="4" max="4" id="form-recharge-edit" name="recharge" value="<?php echo stripslashes($participant_edit['recharge']); ?>" />
									<small>&nbsp;</small>
								</label>
							</div>

							<div>
								<hr />
								<small>Les sports doivent être renseignés dans <a href="<?php url('ecoles/'.$ecole['id'].'/sportifs'); ?>">l'onglet "Sportifs"</a></small>

								<?php 

								$is_capitaine = false;
								if (count($found_sports)) { 

								?>

								<label for="form-null">
									<span>Sport(s)</span>
									<div>
										<table style="margin:0; width:100%">
											<thead>
												<tr>
													<th style="width:80px">Capitaine</th>
													<th>Sport</th>
													<th>Équipe</th>
												</tr>
											</thead>

											<tbody>

											<?php 

											foreach ($found_sports as $sport) { 
												if ($sport['is_capitaine'])
													$is_capitaine = true;

											?>

											<tr class="form">
												<td>
													<input type="checkbox" readonly <?php if ($sport['is_capitaine']) echo 'checked '; ?>/>
													<label class="capitaine"></label>
												</td>
												<td class="content"><?php echo stripslashes($sport['sport']).' '.printSexe($sport['sexe']); ?></td>
												<td class="content"><?php echo stripslashes($sport['label']); ?></td>
											</tr>

											<?php } ?>

											</tbody>
										</table>
									</div>
								</label>

								<?php } ?>

							</div>

							<hr />

							<center>
								<input type="submit" class="success" value="Editer le participant" name="edit_participant" />

								<?php if (empty($is_capitaine) && ($phase_actuelle == $participant_edit['phase'] || !empty($accesAdmin))) { ?>

								<br />
								<input type="submit" class="delete" value="Supprimer le participant" name="del_participant" onclick="$('#modal-edit-participant form').off('submit'); /*I want to end JQuery*/"/>

								<input type="hidden" value="<?php echo $participant_edit['id']; ?>" name="id" />
								<?php } ?>

							</center>
						</fieldset>
					</form>
				</div>

				<?php } ?>


				<script type="text/javascript">
				
				
				$(function() {
					var $speed =  <?php echo APP_SPEED_ERROR; ?>;
					var $ecole_lyonnaise = <?php echo $ecole['ecole_lyonnaise'] ? 'true' : 'false'; ?>;
					var licences = {};
					var sports = {};
					var tarifs = {};
					var asCode = [];


			        $analysisModal = function(edit, event, force) {
			            if (event.keyCode == 13 || force) {
			                event.preventDefault();
			              	$parent = $(edit ? '#modal-edit-participant fieldset' : '#modal-ajout-participant fieldset');
			              	$div_coord = $parent.children('div:not(.alerte)').first();
			              	$div_infos = $div_coord.next();
			              	$div_sport = $div_coord.next().next();

			  				$first_coord = $div_coord.children('label').first();
			  				$nom = $first_coord.children('input').first();
			  				$prenom = $nom.next();
			  				$sexe = $first_coord.next().children('input');
			  				$email = $first_coord.next().next().children('input').first();
			  				$telephone = $email.next();

							$first_infos = $div_infos.children('label').first();
							$sportif = $first_infos.children('input').first();
							$licence = $sportif.next().next();
			  				$pompom = $first_infos.next().next().find('input').first();
			  				$cameraman = $pompom.next().next();
			  				$fanfaron = $cameraman.next().next();
			  				$logement = $first_infos.next().next().next().children('input').first();
			  				$logeur = $logement.next().next();
			  				$tarif = $first_infos.next().next().next().next().children('select');
			  				$recharge = $tarif.next();

			  				$first_sport = $div_sport.children('label').first();
			  				$capitaine = $first_sport.children('input').first();
			  				$sport = $capitaine.next().next();
							$equipes = $('#form-capitaines')[0];

			  				$inscription_on = <?php echo (int) $inscription_on; ?>;
			  				$sportif_on = <?php echo (int) $sportif_on; ?>;
			  				$nonsportif_on = <?php echo (int) $nonsportif_on; ?>;
			                $logement_on = <?php echo (int) $logement_on; ?>;
			                $filles_on = <?php echo (int) $filles_on; ?>;
			                $garcons_on = <?php echo (int) $garcons_on; ?>;
			                $pompom_on = <?php echo (int) $pompom_on; ?>;
			                $pompom_nonsportif_on = <?php echo (int) $pompom_nonsportif_on; ?>;
			                $cameraman_on = <?php echo (int) $cameraman_on; ?>;
			                $cameraman_nonsportif_on = <?php echo (int) $pompom_nonsportif_on; ?>;
			                $fanfaron_on = <?php echo (int) $fanfaron_on; ?>;
			                $fanfaron_nonsportif_on = <?php echo (int) $fanfaron_nonsportif_on; ?>;

			                $places_inscription = <?php echo $places_inscription; ?>;
			                $places_sportif = <?php echo $places_sportif; ?>;
			                $places_nonsportif = <?php echo $places_nonsportif; ?>;
			                $places_filles_logees = <?php echo $places_filles_logees; ?>;
			                $places_garcons_loges = <?php echo $places_garcons_loges; ?>;
			                $places_logement = <?php echo $places_logement; ?>;
			                $places_pompom = <?php echo $places_pompom; ?>;
			                $places_cameraman = <?php echo $places_cameraman; ?>;
			                $places_fanfaron = <?php echo $places_fanfaron; ?>;
							$places_pompom_nonsportif = <?php echo $places_pompom_nonsportif; ?>;
			                $places_cameraman_nonsportif = <?php echo $places_cameraman_nonsportif; ?>;
			                $places_fanfaron_nonsportif = <?php echo $places_fanfaron_nonsportif; ?>;

			                if (($logement_on || $filles_on || $garcons_on) && 
			                	$logement.data('last') &&
			                	$sexe.data('last')) {
			                	if ($logement_on) $places_logement++;
			                	if ($filles_on && $sexe.data('last') == 'f') $places_filles_logees++;
			                	if ($garcons_on && $sexe.data('last') == 'h') $places_garcons_loges++;
			                }

			                if ($inscription_on && $parent.data('id')) $places_inscription++;
			                if ($sportif_on && $sportif.data('last')) $places_sportif++;
			                if ($nonsportif_on && !$sportif.data('last') && $parent.data('id')) $places_nonsportif++;
			                if ($pompom_on && $pompom.data('last')) $places_pompom++;
			                if ($cameraman_on && $cameraman.data('last')) $places_cameraman++;
			                if ($fanfaron_on && $fanfaron.data('last')) $places_fanfaron++;
			                if ($pompom_nonsportif_on && $pompom.data('last') && !$sportif.data('last')) $places_pompom_nonsportif++;
			                if ($cameraman_nonsportif_on && $cameraman.data('last') && !$sportif.data('last')) $places_cameraman_nonsportif++;
			                if ($fanfaron_nonsportif_on && $fanfaron.data('last') && !$sportif.data('last')) $places_fanfaron_nonsportif++;

			                $erreur = false;
			                $alerts = [];
			                $blocserr = [];
			                var datas = {};
		               		datas['load'] = 'send';
		               		if ($parent.data('id'))
		               			datas["id"] = $parent.data('id');


		               		if ($inscription_on && 
		               			$places_inscription <= 0) {
			                	$erreur = true;
			                	$alerts.push('Il n\'y a plus d\'inscription possible');
			                }

			                if (!$nom.val().trim()) {
			                	$erreur = true;
			                	$blocserr.push($nom);
			                }

			                if (!$prenom.val().trim()) {
			                	$erreur = true;
			                	$blocserr.push($prenom);
			                }

						    var re = /^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;
			                if (!$email.val().trim() ||
			                	!re.test($email.val())) {
			                	$erreur = true;
			                	$blocserr.push($email);
			                }

			                if (!$licence.val().trim() &&
			                	$sportif.prop('checked')) {
			                	$erreur = true;
			                	$blocserr.push($licence);
			                }

			                if (!$tarif.html() ||
			                	!$tarif.val() ||
			                	!$tarif.val().trim() ||
			                	!$.isNumeric($tarif.val())) {
			                	$erreur = true;
			                	$blocserr.push($tarif);
			                }

			                if ($recharge.val() && (
			                		!$.isNumeric($recharge.val()) ||
			                		$recharge.val() < 0)) {
			                	$erreur = true;
			                	$blocserr.push($recharge);
			               	} 

			               	if ($logement.prop('checked') && (
			                		$logement_on && $places_logement <= 0 ||
			                		$filles_on && $sexe.val() == 'f' && $places_filles_logees <= 0 ||
			                		$garcons_on && $sexe.val() == 'h' && $places_garcons_loges <= 0)) {
			                	$erreur = true;
			                	$blocserr.push($tarif);
			                	$alerts.push('Vous n\'avez plus de quota pour le logement');
			                }

			                if (!$sportif.prop('checked') &&
			                	!$pompom.prop('checked') &&
			                	!$cameraman.prop('checked') &&
			                	!$fanfaron.prop('checked')) {
			                	$erreur = true;
			                	$blocserr.push($sportif);
			                	$blocserr.push($pompom);
			                	$blocserr.push($cameraman);
			                	$blocserr.push($fanfaron);
			                	$alerts.push('Le participant doit avoir au moins un statut');
			                }

			                if ($sportif.prop('checked') &&
			                	$sportif_on &&
			                	$places_sportif <= 0) {
			                	$erreur = true;
			                	$blocserr.push($sportif);
			                	$alerts.push('Vous n\'avez plus de quota pour les sportifs');
			                }

			                if (!$sportif.prop('checked') &&
			                	$nonsportif_on &&
			                	$places_nonsportif <= 0) {
			                	$erreur = true;
			                	$blocserr.push($sportif);
			                	$alerts.push('Vous n\'avez plus de quota pour les non sportifs');
			                }

			                if ($pompom.prop('checked') && (
			                		$pompom_on && $places_pompom <= 0 ||
			                		$pompom_nonsportif_on && !$sportif.prop('checked') && $places_pompom_nonsportif <= 0)) {
			                	$erreur = true;
			                	$blocserr.push($pompom);
			                	$alerts.push('Vous n\'avez plus de quota pour les pompoms'+($sportif.prop('checked') ? '' : ' non sportifs'));
			               	}

			               	if ($cameraman.prop('checked') && (
			                		$cameraman_on && $places_cameraman <= 0 ||
			                		$cameraman_nonsportif_on && !$sportif.prop('checked') && $places_cameraman_nonsportif <= 0)) {
			                	$erreur = true;
			                	$blocserr.push($cameraman);
			                	$alerts.push('Vous n\'avez plus de quota pour les caméramans'+($sportif.prop('checked') ? '' : ' non sportifs'));
			               	}

			               	if ($fanfaron.prop('checked') && (
			                		$fanfaron_on && $places_fanfaron <= 0 ||
			                		$fanfaron_nonsportif_on && !$sportif.prop('checked') && $places_fanfaron_nonsportif <= 0)) {
			                	$erreur = true;
			                	$blocserr.push($fanfaron);
			                	$alerts.push('Vous n\'avez plus de quota pour les fanfarons'+($sportif.prop('checked') ? '' : ' non sportifs'));
			               	}

			               	if (!$ecole_lyonnaise &&
			               		!$logement.prop('checked') &&
			               		!$logeur.val().trim()) {
			               		$erreur = true;
			                	$blocserr.push($logeur);
			               	}

			               	if ($sportif.prop('checked') &&
			               		$capitaine.prop('checked') &&
			               		!$telephone.val().trim() ||
			               		!$sportif.prop('checked') &&
			               		$telephone.data('last-capitaine')) {
			               		$erreur = true;
			                	$blocserr.push($telephone);
			               	}

			               	if ($capitaine.prop('checked') && (
				               		!$sport.html() ||
				                	!$sport.val() ||
				                	!$sport.val().trim() ||
				                	!$.isNumeric($sport.val()))) {
			                	$erreur = true;
			                	$blocserr.push($sport);
			                }

			                if ($sportif.prop('checked') &&
			                	$sport.html() &&
			                	$sport.val() &&
			                	$sport.val().trim() &&
			                	$.isNumeric($sport.val())) {
			                	if ($capitaine.prop('checked') && 
			                		parseInt($sport.children('option:selected').first().data('equipes')) >= 
			                			parseInt($sport.children('option:selected').first().data('quota-equipes'))) {
			                		$erreur = true;
			                		$blocserr.push($sport);
			                		$alerts.push('Il n\'y a plus de quota suffisant pour créer une nouvelle équipe dans ce sport');
			                	}

			                	// if (!$capitaine.prop('checked') && 
			                	// 	parseInt($sport.children('option:selected').first().data('equipes')) > 1) {
			                	// 	$erreur = true;
			                	// 	$blocserr.push($sport);
			                	// 	$alerts.push('Il y a plus d\'une équipe pour ce sport, merci d\'utiliser l\'onglet "sportifs" pour ajouter le participant dans la bonne équipe');
			                	// }

			                	if (parseInt($sport.children('option:selected').first().data('inscriptions')) >= 
			                			parseInt($sport.children('option:selected').first().data('quota-inscription'))) {
			           				$erreur = true;
			                		$blocserr.push($sport);
			                		$alerts.push('Les inscriptions étant limitées, il n\'y a plus de place dans ce sport');
			                	}

			                	if (parseInt($sport.children('option:selected').first().data('sportifs')) >= 
			                			parseInt($sport.children('option:selected').first().data('quota-max'))) {
			           				$erreur = true;
			                		$blocserr.push($sport);
			                		$alerts.push('Vous avez atteint le quota max pour ce sport');
			                	}
			                }

			               	for (var i in $alerts)
			               		alert($alerts[i]);

			               	for (var i in $blocserr)
			               		$blocserr[i].addClass('form-error').removeClass('form-error', $speed);//.focus();


			               	if (!$erreur) {
			               		datas["nom"] = $nom.val();
			               		datas["prenom"] = $prenom.val();
			               		datas["sexe"] = $sexe.is(':checked') ? 'h' : 'f';
			               		datas["email"] = $email.val();
			               		datas["telephone"] = $telephone.val();

			               		datas["sportif"] = $sportif.is(':checked') ? 'o' : 'n';
			               		datas["licence"] = $licence.val();
			               		datas["pompom"] = $pompom.is(':checked') ? 'o' : 'n';
			               		datas["cameraman"] = $cameraman.is(':checked') ? 'o' : 'n';
			               		datas["fanfaron"] = $fanfaron.is(':checked') ? 'o' : 'n';
			               		
			               		datas["logement"] = $logement.is(':checked') ? 'o' : 'n';
			               		datas["logeur"] = $logeur.val();
			               		datas["tarif"] = $tarif.val();
			               		datas["recharge"] = $recharge.val()!=0 ? 4 : 0;

			               		if (!$parent.data('id')) {
				               		datas["capitaine"] = $capitaine.is(':checked') ? 'o' : 'n';
				               		datas["sport"] = $sport.val();
									if (!$capitaine.is(':checked') && !($equipes.selectedIndex == -1))
									{
										datas["equipe"] = $equipes.options[$equipes.selectedIndex].value
									}
				               	}

								$.ajax({
									url: "<?php url('ecoles/'.$ecole['id'].'/import/ajax'); ?>",
								  	method: "POST",
								  	cache: false,
									dataType: "json",
									data:datas,
									success: function(data) { 
										if (data.error)
											alert(data.message);
										
										else {
											$parent.parent().unbind('submit');
											$parent.children('center').children('input[type=submit]').first().unbind('click').click();
										}
									}});

			               	}
			            }
			        };

					$actualiseMontantModal = function(elem) {
			            $parent = elem.parents('fieldset').first();
		              	$div_infos = $parent.children('div:not(.alerte)').first().next();

		              	$first_infos = $div_infos.children('label').first();
			  			$logement = $first_infos.next().next().next().children('input').first();
		  				$tarif = $first_infos.next().next().next().next().children('select');
		  				$recharge = $tarif.next();
						$soireeW = $recharge.next();
						$texte = $soireeW.next();
						$sport_special = $tarif.children('option:selected').first().data('special');
						$montant = $tarif.children('option:selected').first().data('montant') + parseInt($recharge.val() && $recharge.val()!=0 ? 4 : 0);

						if ($.isNumeric($montant))
							$texte.html('<div class="two_input">' + ($sport_special ? 'Sport spécial : <b>'+$sport_special+'</b>' : '&nbsp;')+'</div>' + 
								'<div class="two_input">Montant total : <b>'+$montant+' €</b></div>');
						
						else
							$texte.html('<div class="two_input">' + ($sport_special ? 'Sport spécial : <b>'+$sport_special+'</b>' : '&nbsp;')+'</div>' + 
								'<div class="two_input">Montant total : <b>-</b></div>');
					};

					$actualiseTarifsModal = function(elem) {
			            $parent = elem.parents('fieldset').first();
		              	$div_infos = $parent.children('div:not(.alerte)').first().next();

						$first_infos = $div_infos.children('label').first();
						$sportif = $first_infos.children('input').first();
						$pompom = $first_infos.next().next().children('div').children('input').first();
						$cameraman = $pompom.next().next();
						$fanfaron = $cameraman.next().next();
						$logement = $first_infos.next().next().next().children('input');

						$select = $first_infos.next().next().next().next().children('select');
						$previous = $select.children('option:selected').first().val();
						$select.html('');

						$is_sportif = $sportif.prop('checked');
						$is_pompom = $pompom.prop('checked');
						$is_cameraman = $cameraman.prop('checked');
						$is_fanfaron = $fanfaron.prop('checked');
						$has_logement = $logement.prop('checked');

						$is_none = !$is_sportif && !$is_pompom &&
							!$is_cameraman && !$is_fanfaron;

						$select.attr('disabled', $select.data('disabled') ? true : false);

						if ($is_none ||
							!tarifs.length) {
							$select.append('<option value=""></option>');
							$select.attr('disabled', true);
						}

						else {
							$tarifs = $filtreTarifs($has_logement, $is_sportif, $is_fanfaron, $is_pompom, $is_cameraman);

							$.each($tarifs, function(i, $tarif) {
								$select.append('<option data-id-special="'+$tarif.id_sport_special+'" data-special="'+($tarif.id_sport_special ? ($tarif.sport + ' (' + 
									($tarif.sexe == 'h' ? 'H' : ($tarif.sexe == 'f' ? 'F' : 'F/G'))+')') : '')+'" ' +
									'data-montant="'+$tarif.tarif+'" value="' + $tarif.id_tarif_ecole + '"'+
									($previous == $tarif.id_tarif_ecole || $select.data('last') == $tarif.id_tarif_ecole ? ' selected' : '') + '>'+ 
									$tarif.nom + '</option>');
							});

							if ($tarifs.length == 0) {
								$select.append('<option value=""></option>');
								$select.attr('disabled', true);
							}
						}

						$actualiseMontantModal(elem);
					};

					$actualiseSportsModal = function(elem) {
			            $parent = elem.parents('fieldset').first();
		              	$div_coord = $parent.children('div:not(.alerte)').first();
		              	$div_infos = $div_coord.next();
		              	$div_sport = $div_infos.next();

		              	$first_coord = $div_coord.children('label').first();
		              	$sexe = $first_coord.next().children('input');

						$first_infos = $div_infos.children('label').first();
						$sportif = $first_infos.children('input').first();
						$tarif = $first_infos.next().next().next().next().children('select');
						$special = $tarif.children('option:selected').data('id-special');

						$first_sport = $div_sport.children('label').first();
						$capitaine = $first_sport.children('input');
						$select = $first_sport.children('select').first();
						$previous = $select.children('option:selected').first().val();
						$select.html('<option value=""></option>');

						$select.attr('disabled', false);
						if ($sportif.prop('checked') && sports.length) 
						{
							$sports = $filtreSports($sexe.is(':checked') ? 'h' : 'f', $special, $capitaine.is(':checked'));	

							$.each($sports, function(i, $sport) 
							{
								$select.append('<option value="' + $sport.id_ecole_sport + '" ' +
									'data-sportifs="' + $sport.nb_sportifs + '" data-quota-max="' + $sport.quota_max + '" ' +
									'data-inscriptions="' + $sport.nb_inscriptions + '" data-quota-inscription="' + $sport.quota_inscription + '" ' +
									'data-equipes="' + $sport.nb_equipes + '" data-quota-equipes="' + $sport.quota_equipes + '" ' +
									'data-special="' + $sport.special + '"' + ($previous == $sport.id_ecole_sport || $select.data('last') == $sport.id_ecole_sport ? ' selected' : '') + '>'+ 
									$sport.sport + '</option>');
							});

							if ($sports.length == 0) {
								$select.append('<option value=""></option>');
								$select.attr('disabled', true);
							}
						}
					};

					$actualiseCapitaines = function(elem) {
						$sportif = $('#form-sportif')[0];
						$capitaine = $('#form-capitaine')[0];
						$select1 = $('#form-sport')[0];
						$select2 = $('#form-capitaines');
						$select2.children().remove();
						$span = $('#form-equipe');
						if ($sportif.checked && !$capitaine.checked) {
							
							$select2[0].style.display="";
							$span[0].style.display="";
							var id_sport = $select1.options[$select1.selectedIndex].value;
							var capitaines = $filtreCapitaines(id_sport);	

							capitaines.forEach(function(capitaine){ 
								$option = document.createElement("option");
								$option.text = capitaine.prenom+' '+capitaine.nom;
								$option.value = capitaine.equipe;
								$select2[0].appendChild($option);
							});
							
						} else 
						{
							$select2[0].style.display="none";
							$span[0].style.display="none";
						}
					};

					$initPost = function(elem, event) {
						$parent = elem.parent().parent();
			            $first = $parent.children('td:first');
						$first.parent().addClass('_tosubmit');
			            $('tr:not(._tosubmit) td input, tr:not(._tosubmit) td select, tr:not(._tosubmit) td button').each(function() { $(this).attr('name', ''); });
					};

					$getLicences = function() {
						$.ajax({
							url: "<?php url('ecoles/'.$ecole['id'].'/import/ajax'); ?>",
						  	method: "POST",
						  	cache: false,
							dataType: "json",
							data:{load:"licences"},
							success: function(data) {
								licences = data['licences'];

								for (var i in data['as']) {
									if (data['as'][i].match(/^[a-z\d]{4}$/i))
										asCode.push(data['as'][i].toUpperCase());
								}

								$initAutocomplete(false);
								$initAutocomplete(true);
							}
						});
					};

					$getTarifs = function() {
						$.ajax({
							url: "<?php url('ecoles/'.$ecole['id'].'/import/ajax'); ?>",
						  	method: "POST",
						  	cache: false,
							dataType: "json",
							data:{load:"tarifs"},
							success: function(data) {
								tarifs = data;
								$actualiseTarifsModal($('#modal-edit-participant form').find('fieldset div').first());
								$actualiseMontantModal($('#modal-edit-participant form').find('fieldset div').first());
							}
						});
					};

					$getSports = function() {
						$.ajax({
							url: "<?php url('ecoles/'.$ecole['id'].'/import/ajax'); ?>",
						  	method: "POST",
						  	cache: false,
							dataType: "json",
							data:{load:"sports"},
							success: function(data) {
								sports = data;
								$actualiseSportsModal($('#modal-edit-participant form').find('fieldset div').first());
							}
						});
					};

					$filtreTarifs = function(logement, sportif, fanfaron, pompom, cameraman) {
						var filtre = [];
						var tarif;
						
						for (var i in tarifs) {
							tarif = tarifs[i];
							if (tarif['logement'] == '0' && logement == '1' ||
								tarif['logement'] == '1' && logement == '0' ||
								tarif['sportif'] == '1' && !sportif ||
								tarif['sportif'] == '0' && sportif ||
								tarif['for_fanfaron'] == 'no' && fanfaron ||
								tarif['for_cameraman'] == 'no' && cameraman ||
								tarif['for_pompom'] == 'no' && pompom ||
								tarif['for_fanfaron'] == 'yes' && !fanfaron ||
								tarif['for_cameraman'] == 'yes' && !cameraman ||
								tarif['for_pompom'] == 'yes' && !pompom)
								continue; 
							filtre.push(tarif);
						}
						return filtre;
					};

					$filtreSports = function(sexe, id_sport_special, capitaine) {
						var filtre = [];
						var sport;
						
						for (var i in sports) {
							sport = sports[i];

							if (sport['special'] == '1' && sport['id'] != id_sport_special ||
								sport['sexe'] == 'f' && sexe == 'h' ||
								sport['sexe'] == 'h' && sexe == 'f')
								continue; 

							filtre.push(sport);
						}
						return filtre;
					};

					$filtreCapitaines = function(ecole_sport){
						var filtre = [];
						var participants = <?php echo json_encode($participants);?>;
						for (var pid in participants) {
							if (participants[pid]['is_capitaine']=="1" && participants[pid]['ecole_sport']==ecole_sport)
							{
								filtre.push(participants[pid]);
							}
						}
						return filtre;
					};

					$removeAccents = function(s){
					    var r = s.toLowerCase();
					    non_asciis = {'a': '[àáâãäå]', 'ae': 'æ', 'c': 'ç', 'e': '[èéêë]', 'i': '[ìíîï]', 'n': 'ñ', 'o': '[òóôõö]', 'oe': 'œ', 'u': '[ùúûűü]', 'y': '[ýÿ]'};
					    for (i in non_asciis) { r = r.replace(new RegExp(non_asciis[i], 'g'), i); }
					    return r;
					};

					$escapeHtml = function(text) {
						return text
							.replace(/&/g, "&amp;")
							.replace(/</g, "&lt;")
							.replace(/>/g, "&gt;")
							.replace(/"/g, "&quot;")
							.replace(/'/g, "&#039;");
					};

					$levenshtein = function(s, t) {
					    var d = []; //2d matrix

					    // Step 1
					    var n = s.length;
					    var m = t.length;

					    if (n == 0) return m;
					    if (m == 0) return n;

					    //Create an array of arrays in javascript (a descending loop is quicker)
					    for (var i = n; i >= 0; i--) d[i] = [];

					    // Step 2
					    for (var i = n; i >= 0; i--) d[i][0] = i;
					    for (var j = m; j >= 0; j--) d[0][j] = j;

					    // Step 3
					    for (var i = 1; i <= n; i++) {
					        var s_i = s.charAt(i - 1);

					        // Step 4
					        for (var j = 1; j <= m; j++) {

					            //Check the jagged ld total so far
					            if (i == j && d[i][j] > 4) return n;

					            var t_j = t.charAt(j - 1);
					            var cost = (s_i == t_j) ? 0 : 1; // Step 5

					            //Calculate the minimum
					            var mi = d[i - 1][j] + 1;
					            var b = d[i][j - 1] + 1;
					            var c = d[i - 1][j - 1] + cost;

					            if (b < mi) mi = b;
					            if (c < mi) mi = c;

					            d[i][j] = mi; // Step 6

					            //Damerau transposition
					            if (i > 1 && j > 1 && s_i == t.charAt(j - 2) && s.charAt(i - 2) == t_j) {
					                d[i][j] = Math.min(d[i][j], d[i - 2][j - 2] + cost);
					            }
					        }
					    }

					    // Step 7
					    return d[n][m];
					};

					var busy = false;

					$onScrollResize = function(force) {
				    	if(busy && !force)
					        return;

					    busy = true;
				        
				        if ($('input[autocomplete="off"].autocomplete-opened').length) {
				        	var inputAutoComplete = $('input[autocomplete="off"].autocomplete-opened').first();
				        	
				        	$('.ui-autocomplete').css({'left': inputAutoComplete.offset().left + $('.ui-autocomplete').outerWidth() > $(window).width() ? 
				        		inputAutoComplete.offset().left + inputAutoComplete.outerWidth() - $('.ui-autocomplete').outerWidth() : inputAutoComplete.offset().left,
				        		'top': inputAutoComplete.offset().top + inputAutoComplete.outerHeight() + $('.ui-autocomplete').outerHeight() > $(window).height() ?
				        		inputAutoComplete.offset().top - $('.ui-autocomplete').outerHeight() : inputAutoComplete.offset().top + inputAutoComplete.outerHeight()});
				        }

				        busy = false;
				    };

					$(document).on('scroll', function() { $onScrollResize(false); });
					$(window).on('resize', function() { setTimeout(function() { $onScrollResize(true); }, 100); });

					$initAutocomplete = function(edit) {
						if (edit && !$('#modal-edit-participant').length)
							return;

						if ($('#form-licence'+(edit ? '-edit' : '')).data('uiAutocomplete'))
							$('#form-licence'+(edit ? '-edit' : '')).autocomplete("destroy").removeData('uiAutocomplete').removeAttr('autocomplete');
						
						$('#form-licence'+(edit ? '-edit' : '')).autocomplete({
							source: function(request, response) {
						        var is = [];
						        var filtresReturn = [];
						        var val;
						        var valnp;
						        var item;
						        var count = 0;
						        var countPrint = 0;
						        var nom = null;
						        var prenom = null;
						        var prenom_nom_l;
						        var nom_prenom_l;
						        var dist = 0;
						        var best = false;

						        prenom = $removeAccents(this.element.parent().parent().prev().find('input[name="prenom"]').val()).replace(/[- ]/g, '').toLowerCase();
								nom = $removeAccents(this.element.parent().parent().prev().find('input[name="nom"]').val()).replace(/[- ]/g, '').toLowerCase();
						        val = (request.term).replace(/[^a-z\d]/gi, '').toUpperCase();
						        valnp = $removeAccents(request.term).replace(/[- ]/g, '').toLowerCase();


								for (var i in licences) {
									item = licences[i];
									item['lui'] = false;
									item['licence'] = item['licence'].replace(/ /g, '');
									item['licence'] = item['licence'].substring(0, 4) + ' ' + item['licence'].substring(4);
									nom_prenom_l = $removeAccents(item['nom']+' '+item['prenom']).replace(/[- ]/g, '').toLowerCase();
									prenom_nom_l = $removeAccents(item['prenom']+' '+item['nom']).replace(/[- ]/g, '').toLowerCase();

									if (item['licence'].replace(/ /g, '').indexOf(val) >= 0 ||
										nom_prenom_l.indexOf(valnp) >= 0 ||
										prenom_nom_l.indexOf(valnp) >= 0) {
										count++;

										if (prenom !== null &&
											nom !== null &&
											(dist = $levenshtein(prenom, item['prenom'].replace(/[- ]/g, '').toLowerCase()) + 
											$levenshtein(nom, item['nom'].replace(/[- ]/g, '').toLowerCase())) < (best === false ? 5 : best)) {
											
											if (best !== false)
												filtresReturn[0]['lui'] = false;
											
											best = dist;
											item['lui'] = true;
											filtresReturn.unshift(item);

											if (countPrint >= 20)
												filtresReturn.pop();

											else
												countPrint++;
										}

										else if (countPrint < 20) {
											countPrint++;
											filtresReturn.push(item);
										}
									}
								}

								if (valnp.length == 0 ||
						        	("certificatmedical").indexOf(valnp) >= 0 ||
						        	("cm").indexOf(valnp) >= 0) {
									filtresReturn.push({cm:true});
						        }

								item = {};
								item['value'] = '...';
								item['plus'] = count - countPrint;
								item['count'] = count;
								item['total'] = Object.keys(licences).length;
								

								if (count)
									filtresReturn.push(item);


								response(filtresReturn);
						    },
						    minLength : 0,
						    open: function(e, ui) {
						    	$(this).addClass('autocomplete-opened');
						    },
						    close: function(e, ui) {
						    	$(this).removeClass('autocomplete-opened');
						    },
					        select: function(e, ui) {
					        	if (ui.item['value'] == '...' && item['total'] !== undefined)
					        		return false;

								$(this).val(ui.item['cm'] ? 'Certificat médical ou questionnaire' : ui.item['licence']).trigger('change');
								return false;
					        },
					        focus: function(e, ui) {
		            			return false;  
					    	},
						}).bind('focus', function(event){
							$(this).autocomplete("search");
						}).data('uiAutocomplete')._renderItem = function( ul, item ) {
							if (item['value'] == '...' && item['total'] !== undefined)
	        					return $('<li class="ui-state-disabled">' + (item['plus'] ? ('... et ' + item['plus'] + ' autre'+(item['plus'] > 1 ? 's' : '')+'<br />') : '') +
	        						'(' + item['count'] + ' sur '+ item['total'] +')</li>').appendTo(ul);

	        				if (item['cm'])
	        					return $("<li class='ui-menu-item cm'></li>")
			                    	.append('Certificat médical ou questionnaire')
			                    	.appendTo(ul);

	            			return $("<li class='ui-menu-item'></li>")
		                    	.append(item['licence'] + '<br /><small' + (item['lui'] === true ? ' class="lui"' : '') + '>' + $escapeHtml(item['prenom'] + ' ' + item['nom']) + '</small>')
		                    	.appendTo(ul);
		                };


		                if ($('#form-recharge'+(edit ? '-edit' : '')).data('uiAutocomplete'))
							$('#form-recharge'+(edit ? '-edit' : '')).autocomplete("destroy").removeData('uiAutocomplete').removeAttr('autocomplete');
						
		                $('#form-recharge'+(edit ? '-edit' : '')).autocomplete({
							source: function(request, response) {
								response(request.term && $.isNumeric(request.term) && request.term >= 0 ?
								 	[request.term] : ["0", "4"]);
							},
						    minLength : 0,
						    open: function(e, ui) {
						    	$(this).addClass('autocomplete-opened');
						    },
						    close: function(e, ui) {
						    	$(this).removeClass('autocomplete-opened');
						    },
						    select: function(e, ui) {
					        	if (ui.item['value'] == '...')
					        		return false;

								$(this).val(ui.item['value']).trigger('change');
								return false;
					        },
					        focus: function(e, ui) {
		            			return false;  
					    	},
						}).bind('focus', function(event){
							$(this).autocomplete("search");
						}).data('uiAutocomplete')._renderItem = function( ul, item ) {
							if (item['value'] == '...')
	        					return $('<li class="ui-state-disabled">ou tout autre valeur...</li>').appendTo(ul);

	            			return $("<li class='ui-menu-item'></li>")
		                    	.append(item['value'] + ' €')
		                    	.appendTo(ul);
		                };
		                $('#form-recharge,#form-recharge-edit').on('change', function() {
		                	if (!$.isNumeric($(this).val()) || $(this).val() < 0)
		                		$(this).val(0);
		                });


		                if ($('#form-nom'+(edit ? '-edit' : '')).data('uiAutocomplete'))
							$('#form-nom'+(edit ? '-edit' : '')).autocomplete("destroy").removeData('uiAutocomplete').removeAttr('autocomplete');
						
		                $('#form-nom'+(edit ? '-edit' : '')).autocomplete({
							source: function(request, response) {
						        var filtresReturn = [];
						        var val;
						        var item;
						        var count = 0;
						        var countPrint = 0;
						        var nom = null;
						        var dist = 0;
						        var best = false;

						        val = $removeAccents(request.term).replace(/[- ]/g, '').toLowerCase();

						        function trierNom(a, b){
									return a['nom'].localeCompare(b['nom']);
								}

								licences.sort(trierNom);

								for (var i in licences) {
									item = licences[i];
									nom = $removeAccents(item['nom']).replace(/[- ]/g, '').toLowerCase();

									if (nom.indexOf(val) >= 0) {
										count++;

										if (countPrint < 20) {
											countPrint++;
											filtresReturn.push(item);
										}
									}
								}

								item = [];
								item['value'] = '...';
								item['plus'] = count - countPrint;
								item['count'] = count;
								item['total'] = Object.keys(licences).length;
								

								if (count)
									filtresReturn.push(item);


								response(filtresReturn);
							},
						    minLength : 0,
						    open: function(e, ui) {
						    	$(this).addClass('autocomplete-opened');
						    },
						    close: function(e, ui) {
						    	$(this).removeClass('autocomplete-opened');
						    },
						    select: function(e, ui) {
					        	if (ui.item['value'] == '...')
					        		return false;

								ui.item['licence'] = ui.item['licence'].replace(/ /g, '');
								ui.item['licence'] = ui.item['licence'].substring(0, 4) + ' ' + ui.item['licence'].substring(4);
					        	$(this).next().val(ui.item['prenom']);
					        	$(this).parent().parent().next().find('input[name="licence"]').val(ui.item['licence']);
								$(this).val(ui.item['nom']).trigger('change');
								return false;
					        },
					        focus: function(e, ui) {
		            			return false;  
					    	},
						}).bind('focus', function(event){
							$(this).autocomplete("search");
						}).data('uiAutocomplete')._renderItem = function( ul, item ) {
							if (item['value'] == '...' && item['total'] !== undefined)
	        					return $('<li class="ui-state-disabled">' + (item['plus'] ? ('... et ' + item['plus'] + ' autre'+(item['plus'] > 1 ? 's' : '')+'<br />') : '') +
	        						'(' + item['count'] + ' sur '+ item['total'] +')</li>').appendTo(ul);

	            			return $("<li class='ui-menu-item'></li>")
		                    	.append($escapeHtml(item['nom']) + '<br /><small>' + $escapeHtml(item['prenom']) + ' <b>' + $escapeHtml(item['nom']) + '</b></small>')
		                    	.appendTo(ul);
		                };
	               	};

	                $('#form-recharge,#form-recharge-edit').on('change', function() {
	                	if (!$.isNumeric($(this).val()) || $(this).val() < 0)
	                		$(this).val(0);
	                });

					$('td.actions button').bind('keypress', function(event) {
						$initPost($(this), event);
					});
					$('td.actions button').bind('click', function(event) {
						$initPost($(this), event);
					});	
					$('tbody tr.form td:not(:last-of-type)').bind('click', function() {
						$(this).parent().find('td:last-of-type button:first-of-type').unbind('click').click();
					});	

					document.onselectstart = function() { return false; };

					$('#modal-ajout-participant form').bind('submit', function(event) { $analysisModal(false, event, true); });
					
					var timerSportif;
					$('#form-sportif,#form-sportif-edit').change(function() { 
						var elem = $(this);

						if (timerSportif !== null)
							clearTimeout(timerSportif);

						if (elem.is(':checked')) {
							elem.parent().parent().next().fadeIn(500);
							elem.next().addClass('two_input');
							timerSportif = setTimeout(function() {
								elem.next().next().removeClass('zero_input'); }, 500);
						}

						else {
							elem.parent().parent().next().fadeOut(500);
							elem.next().next().addClass('zero_input')
							timerSportif = setTimeout(function() {
								elem.next().removeClass('two_input'); }, 500);
						}});
					$('#form-sportif,#form-sportif-edit').change();

					$('#form-licence,#form-licence-edit').change(function() {
						if($(this).val() == 'Certificat médical ou questionnaire' && $('#form-sportif,#form-sportif-edit').is(':checked'))
							$(this).parent().next().removeAttr('hidden');
						else 
							$(this).parent().next().attr('hidden', 'true');
					});
					$('#form-licence,#form-licence-edit').change();
					
					var timerLogement;
					$('#form-logement,#form-logement-edit').change(function() { 
						var elem = $(this);

						if (timerLogement !== null)
							clearTimeout(timerLogement);

						if (!elem.is(':checked') && 
							!$ecole_lyonnaise) {
							elem.next().addClass('two_input');
							timerLogement = setTimeout(function() {
								elem.next().next().removeClass('zero_input'); }, 500);
						}

						else {
							elem.next().next().addClass('zero_input')
							timerLogement = setTimeout(function() {
								elem.next().removeClass('two_input'); }, 500);
						}});
					$('#form-logement,#form-logement-edit').change();

					$('#ajout_participant').on('click', function(e) { 
						e.preventDefault(); 
						$initAutocomplete(false);
						$actualiseTarifsModal($('#modal-ajout-participant form').find('fieldset div').first());
						$actualiseSportsModal($('#modal-ajout-participant form').find('fieldset div').first());
						$actualiseMontantModal($('#modal-ajout-participant form').find('fieldset div').first());
						$('#form-sportif').change();
						$('#form-logement').change();

						$('#modal-ajout-participant').modal({focus:false});
						$('#simplemodal-container').css({'height': 'calc(100% - 50px)', 'top': '20px'});
						$('#modal-ajout-participant input[type=checkbox]:not(#form-capitaine)').bind('change', function() {
							$actualiseTarifsModal($(this));
						});
						$('#modal-ajout-participant select, #modal-ajout-participant input').bind('change', function() {
							$actualiseMontantModal($(this));
						});
						$('#modal-ajout-participant select#form-tarif, #modal-ajout-participant input[type=checkbox]').bind('change', function() {
							$actualiseSportsModal($(this));
						});
						$('#modal-ajout-participant input, #modal-ajout-participant select').bind('keypress', function(event) {
							$actualiseMontantModal($(this));
							$analysisModal(false, event, false);
						});
						$('#modal-ajout-participant input[type=submit]').bind('click', function(event) {
							$analysisModal(false, event, true);
						});	
						
					});

					<?php if (!empty($participant_edit) && (!empty($phase_actuelle) || !empty($accesAdmin))) { ?>

					$('#modal-edit-participant form').bind('submit', function(event) { $analysisModal(true, event, true); });
					$initAutocomplete(true);
					$actualiseTarifsModal($('#modal-edit-participant form').find('fieldset div').first());
					$actualiseSportsModal($('#modal-edit-participant form').find('fieldset div').first());
					$actualiseMontantModal($('#modal-edit-participant form').find('fieldset div').first());

					$('#modal-edit-participant').modal({focus:false});
					$('#simplemodal-container').css({'height': 'calc(100% - 50px)', 'top': '20px'});
					$('#modal-edit-participant input[type=checkbox]:not(#form-capitaine-edit)').bind('change', function() {
						$actualiseTarifsModal($(this));
					});
					$('#modal-edit-participant select, #modal-edit-participant input').bind('change', function() {
						$actualiseMontantModal($(this));
					});
					$('#modal-edit-participant select#form-tarif, #modal-edit-participant input[type=checkbox]').bind('change', function() {
						$actualiseSportsModal($(this));
					});
					$('#modal-edit-participant input, #modal-edit-participant select').bind('keypress', function(event) {
						$actualiseMontantModal($(this));
						$analysisModal(true, event, false);
					});
					$('#modal-edit-participant input[type=submit]').first().bind('click', function(event) {
						$analysisModal(true, event, true);
					});	

					<?php } ?>

					$getLicences();
					$getSports();
					$getTarifs();


				});
				</script>

<?php

//Inclusion du pied de page
require DIR.'templates/_footer.php';
