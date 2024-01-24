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
if (empty($vpTournois)) require DIR.'templates/admin/_header_admin.php';
else require DIR.'templates/centralien/_header_centralien.php';
?>


				<nav class="subnav">
					<h2>Détails des poules<br /> 
						<?php echo stripslashes($phase['nom']).' / <a href="'.url((empty($vpTournois) ? 'admin/module/tournois/' : 'centralien/vptournoi/').$phase['sid'], false, false).'">'.stripslashes($phase['sport']).' '.printSexe($phase['sexe']).'</a>'; ?></h2>

					<ul>
						<li>
							<a href="?p=resume">Résumé</a>
							<ul>
								<li><a href="?p=resume">% Nom</a></li>
								<li><a href="?p=resume&t=1">% Class.</a></li>
							</ul>
						</li>
						<li><a href="?p=details">Détails</a></li>
						<li><a href="?p=matchs">Matchs</a></li>
						<li><a href="?p=groupes">Groupes</a></li>
					</ul>
				</nav>
				<?php if (empty($_GET['p']) || $_GET['p'] == 'resume') {  
					if (empty($phase['nb_matchs']) &&
						count($concurrents)) { 

						$without = false;
						foreach ($concurrents as $concurrent) {
							if (empty($concurrent['id_groupe'])) {
								$without = true;
								break;
							}
						}

						if (!$without) { ?>

					<div class="alerte alerte-info">
						<div class="alerte-contenu">
							Pour valider les groupes, la répartition des concurrents et initier les matchs de cette phase de poules, il vous suffit de cliquer sur le bouton suivant. 
							Attention une fois validée il sera impossible de changer les groupes et la répartition.<br />
							<br />
							<form method="post">
							<center><input type="submit" value="Initier cette phase de poules" name="init" /></center>
							</form>
						</div>
					</div>

					<?php } else { ?>

					<div class="alerte alerte-attention">
						<div class="alerte-contenu">
							Tous les concurrents ne sont pas répartis dans des groupes. <br />
							C'est une condition nécessaire pour initier cette phase de poules. 
						</div>
					</div>

					<?php } ?>

				<?php } 

				foreach ($groupes as $gid => $groupe) { ?>

				<h3>Poule : <b><?php echo stripslashes($groupe['nom']); ?></b></h3>

				<center>
					<table style="width:auto">
					<thead>
						<tr>
							<th style="width:30px"></th>
							
							<?php 

							$i = 0;
							foreach ($groupe['concurrents'] as $id => $concurrent) { ?>

							<th style="width:30px"><?php echo ++$i; ?></th>

							<?php } ?>

							<td class="transparent"></td>
							<th>Ecole</th>
							<?php if ($phase['individuel']) { ?>
							<th>Nom</th>
							<th>Prénom</th>
							<th>Sexe</th>
							<?php } else { ?>
							<th>Equipe</th>
							<?php } ?>
							<th><small>J</small></th>
							<th><small>G</small></th>
							<th><small>N</small></th>
							<th><small>P</small></th>
							<th><small>F</small></th>
							<th><small>Pts.</small></th>
							<th><small>Class.</small></th>
							<th><small>Qualifié</small></th>
						</tr>
					</thead>

					<tbody>

						<?php 

						$i = 0;
						foreach ($groupe['concurrents'] as $id_i => $concurrent_i) {
						
						?>

						<tr>
							<th><?php echo ++$i; ?></th>

							<?php 

							$j = 0;
							foreach ($groupe['concurrents'] as $id_j => $concurrent_j) { ?>

								<?php if ($i == ++$j) { ?> 

								<td class="disabled"></td>

								<?php } else {
									$match = null;
									$label = null; 

									if (isset($concurrent_i['matchs'])) {
										foreach ($concurrent_i['matchs'] as $match_i) {
											if ($match_i['id_concurrent_a'] == $id_i && 
												$match_i['id_concurrent_b'] == $id_j || //Aller-Retour identiques
												$match_i['id_concurrent_b'] == $id_i && 
												$match_i['id_concurrent_a'] == $id_j) {
												$match = $match_i;


												break;
											}
										}
									}

									if ($match !== null) {
										if ($match['gagne'] === null) $label = '';
										else if ($match['gagne'] == 'nul') $label = 'N'; 
										else if ($match['gagne'] == 'a' && $id_i == $match['id_concurrent_a'] ||
											$match['gagne'] == 'b' && $id_i == $match['id_concurrent_b']) $label = 'G';
										else if ($match['forfait']) $label = 'F';
										else $label = 'P'; 
									}

								 ?>

								<td <?php if ($phase['nb_matchs']) { ?>class="clickme" <?php } ?>style="background:<?php echo $match == null ? '#FD9' : ($match['gagne'] == null ? '#DDF' : 'auto'); ?>" 
									<?php if ($phase['nb_matchs']) { ?>onclick="window.location.href = '<?php url((empty($vpTournois) ? 'admin/module/tournois/' : 'centralien/vptournoi/').'phase_'.$phase['id'].'?a='.$id_i.'&b='.$id_j.'&t='.(!empty($_GET['t']) ? '1' : '')); ?>';"<?php } ?>>
									<center><?php echo printEtatMatch($label); ?></center>
								</td>

								<?php } ?>

							<?php } ?>

							<td class="transparent"></td>
							<td><?php echo stripslashes($concurrent_i['enom']); ?></td>
							<?php if ($phase['individuel']) { ?>
							<td><?php echo stripslashes($concurrent_i['pnom']); ?></td>
							<td><?php echo stripslashes($concurrent_i['pprenom']); ?></td>
							<td style="padding:0px">
								<input type="checkbox" <?php if ($concurrent_i['psexe'] == 'h') echo 'checked '; ?>/>
								<label class="sexe"></label>
							</td>
							<?php } else { ?>
							<td><?php echo stripslashes($concurrent_i['label']); ?></td>
							<?php } ?>
							<td><center><?php echo empty($concurrent_i['joues']) ? '0' : stripslashes($concurrent_i['joues']); ?></center></td>
							<td><center><?php echo empty($concurrent_i['joues']) ? '-' : stripslashes($concurrent_i['gagnes']); ?></center></td>
							<td><center><?php echo empty($concurrent_i['joues']) ? '-' : stripslashes($concurrent_i['nuls']); ?></center></td>
							<td><center><?php echo empty($concurrent_i['joues']) ? '-' : stripslashes($concurrent_i['perdus']); ?></center></td>
							<td><center><?php echo empty($concurrent_i['joues']) ? '-' : stripslashes($concurrent_i['forfaits']); ?></center></td>
							<td><center><b><?php echo empty($concurrent_i['joues']) ? '-' : stripslashes($concurrent_i['points']); ?></b></center></td>
							<td><center><b><?php echo $concurrent_i['classement']; ?></b></center></td>
							<td style="padding:0px">
								<?php if (!$phase['cloturee'] && $phase['nb_precedentes_ouvertes'] == 0) { ?>
								<input data-id="<?php echo $id_i; ?>" name id="form-concurrent-<?php echo $id_i; ?>" type="checkbox" <?php if ($concurrent_i['qualifie']) echo 'checked '; ?>/>
								<label for="form-concurrent-<?php echo $id_i; ?>"></label>
								<?php } else { ?>
								<input disabled type="checkbox" <?php if ($concurrent_i['qualifie']) echo 'checked '; ?>/>
								<label></label>
								<?php } ?>
							</td>
						</tr>

						<?php } if (empty($groupe['concurrents'])) { ?>

						<tr class="vide">
							<td colspan="<?php echo ($phase['individuel'] ? 14 : 12) + count($groupe['concurrents']); ?>">Aucun concurrent</td>
						</tr>

						<?php } ?>

					</tbody>
					</table>
				</center>

					<?php if (!$phase['cloturee'] && $phase['nb_precedentes_ouvertes'] == 0) { ?>

					<script type="text/javascript">
					$('input[type=checkbox]').on('change', function() {
						$.ajax({
							url: "<?php url((empty($vpTournois) ? 'admin/module/tournois/' : 'centralien/vptournoi/').'phase_'.$id_phase.'?ajax'); ?>",
							method: "POST",
							data: {
								concurrent: $(this).data('id'),
								qualifie: $(this).is(':checked') ? 1 : 0
							}
						});
					});
					</script>

					<?php } ?>

				<?php } if (empty($groupes)) { ?>

				<div class="alerte alerte-info">
					<div class="alerte-contenu">
						Il n'y a aucune poule définie, vous pouvez les gérér et définir les compositions depuis l'onglet "<a href="?p=groupes">Groupes</a>".
					</div>
				</div>

				<?php } 

				} else if ($_GET['p'] == 'groupes') { ?>

				<?php if ($phase['nb_matchs'] == 0) { ?>

				<form method="post">

				<h3>Liste des groupes</h3>

				<table class="table-small">
					<thead>
						<tr class="form">
							<td><input type="text" name="nom[]" value="" placeholder="Nom..." /></td>
							<td class="actions">
								<button type="submit" name="add">
									<img src="<?php url('assets/images/actions/add.png'); ?>" alt="Add" />
								</button>
								<input type="hidden" name="id[]" />
							</td>
						</tr>

						<tr>
							<th>Nom</th>
							<th class="actions">Actions</th>
						</tr>
					</thead>

					<tbody>
						<?php foreach ($groupes as $gid => $groupe) { ?>

						<tr class="form">
							<td><input type="text" name="nom[]" value="<?php echo stripslashes($groupe['nom']); ?>" /></td>
							
							<td class="actions" style="text-align:left; padding-left:10px !important">
								<button type="submit" name="edit" value="<?php echo stripslashes($gid); ?>">
									<img src="<?php url('assets/images/actions/edit.png'); ?>" alt="Edit" />
								</button>									
								<?php if (!count($groupe['concurrents'])) { ?>
								<button type="submit" name="delete" value="<?php echo stripslashes($gid); ?>" />
									<img src="<?php url('assets/images/actions/delete.png'); ?>" alt="Delete" />
								</button>
								<?php } ?>
								<input type="hidden" name="id[]" value="<?php echo stripslashes($gid); ?>" />
							</td>
						</tr>

						<?php } if (empty($groupes)) { ?>
						<tr class="vide">
							<td colspan="2">Aucun groupe</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>

				</form>

				<style>
				.conseille td { background:#DDF !important;}
				</style>

				<center><small>Le pourcentage est le rapport du nombre d'équipes qualifiées sans repechages <br />
					sur le nombre d'équipes totales, ainsi plus il est proche de 100 mieux c'est !</small></center>
				<table class="table-small">
					<thead>
						<th>Poules</th>
						<th><small>Equipes / Poule</small></th>
						<th><small>Qualifiées / Poule</small></th>
						<th>Repêchées</th>
						<th>Matchs</th>
						<th><small>Matchs / Poule</small></th>
						<th>Pourcentage</th>
					</thead>

					<tbody>

						<?php foreach ($formats as $nbPoules => $format) { ?>

						<tr<?php if (2 * $format['nb_matchs'] >= $nbEquipes && $format['nb_matchs'] <= 2 * $nbEquipes && $format['pourcentage'] >= 0.5) echo ' class="conseille"'; ?>>
							<td><center><b><?php echo $nbPoules; ?></b></center></td>
							<td><center><?php echo $format['nb_equipes'].($format['nb_equipes'] * $nbPoules < $nbEquipes ? ' ('.($format['nb_equipes']+1).')' : ''); ?></center></td>
							<td><center><?php echo $format['nb_qualifiees']; ?></center></td>
							<td><center><?php echo $format['nb_repechees']; ?></center></td>
							<td><center><?php echo $format['nb_matchs']; ?></center></td>
							<td><center><?php echo ($format['nb_equipes'] * ($format['nb_equipes'] - 1) / 2).
								($format['nb_equipes'] * $nbPoules < $nbEquipes ? ' ('.(($format['nb_equipes'] + 1) * $format['nb_equipes'] / 2).')' : ''); ?></center></td>
							<td><center><?php echo round($format['pourcentage'] * 100); ?>%</center></td>
						</tr>

						<?php } ?>

					</tbody>
				</table>

				<?php } ?>

				<h3>Répartition des concurrents</h3>
				<center>
					<table style="width:auto !important">
					<thead>
						<th>Concurrent<br /><br /><small>Un seul groupe par concurrent</small></th>

						<?php foreach ($groupes as $gid => $groupe) { ?>

						<th class="vertical"><span><?php echo stripslashes($groupe['nom']); ?></span></th>

						<?php } ?>
					</thead>

					<tbody>
						<?php foreach ($concurrents as $cid => $concurrent) { ?>

						<tr class="form">
							<td class="content"><?php echo printConcurrent($cid); ?></td>

							<?php foreach ($groupes as $gid => $groupe) { ?>

							<td>
								<input type="checkbox" id="form-<?php echo $cid.'-'.$gid; ?>" <?php if ($phase['nb_matchs']) echo 'disabled '; else { ?>
									onclick="changeGroupe(this, <?php echo $cid; ?>, <?php echo $gid; ?>)" <?php }  if ($concurrent['id_groupe'] == $gid) echo 'checked '; ?>/>
								<label for="form-<?php echo $cid.'-'.$gid; ?>"></label>
							</td>

							<?php } ?>

						</tr>

						<?php } if (empty($concurrents)) { ?>

						<tr class="vide">
							<td colspan="<?php echo 1 + count($groupes); ?>">Aucun concurrent</td>
						</tr>

						<?php } ?>
					</tbody>
					</table>
				</center>

				<?php if (empty($phase['nb_matchs'])) { ?>

				<script type="text/javascript">
				changeGroupe = function(elem, cid, gid) {
					$.ajax({
						url: "<?php url((empty($vpTournois) ? 'admin/module/tournois/' : 'centralien/vptournoi/').'phase_'.$phase['id']); ?>",
						type: "POST",
						cache: false, 
						data: {
							cid: cid, 
							gid: gid,
							checked: $(elem).is(':checked') ? 1 : 0
						}
					});

					$(elem).parent().parent().find('input[type=checkbox]').each(function() {
						if ($(this).attr('id') != $(elem).attr('id'))
							$(this).attr('checked', false);
					});
				};

				$(function() {
					$speed =  <?php echo APP_SPEED_ERROR; ?>;

			    	$analysis = function(elem, event, force) {
			            if (event.keyCode == 13 || force) {
			                event.preventDefault();
			              	$parent = elem.parent().parent();
			              	$first = $parent.children('td:first');
			  				$nom = $first.children('input');
			  				$erreur = false;

			                if (!$nom.val().trim()) {
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

				<?php } } 

				 else if ($_GET['p'] == 'details') { ?>

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
									Attention, une fois la phase cloturée, il ne sera plus possible d'éditer la phase, ses matchs, ses concurrents qualifiés. Il sera par ailleurs impossible de rouvrir cette phase.
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

					<tr style="border-top:2px solid #000; height:50% !important" class="form<?php if ($phase['nb_precedentes_ouvertes'] == 0) { ?> clickme match-<?php echo $match['id']; ?>" data-id="<?php echo $match['id']; ?>" onclick="window.location.href = '<?php url((empty($vpTournois) ? 'admin/module/tournois/' : 'centralien/vptournoi/').'phase_'.$phase['id'].'?p=matchs&a='.$match['id_concurrent_a'].'&b='.$match['id_concurrent_b'].'&'.rand().'#m'.$match['id']); ?>';<?php } ?>">
						<td rowspan="2" class="content"><center>
							<a name="m<?php echo $match['id']; ?>"></a>
							<?php echo !empty($match['date']) ? (new DateTime($match['date']))->format('Y/m/d<\b\r /><\b>H:i</\b>') : ''; ?></center></td>
						<td rowspan="2" class="content"><center><?php echo !empty($match['id_site']) ? $sites[$match['id_site']]['nom'] : ''; ?></center></td>
						<td rowspan="2"><textarea readonly onclick="event.stopPropagation();"><?php echo stripslashes(empty($match['commentaire']) ? "" : $match['commentaire']) ; ?></textarea></td>

						<td class="content"><center><b>A</b></center></td>
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

					<tr style="height:49% !important" class="form<?php if ($phase['nb_precedentes_ouvertes'] == 0) { ?> clickme match-<?php echo $match['id']; ?>" data-id="<?php echo $match['id']; ?>" onclick="window.location.href = '<?php url((empty($vpTournois) ? 'admin/module/tournois/' : 'centralien/vptournoi/').'phase_'.$phase['id'].'?p=matchs&a='.$match['id_concurrent_a'].'&b='.$match['id_concurrent_b'].'&'.rand().'#m'.$match['id']); ?>';<?php } ?>">
						<td class="content"><center><b>B</b></center></td>
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

			<?php } 

			if ($phase['nb_precedentes_ouvertes'] == 0 &&
				!empty($select)) { 
				$sets_a = !empty($select['sets_a']) ? json_decode(unsecure($select['sets_a'])) : [''];
				$sets_b = !empty($select['sets_b']) ? json_decode(unsecure($select['sets_b'])) : [''];
				$gagne = !empty($select['gagne']) ? $select['gagne'] : '';
				$datetime = !empty($select['date']) ? new DateTime($select['date']) : '';
				$duree = !empty($select['duree']) ? new DateTime($select['duree']) : '';
				$date = !empty($datetime) ? $datetime->format('Y-m-d') : '';
				$time = !empty($datetime) ? $datetime->format('H:i') : '';
				$duree = !empty($duree) ? $duree->format('H:i') : '';

			?>

			<div id="modal-match" class="modal big-modal">
					<form method="post" action="<?php url((empty($vpTournois) ? 'admin/module/tournois/' : 'centralien/vptournoi/').'phase_'.$id_phase.'?t='.(!empty($_GET['t']) ? '1' : '').(!empty($_GET['p']) ? '&p='.$_GET['p'].($_GET['p'] == 'matchs' && !empty($select['id']) ? '#m'.$select['id'] : '#c'.$_GET['a']) : '#c'.$_GET['a'])); ?>">
						<fieldset>
							<legend>Edition d'un match (<?php print_r($select['id']) ?>)</legend>

							<div>
								<label for="form-null">
									<span>Concurrent A<br /><span style="color:#999">Sets</span></span>
									<div>
										<input type="hidden" name="a" value="<?php echo $_GET['a']; ?>" />
										<?php echo stripslashes($concurrents[$select['id_concurrent_a']]['enom']).' / '.($phase['individuel'] ? 
											stripslashes($concurrents[$select['id_concurrent_a']]['pnom']).' '.stripslashes($concurrents[$select['id_concurrent_a']]['pprenom']) : 
											stripslashes($concurrents[$select['id_concurrent_a']]['label'])); ?>
										<table style="border:none; margin:0" id="form-sets-a">
											<tr class="form">
												<?php for ($i = 0; $i < max(count($sets_a), count($sets_b)); $i++) { ?>
												<td><input type="text" name="sets_a[]" value="<?php echo !isset($sets_a[$i]) ? '' : $sets_a[$i]; ?>" /></td>
												<?php } ?>
												<td class="content transparent" style="width:20px !important"><img src="<?php url('assets/images/actions/add.png'); ?>" alt="Add" style="cursor:pointer;" class="add-set" /></td>
											</tr>
										</table>
									</div>
								</label>

								<label for="form-null">
									<span>Concurrent B<br /><span style="color:#999">Sets</span></span>
									<div>
										<input type="hidden" name="b" value="<?php echo $_GET['b']; ?>" />
										<?php echo stripslashes($concurrents[$select['id_concurrent_b']]['enom']).' / '.($phase['individuel'] ? 
											stripslashes($concurrents[$select['id_concurrent_b']]['pnom']).' '.stripslashes($concurrents[$select['id_concurrent_b']]['pprenom']) : 
											stripslashes($concurrents[$select['id_concurrent_b']]['label'])); ?>										<table style="border:none; margin:0" id="form-sets-b">
											<tr class="form">
												<?php for ($i = 0; $i < max(count($sets_a), count($sets_b)); $i++) { ?>
												<td><input type="text" name="sets_b[]" value="<?php echo !isset($sets_b[$i]) ? '' : $sets_b[$i]; ?>" /></td>
												<?php } ?>
												<td class="content transparent" style="width:20px !important"><img src="<?php url('assets/images/actions/add.png'); ?>" alt="Add" style="cursor:pointer;" class="add-set" /></td>
											</tr>
										</table>
									</div>
								</label>

								<label for="form-null">
									<span>Vainqueur</span>
									<div class="triple">
										<input type="checkbox" name="gagne_a" id="form-gagne-a" value="gagne_a" <?php echo $gagne == 'a' ? 'checked ' : ''; ?>/>
										<label for="form-gagne-a" class="vainqueur-a"></label>

										<input type="checkbox" name="gagne_nul" id="form-gagne-nul" value="gagne_nul" <?php echo $gagne == 'nul' ? 'checked ' : ''; ?>/>
										<label for="form-gagne-nul" class="vainqueur-nul"></label>

										<input type="checkbox" name="gagne_b" id="form-gagne-b" value="gagne_b" <?php echo $gagne == 'b' ? 'checked ' : ''; ?>/>
										<label for="form-gagne-b" class="vainqueur-b"></label>
									</div>
								</label>

								<label for="form-forfait">
									<span>Forfait</span>
									<input type="checkbox" name="forfait" id="form-forfait" value="forfait" <?php echo !empty($select['forfait']) ? 'checked ' : ''; ?>/>
									<label for="form-forfait"></label>
								</label>
							</div>
							<hr />

							<div>
								<label for="form-site">
									<span>Site</span>
									<select name="site" id="form-site">
										<option value=""></option>

										<?php foreach ($sites as $sid => $site) { ?>

										<option value="<?php echo $sid; ?>"<?php
											echo !empty($select['id_site']) && $select['id_site'] == $sid ? ' selected' : ''; ?>><?php echo stripslashes($site['nom']); ?></option>

										<?php } ?>
									</select>
								</label>

								<label>
									<span>Date/ Heure / Durée</span>
									<input class="three_input" type="date" name="date" value="<?php echo $date; ?>" />
									<input class="three_input" type="time" name="time" value="<?php echo $time; ?>" />
									<input class="three_input" type="time" name="duree" value="<?php echo $duree; ?>" />
								</label>

								<label>
									<span>Commentaire</span>
									<textarea name="commentaire"><?php echo !empty($select['commentaire']) ? stripslashes($select['commentaire']) : ''; ?></textarea>
								</label>
							</div>

							<hr />

							<center>
								<input type="submit" class="success" value="Editer le match" name="edit_match" />
								<?php if (!empty($select['id'])) { ?>
								<br />
								<input type="submit" class="delete" value="Supprimer le match" name="del_match" />
								<?php } ?>
							</center>
						</fieldset>
					</form>
				</div>

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
