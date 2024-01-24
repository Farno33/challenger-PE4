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
					<h2>Détails des éliminations<br /> 
						<?php echo stripslashes($phase['nom']).' / <a href="'.url((empty($vpTournois) ? 'admin/module/tournois/' : 'centralien/vptournoi/').$phase['sid'], false, false).'">'.stripslashes($phase['sport']).' '.printSexe($phase['sexe']).'</a>'; ?></h2>

					<ul>
						<li><a href="?p=resume">Résumé</a></li>
						<li><a href="?p=details">Détails</a></li>
						<li><a href="?p=matchs">Matchs</a></li>
					</ul>
				</nav>

<style>
table.bracket { 
	background:#CCC;
	border:20px solid #CCC;
	margin:0;
	width:auto;
}

table.bracket tr, 
table.bracket td { 
	height: 100%;
	border: none;
	background:none !important;
	box-sizing:border-box;
}

table.bracket td table {
	margin:0;
	border:none;
}

/* Hack IE */
@media screen and (-ms-high-contrast: active), (-ms-high-contrast: none) { 
	table.bracket tr, 
	table.bracket td { 
		height: 1px;
	}
}

table.bracket td.match table {
	width:100%;
}

table.bracket td.match {
	padding-left:10px;
	padding:0px;
	min-width:100px;

}

table.bracket td.match-team,
table.bracket td.match-infos,
table.bracket td.match-set,
table.bracket td.match-medal {
	background:#FFF !important;
	padding:3px;
	text-align: center;
	color:#555;
}

table.bracket td table td.match-team,
table.bracket td table td.match-team div {
	max-width:120px !important;
	height:1.25em;
	overflow:hidden;
}

table.bracket td.match-set {
	border-left:1px solid #BBB;
	width:15px;
} 

table.bracket td.match-infos {
	border-left:1px solid #BBB;
} 

table.bracket td.team-win {
	font-weight:bold;
	color:#000;
}

table.bracket td.set-win {
	font-weight:bold;
	color:red;
}

table.bracket td.team-empty,
table.bracket td.set-empty {
	font-style:italic;
	color:#CCC;
}

table.bracket td.match-numero {
	vertical-align: middle;
	color:#666;
	width:1px;
	background:#FFF !important;
	font-size:75%;
	padding:3px;
	border-top-left-radius:10px;
	border-bottom-left-radius:10px;
}

table.bracket td.match-edit {
	background:#FFF !important;
	color:#FFF;
	width:10px;
	border-top-right-radius:10px;
	border-bottom-right-radius:10px;
}

table.bracket td.has-a-left, 
table.bracket td.has-b-left,
table.bracket td.has-match-top-finales,
table.bracket td.has-match-bottom-finales,
table.bracket td.line {
	padding:0px !important;
}

table.bracket td.has-a-left {
	vertical-align: bottom;
}

table.bracket td.has-b-left {
	vertical-align: top;
}

table.bracket td.line div {
	width: 50%;
	height: 100%;
	display: inline-block !important;
	border-right: 5px solid #FFF;
	box-sizing: content-box;

}

table.bracket td.has-a-left div {
	width: 50%;
	height: 50%;
	display: inline-block;
	border-right:5px solid #FFF;
	border-top: 5px solid #FFF;
	border-top-right-radius: 10px;
	box-sizing: content-box;
}

table.bracket td.has-b-left div {
	width: 50%;
	height: 50%;
	display: inline-block;
	border-right: 5px solid #FFF;
	border-bottom: 5px solid #FFF;
	border-bottom-right-radius: 10px;
	box-sizing: content-box;
}

table.bracket td.has-match-bottom-finales div {
	width: 50%;
	height: 100%;
	margin-left: 25%;
	display: inline-block;
	border-right: 5px solid #FFF;
	border-top: 5px solid #FFF;
	border-left: 5px solid #FFF;
	border-top-left-radius: 10px;
	border-top-right-radius: 10px;
	box-sizing: content-box;
}

