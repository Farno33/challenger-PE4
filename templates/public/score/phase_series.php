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

?>

				<nav class="subnav">
					<h2>Détails des séries<br /> 
						<?php echo stripslashes($phase['nom']).' / <a href="'.url('score/tournois/'.$phase['sid'], false, false).'">'.stripslashes($phase['sport']).' '.printSexe($phase['sexe']).'</a>'; ?></h2>

					<ul>
						<li><a href="?p=resume">Résumé</a></li>
						<li><a href="?p=details">Détails</a></li>
						<li><a href="?p=matchs">Matchs</a></li>
					</ul>
				</nav>

			<?php if (empty($_GET['p']) || $_GET['p'] == 'resume') { ?>

				Blabla...

				<?php if (!$phase['cloturee'] && $phase['nb_precedentes_ouvertes'] == 0) { ?>

				<script type="text/javascript">
				$('input[type=checkbox]').on('change', function() {
					$.ajax({
						url: "<?php url('score/tournois/phase_'.$id_phase.'?ajax'); ?>",
						method: "POST",
						data: {
							concurrent: $(this).data('id'),
							qualifie: $(this).is(':checked') ? 1 : 0
						}
					});
				});
				</script>

				<?php } ?>

			<?php } else if ($_GET['p'] == 'details') { ?>

				<form method="post" autocomplete="off" class="form-table" action="?p=details">
					<fieldset>
						<h3>Détails de la phase</h3>
						<div>
							<label for="form-null">
								<span>Type / Initiale</span>
								<div class="two_input"><?php echo printEtatTournoi($phase['type']); ?></div>
								<input type="checkbox" <?php if ($phase['nb_precedentes'] == 0) echo 'checked '; ?>/>
								<label class="two_input"></label>
							</label>

							<label for="form-null">
								<span>Phase suivante</span>
								<div><?php echo !empty($phase['id_phase_suivante']) ? stripslashes($phase['nom_suivante']) : '<i>Aucune phase suivante</i>'; ?></div>
							</label>
						</div>
					</fieldset>

					<fieldset>
						<h3>Paramètres éditables</h3>
						<div>
							<label for="form-nom" class="needed">
								<span>Nom</span>
								<input <?php echo $phase['cloturee'] ? 'disabled ' : ''; ?>type="text" name="nom" id="form-nom" value="<?php echo stripslashes($phase['nom']); ?>" />
							</label>

							<label for="form-null" class="needed">
								<span>Points G/N/P/F</span>
								<input <?php echo $phase['cloturee'] ? 'disabled ' : ''; ?>class="four_input" type="number" step="1" name="pts_g" placeholder="G" value="<?php echo $phase['points_victoire']; ?>" />
								<input <?php echo $phase['cloturee'] ? 'disabled ' : ''; ?>class="four_input" type="number" step="1" name="pts_n" placeholder="N" value="<?php echo $phase['points_nul']; ?>" />
								<input <?php echo $phase['cloturee'] ? 'disabled ' : ''; ?>class="four_input" type="number" step="1" name="pts_p" placeholder="P" value="<?php echo $phase['points_defaite']; ?>" />
								<input <?php echo $phase['cloturee'] ? 'disabled ' : ''; ?>class="four_input" type="number" step="1" name="pts_f" placeholder="F" value="<?php echo $phase['points_forfait']; ?>" />
							</label>

							<label for="form-cloturee">
								<span>Cloturée</span>
								<input <?php echo $phase['cloturee'] || $phase['nb_precedentes_ouvertes'] > 0 ? 'disabled ' : ''; ?>type="checkbox" name="cloturee" id="form-cloturee" <?php if ($phase['cloturee']) echo 'checked '; ?>/>
								<label for="form-cloturee"></label>
							</label>

							<?php if (!$phase['cloturee']) { ?>
							
							<div class="alerte alerte-info">
								<div class="alerte-contenu">
									Attention, une fois la phase cloturée, il ne sera plus possible d'éditer la phase, ses concurrents qualifiés. Il sera par ailleurs impossible de rouvrir cette phase.
									<?php if ($phase['nb_precedentes_ouvertes'] > 0) { ?><br /><br />
									Cependant une des phases précédentes est encore ouverte, vous ne pouvez donc pas cloturer cette phase.
									<?php } ?>
								</div>
							</div> 

							<center>
								<input type="submit" name="maj" value="Mettre à jour les données" class="success" />
							</center>
							<?php } ?>
						</div>
					</fieldset>
				</form>

			<?php } else if ($_GET['p'] == 'matchs') { ?>

			<table>
				<thead>
					<tr>
						<th style="width:100px">Date</th>
						<th>Site</th>
						<th style="width:15% !important">Commentaire</th>
						<th style="width:35px"></th>
						<th>Ecoles</th>
						<?php if ($phase['individuel']) { ?>
						<th>Nom</th>
						<th>Prénom</th>
						<th>Sexe</th>
						<?php } else { ?>
						<th>Equipe</th>
						<?php } ?>
						<th>Sets</th>
						<th style="width:60px">Vainqueur</th>
						<th style="width:60px">Forfait</th>
					</tr>
				</thead>

				<tbody>

					<?php foreach ($matchs as $match) { ?>

					<tr style="border-top:2px solid #000; height:50% !important" class="form<?php if ($phase['nb_precedentes_ouvertes'] == 0) { ?> clickme match-<?php echo $match['id']; ?>" data-id="<?php echo $match['id']; ?>" onclick="window.location.href = '<?php url('score/tournois/phase_'.$phase['id'].'?p=matchs&a='.$match['id_concurrent_a'].'&b='.$match['id_concurrent_b'].'#m'.$match['id']); ?>';<?php } ?>">
						<td rowspan="2" class="content"><center>
							<a name="m<?php echo $match['id']; ?>"></a>
							<?php echo !empty($match['date']) ? (new DateTime($match['date']))->format('Y/m/d<\b\r /><\b>H:i</\b>') : ''; ?></center></td>
						<td rowspan="2" class="content"><center><?php echo !empty($match['id_site']) ? $sites[$match['id_site']]['nom'] : ''; ?></center></td>
						<td rowspan="2"><textarea readonly onclick="event.stopPropagation();"><?php echo (empty($match['commentaire']) ? '' : stripslashes($match['commentaire'])); ?></textarea></td>

						<td><div><center><b>A</b></center></div></td>
						<td><div><?php echo stripslashes($concurrents[$match['id_concurrent_a']]['enom']); ?></div></td>
						<?php if ($phase['individuel']) { ?>
						<td><div><?php echo stripslashes($concurrents[$match['id_concurrent_a']]['pnom']); ?></div></td>
						<td><div><?php echo stripslashes($concurrents[$match['id_concurrent_a']]['pprenom']); ?></div></td>
						<td>
							<input type="checkbox" <?php if ($concurrents[$match['id_concurrent_a']]['psexe'] == 'h') echo 'checked '; ?>/>
							<label class="sexe"></label>
						</td>
						<?php } else { ?>
						<td><div><?php echo stripslashes($concurrents[$match['id_concurrent_a']]['label']); ?></div></td>
						<?php } ?>

						<td rowspan="2" class="transparent">
							<table style="border:none; margin:0 !important; text-align:center">
								<tr>
									<?php 
									$sets_a = empty($match['sets_a']) ? [] : json_decode(unsecure($match['sets_a']));
									$sets_b = empty($match['sets_b']) ? [] : json_decode(unsecure($match['sets_b']));
									for ($i = 0; $i < max(count($sets_a), count($sets_b)); $i++) { ?>
									<td style="padding:0px 3px !important"><?php echo isset($sets_a[$i]) ? (!isset($sets_b[$i]) || $sets_b[$i] <= $sets_a[$i] ? '<b>'.$sets_a[$i].'</b>' : $sets_a[$i]) : ''; ?></td>
									<?php } ?>
								</tr>
								<tr>
									<?php for ($i = 0; $i < max(count($sets_a), count($sets_b)); $i++) { ?>
									<td style="padding:0px 3px !important"><?php echo isset($sets_b[$i]) ? (!isset($sets_a[$i]) || $sets_a[$i] <= $sets_b[$i] ? '<b>'.$sets_b[$i].'</b>' : $sets_b[$i]) : ''; ?></td>
									<?php } ?>
								</tr>
							</table>
						</td>
						<td rowspan="2">
							<?php if (!empty($match['gagne'])) { ?>
							<input type="checkbox" checked /> 
							<label class="vainqueur-<?php echo $match['gagne']; ?>"></label>
							<?php } ?>
						</td>
						<td rowspan="2">
							<?php if (in_array($match['gagne'], ['a', 'b'])) { ?>
							<input type="checkbox" <?php echo $match['forfait'] ? 'checked ' : ''; ?>/> 
							<label></label>
							<?php } ?>
						</td>
					</tr>

					<tr style="height:49% !important" class="form<?php if ($phase['nb_precedentes_ouvertes'] == 0) { ?> clickme match-<?php echo $match['id']; ?>" data-id="<?php echo $match['id']; ?>" onclick="window.location.href = '<?php url('score/tournois/phase_'.$phase['id'].'?p=matchs&a='.$match['id_concurrent_a'].'&b='.$match['id_concurrent_b'].'#m'.$match['id']); ?>';<?php } ?>">
						<td><div><center><b>B</b></center></div></td>
						<td><div><?php echo stripslashes($concurrents[$match['id_concurrent_b']]['enom']); ?></div></td>
						<?php if ($phase['individuel']) { ?>
						<td><div><?php echo stripslashes($concurrents[$match['id_concurrent_b']]['pnom']); ?></div></td>
						<td><div><?php echo stripslashes($concurrents[$match['id_concurrent_b']]['pprenom']); ?></div></td>
						<td>
							<input type="checkbox" <?php if ($concurrents[$match['id_concurrent_b']]['psexe'] == 'h') echo 'checked '; ?>/>
							<label class="sexe"></label>
						</td>
						<?php } else { ?>
						<td><div><?php echo stripslashes($concurrents[$match['id_concurrent_b']]['label']); ?></div></td>
						<?php } ?>
					</tr>

					<?php } if (empty($matchs)) { ?>

					<tr class="vide">
						<td colspan="<?php echo $phase['individuel'] ? 11 : 9; ?>">Aucun match</td>
					</tr>

					<?php } ?>

				</tbody>
			</table>

			<script type="text/javascript">
			$('.clickme').on('mouseover', function() {
				var data = $(this).data('id');
				$('.match-' + data).addClass('clickmeforce');
			});
			$('.clickme').on('mouseout', function() {
				var data = $(this).data('id');
				$('.match-' + data).removeClass('clickmeforce');
			});
			</script>

			<?php } ?>

			<?php 
			if ($phase['nb_precedentes_ouvertes'] == 0 &&
				!empty($select)) { 
				$sets_a = !empty($select['sets_a']) ? json_decode(unsecure($select['sets_a'])) : [''];
				$sets_b = !empty($select['sets_b']) ? json_decode(unsecure($select['sets_b'])) : [''];
				$gagne = !empty($select['gagne']) ? $select['gagne'] : '';
				$datetime = !empty($select['date']) ? new DateTime($select['date']) : '';
				$date = !empty($datetime) ? $datetime->format('Y-m-d') : '';
				$time = !empty($datetime) ? $datetime->format('H:i') : '';
			?>

			

				<script type="text/javascript">
				$('#modal-match').modal({focus:false});
				$('#simplemodal-container').css({'height': 'calc(100% - 50px)', 'top': '20px'});
				$('.add-set').on('click', function() {
					$('#form-sets-a tr td:last').before('<td><input type="text" name="sets_a[]" value="" /></td>');
					$('#form-sets-b tr td:last').before('<td><input type="text" name="sets_b[]" value="" /></td>');
				});
				$('.triple input').on('change', function() { 
					var checked = $(this).is(':checked');
					$('.triple input').attr('checked', false);

					if (checked)
						$(this).prop('checked', 'checked');
				});
				</script>

				<?php } ?>

<?php 

//Inclusion du pied de page
require DIR.'templates/_footer.php';
