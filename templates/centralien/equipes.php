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
$cap = false;
?>

<h2>Liste des mes équipes</h2>

<?php if (!count($mes_equipes)) { ?>

	<div class="alerte alerte-attention">
		<div class="alerte-contenu">
			Tu n'es sportif dans aucun sport/équipe.<br />
			Il t'est possible de changer cela sur <a href="<?php url('centralien/prestations'); ?>">la page associée</a>
		</div>
	</div>

<?php }
foreach ($mes_equipes as $eid => $equipe) { $cap |= $equipe['cap'] == $options['id_participant'];?>

	<h3><a name="e<?php echo $eid; ?>"></a><?php echo stripslashes($equipe['sport'] . ' ' . printSexe($equipe['sexe']) .
												(empty($equipe['individuel']) ? ' / ' . $equipe['label'] : '')); ?> &Tab; <?php if ($equipe['cap'] == $options['id_participant']) { ?> <a onclick="lock(event);" style="text-decoration: none;"><?php echo (strpos($equipe["_message"], "locked") === false ? "🔓</a>" : "🔒</a>");
																																																				} ?></h3>

	<table class="table-small">
		<tr>
			<th style="width:70px">Capitaine</th>
			<th>Nom</th>
			<th>Prénom</th>
			<th style="width:70px">Sexe</th>
			<?php if ($equipe['cap'] == $options['id_participant']) { ?>
				<th style="width:70px">Supprimer</th>
			<?php } ?>
		</tr>


		<?php if (empty($sportifs[$eid])) { ?>

			<tr class="vide">
				<td colspan="4">Aucun sportif pour le moment</td>
			</tr>

			<?php } else {
			foreach ($sportifs[$eid] as $sportif) { ?>
				<form method="post">
					<input type="hidden" name="id_equipe" value="<?php echo $eid; ?>" />
					<tr class="form">
						<td>
							<input type="checkbox" <?php if (!empty($sportif['capitaine'])) echo 'checked '; ?> />
							<label class="capitaine"></label>
						</td>
						<td class="content"><?php echo stripslashes($sportif['nom']); ?></td>
						<td class="content"><?php echo stripslashes($sportif['prenom']); ?></td>
						<td>
							<input type="checkbox" <?php if ($sportif['sexe'] == 'h') echo 'checked '; ?> />
							<label class="sexe"></label>
						</td>
						<?php if ($equipe['cap'] == $options['id_participant']) { ?>
							<td class="content">
								<center>
									<button type="submit" name="delete" value="<?php echo stripslashes($sportif['pid']); ?>" <?php if ($equipe['cap'] == $options['id_participant'] && !$equipe['individuel'] && $sportif['capitaine']) echo ('onClick="return confirm(\'Êtes vous sûr ? Vous êtes le capitaine de cette équipe, si vous confirmez, votre équipe sera dissout !\')"'); ?> />
									<img src="<?php url('assets/images/actions/delete.png'); ?>" alt="Delete" />
									</button>
								</center>
							</td>
						<?php } ?>
					</tr>
				</form>
		<?php }
		} ?>

	</table>

<?php }
if ($cap) { ?>

	<script>
		function lock(e) {
			let eq;
			let lock = e.target;
			e.target.parentNode.childNodes.forEach(function(node) {
				if (node.name && node.name.indexOf("e") === 0) {
					eq = parseInt(node.name.substring(1));
				}
			});
			if (!isNaN(eq)) {
				$.ajax({
					type: "POST",
					data: {
						[e.target.innerHTML == '🔓' ? 'lock' : 'unlock']: eq
					},
					success: function(data) {
						if (data == "locked") {
							lock.innerHTML = "🔒";
						} else if (data == "open") {
							lock.innerHTML = "🔓";
						}
					}
				});
			}
		}
	</script>

<?php } ?>


<?php

//Inclusion du pied de page
require DIR . 'templates/_footer.php';