table.bracket td.has-match-top-finales div {
	width: 50%;
	height: 100%;
	margin-left: 25%;
	display: inline-block;
	border-right: 5px solid #FFF;
	border-bottom: 5px solid #FFF;
	border-left: 5px solid #FFF;
	border-bottom-left-radius: 10px;
	border-bottom-right-radius: 10px;
	box-sizing: content-box;
}

table.bracket td.link-highlight div {
	border-color:#99F;
}

table.bracket td.match-highlight td.match-team,
table.bracket td.match-highlight td.match-set,
table.bracket td.match-highlight td.match-numero,
table.bracket td.match-highlight td.match-edit,
table.bracket td.match-highlight td.match-infos,
table.bracket td.match-highlight td.match-medal {
	background:#99F !important;
}

table.bracket td.match-highlight td.team-highlight.team-win {
	background:green !important;
}

table.bracket td.match-highlight td.team-highlight.team-lose {
	background:red !important;
}

table.bracket td.match td.match-edit div {
	display:none;
}

table.bracket td.match.petite-finale {
	border-right:10px solid #CCC;
}

table.bracket td.match:hover td.match-team,
table.bracket td.match:hover td.match-set,
table.bracket td.match:hover td.match-numero,
table.bracket td.match:hover td.match-edit,
table.bracket td.match:hover td.match-infos,
table.bracket td.match:hover td.match-medal {
	background:#FFB !important;
	color:#000;
}

table.bracket td.etape {
	font-variant: small-caps;
	text-align: center;
	font-weight: bold;
	background: #000 !important;
	color: #FFF;
	padding: 5px;
	border-bottom: 20px solid #CCC;
}

