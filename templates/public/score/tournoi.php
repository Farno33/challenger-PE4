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
require DIR.'templates/_header.php';

$cql = 'T|ph:phases:
TL|phs:phases:
C|UP|ph.id_phase_suivante:phs.id|OR|phs.id:null|DOWN
F|ph.*|phs.nom
C|ph.id_sport:'.$sport['id'];

$cql_concurrents = 'T|s:sports:|c:concurrents:
TL|sp:sportifs:|p:participants:|eq:equipes:c|es:ecoles_sports:eq|e:ecoles:#
C|UP|e.id:p.id_ecole|OR|e.id:es.id_ecole|DOWN
F|e.nom|eq.label|p.prenom|p.nom
C|s.id:'.$sport['id'];

?>
				<?php 

				if (empty($_GET['p']) || $_GET['p'] == 'phases') { 

				$labels_groupes = [
					'initiales' => 'Phases initiales',
					'intermediaires' => 'Phases intermédiaires',
					'finales' => 'Phases finales'];

				foreach ($phases as $groupe => $phases_groupe) {
					if (empty($phases_groupe) && $groupe != 'initiales')
						continue; 
				 ?>


				<h3><?php echo $labels_groupes[$groupe]; ?></h3>

				<table class="table-small">
					<thead>
						<tr>
							<th>Nom</th>
							<th>Type</th>
							<th style="width:50px"><small>Cloturee</small></th>
							<th>Suivante</th>
							<th style="width:120px"><small>Pts. G / N / D / F</small></th>
							<th style="width:50px"><small>Groupes</small></th>
							<th style="width:50px"><small>Qualifiés</small></th>
						</tr>
					</thead>

					<tbody>

						<?php if (empty($phases_groupe)) { ?>

						<tr class="vide">
							<td colspan="7">Aucune phase</td>
						</tr>

						<?php } foreach ($phases_groupe as $phase) { 
							$isChampOrPoules = in_array($phase['type'], $typesPhasesPoints);
							$isPoulesOrSeries = in_array($phase['type'], ['poules', 'series']);

							?>

						<tr class="form clickme" onclick="window.location.href = '<?php url('score/tournois/'.($sport['tournoi_initie'] ? 'phase_'.$phase['id'] : $id_sport.'?edit='.$phase['id'])); ?>';">
							<td class="content"><?php echo $phase['nom']; ?>  (<?php echo $phase['id']; ?>)</td>
							<td class="content"><center><?php echo printEtatTournoi($phase['type']); ?></center></td>
							<td>
								<input type="checkbox" <?php echo $phase['cloturee'] ? 'checked ' : ''; ?>/>
								<label></label>
							</td>
							<td class="content"><?php if (!empty($phase['id_phase_suivante'])) echo $phase['nom_suivante']; ?></td>
							<td class="content"><center><b><?php echo $isChampOrPoules ? 
								$phase['points_victoire'] . ' <span style="color:#999">/</span> '.
								$phase['points_nul'] . ' <span style="color:#999">/</span> '.
								$phase['points_defaite'] . ' <span style="color:#999">/</span> '.
								$phase['points_forfait'] : '-'; ?></b></center></td>
							<td class="content"><center><b><?php echo $isPoulesOrSeries ? $phase['nb_groupes'] : '-'; ?></b></center></td>
							<td class="content"><center><b><?php echo $isChampOrPoules || $isPoulesOrSeries ? $phase['nb_qualifies'] : '-'; ?></b></center></td>
						</tr>

						<?php } ?>

					</tbody>
				</table>

				<?php }


				if (!$sport['tournoi_initie']) { ?>

				
					<div class="alerte alerte-attention" style="width:800px">
						<div class="alerte-contenu">
							L'ajout et la suppression de phases ne peut se faire après que la tournoi ait été initié. <br />
							L'édition de certains paramètres restera pour autant possible.<br />
							Pour initier un tournoi, il faut au moins 1 concurrent :)<br />
							<br />
							<form method="post">
								<center>
								<input id="form-add" class="success" type="submit" name="add" value="Ajouter une phase" />
								<?php if ($sport['nb_concurrents'] > 0) { ?><br /><input type="submit" name="init" value="Initier le tournoi" /><?php } ?>
								</center>
							</form>
						</div>
					</div>						
				</form>

				<div id="modal-ajout" class="modal big-modal">
					<form method="post">
						<fieldset>
							<legend>Ajout d'une phase</legend>
							<small>Commencez par les phases finales pour remonter aux phases initiales.</small>
							<label for="form-null" class="needed">
								<span>Type / Nom</span>
								<select class="two_input" name="type" id="form-type">
									<?php foreach ($typesPhases as $type) { ?>
									<option value="<?php echo $type; ?>"><?php echo strip_tags(printEtatTournoi($type)); ?></option>
									<?php } ?>
								</select>

								<input class="two_input" type="text" name="nom" id="form-nom" value="" />
							</label>

							<label for="form-next">
								<span>Phase suivante</span>
								<select id="form-next" name="next">
									<option value=""></option>
									<?php foreach ($phases_ as $phase) { ?>
									<option value="<?php echo $phase['id']; ?>"><?php echo stripslashes($phase['nom']); ?></option>
									<?php } ?>
								</select>
							</label>

							<label for="form-null" class="needed">
								<span>Points G/N/P/F</span>
								<input name="pts_g" class="four_input" type="number" step="1" value="3" />
								<input name="pts_n" class="four_input" type="number" step="1" value="1" />
								<input name="pts_p" class="four_input" type="number" step="1" value="0" />
								<input name="pts_f" class="four_input" type="number" step="1" value="-1" />
								<small>Uniquement pour les poules et les championnats</small>
							</label>

							<center>
								<input type="submit" class="success" value="Ajouter la phase" name="add_phase" />
							</center>
						</fieldset>
					</form>
				</div>

				<?php 
				if (!empty($_GET['edit']) && in_array($_GET['edit'], array_keys($phases_))) { 
					$select = $phases_[$_GET['edit']]; 
				?>
				<div id="modal-edit" class="modal big-modal">
					<form method="post" action="?p=phases">
						<fieldset>
							<legend>Edition d'une phase</legend>
							<small>Commencez par les phases finales pour remonter aux phases initiales.</small>
							<label for="form-null" class="needed">
								<span>Type / Nom</span>
								<select class="two_input" name="type" id="form-type">
									<?php foreach ($typesPhases as $type) { ?>
									<option value="<?php echo $type; ?>"<?php if ($type == $select['type']) echo ' selected'; ?>><?php echo strip_tags(printEtatTournoi($type)); ?></option>
									<?php } ?>
								</select>

								<input class="two_input" type="text" name="nom" id="form-nom" value="<?php echo stripslashes($select['nom']); ?>" />
							</label>

							<label for="form-next">
								<span>Phase suivante</span>
								<select id="form-next" name="next">
									<option value=""></option>
									<?php foreach ($phases_ as $phase) { ?>
									<option value="<?php echo $phase['id']; ?>"<?php if ($select['id_phase_suivante'] == $phase['id']) echo ' selected'; ?>><?php echo stripslashes($phase['nom']); ?></option>
									<?php } ?>
								</select>
							</label>

							<label for="form-null" class="needed">
								<span>Points G/N/P/F</span>
								<input name="pts_g" class="four_input" type="number" step="1" value="<?php echo $select['points_victoire']; ?>" />
								<input name="pts_n" class="four_input" type="number" step="1" value="<?php echo $select['points_nul']; ?>" />
								<input name="pts_p" class="four_input" type="number" step="1" value="<?php echo $select['points_defaite']; ?>" />
								<input name="pts_f" class="four_input" type="number" step="1" value="<?php echo $select['points_forfait']; ?>" />
								<small>Uniquement pour les poules et les championnats</small>
							</label>

							<center>
								<input type="hidden" name="id" value="<?php echo $_GET['edit']; ?>" />
								<input type="submit" class="success" value="Editer la phase" name="edit_phase" />
								<?php if ($select['nb_precedentes'] == 0) { ?>
								<br /><input type="submit" class="delete" value="Supprimer la phase" name="del_phase" />
								<?php } ?>
							</center>
						</fieldset>
					</form>
				</div>

				<script type="text/javascript">
				$('#modal-edit').modal();
				$('#simplemodal-container').css('height', '370px');
				</script>

				<?php } ?>

				<script type="text/javascript">
				$('#form-add').on('click', function(e) {
					e.preventDefault();
					$('#modal-ajout').modal();
					$('#simplemodal-container').css('height', '370px');
				});
				</script>

				<?php }

				} else if ($_GET['p'] == 'podium') { ?>

				<form method="post" class="form-table" >
					<div>
						<fieldset>
							<h3>Définition du podium</h3>

							<label for="form-coeff">
								<span>Coefficient</span>
								<input type="number" class="two_input" name="coeff" id="form-coeff" value="<?php echo $sport['coeff']; ?>" />
								<small>Il y a jusqu'à présent <b><?php echo $sport['nb_concurrents']; ?></b> concurrents</small>
							</label>

							<label for="form-null">
								<span>1er / Ex-aequo</span>
								<select name="id_concurrent1" id="form-concurrent1" class="two_input">
									<option value=""></option>

									<?php foreach ($concurrents as $cid => $concurrent) { if (empty($concurrent['coid'])) continue; ?>

									<option value="<?php echo $cid; ?>"<?php 
										if ($sport['id_concurrent1'] == $concurrent['coid']) echo ' selected'; ?>><?php echo 
											stripslashes($sport['individuel'] ? $concurrent['prenom'].' '.$concurrent['nom'].' / '.$concurrent['enom'] :
												$concurrent['enom'].' / '.$concurrent['label']); ?></option>

									<?php } ?>

								</select>
								<input type="checkbox" name="ex_12" id="form-ex-12" <?php if ($sport['ex_12']) echo 'checked '; ?> />
								<label class="two_input" for="form-ex-12"></label>
							</label>

							<label for="form-null">
								<span>2e / Ex-aequo</span>
								<select name="id_concurrent2" id="form-concurrent2" class="two_input">
									<option value=""></option>

									<?php foreach ($concurrents as $cid => $concurrent) { if (empty($concurrent['coid'])) continue; ?>

									<option value="<?php echo $cid; ?>"<?php 
										if ($sport['id_concurrent2'] == $concurrent['coid']) echo ' selected'; ?>><?php echo 
											stripslashes($sport['individuel'] ? $concurrent['prenom'].' '.$concurrent['nom'].' / '.$concurrent['enom'] :
												$concurrent['enom'].' / '.$concurrent['label']); ?></option>

									<?php } ?>

								</select>
								<input type="checkbox" name="ex_23" id="form-ex-23" <?php if ($sport['ex_23']) echo 'checked '; ?> />
								<label class="two_input" for="form-ex-23"></label>
							</label>

							<label for="form-null">
								<span>3e / Ex-aequo</span>
								<select name="id_concurrent3" id="form-concurrent3" class="two_input">
									<option value=""></option>

									<?php foreach ($concurrents as $cid => $concurrent) { if (empty($concurrent['coid'])) continue; ?>

									<option value="<?php echo $cid; ?>"<?php 
										if ($sport['id_concurrent3'] == $concurrent['coid']) echo ' selected'; ?>><?php echo 
											stripslashes($sport['individuel'] ? $concurrent['prenom'].' '.$concurrent['nom'].' / '.$concurrent['enom'] :
												$concurrent['enom'].' / '.$concurrent['label']); ?></option>

									<?php } ?>

								</select>
								<input type="checkbox" name="ex_3" id="form-ex-3" <?php if ($sport['ex_3']) echo 'checked '; ?> />
								<label class="two_input" for="form-ex-3"></label>
							</label>

							<label for="form-null">
								<span>3e ex</span>
								<select name="id_concurrent3ex" id="form-concurrent3ex" class="two_input">
									<option value=""></option>

									<?php foreach ($concurrents as $cid => $concurrent) { if (empty($concurrent['coid'])) continue; ?>

									<option value="<?php echo $cid; ?>"<?php 
										if ($sport['id_concurrent3ex'] == $concurrent['coid']) echo ' selected'; ?>><?php echo 
											stripslashes($sport['individuel'] ? $concurrent['prenom'].' '.$concurrent['nom'].' / '.$concurrent['enom'] :
												$concurrent['enom'].' / '.$concurrent['label']); ?></option>

									<?php } ?>

								</select>
							</label>

							<center>
								<input type="submit" class="success" name="podium" value="Enregistrer les modifications" />
							</center>
						</fieldset>
					</div>
				</form>



				<?php } else if ($_GET['p'] == 'concurrents') { ?>

				<table class="table-small">
					<thead>
						<tr>
							<th>Ecole</th>
							<?php if ($sport['individuel']) { ?>
							<th>Nom</th>
							<th>Prénom</th>
							<th style="width:60px">Sexe</th>
							<?php } else { ?>
							<th>Equipe</th>
							<?php } ?>
							<th style="width:60px">Concurrent</th>
						</tr>
					</thead>

					<tbody>
						<?php foreach ($concurrents as $cid => $concurrent) { ?>
						<tr class="form">
							<td><div><?php echo stripslashes($concurrent['enom']); ?></div></td>
							<?php if ($sport['individuel']) { ?>
							<td><div><?php echo stripslashes($concurrent['nom']); ?></div></td>
							<td><div><?php echo stripslashes($concurrent['prenom']); ?></div></td>
							<td>
								<input type="checkbox" <?php if ($concurrent['sexe'] == 'h') echo 'checked '; ?>/>
								<label class="sexe"></label>
							</td>
							<?php } else { ?>
							<td><div><?php echo stripslashes($concurrent['label']); ?></div></td>
							<?php } ?>
							<td>
								<?php if (!$sport['tournoi_initie']) { ?>
								<input name id="concurrent-<?php echo $cid; ?>" data-id="<?php echo $cid; ?>" type="checkbox" <?php if (!empty($concurrent['coid'])) echo 'checked '; ?>/>
								<label for="concurrent-<?php echo $cid; ?>"></label>
								<?php } else { ?>
								<input disabled type="checkbox" <?php if (!empty($concurrent['coid'])) echo 'checked '; ?>/>
								<label></label>
								<?php } ?>
							</td>
						</tr>
						<?php } if (empty($concurrents)) { ?>
						<tr class="vide">
							<td colspan="<?php echo $sport['individuel'] ? 5 : 3; ?>">Aucun concurrent</td>
						<?php } ?>
					</tbody>
				</table>

				<div class="alerte alerte-info">
					<div class="alerte-contenu">
						La modification des concurrents n'est plus possible une fois le tournoi initié. <br />
						Les concurrents sélectionnés correspondent à ceux composant les phases initiales, 
						les phases suivantes sont composés des concurrents qualifiés lors des phases précédentes.
					</div>
				</div>

				<?php if (!$sport['tournoi_initie']) { ?>
				<script type="text/javascript">
				$('input[type=checkbox]').on('change', function() {
					$.ajax({
						url: "<?php url('score/tournois/'.$id_sport.'?ajax'); ?>",
						method: "POST",
						data: {
							concurrent: $(this).data('id'),
							active: $(this).is(':checked') ? 1 : 0
						}
					});
				});
				</script>

				<?php } } ?>


<?php 

//Inclusion du pied de page
require DIR.'templates/_footer.php';
