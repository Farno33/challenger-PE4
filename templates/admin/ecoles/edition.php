<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* templates/admin/ecoles/liste.php ************************/
/* Template de la liste du module des Ecoles ***************/
/* *********************************************************/
/* Dernière modification : le 16/02/15 *********************/
/* *********************************************************/


//Inclusion de l'entête de page
require DIR.'templates/admin/_header_admin.php';

?>
			

				<nav class="subnav">
				<h2><a href="?p=">Détail de l'école <i><?php echo stripslashes($ecole['nom']); ?></i></a></h2>

					<ul>
						<li><a href="?p=coords">Coordonnées</a></li>
						<li><a href="?p=quotas">Quotas</a></li>
						<li><a href="?p=etat">Etat</a></li>
						<li><a href="?p=sports">Sports</a></li>
						<li><a href="?p=droits">Droits</a></li>
						<li><a href="?p=paiements">Paiements</a></li>
						<!--<li><a href="?p=actions">Actions</a></li>-->
					</ul>
				</nav>

				<?php if (empty($_GET['p']) || $_GET['p'] == 'coords') { ?>

				<form method="post" autocomplete="off" id="form-coords" class="form-table" action="?p=coords" enctype="multipart/form-data">
					<fieldset>
						<div>
							<h3>Attributs</h3>
							
							<?php if (isset($erreur_maj)) { ?>

							<div class="alerte alerte-<?php echo $erreur_maj === false ? 'success' : 'erreur'; ?>">
								<div class="alerte-contenu">
									<?php 
									if ($erreur_maj == 'champs') echo 'Tous les champs n\'ont pas été correctement remplis'; 
									else if ($erreur_maj == false) echo 'Les données de l\'école ont bien été enregistrées'; 
									else echo 'Une erreur s\'est produite';
									?>
								</div>
							</div>

							<?php } ?>

							<label for="form-null" class="needed">
								<span>Nom / Abréviation</span>
								<input class="two_input" placeholder="Nom..." type="text" name="nom" id="form-nom" value="<?php echo stripslashes($ecole['nom']); ?>" />
								<input class="two_input" placeholder="Abréviation..." type="text" name="abreviation" id="form-abreviation" value="<?php echo stripslashes($ecole['abreviation']); ?>" />
							</label>

							<label for="form-null" class="needed">
								<span>Lyonnaise / Format</span>
								<input type="checkbox" <?php if ($ecole['nb_inscriptions']) echo 'disabled'; ?> name="ecole_lyonnaise" id="form-ecole-lyonnaise" value="<?php echo $ecole['id']; ?>" <?php if ($ecole['ecole_lyonnaise']) echo 'checked'; ?> />
								<label class="two_input" for="form-ecole-lyonnaise"></label>
							
								<input type="checkbox" <?php if ($ecole['nb_inscriptions']) echo 'disabled'; ?> name="format_long" id="form-format-long" value="<?php echo $ecole['id']; ?>" <?php if ($ecole['format_long']) echo 'checked'; ?> />
								<label class="two_input format" for="form-format-long"></label>
							</label>
						
							<label for="form-as-code">
								<span>Code(s) AS / Langue</span>
								<input class="two_input" type="text" name="as_code" id="form-as-code" value="<?php echo stripslashes($ecole['as_code']); ?>" placeholder="Si plusieurs, séparez-les par des virgules" />
								<select class="two_input" name="langue" id="form-langue">
									<?php foreach ($languesEcole as $langue => $label) { ?>
									<option value="<?php echo $langue; ?>"<?php if ($ecole['langue'] == $langue) echo ' selected'; ?>><?php echo $label; ?></option>
									<?php } ?>
								</select>
							</label>

							<label for="form-image" class="needed">
								<span>Image (max 256Kio)</span>
								<input placeholder="Image (max 256Kio)..." type="file" name="image" id="form-image" />
								<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo 256 * 1024; ?>" />
							</label>

							<?php if (!empty($ecole['token'])) { ?>

							<center><img src="<?php url('image/'.$ecole['token']).'?'.rand(); ?>" alt="" style="<?php echo ($ecole['width'] && $ecole['width'] * 125 / $ecole['height'] > 200 ? 'width:200px' : 'height:125px'); ?>; background-color: white;" /><br />
								<a href="?p=coords&dimg=<?php echo $ecole['token']; ?>">Supprimer l'image</a></center>
							<br />

							<?php } ?>


							<h3>Coordonnées</h3>
						
							<label for="form-adresse">
								<span>Adresse</span>
								<textarea name="adresse" id="form-adresse"><?php echo stripslashes($ecole['adresse']); ?></textarea>
							</label>
						
							<label for="form-null">
								<span>Code postal / Ville</span>
								<input class="two_input" placeholder="Code postal..." type="text" name="code_postal" id="form-code-postal" value="<?php echo stripslashes($ecole['code_postal']); ?>" />
								<input class="two_input" placeholder="Ville..." type="text" name="ville" id="form-ville" value="<?php echo stripslashes($ecole['ville']); ?>" />
							</label>
						
							<label for="form-null">
								<span>Email / Tel. Ecole</span>
								<input class="two_input" placeholder="Email..." type="text" name="email_ecole" id="form-email-ecole" value="<?php echo stripslashes($ecole['email_ecole']); ?>" />
								<input class="two_input" placeholder="Téléphone..." type="text" name="telephone_ecole" id="form-telephone-ecole" value="<?php echo stripslashes($ecole['telephone_ecole']); ?>" />
							</label>
						</div>

						<div>
							<h4>Responsable administratif</h4>

							<label for="form-nom-respo">
								<span>Nom / Prénom</span>
								<input class="two_input" placeholder="Nom..." type="text" name="nom_respo" id="form-nom-respo" value="<?php echo stripslashes($ecole['nom_respo']); ?>" />
								<input class="two_input" placeholder="Prénom..." type="text" name="prenom_respo" id="form-prenom-respo" value="<?php echo stripslashes($ecole['prenom_respo']); ?>" />
							</label>

							<label for="form-email-respo">
								<span>Email / Téléphone</span>
								<input class="two_input" placeholder="Email..." type="text" name="email_respo" id="form-email-respo" value="<?php echo stripslashes($ecole['email_respo']); ?>" />
								<input class="two_input" placeholder="Téléphone..." type="text" name="telephone_respo" id="form-telephone-respo" value="<?php echo stripslashes($ecole['telephone_respo']); ?>" />
							</label>
						</div>

						<div>
							<h4>Responsable organisation</h4>

							<label for="form-nom-corespo">
								<span>Nom / Prénom</span>
								<input class="two_input" placeholder="Nom..." type="text" name="nom_corespo" id="form-nom-corespo" value="<?php echo stripslashes($ecole['nom_corespo']); ?>" />
								<input class="two_input" placeholder="Prénom..." type="text" name="prenom_corespo" id="form-prenom-corespo" value="<?php echo stripslashes($ecole['prenom_corespo']); ?>" />
							</label>

							<label for="form-email-corespo">
								<span>Email / Téléphone</span>
								<input class="two_input" placeholder="Email..." type="text" name="email_corespo" id="form-email-corespo" value="<?php echo stripslashes($ecole['email_corespo']); ?>" />
								<input class="two_input" placeholder="Téléphone..." type="text" name="telephone_corespo" id="form-telephone-corespo" value="<?php echo stripslashes($ecole['telephone_corespo']); ?>" />
							</label>
						</div>
					</fieldset>

					<fieldset>
						<div>
							<h3>Commentaire</h3>

							<label for="form-commentaire">
								<span>Commentaire</span>
								<textarea placeholder="Commentaire, TODO..." name="commentaire" id="form-commentaire"><?php echo empty($ecole['commentaire']) ? '' : stripslashes($ecole['commentaire']); ?></textarea>
							</label>
						</div>
					</fieldset>

					<center>
						<input type="submit" name="maj" value="Mettre à jour les données" class="success" />
					</center>
				</form>

				<?php } else if ($_GET['p'] == 'quotas') { ?>

				<form method="post" id="form-quotas" autocomplete="off" class="form-table" action="?p=quotas">
					<fieldset>
						<div>
							<h3>Quotas</h3>

							<?php if (isset($erreur_quotas)) { ?>

							<div class="alerte alerte-<?php echo $erreur_quotas === false ? 'success' : 'erreur'; ?>">
								<div class="alerte-contenu">
									<?php 
									if ($erreur_quotas == 'quotas') echo 'Il y a une erreur dans les quotas'; 
									else if ($erreur_quotas == 'champs') echo 'Tous les champs n\'ont pas été correctement remplis'; 
									else if ($erreur_quotas == false) echo 'Les quotas de l\'école ont bien été enregistrées'; 
									else echo 'Une erreur s\'est produite';
									?>
								</div>
							</div>

							<?php } ?>

							<label for="form-null">
								<span><i>Q.</i> Total</span>
								<input type="checkbox" name="quota_total_on" id="form-quota-total-on" value="<?php echo (int) $ecole['id']; ?>" <?php if (!empty($quotas['total'])) echo 'checked '; ?>/>
								<label class="four_input" for="form-quota-total-on"></label>
								<input class="fourtwo_input" type="number" min="0" name="quota_total" id="form-quota-total" value="<?php echo empty($quotas['total']) ? '' : (int) $quotas['total']['valeur']; ?>" />
								<small>Il y a jusqu'à présent <b><?php echo (int) $ecole['nb_inscriptions']; ?></b> inscriptions</small>
							</label>
						
							<label for="form-null">
								<span><i>Q.</i> Sportifs / Non</span>
								<input type="checkbox" name="quota_sportif_on" id="form-quota-sportif-on" value="<?php echo (int) $ecole['id']; ?>" <?php if (!empty($quotas['sportif'])) echo 'checked '; ?>/>
								<label class="four_input" for="form-quota-sportif-on"></label>
								<input class="four_input" class="four_input" type="number" min="0" name="quota_sportif" id="form-quota-sportif" value="<?php echo empty($quotas['sportif']) ? '' : (int) $quotas['sportif']['valeur']; ?>" />
								<input type="checkbox" name="quota_nonsportif_on" id="form-quota-nonsportif-on" value="<?php echo (int) $ecole['id']; ?>" <?php if (!empty($quotas['nonsportif'])) echo 'checked '; ?>/>
								<label class="four_input" for="form-quota-nonsportif-on"></label>
								<input class="four_input" placeholder="Non sportifs" type="number" min="0" name="quota_nonsportif" id="form-quota-nonsportif" value="<?php echo empty($quotas['nonsportif']) ? '' : (int) $quotas['nonsportif']['valeur']; ?>" />
								<small>
									<div class="two_input">Il y a jusqu'à présent <b><?php echo (int) $ecole['nb_sportif']; ?></b> sportifs</div>
									<div class="two_input">Il y a jusqu'à présent <b><?php echo (int) $ecole['nb_inscriptions'] - (int) $ecole['nb_sportif']; ?></b> non sportifs</div>
								</small>
							</label>

							<label for="form-null">
								<span><i>Q.</i> Logement</span>
								<input type="checkbox" name="quota_logement_on" id="form-quota-logement-on" value="<?php echo (int) $ecole['id']; ?>" <?php if (!empty($quotas['logement'])) echo 'checked '; ?>/>
								<label class="four_input" for="form-quota-logement-on"></label>
								<input class="fourtwo_input" type="number" min="0" name="quota_logement" id="form-quota-logement" value="<?php echo empty($quotas['logement']) ? '' : (int) $quotas['logement']['valeur']; ?>" />
								<small>Il y a jusqu'à présent <b><?php echo (int) $ecole['nb_filles_logees'] + (int) $ecole['nb_garcons_loges']; ?></b> participants logés</small>
							</label>

							<label for="form-null">
								<span><i>Q.</i> F/G logés</span>
								<input type="checkbox" name="quota_filles_on" id="form-quota-filles-on" value="<?php echo (int) $ecole['id']; ?>" <?php if (!empty($quotas['filles_logees'])) echo 'checked '; ?>/>
								<label class="four_input" for="form-quota-filles-on"></label>
								<input class="four_input" type="number" min="0" name="quota_filles_logees" id="form-quota-filles" value="<?php echo empty($quotas['filles_logees']) ? '' : (int) $quotas['filles_logees']['valeur']; ?>" />
								<input type="checkbox" name="quota_garcons_on" id="form-quota-garcons-on" value="<?php echo (int) $ecole['id']; ?>" <?php if (!empty($quotas['garcons_loges'])) echo 'checked '; ?>/>
								<label class="four_input" for="form-quota-garcons-on"></label>
								<input class="four_input" type="number" min="0" name="quota_garcons_loges" id="form-quota-garcons" value="<?php echo empty($quotas['garcons_loges']) ? '' : (int) $quotas['garcons_loges']['valeur']; ?>" />
								<small>
									<div class="two_input">Il y a jusqu'à présent <b><?php echo (int) $ecole['nb_filles_logees']; ?></b> filles logées</div>
									<div class="two_input">Il y a jusqu'à présent <b><?php echo (int) $ecole['nb_garcons_loges']; ?></b> garçons logés</div>
								</small>
							</label>
							
							<label for="form-null">
								<span><i>Q.</i> Pompoms</span>
								<input type="checkbox" name="quota_pompom_on" id="form-quota-pompom-on" value="<?php echo (int) $ecole['id']; ?>" <?php if (!empty($quotas['pompom'])) echo 'checked '; ?>/>
								<label class="four_input" for="form-quota-pompom-on"></label>
								<input class="four_input" type="number" min="0" name="quota_pompom" id="form-quota-pompom" value="<?php echo empty($quotas['pompom']) ? '' : (int) $quotas['pompom']['valeur']; ?>" />
								<input type="checkbox" name="quota_pompom_nonsportif_on" id="form-quota-pompom-nonsportif-on" value="<?php echo (int) $ecole['id']; ?>" <?php if (!empty($quotas['pompom_nonsportif'])) echo 'checked '; ?>/>
								<label class="four_input" for="form-quota-pompom-nonsportif-on"></label>
								<input class="four_input" type="number" min="0" name="quota_pompom_nonsportif" id="form-quota-pompom-nonsportif" value="<?php echo empty($quotas['pompom_nonsportif']) ? '' : (int) $quotas['pompom_nonsportif']['valeur']; ?>" placeholder="Pompoms non sportifs" />
								<small>
									<div class="two_input">Il y a jusqu'à présent <b><?php echo (int) $ecole['nb_pompom']; ?></b> pompoms.</div>
									<div class="two_input">Dont <b><?php echo (int) $ecole['nb_pompom_nonsportif']; ?></b> non sportifs.</div>
								</small>
							</label>

							<label for="form-null">
								<span><i>Q.</i> Caméramans</span>
								<input type="checkbox" name="quota_cameraman_on" id="form-quota-cameraman-on" value="<?php echo (int) $ecole['id']; ?>" <?php if (!empty($quotas['cameraman'])) echo 'checked '; ?>/>
								<label class="four_input" for="form-quota-cameraman-on"></label>
								<input class="four_input" type="number" min="0" name="quota_cameraman" id="form-quota-cameraman" value="<?php echo empty($quotas['cameraman']) ? '' : (int) $quotas['cameraman']['valeur']; ?>" />
								<input type="checkbox" name="quota_cameraman_nonsportif_on" id="form-quota-cameraman-nonsportif-on" value="<?php echo (int) $ecole['id']; ?>" <?php if (!empty($quotas['cameraman_nonsportif'])) echo 'checked '; ?>/>
								<label class="four_input" for="form-quota-cameraman-nonsportif-on"></label>
								<input class="four_input" type="number" min="0" name="quota_cameraman_nonsportif" id="form-quota-cameraman-nonsportif" value="<?php echo empty($quotas['cameraman_nonsportif']) ? '' : (int) $quotas['cameraman_nonsportif']['valeur']; ?>" placeholder="Caméramans non sportifs" />
								<small>
									<div class="two_input">Il y a jusqu'à présent <b><?php echo (int) $ecole['nb_cameraman']; ?></b> caméramans.</div>
									<div class="two_input">Dont <b><?php echo (int) $ecole['nb_cameraman_nonsportif']; ?></b> non sportifs.</div>
								</small>
							</label>
						
							<label for="form-null">
								<span><i>Q.</i> Fanfarons</span>
								<input type="checkbox" name="quota_fanfaron_on" id="form-quota-fanfaron-on" value="<?php echo (int) $ecole['id']; ?>" <?php if (!empty($quotas['fanfaron'])) echo 'checked '; ?>/>
								<label class="four_input" for="form-quota-fanfaron-on"></label>
								<input class="four_input" type="number" min="0" name="quota_fanfaron" id="form-quota-fanfaron" value="<?php echo empty($quotas['fanfaron']) ? '' : (int) $quotas['fanfaron']['valeur']; ?>" />
								<input type="checkbox" name="quota_fanfaron_nonsportif_on" id="form-quota-fanfaron-nonsportif-on" value="<?php echo (int) $ecole['id']; ?>" <?php if (!empty($quotas['fanfaron_nonsportif'])) echo 'checked '; ?>/>
								<label class="four_input" for="form-quota-fanfaron-nonsportif-on"></label>
								<input class="four_input" type="number" min="0" name="quota_fanfaron_nonsportif" id="form-quota-fanfaron-nonsportif" value="<?php echo empty($quotas['fanfaron_nonsportif']) ? '' : (int) $quotas['fanfaron_nonsportif']['valeur']; ?>" placeholder="Fanfarons non sportifs" />
								<small>
									<div class="two_input">Il y a jusqu'à présent <b><?php echo (int) $ecole['nb_fanfaron']; ?></b> fanfarons.</div>
									<div class="two_input">Dont <b><?php echo (int) $ecole['nb_fanfaron_nonsportif']; ?></b> non sportifs.</div>
								</small>
							</label>
						</div>

						<center>
							<input type="submit" name="quotas" value="Mettre à jour les quotas" class="success" />
						</center>
					</fieldset>
				</form>

				<?php } else if ($_GET['p'] == 'sports') { ?>

				<style>
				h3 small {
					color:gray;
					font-weight: normal;
					font-size:75%;
				}
				</style>

				<form method="post" id="form-quotas-sports" class="form-table" action="?p=sports">
					<fieldset>
						<h3>Liste des sports<br /><small>Mettre le quota max à 0 supprime le sport si il n'y a aucune inscription</small></h3>
					
						<?php if (isset($erreur_quotas_sports)) { ?>

						<div class="alerte alerte-<?php echo $erreur_quotas_sports ? 'erreur' : 'success'; ?>">
							<div class="alerte-contenu">
								<?php echo $erreur_quotas_sports ? 'Les données saisies ne sont pas correctes' : 'Les sports de l\'école ont bien été mis à jour'; ?>
							</div>
						</div>

						<?php } 
						if (isset($erreur_clone)) { ?>

						<div class="alerte alerte-<?php echo $erreur_clone === true ? 'erreur' : 'success'; ?>">
							<div class="alerte-contenu">
								<?php 
									if ($erreur_clone === true) echo 'Une erreur s\'est produite lors du clonnage des quotas.<br>Rien n\'as été clonné.';
									if ($erreur_clone === 'missing') echo 'Certains quotas n\'ont pas été copiés, ceci est surement dû au fait que certaines écoles ont déjà des sportifs sur ces quotas.'; 
									if ($erreur_clone === false) echo 'Les quotas ont bien été clonés.';
								?>
							</div>
						</div>
						
						<?php } ?>

						
						<table>
							<thead>
								<tr>
									<th>Nom</th>
									<th style="width:100px">Q. Equipes</th>
									<th style="width:100px">Q. Sportifs</th>
									<th style="width:100px">Q. Réservés</th>
									<th style="width:120px">Equipes</th>
									<th style="width:120px">Sportifs</th>
								</tr>
							</thead>

							<tbody>

								<?php if (!count($sports)) { ?> 

								<tr class="vide">
									<td colspan="6">Aucun sport</td>
								</tr>

								<?php } foreach ($sports as $sport) { ?>

								<tr class="form">
									<td>
										<center><?php echo stripslashes($sport['sport']).' '.printSexe($sport['sexe']); ?></center>
										<input type="hidden" name="sports[]" value="<?php echo $sport['id']; ?>" />
									</td>
									<td style="width:100px">
										<input type="number" name="equipes[]" value="<?php echo $sport['quota_equipes']; ?>" min="<?php echo max(1, (int) $sport['equipes']); ?>" />
									</td>
									<td style="width:100px">
										<input type="number" name="quotas[]" value="<?php echo $sport['quota_max']; ?>" min="<?php echo (int) $sport['inscriptions']; ?>" />
									</td>
									<td style="width:100px">
										<input type="number" name="reserves[]" value="<?php echo $sport['quota_reserves']; ?>" min="0" />
									</td>
									<td><center><?php echo (int) $sport['equipes']; ?></center></td>
									<td><center><?php echo (int) $sport['inscriptions']; ?></center></td>
								</tr>

								<?php } ?>

							</tbody>
						</table>

						<center>
							<input type="submit" name="maj_sports" value="Mettre à jour les quotas" class="success" />
							<input type="button" id="form-clone-quotas-sport" value="Clonner les quotas" class="delete" />
							<input type="button" id="form-ajout-sport" value="Ajouter un sport" />
						</center>
					</fieldset>

					<fieldset>
						<h3>Listes des licences invalides<br /><small>Certains cas d'exception peuvent être listés ici</small></h3>

				<table class="table-small">
					<thead>
						<tr>
							<th>Licence</th>
							<th>Nom</th>
							<th>Prénom</th>
							<th style="width:60px">Sexe</th>
							<th style="width:60px"><small>C. Médical</small></th>
							<th style="width:60px"><small>C. Assurance</small></th>
						</tr>
					</thead>

					<tbody>

						<?php 

						$count = 0;
						$asCodes = explode(',', $ecole['as_code']);

						foreach ($sportifs as $sid => $sportif) {
							$licence = $sportif['licence'];
							$licence = strtoupper(str_replace(' ', '', strlen($licence) < 10 && count($asCodes) == 1 ? $asCodes[0].$licence : $licence));

							if (empty($sportif['licence']) || isValidLicence($licence) && isLicenceEcole($licence, $asCodes))
								continue;

							$count++;
						  ?>

							<tr>
								<td><b><?php echo stripslashes($sportif['licence']); ?></b></td>
								<td><?php echo stripslashes($sportif['nom']); ?></td>
								<td><?php echo stripslashes($sportif['prenom']); ?></td>
								<td style="padding:0px">
									<input type="checkbox" <?php echo $sportif['sexe'] == 'h' ? 'checked ' : ''; ?>/>
									<label class="sexe"></label>
								</td>

								<td style="padding:0px">
									<input onchange="changeCertificat('medical', <?php echo $sid; ?>, this)" id="cm-<?php echo $sid; ?>" type="checkbox" <?php echo !empty($sportif['certificat_medical']) ? 'checked ' : ''; ?>/>
									<label for="cm-<?php echo $sid; ?>"></label>
								</td>

								<td style="padding:0px">
									<input onchange="changeCertificat('assurance', <?php echo $sid; ?>, this)" id="ca-<?php echo $sid; ?>" type="checkbox" <?php echo !empty($sportif['certificat_assurance']) ? 'checked ' : ''; ?>/>
									<label  for="ca-<?php echo $sid; ?>"></label>
								</td>
							</tr>

						<?php } if (empty($count)) { ?>

						<tr class="vide">
							<td colspan="6">Aucune licence invalide</td>
						</tr>

						<?php } ?>
					</tbody>
					</table>

					<script type="text/javascript">
					var changeCertificat = function(type, id, elem) {
						$.ajax({
							url: '<?php url('admin/module/ecoles/'.$ecole['id']); ?>',
							cache: false,
							method: 'POST',
							data: {
								action: 'changeCertificat',
								id: id,
								check: $(elem).is(':checked') ? '1' : '0',
								type: type
							}
						});
					};
					</script>

					</fieldset>
				</form>



				<?php } else if ($_GET['p'] == 'droits') { ?>

				<form method="post" class="form-table" action="?p=droits">
					<fieldset>
						<h3>Liste des droits</h3>
					
						<?php if (isset($add_droit) ||
							isset($delete_droit)) { ?>

						<div class="alerte alerte-success">
							<div class="alerte-contenu">
								<?php echo isset($add_droit) ? 
									'Le droit a bien été ajouté' : 
									'Le droit a bien été supprimé'; ?>
							</div>
						</div>

						<?php } ?>

						<table class="table-small">
							<thead>
								<tr class="form">
									<td>
										<input type="hidden" name="utilisateur" value="" id="form-utilisateur" />
										<input type="text" id="form-utilisateur-autocomplete" placeholder="Utilisateur..." />
									</td>
									<td class="actions">
										<center>
											<button type="submit" name="add_droit" value="<?php echo $ecole['id']; ?>" />
												<img src="<?php url('assets/images/actions/add.png'); ?>" alt="Add" />
											</button>
										</center>
									</td>
								</tr>

								<tr>
									<th>Utilisateur</th>
									<th class="actions">Actions</th>
								</tr>
							</thead>

							<tbody>

								<?php if (!count($droits)) { ?> 

								<tr class="vide">
									<td colspan="2">Aucun droit</td>
								</tr>

								<?php } foreach ($droits as $id => $droit) { ?>

								<tr class="form">
									<td><div><center><?php echo stripslashes($droit['nom'].' '.$droit['prenom']); ?></center></div></td>
									<td>
										<center>
											<button type="submit" name="del_droit" value="<?php echo $id; ?>" />
												<img src="<?php url('assets/images/actions/delete.png'); ?>" alt="Delete" />
											</button>
										</center>
									</td>
								</tr>

								<?php } ?>

							</tbody>
						</table>
					</fieldset>
				</form>

				<?php } else if ($_GET['p'] == 'etat') { ?>

				<form method="post" id="form-change-etat" class="form-table" action="?p=etat">
					<fieldset>
						<div>
							<h3>Etat de l'inscription</h3>

							<?php if (isset($erreur_etat)) { ?>

							<div class="alerte alerte-<?php echo $erreur_etat === false ? 'success' : 'erreur'; ?>">
								<div class="alerte-contenu">
									<?php 
									if ($erreur_etat == 'champs') echo 'Tous les champs n\'ont pas été correctement remplis'; 
									else if ($erreur_etat == false) echo 'L\'état de l\'inscription de l\'école a bien été modifié'; 
									else echo 'Une erreur s\'est produite';
									?>
								</div>
							</div>

							<?php } ?>

							<label for="form-etat" class="needed">
								<span>Etat</span>
								<select name="etat">
									<option value="fermee"<?php if ($ecole['etat_inscription'] == 'fermee') echo ' selected'; ?>><?php echo strip_tags(printEtatEcole('fermee')); ?></option>
									<option value="ouverte"<?php if ($ecole['etat_inscription'] == 'ouverte') echo ' selected'; ?>><?php echo strip_tags(printEtatEcole('ouverte')); ?></option>
									<option value="limitee"<?php if ($ecole['etat_inscription'] == 'limitee') echo ' selected'; ?>><?php echo strip_tags(printEtatEcole('limitee')); ?></option>
									<option value="close"<?php if ($ecole['etat_inscription'] == 'close') echo ' selected'; ?>><?php echo strip_tags(printEtatEcole('close')); ?></option>
									<option value="validee"<?php if ($ecole['etat_inscription'] == 'validee') echo ' selected'; ?>><?php echo strip_tags(printEtatEcole('validee')); ?></option>
								</select>
							</label>

							<label for="form-charte">
								<span>Charte acceptée</span>
								<input type="checkbox" name="charte" id="form-charte" value="<?php echo (int) $ecole['id']; ?>" <?php if (!empty($ecole['charte_acceptee'])) echo 'checked '; ?>/>
								<label for="form-charte"></label>
							</label>


							<label for="form-caution">
								<span>Caution reçue</span>
								<input type="checkbox" name="caution" id="form-caution" value="<?php echo (int) $ecole['id']; ?>" <?php if (!empty($ecole['caution_recue'])) echo 'checked '; ?>/>
								<label for="form-caution"></label>
							</label>



							<label for="form-caution-logement">
								<span>Caution logement</span>
								<input type="checkbox" name="caution_logement" id="form-caution-logement" value="<?php echo (int) $ecole['id']; ?>" <?php if (!empty($ecole['caution_logement'])) echo 'checked '; ?>/>
								<label for="form-caution-logement"></label>
							</label>


							<label for="form-respo" class="needed">
								<span>Responsable</span>
								<select name="respo">
									
									<?php if (empty($ecole['id_respo'])) { ?>

									<option value="">Responsable...</option>

									<?php } foreach ($respos as $rid => $respo) { ?>

									<option value="<?php echo $rid; ?>"<?php if ($rid == $ecole['id_respo']) echo ' selected'; ?>><?php echo stripslashes(strtoupper($respo['nom']).' '.$respo['prenom']); ?></option>

									<?php } ?>

								</select>
							</label>
							
							<label for="form-malus" class="needed">
								<span>Malus %</span>
								<input type="number" step="any" min="0" name="malus" id="form-malus" value="<?php echo (float) $ecole['malus']; ?>" />
							</label>


							<center><input class="success" type="submit" name="change_etat" value="Changer l'état" /></center><br />
						</div>
					</fieldset>
				</form>

				<?php } else if ($_GET['p'] == 'paiements') { ?>

				<form method="post" class="form-table" action="?p=paiements">
					<fieldset>
						<h3>Etat du paiement</h3>

						<label class="nomargin">
							<span>Prix participants</span>
							<div><?php echo printMoney($montant_inscriptions['montant']); ?></div>
						</label>

						<label class="nomargin">
							<span>Prix Gourde</span>
							<div><?php echo printMoney($montant_recharges['montant']); ?></div>
						</label>

						<?php 

						$montant = $montant_inscriptions['montant'] + $montant_recharges['montant']; 
						$malus = (float) $ecole['malus'] / 100 *  $inscriptions_enretard['montant'];
						
						if ($inscriptions_enretard['nbretards'] > 0 ) { ?>

						<label class="nomargin">
							<span>Pénalités (+<?php echo (float) $ecole['malus']; ?> %)</span>
							<div><?php echo printMoney($malus); ?></div>
							<small><b><?php echo $inscriptions_enretard['nbretards']; ?></b> inscriptions après <?php echo printDateTime(APP_DATE_MALUS, false); ?></small>
						</label>

						<?php } ?>

						<hr />

						<label class="nomargin">
							<span>Montant total</span>
							<?php echo printMoney($montant + $malus); ?>
						</label>

						<label class="nomargin">
							<span>Montant payé</span>
							<?php echo printMoney($montant_paye['montant']); ?>
						</label>

						<label class="nomargin">
							<span>Montant restant</span>
							<?php echo printMoney($montant + $malus - $montant_paye['montant']); ?>
						</label>
					</fieldset>

					<fieldset>
						<h3>Liste des paiements</h3>

						<?php if (isset($ajout_paiement)) { ?>

						<div class="alerte alerte-success">
							<div class="alerte-contenu">
								Le paiement a bien été ajouté
							</div>
						</div>

						<?php } if (isset($delete_paiement)) { ?>

						<div class="alerte alerte-success">
							<div class="alerte-contenu">
								Le paiement a bien été supprimé
							</div>
						</div>

						<?php } ?>

						<table>
							<thead>
								<tr>
									<th>Date</th>
									<th>Type</th>
									<th>Montant</th>
									<th>Commentaire</th>
									<th class="actions">Actions</th>
								</tr>
							</thead>

							<tbody>

								<?php if (!count($paiements)) { ?> 

								<tr class="vide">
									<td colspan="5">Aucun paiement</td>
								</tr>

								<?php } foreach ($paiements as $paiement) { ?>

								<tr class="form">
									<td><div><?php echo printDateTime($paiement['_date']); ?></div></td>
									<td><div><center><?php echo printTypePaiement($paiement['type']); ?></center></div></td>
									<td><div><center><?php echo printMoney($paiement['montant']); ?></center></div></td>
									<td><textarea readonly><?php echo stripslashes($paiement['commentaire']); ?></textarea></td>
									<td class="actions">
										<button type="submit" name="del_paiement" value="<?php echo $paiement['id']; ?>" />
											<img src="<?php url('assets/images/actions/delete.png'); ?>" alt="Delete" />
										</button>
									</td>
								</tr>

								<?php } ?>

							</tbody>
						</table>

						<center>
							<input type="button" id="form-ajout-paiement" value="Ajouter un paiement" class="success" />
						</center>
					</fieldset>

					<?php if (count($retards)) { ?>

					<fieldset>
						<h3>Liste des retards</h3>

						<?php if (isset($change_excuse)) { ?>

						<div class="alerte alerte-success">
							<div class="alerte-contenu">
								L'excuse du retard a bien été modifié
							</div>
						</div>

						<?php } ?>

						<table class="table-small">
							<thead>
								<tr>
									<th>Participant</th>
									<th>Inscription</th>
									<th style="width:80px">Excuse</th>
									<th class="actions">Actions</th>
								</tr>
							</thead>

							<tbody>

								<?php foreach ($retards as $pid => $participant) { ?>

								<tr class="form">
									<td class="content"><?php echo stripslashes($participant['nom'].' '.$participant['prenom']); ?></td>
									<td><div><?php echo printDateTime($participant['date_inscription']); ?></div></td>
									<td>
										<input type="checkbox" checked />
										<label class="retard<?php echo !empty($participant['hors_malus']) ? '-excuse' : ''; ?>"></label>
									</td>
									<td class="actions">
										<button type="submit" name="change_retard" value="<?php echo $pid; ?>" />
											<img src="<?php url('assets/images/actions/change.png'); ?>" alt="Change" />
										</button>
									</td>
								</tr>

								<?php } ?>

							</tbody>
						</table>
					</fieldset>

					<?php } ?>

				</form>

				<?php } else if ($_GET['p'] == 'actions') { ?>

				<form method="post" action="<?php url('admin/module/ecoles/liste'); ?>" class="form-table" onsubmit="return confirm('Etes-vous sûr de vouloir vider/supprimer cette école?');" >
					<fieldset>
						<h3>Vider l'école / Suppression de l'école</h3>
						<center>
							En vidant/supprimant l'école vous supprimerez toutes les données attachées.<br />
							À savoir : équipes, participants, paiements, ...<br />
							<b>Ces actions ne sont pas annulables !</b><br />
							<br />
							<input type="hidden" name="id" value="<?php echo $ecole['id']; ?>" />
							<input class="delete" type="submit" name="empty_ecole" value="Vider l'école de ses participants" /><br />
							<br />
							<input class="delete" type="submit" name="del_ecole" value="Supprimer l'école" />
						</center>
					</fieldset>
				</form>

				<?php } ?>

				<div id="modal-ajout-paiement" class="modal">
					<form method="post" action="#ancre-paiements">
						<fieldset>
							<legend>Ajout d'un paiement manuel</legend>

							<label for="form-montant">
								<span>Montant</span>
								<input type="number" step="any" name="montant" id="form-montant" value="" />
								<small>La valeur peut être négative afin de faire une régulation</small>
							</label>

							<label for="form-type">
								<span>Type</span>
								<select name="type" id="form-type">
									<option value=""></option>
									<option value="cb">Carte-Bleue</option>
									<option value="cheque">Chèque</option>
									<option value="especes">Espèces</option>
									<option value="virement">Virement</option>
									<option value="regulation">Régulation</option>
								</select>
							</label>

							<label for="form-commentaire">
								<span>Commentaire</span>
								<input type="text" id="form-commentaire" name="commentaire" value="" />
							</label>

							<center>
								<input type="submit" class="success" value="Ajouter le paiement" name="add_paiement" />
							</center>
						</fieldset>
					</form>
				</div>

				<div id="modal-clone-quotas-sport" class="modal">
					<form method="post" action="#">
						<fieldset>
							<legend>Clonage des quotas depuis <?php echo $ecole['nom'] ?></legend>
							<h3>
								Sports à copier
							</h3>
							<table>
								<thead>
									<tr>
										<th>Sport</th>
										<th style="width:100px">Selection</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($sports as $sport) { ?>
									<tr class="form">
										<td>
											<center><?php echo stripslashes($sport['sport']).' '.printSexe($sport['sexe']); ?></center>
										</td>
										<td>
											<input id="s<?php echo $ecole['id']; ?>" type="checkbox" name="clone_sports[]" value="<?php echo $sport['id']; ?>" checked />
											<label for="s<?php echo $ecole['id']; ?>"></label>
										</td>
									</tr>
									<?php } 
									if (count($sports) == 0) {?>
									<tr class="vide">
										<td colspan="2">Aucun sport à clonner</td>
									</tr>
									<?php } ?>
								</tbody>
							</table>
							<h3>
								Écoles cibles
								<br>
								<small>Les écoles n'ayant aucun quota sont pré-séléctionnés</small>
							</h3>
							<table>
								<thead>
									<tr>
										<th>Ecole</th>
										<th style="width:100px">Selection</th>
									</tr>
								</thead>
								<tbody>
								<?php foreach ($ecoles_cible as $ecole) { ?>
									<tr class="form">
										<td>
											<center><?php echo stripslashes($ecole['nom']); ?></center>
										</td>
										<td>
											<input id="e<?php echo $ecole['id']; ?>" type="checkbox" name="clone_ecoles[]" value="<?php echo $ecole['id']; ?>" checked />
											<label for="e<?php echo $ecole['id']; ?>"></label>
										</td>
									</tr>
								<?php } 
								if (count($ecoles_cible) == 0) {?>
								<tr class="vide">
									<td colspan="2">Aucune école à clonner</td>
								</tr>
								<?php } ?>
							</tbody>
							</table>
							<h3>
								Attention la copie écrasera les quotas existants sur les ecoles cibles
								<br>
								<small>Sauf s'il y a déjà des inscrits, au quel cas la modification sera ignorée</small>
							</h3>
							<center>
								<input type="submit" class="success" value="Cloner les quotas" name="clone_quotas" />
							</center>
						</fieldset>
					</form>
				</div>

				<div id="modal-ajout-sport" class="modal">
					<form method="post" action="#ancre-sports">
						<fieldset>
							<legend>Ajout d'un sport</legend>

							<label for="form-sport">
								<span>Sport</span>
								<select name="sport" id="form-sport">

									<?php foreach ($sports_ajout as $sport) { ?>

									<option value="<?php echo $sport['id']; ?>"><?php echo strip_tags(stripslashes($sport['sport']).' '.printSexe($sport['sexe'])); ?></option>

									<?php } ?>

								</select>
							</label>

							<label for="form-quota-equipes" class="needed">
								<span>Quota Equipes</span>
								<input type="number" min="1" name="quota-equipes" id="form-quota-equipes" value="1" />
							</label>

							<label for="form-quota-max" class="needed">
								<span>Quota Max</span>
								<input type="number" min="1" name="quota-max" id="form-quota-max" value="" />
							</label>

							<label for="form-quota-reserves" class="needed">
								<span>Quota Réservés</span>
								<input type="number" min="0" name="quota-reserves" id="form-quota-reserves" value="0" />
							</label>

							<center>
								<input type="submit" class="success" value="Ajouter le sport" name="add_sport" />
							</center>
						</fieldset>
					</form>
				</div>

				<script type="text/javascript">
				$(function() {
					$speed =  <?php echo APP_SPEED_ERROR; ?>;
			    	
			    	$analysisData = function(elem, event, force) {
			            if (event.keyCode == 13 || force) {
			              	$field_coords = elem.children('fieldset').children('div').first();
			  				$nom = $field_coords.children('label').first().children('input').first();
			  				$image = $nom.next()[0];
			  				$erreur = false;
			  				
			                if (!$nom.val().trim()) {
			                	$erreur = true;
			                	$nom.addClass('form-error').removeClass('form-error', $speed).focus();
			                }

			                if ($image.files.length > 1 ||
			                	$image.files.length == 1 &&
			                	$image.files[0].size >= 256 * 1024) {
			                	$erreur = true;
			                	//$image.addClass('form-error').removeClass('form-error', $speed).focus();
			                	alert('Il faut au maximum un seul fichier de 256Kio');
			                }

			                if ($erreur)
			                	event.preventDefault();  

			            }
			        };


			        $analysisQuotas = function(elem, event, force) {
			            if (event.keyCode == 13 || force) {
			              	$field_quotas = elem.children('fieldset').children('div').first();
			  				$first = $field_quotas.children('label').first();
			  				
			  				$total_on = $first.children('input').first();
			  				$total = $total_on.next().next();
			  				$sportif_on = $first.next().children('input').first();
			  				$sportif = $sportif_on.next().next();
			  				$nonsportif_on = $sportif.next();
			  				$nonsportif = $nonsportif_on.next().next();
			  				$logement_on = $first.next().next().children('input').first();
			  				$logement = $logement_on.next().next();

			  				$filles_on = $first.next().next().next().children('input').first();
			  				$filles = $filles_on.next().next();
			  				$garcons_on = $filles.next();
			  				$garcons = $garcons_on.next().next();

			  				$pompom_on = $first.next().next().next().next().children('input').first();
			  				$pompom = $pompom_on.next().next();
			  				$pompom_nonsportif_on = $pompom.next();
			  				$pompom_nonsportif = $pompom_nonsportif_on.next().next();
			  				$cameraman_on = $first.next().next().next().next().next().children('input').first();
			  				$cameraman = $cameraman_on.next().next();
			  				$cameraman_nonsportif_on = $cameraman.next();
			  				$cameraman_nonsportif = $cameraman_nonsportif_on.next().next();
			  				$fanfaron_on = $first.next().next().next().next().next().next().children('input').first();
			  				$fanfaron = $fanfaron_on.next().next();
			  				$fanfaron_nonsportif_on = $fanfaron.next();
			  				$fanfaron_nonsportif = $fanfaron_nonsportif_on.next().next();

			  				$erreur = false;
			  				

			                if ($filles_on.is(':checked') && (
				                	!$.isNumeric($filles.val()) ||
				                	$filles.val() < 0 ||
				                	Math.floor($filles.val()) != $filles.val() ||
				                	$filles.val() < <?php echo (empty($ecole['nb_filles_logees']) ? 0 : (int) $ecole['nb_filles_logees']); ?>)) {
			                	$erreur = true;
			                	$filles.addClass('form-error').removeClass('form-error', $speed).focus();
			                }

			                if ($garcons_on.is(':checked') && (
				                	!$.isNumeric($garcons.val()) ||
				                	$garcons.val() < 0 ||
				                	Math.floor($garcons.val()) != $garcons.val() ||
				                	$garcons.val() < <?php echo (empty($ecole['nb_garcons_loges']) ? 0 : (int) $ecole['nb_garcons_loges']); ?>)) {
			                	$erreur = true;
			                	$garcons.addClass('form-error').removeClass('form-error', $speed).focus();
			                }

			                if ($logement_on.is(':checked') && (
				                	!$.isNumeric($logement.val()) ||
				                	$logement.val() < 0 ||
				                	Math.floor($logement.val()) != $logement.val() ||
				                	$logement.val() < <?php echo (empty($ecole['nb_garcons_loges']) ? 0 : (int) $ecole['nb_garcons_loges']) + (empty($ecole['nb_filles_logees']) ? 0 : (int) $ecole['nb_filles_logees']); ?> ||
				                	// $filles_on.is(':checked') && $garcons_on.is(':checked') && $logement.val() < parseInt($filles.val()) + parseInt($garcons.val()) ||
				                	$filles_on.is(':checked') && !$garcons_on.is(':checked') && $logement.val() < parseInt($filles.val()) ||
				                	!$filles_on.is(':checked') && $garcons_on.is(':checked') && $logement.val() < parseInt($garcons.val()))) {
			                	$erreur = true;
			                	$logement.addClass('form-error').removeClass('form-error', $speed).focus();
			                }

			                if ($pompom_nonsportif_on.is(':checked') && (
				                	!$.isNumeric($pompom_nonsportif.val()) ||
				                	$pompom_nonsportif.val() < 0 ||
				                	Math.floor($pompom_nonsportif.val()) != $pompom_nonsportif.val() ||
				                	$pompom_nonsportif.val() < <?php echo (int) (empty($ecole['nb_pompom_nonsportif']) ? 0 : $ecole['nb_pompom_nonsportif']); ?>)) {
			                	$erreur = true;
			                	$pompom_nonsportif.addClass('form-error').removeClass('form-error', $speed).focus();
			                }

			                if ($pompom_on.is(':checked') && (
				                	!$.isNumeric($pompom.val()) ||
				                	$pompom.val() < 0 ||
				                	Math.floor($pompom.val()) != $pompom.val() ||
				                	$pompom.val() < <?php echo (int) (empty($ecole['nb_pompom']) ? 0 : $ecole['nb_pompom']); ?> ||
				                	$pompom_nonsportif_on.is(':checked') && $pompom.val() < parseInt($pompom_nonsportif.val()))) {
			                	$erreur = true;
			                	$pompom.addClass('form-error').removeClass('form-error', $speed).focus();
			                }

			                if ($cameraman_nonsportif_on.is(':checked') && (
				                	!$.isNumeric($cameraman_nonsportif.val()) ||
				                	$cameraman_nonsportif.val() < 0 ||
				                	Math.floor($cameraman_nonsportif.val()) != $cameraman_nonsportif.val() ||
				                	$cameraman_nonsportif.val() < <?php echo (int) (empty($ecole['nb_cameraman_nonsportif']) ? 0 : $ecole['nb_cameraman_nonsportif']); ?>)) {
			                	$erreur = true;
			                	$cameraman_nonsportif.addClass('form-error').removeClass('form-error', $speed).focus();
			                }

			                if ($cameraman_on.is(':checked') && (
				                	!$.isNumeric($cameraman.val()) ||
				                	$cameraman.val() < 0 ||
				                	Math.floor($cameraman.val()) != $cameraman.val() ||
				                	$cameraman.val() < <?php echo (int) (empty($ecole['nb_cameraman']) ? 0 : $ecole['nb_cameraman']); ?> ||
				                	$cameraman_nonsportif_on.is(':checked') && $cameraman.val() < parseInt($cameraman_nonsportif.val()))) {
			                	$erreur = true;
			                	$cameraman.addClass('form-error').removeClass('form-error', $speed).focus();
			                }

			                if ($fanfaron_nonsportif_on.is(':checked') && (
				                	!$.isNumeric($fanfaron_nonsportif.val()) ||
				                	$fanfaron_nonsportif.val() < 0 ||
				                	Math.floor($fanfaron_nonsportif.val()) != $fanfaron_nonsportif.val() ||
				                	$fanfaron_nonsportif.val() < <?php echo (int) (empty($ecole['nb_fanfaron_nonsportif']) ? 0 : $ecole['nb_fanfaron_nonsportif']); ?>)) {
			                	$erreur = true;
			                	$fanfaron_nonsportif.addClass('form-error').removeClass('form-error', $speed).focus();
			                }

			                if ($fanfaron_on.is(':checked') && (
				                	!$.isNumeric($fanfaron.val()) ||
				                	$fanfaron.val() < 0 ||
				                	Math.floor($fanfaron.val()) != $fanfaron.val() ||
				                	$fanfaron.val() < <?php echo (int) (empty($ecole['nb_fanfaron']) ? 0 : $ecole['nb_fanfaron']); ?> ||
				                	$fanfaron_nonsportif_on.is(':checked') && $fanfaron.val() < parseInt($fanfaron_nonsportif.val()))) {
			                	$erreur = true;
			                	$fanfaron.addClass('form-error').removeClass('form-error', $speed).focus();
			                }

			                if ($sportif_on.is(':checked') && (
				                	!$.isNumeric($sportif.val()) ||
				                	$sportif.val() < 0 ||
				                	Math.floor($sportif.val()) != $sportif.val() ||
				                	$sportif.val() < <?php echo (int) (empty($ecole['nb_sportif']) ? 0 : $ecole['nb_sportif']); ?>)) {
			                	$erreur = true;
			                	$sportif.addClass('form-error').removeClass('form-error', $speed).focus();
			                }

			                if ($nonsportif_on.is(':checked') && (
				                	!$.isNumeric($nonsportif.val()) ||
				                	$nonsportif.val() < 0 ||
				                	Math.floor($nonsportif.val()) != $nonsportif.val() ||
				                	$nonsportif.val() < <?php echo (int) (empty($ecole['nb_inscriptions']) ? 0 : $ecole['nb_inscriptions']) - (int) (empty($ecole['nb_sportif']) ? 0 : $ecole['nb_sportif']); ?>)) {
			                	$erreur = true;
			                	$nonsportif.addClass('form-error').removeClass('form-error', $speed).focus();
			                }

			                if ($total_on.is(':checked') && (
				                	!$.isNumeric($total.val()) ||
				                	$total.val() < 0 ||
				                	Math.floor($total.val()) != $total.val() ||
				                	$total.val() < <?php echo (int) (empty($ecole['nb_inscriptions']) ? 0 : $ecole['nb_inscriptions']); ?> ||
				                	$logement_on.is(':checked') && $total.val() < parseInt($logement.val()) ||
				                	$filles_on.is(':checked') && $total.val() < parseInt($filles.val()) ||
				                	$garcons_on.is(':checked') && $total.val() < parseInt($garcons.val()) ||
				                	$pompom_on.is(':checked') && $total.val() < parseInt($pompom.val()) ||
				                	$pompom_nonsportif_on.is(':checked') && $total.val() < parseInt($pompom_nonsportif.val()) ||
				                	//$pompom_nonsportif_on.is(':checked') && $total.val() - parseInt($sportif.val()) < parseInt($pompom_nonsportif.val()) ||
				                	$cameraman_on.is(':checked') && $total.val() < parseInt($cameraman.val()) ||
				                	$cameraman_nonsportif_on.is(':checked') && $total.val() < parseInt($cameraman_nonsportif.val()) ||
				                	//$cameraman_nonsportif_on.is(':checked') && $total.val() - parseInt($sportif.val()) < parseInt($cameraman_nonsportif.val()) ||
				                	$fanfaron_on.is(':checked') && $total.val() < parseInt($fanfaron.val()) ||
				                	$fanfaron_nonsportif_on.is(':checked') && $total.val() < parseInt($fanfaron_nonsportif.val()) ||
				                	//$fanfaron_nonsportif_on.is(':checked') && $total.val() - parseInt($sportif.val()) < parseInt($fanfaron_nonsportif.val()) ||
				                	//$filles_on.is(':checked') && $garcons_on.is(':checked') && $total.val() < parseInt($filles.val()) + parseInt($garcons.val()) ||
				                	$total.val() < parseInt($sportif.val()))) {
			                	$erreur = true;
			                	$total.addClass('form-error').removeClass('form-error', $speed).focus();
			                }

			                if ($erreur)
			                	event.preventDefault();  

			                else {
			                	$('#form-quota-total').prop('disabled', false);
			                	$('#form-quota-sportif').prop('disabled', false);
			                	$('#form-quota-nonsportif').prop('disabled', false);
			                	$('#form-quota-logement').prop('disabled', false);
								$('#form-quota-filles').prop('disabled', false);
								$('#form-quota-garcons').prop('disabled', false);
								$('#form-quota-pompom').prop('disabled', false);
								$('#form-quota-cameraman').prop('disabled', false);
								$('#form-quota-fanfaron').prop('disabled', false);
								$('#form-quota-pompom-nonsportif').prop('disabled', false);
								$('#form-quota-cameraman-nonsportif').prop('disabled', false);
								$('#form-quota-fanfaron-nonsportif').prop('disabled', false);
			                }
			           
			            }
			        };


			    	$analysisSport = function(elem, event, force) {
			            if (event.keyCode == 13 || force) {
			              	$parent = elem.children('fieldset');
			              	$first = $parent.children('label').first();
			  				$sport = $first.children('select');
			  				$equipes = $first.next().children('input');
			  				$quota = $first.next().next().children('input');
			  				$reserves = $first.next().next().next().children('input');
			  				$erreur = false;
			  				
			                if (!$.isNumeric($sport.val()) ||
			                	$sport.val() <= 0 ||
			                	Math.floor($sport.val()) != $sport.val()) {
			                	$erreur = true;
			                	$sport.addClass('form-error').removeClass('form-error', $speed).focus();
			                }

			                if (!$.isNumeric($quota.val()) ||
			                	$quota.val() <= 0 ||
			                	Math.floor($quota.val()) != $quota.val()) {
			                	$erreur = true;
			                	$quota.addClass('form-error').removeClass('form-error', $speed).focus();
			                }

			                if (!$.isNumeric($reserves.val()) ||
			                	$reserves.val() < 0 ||
			                	Math.floor($reserves.val()) != $reserves.val()) {
			                	$erreur = true;
			                	$reserves.addClass('form-error').removeClass('form-error', $speed).focus();
			                }

			                if (!$.isNumeric($equipes.val()) ||
			                	$equipes.val() <= 0 ||
			                	Math.floor($equipes.val()) != $equipes.val()) {
			                	$erreur = true;
			                	$equipes.addClass('form-error').removeClass('form-error', $speed).focus();
			                }

			                if ($erreur)
			                	event.preventDefault();  
			           
			            }
			        };

			        $analysisPaiement = function(elem, event, force) {
			            if (event.keyCode == 13 || force) {
			              	$parent = elem.children('fieldset');
			              	$first = $parent.children('label').first();
			  				$montant = $first.children('input');
			  				$erreur = false;
			  				
			                if (!$.isNumeric($montant.val())) {
			                	$erreur = true;
			                	$montant.addClass('form-error').removeClass('form-error', $speed).focus();
			                }

			                if ($erreur)
			                	event.preventDefault();   
			           
			            }
			        };

			        $analysisEtat = function(elem, event, force) {
			            if (event.keyCode == 13 || force) {
			              	$parent = elem.children('fieldset').children('div').first();
			              	$first = $parent.children('label').first();
			  				$etat = $first.children('select');
			  				$respo = $first.next().next().next().next().children('select');
			  				$malus = $first.next().next().next().next().next().children('input');
			  				$erreur = false;
			  				
			                if ($.inArray($etat.val(), ['fermee', 'ouverte', 'limitee', 'close', 'validee']) < 0) {
			                	$erreur = true;
			                	$etat.addClass('form-error').removeClass('form-error', $speed).focus();
			                }

			                if ($etat.val() != 'fermee' && (
				                	!$.isNumeric($respo.val()) ||
				                	$respo.val() < 0 ||
				                	Math.floor($respo.val()) != $respo.val())) {
			                	$erreur = true;
			                	$respo.addClass('form-error').removeClass('form-error', $speed).focus();
			                }

			                if ($malus.val().trim() != '' && (
			                		!$.isNumeric($malus.val()) ||
			                		$malus.val() < 0)) {
			                	$erreur = true;
			                	$malus.addClass('form-error').removeClass('form-error', $speed).focus();
			                }

			                if ($erreur)
			                	event.preventDefault();  
			           
			            }
			        };

			        $analysisQuotasSports = function(elem, event, force) {
			            if (event.keyCode == 13 || force) {
			              	$parents = $('fieldset:first table tbody tr', elem);
			              	$erreur = false;

			              	$parents.each(function(index) {
			              		$first = $(this).children('td').first();
			              		if ($first.attr('colspan') > 1)
			              			return false;
			              		$equipes = $first.next().children('input');
			              		$quota = $first.next().next().children('input');
			              		$nbequipes = parseInt($first.next().next().next().text());
			              		$inscriptions = parseInt($first.next().next().next().next().text());

			              		if (!$.isNumeric($quota.val()) ||
				                	$quota.val() < 0 ||
				                	$quota.val() < parseInt($inscriptions) && $quota.val() > 0 ||
				                	$quota.val() > parseInt($quota.attr('max')) ||
				                	$quota.val() == 0 && $inscriptions > 0) {
				                	$erreur = true;
				                	$quota.addClass('form-error').removeClass('form-error', $speed).focus();
				                }

				                if (!$.isNumeric($equipes.val()) ||
				                	$equipes.val() < 1 ||
				                	$equipes.val() < parseInt($nbequipes) && $equipes.val() > 0) {
				                	$erreur = true;
				                	$equipes.addClass('form-error').removeClass('form-error', $speed).focus();
				                }
			              	});

			                if ($erreur)
			                	event.preventDefault();  
			           
			            }
			        };

			        $('#form-utilisateur-autocomplete').autocomplete({
						source: function( request, response ) {
							$.ajax({
								url: "<?php url('admin/module/ecoles/'.$ecole['id'].'?p=ajax'); ?>",
							  	method: "POST",
							  	cache: false,
								dataType: "json",
								data:{filtre:request.term},
								success: function(data) {
									response(data);
									if ($.isEmptyObject(data))
										$("#form-utilisateur-autocomplete").animate({backgroundColor:'#FBB'}, 100, function() {
			                				$(this).animate({backgroundColor:'none'}, 1000); });
								}
							});
						},
				        minLength:2,
				        select: function(e, ui) {
				            e.preventDefault();
				            $("#form-utilisateur").val(ui.item.id).trigger('change');
				            $(this).val(ui.item.nom);
				            $(':focus').blur();
				            $(this).focus();
				        },
				        focus: function(e, ui) {
	            			//$("#form-utilisateur-autocomplete").val(ui.item.nom);
	            			return false;  
				    	}
			   		});

			       	$('#form-coords').bind('submit', function(event) { $analysisData($(this), event, true); });
			       	$('#form-quotas').bind('submit', function(event) { $analysisQuotas($(this), event, true); });
			        $('#form-quotas-sports').bind('submit', function(event) { $analysisQuotasSports($(this), event, true); });
					$('#modal-ajout-paiement form').bind('submit', function(event) { $analysisPaiement($(this), event, true); });
					$('#modal-ajout-sport form').bind('submit', function(event) { $analysisSport($(this), event, true); });
					$('#form-change-etat').bind('submit', function(event) { $analysisEtat($(this), event, true); });
					$('#form-ajout-paiement').bind('click', function() { $('#modal-ajout-paiement').modal(); });
					$('#form-ajout-sport').bind('click', function() { $('#modal-ajout-sport').modal(); });
					$('#form-clone-quotas-sport').bind('click', function() { $('#modal-clone-quotas-sport').modal(); });

					$('#form-quota-total-on').change(function() { $(this).next().next().prop('disabled', !$(this).is(':checked')); });
					$('#form-quota-sportif-on').change(function() { $(this).next().next().prop('disabled', !$(this).is(':checked')); });
					$('#form-quota-nonsportif-on').change(function() { $(this).next().next().prop('disabled', !$(this).is(':checked')); });
					$('#form-quota-logement-on').change(function() { $(this).next().next().prop('disabled', !$(this).is(':checked')); });
					$('#form-quota-filles-on').change(function() { $(this).next().next().prop('disabled', !$(this).is(':checked')); });
					$('#form-quota-garcons-on').change(function() { $(this).next().next().prop('disabled', !$(this).is(':checked')); });
					$('#form-quota-pompom-on').change(function() { $(this).next().next().prop('disabled', !$(this).is(':checked')); });
					$('#form-quota-cameraman-on').change(function() { $(this).next().next().prop('disabled', !$(this).is(':checked')); });
					$('#form-quota-fanfaron-on').change(function() { $(this).next().next().prop('disabled', !$(this).is(':checked')); });
					$('#form-quota-pompom-nonsportif-on').change(function() { $(this).next().next().prop('disabled', !$(this).is(':checked')); });
					$('#form-quota-cameraman-nonsportif-on').change(function() { $(this).next().next().prop('disabled', !$(this).is(':checked')); });
					$('#form-quota-fanfaron-nonsportif-on').change(function() { $(this).next().next().prop('disabled', !$(this).is(':checked')); });
					
					$('#form-quota-total').prop('disabled', !$('#form-quota-total-on').is(':checked'));
					$('#form-quota-sportif').prop('disabled', !$('#form-quota-sportif-on').is(':checked'));
					$('#form-quota-nonsportif').prop('disabled', !$('#form-quota-nonsportif-on').is(':checked'));
					$('#form-quota-logement').prop('disabled', !$('#form-quota-logement-on').is(':checked'));
					$('#form-quota-filles').prop('disabled', !$('#form-quota-filles-on').is(':checked'));
					$('#form-quota-garcons').prop('disabled', !$('#form-quota-garcons-on').is(':checked'));
					$('#form-quota-pompom').prop('disabled', !$('#form-quota-pompom-on').is(':checked'));
					$('#form-quota-cameraman').prop('disabled', !$('#form-quota-cameraman-on').is(':checked'));
					$('#form-quota-fanfaron').prop('disabled', !$('#form-quota-fanfaron-on').is(':checked'));
					$('#form-quota-pompom-nonsportif').prop('disabled', !$('#form-quota-pompom-nonsportif-on').is(':checked'));
					$('#form-quota-cameraman-nonsportif').prop('disabled', !$('#form-quota-cameraman-nonsportif-on').is(':checked'));
					$('#form-quota-fanfaron-nonsportif').prop('disabled', !$('#form-quota-fanfaron-nonsportif-on').is(':checked'));
				});
				</script>

<?php

//Inclusion du pied de page
require DIR.'templates/_footer.php';