</style>

				<script type="text/javascript">
				function preloadMatchs(elem, e) {
					var equipes = prompt("Nombre d'équipes en lice?");
					if (!equipes || parseInt(equipes) != equipes && parseInt(equipes) > 0) {
						e.preventDefault();
						return false;
					}

					$(elem).prev().val(equipes);
				}
				</script>


			<?php if (empty($_GET['p']) || $_GET['p'] == 'resume') { 

				if ($phase['nb_precedentes_ouvertes'] > 0 && empty($lines)) { ?>

				<div class="alerte alerte-attention">
					<div class="alerte-contenu">
						Au moins une phase précédente est encore ouverte. En prévision il est tout de même possible de prévoir le nombre de concurrents.
						<b>Attention : </b> Le nombre renseigné et devra être égal (dans l'idéal) aux nombre de qualifiés des phases précédentes. 
						<br /><br />
						<form method="post">
						<center>
							<input type="hidden" name="equipes" value="0" />
							<input type="submit" value="Prévoir les matchs pour cette phase" name="preload" onclick="return preloadMatchs(this, event)" />
						</center>
						</form>
					</div>
				</div>


				<?php } else if (empty($lines)) { ?>

				<div class="alerte alerte-attention">
						<div class="alerte-contenu">
							Il n'y a aucun match prévu dans cette phase d'élimination...
						</div>
					</div>

				<?php } else {

					if ($phase['nb_precedentes_ouvertes'] > 0) { ?>

				<div class="alerte alerte-attention">
					<div class="alerte-contenu">
						Au moins une phase précédente est encore ouverte. Il est tout de même possible de changer le nombre de concurrents prévus. 
						<b>Attention : </b> Si des matchs sont déjà présents, l'ordre ne sera pas conservé, il est donc fortement conseillé de supprimer tous les matchs.
						<br /><br />
						<form method="post">
						<center>
							<input type="hidden" name="equipes" value="0" />
							<input type="submit" value="Changer le nombre prévu de concurrents" name="preload" onclick="return preloadMatchs(this, event)" /><br />
							<?php if (!empty($phase['nb_matchs'])) { ?><input type="submit" class="delete" value="Supprimer tous les matchs" name="delete_all" /><?php } ?>
						</center>
						</form>
					</div>
				</div>



					<?php } 


					if (empty($phase['nb_matchs'])) { ?>

					
					<div class="alerte alerte-info">
						<div class="alerte-contenu">
							Pour correctement initialiser cette phase d'élimination il est fortement recommandé de procéder à une initialisation.
							Cette étape crééra tous les matchs.<br />
							<br />
							<form method="post">
							<center><input type="submit" value="Initier cette phase d'élimination" name="init" /></center>
							</form>
						</div>
					</div>

					<?php } 


					echo '<center><table class="bracket">';
					foreach ($lines as $line) {
						echo '<tr>';

						foreach ($line as $cell) {
							echo $cell;
						}

						echo '</tr>';
					}
					echo '</table></center>'; 

					}
				
				} else if ($_GET['p'] == 'details') { ?>

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
						<th style="width:50px">Phase</th>
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

					<tr style="border-top:2px solid #000; height:50% !important" class="form clickme match-<?php echo $match['id']; ?>" data-id="<?php echo $match['id']; ?>" onclick="window.location.href = '<?php url((empty($vpTournois) ? 'admin/module/tournois/' : 'centralien/vptournoi/').'phase_'.$phase['id'].'?p=matchs&m='.$match['numero_elimination'].'&'.rand().'#m'.$match['numero_elimination']); ?>';">
						<td rowspan="2" class="content"><center>
							<a name="m<?php echo $match['numero_elimination']; ?>"></a>
							<?php echo !empty($match['date']) ? (new DateTime($match['date']))->format('Y/m/d<\b\r /><\b>H:i</\b>') : ''; ?></center></td>
						<td rowspan="2" class="content"><center><?php echo $match['phase_elimination']; ?></center></td>
						<td rowspan="2" class="content"><center><?php echo !empty($match['id_site']) ? $sites[$match['id_site']]['nom'] : ''; ?></center></td>
						<td rowspan="2"><textarea readonly onclick="event.stopPropagation();"><?php echo (empty($match['commentaire']) ? '' : stripslashes($match['commentaire'])); ?></textarea></td>

						<td class="content"><center><b>A</b></center></td>
						<td><div><?php echo empty($match['id_concurrent_a']) ? '' : stripslashes($concurrents[$match['id_concurrent_a']]['enom']); ?></div></td>
						<?php if ($phase['individuel']) { ?>
						<td><div><?php echo empty($match['id_concurrent_a']) ? '' : stripslashes($concurrents[$match['id_concurrent_a']]['pnom']); ?></div></td>
						<td><div><?php echo empty($match['id_concurrent_a']) ? '' : stripslashes($concurrents[$match['id_concurrent_a']]['pprenom']); ?></div></td>
						<td>
							<?php if (!empty($match['id_concurrent_a'])) { ?>
							<input type="checkbox" <?php if ($concurrents[$match['id_concurrent_a']]['psexe'] == 'h') echo 'checked '; ?>/>
							<label class="sexe"></label>
							<?php } ?>
						</td>
						<?php } else { ?>
						<td><div><?php echo empty($match['id_concurrent_a']) ? '' : stripslashes($concurrents[$match['id_concurrent_a']]['label']); ?></div></td>
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

					<tr style="height:49% !important" class="form clickme match-<?php echo $match['id']; ?>" data-id="<?php echo $match['id']; ?>" onclick="window.location.href = '<?php url((empty($vpTournois) ? 'admin/module/tournois/' : 'centralien/vptournoi/').'phase_'.$phase['id'].'?p=matchs&m='.$match['numero_elimination'].'&'.rand().'#m'.$match['numero_elimination']); ?>';">
						<td class="content"><center><b>B</b></center></td>
						<td><div><?php echo empty($match['id_concurrent_b']) ? '' : stripslashes($concurrents[$match['id_concurrent_b']]['enom']); ?></div></td>
						<?php if ($phase['individuel']) { ?>
						<td><div><?php echo empty($match['id_concurrent_b']) ? '' : stripslashes($concurrents[$match['id_concurrent_b']]['pnom']); ?></div></td>
						<td><div><?php echo empty($match['id_concurrent_b']) ? '' : stripslashes($concurrents[$match['id_concurrent_b']]['pprenom']); ?></div></td>
						<td>
							<?php if (!empty($match['id_concurrent_b'])) { ?>
							<input type="checkbox" <?php if ($concurrents[$match['id_concurrent_b']]['psexe'] == 'h') echo 'checked '; ?>/>
							<label class="sexe"></label>
							<?php } ?>
						</td>
						<?php } else { ?>
						<td><div><?php echo empty($match['id_concurrent_b']) ? '' : stripslashes($concurrents[$match['id_concurrent_b']]['label']); ?></div></td>
						<?php } ?>
					</tr>

					<?php } if (empty($matchs)) { ?>

					<tr class="vide">
						<td colspan="<?php echo $phase['individuel'] ? 12 : 10; ?>">Aucun match</td>
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

			if (!empty($select)) { 
				$sets_a = !empty($select['sets_a']) ? json_decode(unsecure($select['sets_a'])) : [''];
				$sets_b = !empty($select['sets_b']) ? json_decode(unsecure($select['sets_b'])) : [''];
				$gagne = !empty($select['gagne']) ? $select['gagne'] : '';
				$datetime = !empty($select['date']) ? new DateTime($select['date']) : '';
				$date = !empty($datetime) ? $datetime->format('Y-m-d') : '';
				$time = !empty($datetime) ? $datetime->format('H:i') : '';

			?>

			<div id="modal-match" class="modal big-modal">
					<form method="post" action="<?php url((empty($vpTournois) ? 'admin/module/tournois/' : 'centralien/vptournoi/').'phase_'.$id_phase.(!empty($_GET['p']) ? '?p='.$_GET['p'] : '').'#m'.$select['numero_elimination']); ?>">
						<fieldset>
							<legend>Edition d'un match (<?php print_r($select['id']) ?>)</legend>

							<div>
								<label for="form-null">
									<span>Concurrent A<br /><span style="color:#999">Sets</span></span>
									<div>
										<select style="width:<?php echo !empty($select['from_a']) ? '70' : '100'; ?>%; margin-bottom:5px" name="a">
											<option value=""></option>
											<?php foreach ($concurrents as $cid => $concurrent) { ?>
											<option value="<?php echo $cid; ?>"<?php if ($select['id_concurrent_a'] == $cid || empty($select['id_concurrent_a']) && findA($select['numero_elimination'], phase($select['numero_elimination']) == $labelsPhasesFinales['petite_finale']) == $cid) echo ' selected'; ?>><?php echo printConcurrent($cid); ?></option>
											<?php } ?>
										</select><?php if (!empty($select['from_a'])) echo ' &nbsp; <i>Si vide : <b>'.
											($select['numero_elimination'] == $nbMatchs && $hasPetiteFinale ? 'P' : 'G').$select['from_a'].'</b></i>'; ?>
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
										<select style="width:<?php echo !empty($select['from_b']) ? '70' : '100'; ?>%; margin-bottom:5px" name="b">
											<option value=""></option>
											<?php foreach ($concurrents as $cid => $concurrent) { ?>
											<option value="<?php echo $cid; ?>"<?php if ($select['id_concurrent_b'] == $cid || empty($select['id_concurrent_b']) && findB($select['numero_elimination'], phase($select['numero_elimination']) == $labelsPhasesFinales['petite_finale']) == $cid) echo ' selected'; ?>><?php echo printConcurrent($cid); ?></option>
											<?php } ?>
										</select><?php if (!empty($select['from_b'])) echo ' &nbsp; <i>Si vide : <b>'.
											($select['numero_elimination'] == $nbMatchs && $hasPetiteFinale ? 'P' : 'G').$select['from_b'].'</b></i>'; ?>
										<table style="border:none; margin:0" id="form-sets-b">
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
									<span>Date / Heure</span>
									<input class="two_input" type="date" name="date" value="<?php echo $date; ?>" />
									<input class="two_input" type="time" name="time" value="<?php echo $time; ?>" />
								</label>

								<label>
									<span>Commentaire</span>
									<textarea name="commentaire"><?php echo !empty($select['commentaire']) ? stripslashes($select['commentaire']) : ''; ?></textarea>
								</label>
							</div>

							<hr />

							<center>
								<input type="hidden" name="numero" value="<?php echo $select['numero_elimination']; ?>" />
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
