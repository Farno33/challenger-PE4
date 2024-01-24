<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* templates/ecoles/equipes.php ****************************/
/* Templates de la gestion des équipes d'une école *********/
/* *********************************************************/
/* Dernière modification : le 10/12/14 *********************/
/* *********************************************************/


//Inclusion de l'entête de page
require DIR.'templates/ecoles/_header_ecoles.php';

?>
			
				<h2>Liste des Sportifs</h2>

				<center>
					<form method="get">
						<fieldset>
							<select name="sport" style="width:300px" onchange="$(this).parent().parent().submit();">
								<option value="" <?php if (!isset($_GET['sport']) || !in_array($_GET['sport'], array_keys($ecoles_sports)))
									echo 'selected'; ?>>Tous les sports</option>

								<?php foreach ($ecoles_sports as $sid => $equipes) { ?>

								<option value="<?php echo $sid; ?>" <?php if (!empty($_GET['sport']) && $_GET['sport'] == $sid) 
									echo ' selected'; ?>><?php echo stripslashes($equipes[array_keys($equipes)[0]]['sport']).' '.
										strip_tags(printSexe($equipes[array_keys($equipes)[0]]['sexe'])); ?></option>

								<?php } ?>

							</select>
							<input type="submit" class="success" value="Afficher" style="margin-bottom:0px !important" />
						</fieldset>
					</form>
				</center>


				<?php 

				$nbMaxGroupes = 0;
				foreach ($groupes as $groupe => $sids) {
					if ($groupe > 0 && count($sids) > $nbMaxGroupes)
						$nbMaxGroupes = count($sids);
				}

				if ($nbMaxGroupes > 1) { ?>

				<div class="alerte alerte-info">
					<div class="alerte-contenu">
						Un sportif ne peut intégrer qu'un seul sport sauf pour les groupes de sports suivants : 
						<ul style="margin-left:20px"> 
						<?php 
						foreach ($groupes as $groupe => $sids) {
							if ($groupe == 0 ||
								count($sids) <= 1)
								continue;
						
							$i = 0;
							echo '<li>';
							foreach ($sids as $sid) { $i++;
								if ($i == count($sids)) echo ' et ';
								else if($i > 1) echo ', ';
								echo '<b>'.$sports_groupes[$sid]['sport'].' '.printSexe($sports_groupes[$sid]['sexe']).'</b>';
							}
							echo '</li>'; 
						}
						?>
						</ul>
					</div>
				</div><br />

				<?php } ?>


				<?php if (!empty($_SESSION['user']['privileges']) &&
					in_array('ecoles', $_SESSION['user']['privileges']) && 
					!in_array($ecole['etat_inscription'], ['ouverte', 'limitee'])) { ?>

				<div class="alerte alerte-attention">
					<div class="alerte-contenu">
						Vous avez les droits d'accès au module "Ecoles" de l'administration, 
						et à ce titre vous avez accès à cette page malgré l'état non ouvert de l'inscription de l'école.
					</div>
				</div><br />

				<?php } 

				foreach ($ecoles_sports as $sid => $equipes) {
					$data = $equipes[array_keys($equipes)[0]];			
					$nb_equipes = empty($data['eid']) ? 0 : count($equipes);
				
					if (!empty($_GET['sport']) &&
						$_GET['sport'] != $sid)
						continue;
				?>


				<a name="s<?php echo $sid; ?>"></a>
				<fieldset class="fieldset-table">
					<form method="post" action="#s<?php echo $sid; ?>">
						<input type="hidden" name="sport" value="<?php echo $sid; ?>" />
						<h3>
							<?php echo ($data['special'] ? '<small>Sport Spécial : </small>' : '').stripslashes($data['sport']).' '.printSexe($data['sexe']); ?>
							<?php if ($nb_equipes == 1 && $nb_equipes >= $data['quota_equipes']) { ?>

							<button type="submit" name="listing" value="<?php echo stripslashes($data['eid']); ?>" />
								<img src="<?php url('assets/images/actions/list.png'); ?>" alt="Listing" />
							</button>
						
							<button type="submit" name="delete" value="<?php echo stripslashes($data['eid']); ?>" />
								<img src="<?php url('assets/images/actions/delete.png'); ?>" alt="Delete" />
							</button>

							<?php } ?>
						</h3>


						<?php if (!empty($remove) && $remove == $sid) { ?>

						<div class="alerte alerte-success alerte-small">
							<div class="alerte-contenu">
								L'équipe a bien été supprimée
							</div>
						</div>

						<?php } else if (!empty($new) && $new == $sid) { ?>

						<div class="alerte alerte-success alerte-small">
							<div class="alerte-contenu">
								La nouvelle équipe a bien été créée
							</div>
						</div>

						<?php } else if (!empty($_POST['add']) && !empty($_POST['sport']) &&
							$_POST['sport'] == $sid && empty($_POST['equipe'])) {  ?>

						<div class="alerte alerte-erreur alerte-small">
							<div class="alerte-contenu">
								Une erreur s'est produite lors de l'ajout de l'équipe
							</div>
						</div>

						<?php } ?>
						
						<?php if ($nb_equipes < $data['quota_equipes'] || $nb_equipes > 1) { ?> 

						<table class="table-small">
							<thead>

								<?php if ($nb_equipes < $data['quota_equipes']) { ?>

								<tr class="form">
									<td class="content" style="width:60px"><center><small><?php echo 
											$data['nb_sportifs'].' / '.$data['quota_max']; ?></small></center></td>
									
									<?php if ($data['quota_max'] <= $data['nb_sportifs']) { ?>

									<td class="content" colspan="4"><small>Le quota max pour ce sport (<b><?php echo $data['quota_max']; ?></b>) a été atteint</small></td>

									<?php } else if (!empty($data['quota_inscription']) && 
										$data['quota_inscription'] <= $data['nb_inscriptions']) { ?>

									<td class="content" colspan="4"><small>Le quota d'inscriptions pour ce sport (<b><?php echo $data['quota_inscription']; ?></b>) a été atteint</small></td>

									<?php } else { ?>

									<?php if ($data['quota_equipes'] > 1) { ?>

									<td style="width:120px">
										<input type="text" name="label" value="" placeholder="Libellé..." />
									</td>

									<?php } ?>

									<td colspan="2">
										<select name="capitaine">
											<option value="" selected disabled>Capitaine...</option>

											<?php 

											foreach ($sportifs as $pid => $sportif) { 
												$sportif_sports = explode(',', empty($sportif['id_sports']) ? "" : $sportif['id_sports']);
												$sportif_sports = array_unique(array_filter($sportif_sports));

												if (empty($sportif['telephone']) ||
													empty($data['groupe_multiple']) &&
													!empty($sportif_sports) ||
													$data['sexe'] != 'm' &&
													$data['sexe'] != $sportif['sexe'] ||
													!empty($data['special']) && 
													$sid != $sportif['id_sport_special'] ||
													in_array($sid, $sportif_sports))
													continue;

												$affiche = true;
												foreach ($sportif_sports as $sportif_sport) {
													if (!empty($data['groupe_multiple']) &&
														!in_array($sportif_sport, $groupes[$data['groupe_multiple']])) {
														$affiche = false;
														break;
													}
												}

												if (!$affiche)
													continue;

											?>

											<option value="<?php echo $pid; ?>"><?php echo stripslashes($sportif['nom'].' '.$sportif['prenom']); ?></option>

											<?php } ?>

										</select>
									</td>

									<td class="actions">
										<button type="submit" name="add" value="<?php echo $sid; ?>">
											<img src="<?php url('assets/images/actions/team.png'); ?>" alt="New" />
										</button>
									</td>

									<?php } ?>

								</tr>

								<?php } ?>

								<tr>
									<th style="width:60px"><small><?php echo '<b>'.$nb_equipes.'</b> / '.$data['quota_equipes']; ?></small></th>

									<?php if ($data['quota_equipes'] > 1) { ?>

									<th>Équipe</th>

									<?php }  ?>

									<th>Capitaine</th>
									<th>Téléphone</th>
									<th class="actions">Actions</th>
								</tr>
							</thead>

							<tbody>

								<?php 

								if ($nb_equipes) {
									foreach ($equipes as $equipe) {

								?>

								<tr class="form">

									<?php if ($data['quota_equipes'] > 1) { ?>
									
									<td class="content" colspan="2"><?php echo stripslashes($equipe['label']); ?></td>
									
									<?php } ?>

									<td class="content"<?php echo $data['quota_equipes'] <= 1 ? ' colspan="2"' : ''; ?>><?php echo !empty($sportifs_equipes[$equipe['eid']][$equipe['id_capitaine']]) ? 
										stripslashes($sportifs_equipes[$equipe['eid']][$equipe['id_capitaine']]['nom'].' '.$sportifs_equipes[$equipe['eid']][$equipe['id_capitaine']]['prenom']) : ''; ?></td>

									<td class="content"><?php echo !empty($sportifs_equipes[$equipe['eid']][$equipe['id_capitaine']]) ? 
										stripslashes($sportifs_equipes[$equipe['eid']][$equipe['id_capitaine']]['telephone']) : ''; ?></td>

									<td class="actions">
										<button type="submit" name="listing" value="<?php echo stripslashes($equipe['eid']); ?>" />
											<img src="<?php url('assets/images/actions/list.png'); ?>" alt="Listing" />
										</button>
									
										<button type="submit" name="delete" value="<?php echo stripslashes($equipe['eid']); ?>" />
											<img src="<?php url('assets/images/actions/delete.png'); ?>" alt="Delete" />
										</button>
									</td>
								</tr>

								<?php } } else { ?>

								<tr class="vide">
									<td colspan="<?php echo $data['quota_equipes'] > 1 ? '5' : '4'; ?>">Aucune équipe</td>
								</tr>

								<?php } ?>

							</tbody>
						</table>

						<?php } ?>

					</form>


					<?php 

					if ($nb_equipes) { 
						foreach ($equipes as $equipe) { 

					?>

					<a name="e<?php echo $equipe['eid']; ?>"></a>
					
					<?php if ($data['quota_equipes'] > 1) { ?>

					<h4>Équipe : <b><?php echo stripslashes($equipe['label']); ?></b></h4>
					
					<?php } ?>


					<?php if (!empty($delete) && $delete == $equipe['eid']) { ?>

					<div class="alerte alerte-success alerte-small">
						<div class="alerte-contenu">
							Le sportif a bien été supprimé de l'équipe
						</div>
					</div>

					<?php } else if (!empty($add) && $add == $equipe['eid']) { ?>

					<div class="alerte alerte-success alerte-small">
						<div class="alerte-contenu">
							Le sportif a bien été ajouté à l'équipe
						</div>
					</div>

					<?php } else if (!empty($_POST['add']) && !empty($_POST['sport']) &&
						!empty($_POST['equipe']) && $_POST['equipe'] == $equipe['eid'] && $_POST['sport'] == $sid) {  ?>

					<div class="alerte alerte-erreur alerte-small">
						<div class="alerte-contenu">
							Une erreur s'est produite lors de l'ajout du sportif
						</div>
					</div>

					<?php } ?>


					<?php if (!empty($captain) && $captain == $equipe['eid']) { ?>

					<div class="alerte alerte-success alerte-small">
						<div class="alerte-contenu">
							Le capitaine de l'équipe a bien été modifié
						</div>
					</div>
					
					<?php } else if (!empty($_POST['captain']) && !empty($_POST['sport']) &&
						!empty($_POST['equipe']) && $_POST['equipe'] == $equipe['eid'] && $_POST['sport'] == $sid) {  ?>

					<div class="alerte alerte-erreur alerte-small">
						<div class="alerte-contenu">
							Une erreur s'est produite lors du changement de capitaine
						</div>
					</div>

					<?php } ?>

					<form method="post" action="#e<?php echo $equipe['eid']; ?>">
						<input type="hidden" name="equipe" value="<?php echo $equipe['eid']; ?>" />
						<input type="hidden" name="sport" value="<?php echo $sid; ?>" />
						<table class="table-small">
							<thead>
								<tr class="form">
									<td class="content"><center><small><?php echo 
											($nb_equipes > 1 ? '<b>'.(empty($sportifs_equipes[$equipe['eid']]) ? 0 : count($sportifs_equipes[$equipe['eid']])).'</b> de ' : '').
											'<b>'.$equipe['nb_sportifs'].'</b> / '.$equipe['quota_max']; ?></small></center></td>
									

									<?php if ($data['quota_max'] <= $data['nb_sportifs']) { ?>

									<td class="content" colspan="3"><small>Le quota max pour ce sport (<b><?php echo $data['quota_max']; ?></b>) a été atteint</small></td>

									<?php } else if (!empty($data['quota_inscription']) && 
										$data['quota_inscription'] <= $data['nb_inscriptions']) { ?>

									<td class="content" colspan="3"><small>Le quota d'inscriptions pour ce sport (<b><?php echo $data['quota_inscription']; ?></b>) a été atteint</small></td>

									<?php } else { ?>

									<td colspan="2">
										<select name="sportif">
											<option value="" selected disabled>Sportif...</option>

											<?php 

											foreach ($sportifs as $pid => $sportif) { 
												$sportif_sports = explode(',', $sportif['id_sports']);
												$sportif_sports = array_unique(array_filter($sportif_sports));

												if (empty($data['groupe_multiple']) &&
													!empty($sportif_sports) ||
													$data['sexe'] != 'm' &&
													$data['sexe'] != $sportif['sexe'] ||
													!empty($data['special']) && 
													$sid != $sportif['id_sport_special'] ||
													in_array($sid, $sportif_sports))
													continue;

												$affiche = true;
												foreach ($sportif_sports as $sportif_sport) {
													if (!empty($data['groupe_multiple']) &&
														!in_array($sportif_sport, $groupes[$data['groupe_multiple']])) {
														$affiche = false;
														break;
													}
												}

												if (!$affiche)
													continue;

											?>

											<option value="<?php echo $pid; ?>"><?php echo stripslashes($sportif['nom'].' '.$sportif['prenom']); ?></option>

											<?php } ?>

										</select>
									</td>

									<td class="actions">
										<button type="submit" name="add" value="<?php echo $equipe['eid']; ?>">
											<img src="<?php url('assets/images/actions/add.png'); ?>" alt="Add" />
										</button>
									</td>

									<?php } ?>

								</tr>

								<tr>
									<th style="width:60px"><small>Capitaine</small></th>
									<th>Sportif</th>
									<th style="width:200px">Licence</th>
									<th class="actions">Actions</th>
								</tr>
							</thead>

							<tbody>

								<?php if (empty($sportifs_equipes[$equipe['eid']])) { ?>

								<tr class="vide">
									<td colspan="4">Aucun sportif</td>
								</tr>

								<?php } else { foreach ($sportifs_equipes[$equipe['eid']] as $pid => $sportif) { ?>

								<tr class="form">
									<td>

										<?php if ($pid == $equipe['id_capitaine']) { ?>

										<input type="checkbox" checked />
										<label class="capitaine"></label>

										<?php } else if (!empty($sportif['telephone'])) { ?>

										<center>
											<button type="submit" name="captain" value="<?php echo $pid; ?>" />
												<img src="<?php url('assets/images/actions/captain.png'); ?>" alt="Capitaine" />
											</button>
										</center>

										<?php } ?>

									</td>
									<td class="content"><?php echo stripslashes($sportif['nom'].' '.$sportif['prenom']); ?></td>
									<td class="content"><?php echo stripslashes($sportif['licence']); ?></td>
									<td class="content">
										
										<button type="submit" name="listing" value="<?php echo $pid; ?>" />
											<img src="<?php url('assets/images/actions/list.png'); ?>" alt="Listing" />
										</button>

										<?php if ($pid != $equipe['id_capitaine']) { ?>

										<button type="submit" name="delete" value="<?php echo $pid; ?>" />
											<img src="<?php url('assets/images/actions/delete.png'); ?>" alt="Delete" />
										</button>

										<?php } ?>

									</td>
								</tr>

								<?php } }  ?>

							</tbody>
						</table>
					</form>


					<?php } } ?>

				</fieldset>

				<?php } ?>

<?php

//Inclusion du pied de page
require DIR.'templates/_footer.php';
