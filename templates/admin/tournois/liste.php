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
if (empty($vpTournois)) require DIR . 'templates/admin/_header_admin.php';
else require DIR . 'templates/centralien/_header_centralien.php';

$cql = 'T|s:sports:
S|s.individuel
B|s.individuel|s.tournoi_initie';

?>

<form method="post" class="form-table" action="<?php url('admin/module/competition/extract'); ?>">
	<h2>
		Liste des Tournois
		<?php if (empty($vpTournois)) { ?> <input type="submit" value="CQL" /> <?php } else {
																				echo 'dont tu est reponsable';
																			} ?>
	</h2>

	<input type="hidden" name="cql" value='<?php echo $cql; ?>' />
</form>

<table class="table-small">
	<thead>
		<tr>
			<th>Sport</th>
			<th style="width:60px">Sexe</th>
			<th style="width:60px"><small>Individuel</small></th>
			<th style="width:50px"><small>Ecoles</small></th>
			<th style="width:50px"><small>Equipes</small></th>
			<th style="width:50px"><small>Sportifs</small></th>
			<th style="width:50px"><small>Initié</small></th>
			<th style="width:50px"><small>Phases</small></th>
			<th style="width:50px"><small>Concurrents</small></th>
		</tr>
	</thead>

	<tbody>

		<?php if (empty($sports)) { ?>

			<tr class="vide">
				<td colspan="9">Aucun sport</td>
			</tr>

		<?php }

		$previousIndividuel = null;
		foreach ($sports as $sport) {

			if (
				$previousIndividuel !== $sport['individuel'] &&
				$previousIndividuel !== null
			) echo '<tr><th colspan="9"></th></tr>';

			$previousIndividuel = $sport['individuel'];

		?>

			<tr class="form clickme" onclick="window.location.href = '<?php url((empty($vpTournois) ? 'admin/module/tournois/' : 'centralien/vptournoi/') . $sport['id']); ?>';">
				<td>
					<div><?php echo stripslashes($sport['sport']); ?></div>
				</td>
				<td>
					<input type="checkbox" <?php if ($sport['sexe'] != 'f') echo 'checked '; ?> />
					<label class="sexe<?php if ($sport['sexe'] == 'm') echo ' sexe-m'; ?>"></label>
				</td>
				<td>
					<input type="checkbox" <?php if ($sport['individuel']) echo 'checked '; ?> />
					<label></label>
				</td>
				<td>
					<div>
						<center><b><?php echo $sport['nb_ecoles']; ?></b></center>
					</div>
				</td>
				<td>
					<div>
						<center><b><?php echo $sport['nb_equipes']; ?></b></center>
					</div>
				</td>
				<td>
					<div>
						<center><b><?php echo $sport['nb_sportifs']; ?></b></center>
					</div>
				</td>
				<td>
					<input type="checkbox" <?php echo $sport['tournoi_initie'] === null ? 'disabled ' : ($sport['tournoi_initie'] ? 'checked ' : ''); ?> />
					<label></label>
				</td>
				<td>
					<div>
						<center><b><?php echo $sport['tournoi_initie'] === null ? '-' : $sport['nb_phases']; ?></b></center>
					</div>
				</td>
				<td>
					<div>
						<center><b><?php echo $sport['tournoi_initie'] === null ? '-' : $sport['nb_concurrents']; ?></b></center>
					</div>
				</td>
			</tr>

		<?php } ?>

	</tbody>


	</tbody>
</table>


<?php if (empty($vpTournois)) {	?>
	<fieldset class="fieldset-table">
		<h2>
			VP Tournois
		</h2>

		<form method="post" action="<?php url('admin/module/tournois/liste'); ?>">
			<label for="form-newVP">
				<span>Ajouter</span>
				<input class="three_input" name="newVP" type="text" id="form-newVP" value="" placeholder="Centralien à ajouter" required />
				<input type="hidden" id="id-newVP" name="id" value="" required />
				<select class="three_input" name="sport" required>
					<option value="" disabled selected>Sport</option>
					<?php foreach ($sports as $sport) { ?>
						<option value="<?php echo $sport['id']; ?>"><?php echo $sport['sport'] . ' ' . $sport['sexe']; ?></option>
					<?php } ?>
				</select>
				<input class="three_input" type="submit" name="add" value="Ajouter" />
				<small>Un vp a acces à tous les sports d'un même groupe</small>
			</label>
		</form>

		<form method="POST" action="<?php url('admin/module/tournois/liste'); ?>">
			<table class="table-small">
				<thead>
					<tr>
						<th>Nom</th>
						<th>Sport</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($vps as $vp) { ?>
						<tr class="form">
							<td><div><?php echo $vp['nom'] . ' ' . $vp['prenom']; ?></div></td>
							<td>
								<select id="<?php echo $vp['id']; ?>" name="sport" required onchange="change(event);">
									<option value="" disabled>Sport</option>
									<?php foreach ($sports as $sport) { ?>
										<option value="<?php echo $sport['id']; ?>" <?php if ($sport['id'] == $vp['vptournoi']) echo "selected"; ?>><?php echo $sport['sport'] . ' ' . $sport['sexe']; ?></option>
									<?php } ?>
								</select>
							</td>
							<td>
								<input type="hidden" name="id" value="<?php echo $vp['id']; ?>" />
								<input type="submit" name="delete" value="Supprimer" />
							</td>
						</tr>
					<?php } ?>
			</table>
		</form>

	</fieldset>
	<script type="text/javascript">
		change = (event) => {
			element = event.target;
			element.disabled = true;

			$.ajax({
				url: "<?php url('admin/module/tournois/liste?ajax'); ?>",
				method: "POST",
				cache: false,
				dataType: "json",
				data: {
					id: element.id,
					sport: element.value
				},
				success: function(data) {
					element.disabled = false;
				},
				error: function(data) {
					element.disabled = false;
					element.firstElementChild.selected = true;
					element.firstElementChild.innerHMTL = "Une erreur s'est produite";
				}
			});
		}

		$(function() {
			var canSearch = false;
			var onlyOnEnter = false;
			$("#form-newVP").autocomplete({
				source: function(request, response) {
					var $me = this.element;
					$.ajax({
						url: "<?php url('admin/module/tournois/liste?ajax'); ?>",
						method: "POST",
						cache: true,
						dataType: "json",
						data: {
							filter: request.term
						},
						success: function(data) {
							response(data);
						}
					});
				},
				minLength: 2,
				select: function(e, ui) {
					$(this).val(ui.item.nom + ' ' + ui.item.prenom);
					$("#id-newVP").val(ui.item.id);
					return false;
				},
				focus: function(e, ui) {
					return false;
				},
				search: function(e, ui) {
					var canTempSearch = canSearch;
					canSearch = false;
					return !onlyOnEnter || onlyOnEnter && canTempSearch;
				}
			}).bind('keyup', function(e) {
				if (e.keyCode == 13) {
					canSearch = true;
					$(this).autocomplete("search", $(this).val());
				}
			}).focus(function() {
				if (!onlyOnEnter)
					$(this).autocomplete("search");
			}).blur(function() {});

			$.ui.autocomplete.prototype._renderItem = function(ul, item) {
				return $("<li>")
					.append("<a>" + item.nom + ' ' + item.prenom + "</a>")
					.appendTo(ul);
			};
		});
	</script>

<?php }

//Inclusion du pied de page
require DIR . 'templates/_footer.php';
