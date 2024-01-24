<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* templates/admin/ecoles/visibilite.php *******************/
/* Template de la gestion de la visibilite des tarifs ******/
/* *********************************************************/
/* Dernière modification : le 18/12/14 *********************/
/* *********************************************************/


//Inclusion de l'entête de page
require DIR.'templates/admin/_header_admin.php';

?>
			
				<style>
				.subnav li, 
				.subnav li ul { width:200px !important;}
				</style>
				
				<nav class="subnav">
					<h2>Visibilité des tarifs</h2>
	 					
					<ul>
						<li><a href="<?php url('admin/module/ecoles/visibilite'); ?>">Récapitulatif</a></li>
						<li>
							<span>Ecoles Non-Lyonnaises</span>
							<ul>
								<li><a href="?e=0&l=0">Format court</a></li>
								<li><a href="?e=0&l=1">Format long</a></li>
							</ul>
						<li>
							<span>Ecoles Lyonnaises</span>
							<ul>
								<li><a href="?e=1&l=0">Format court</a></li>
								<li><a href="?e=1&l=1">Format long</a></li>
							</ul>
						</li>
					</ul>
				</nav>

				<?php 
				if (isset($_GET['e']) && 
					isset($_GET['l']) &&
					in_array($_GET['e'], ['0', '1']) &&
					in_array($_GET['l'], ['0', '1'])) { 
				?>

				<h3>Tarifs des <u>Ecoles <?php if (!$_GET['e']) echo 'Non-'; ?>Lyonnaises</u> / <u>Format <?php echo $_GET['l'] ? 'Long' : 'Court'; ?></u></h3>

				<?php foreach (range(0, 1) as $typeSportif) { ?>

				<h4>Tarifs <u><?php if (!$typeSportif) echo 'Non-'; ?>Sportifs</u></h4>
				<form method="post">
					<center>
					<table style="width:auto">
						<thead>
							<tr>
								<th>Ecole</th>

								<?php
								
								$nbTarifs = 0;

								foreach ($tarifs as $id => $tarif) {
									
									if ((int) $tarif['sportif'] != $typeSportif ||
										(int) $tarif['ecole_lyonnaise'] != $_GET['e'] ||
										(int) $tarif['format_long'] != $_GET['l'])
										continue;

									$nbTarifs++;

								 ?>

								<th class="vertical"><span><?php echo stripslashes($tarif['nom']); ?></span></th>

								<?php } ?>

							</tr>
						</thead>

						<tbody>

							<?php

							$nbEcoles = 0;
							foreach ($ecoles as $ecole) {

								if ((int) $ecole['ecole_lyonnaise'] != $_GET['e'] ||
									(int) $ecole['format_long'] != $_GET['l'])
									continue;

							$nbEcoles++;

							?>

							<tr class="form">
								<td><center><?php echo stripslashes($ecole['nom']); ?></center></td>
								
								<?php

								foreach ($tarifs as $id => $tarif) {

									if ((int) $tarif['sportif'] != $typeSportif ||
										(int) $tarif['ecole_lyonnaise'] != $_GET['e'] ||
										(int) $tarif['format_long'] != $_GET['l'])
										continue;

								?>

								<td class="vertical">
									<input type="checkbox" data-ecole="<?php echo $ecole['id']; ?>" data-tarif="<?php echo $id; ?>" id="tarifs_<?php echo $typeSportif; ?>_<?php echo $ecole['id'].'_'.$id; ?>" name="tarifs_<?php echo $ecole['id']; ?>[]" value="<?php echo $id; ?>"<?php 
										if (in_array($id, array_keys($ecole['tarifs']))) echo ' checked'; 
										if (!empty($ecole['tarifs'][$id]['pnb'])) echo ' disabled'; ?> />
									<label for="tarifs_<?php echo $typeSportif; ?>_<?php echo $ecole['id'].'_'.$id; ?>"></label>
								</td>

								<?php } ?>

							</tr>

							<?php } if (!$nbEcoles) { ?>

							<tr class="vide">
								<td colspan="<?php echo 1 + $nbTarifs; ?>">Aucune école</td>
							</tr>

							<?php } ?>

						</tbody>
					</table>
					</center>
				</form>


			<?php } ?>

			<script type="text/javascript">
			$(function() {
				$('input[type=checkbox]').on('change', function(e) {
					$.ajax({
						url: "<?php url('admin/module/ecoles/visibilite'); ?>",
					  	method: "POST",
					  	cache: false,
						data:{
							ecole:$(this).data('ecole'),
							tarif:$(this).data('tarif')
						},
						success: function(data) { if (data != '1') e.preventDefault(); },
						error: function() { e.preventDefault(); }
					});
				});
			});
			</script>

			<?php } else { ?>

			<table class="table-small">
				<thead>
					<tr>
						<th>Ecole</th>
						<th>Lyonnaise</th>
						<th>Format long</th>
						<th><small>Tarifs sportifs</small></th>
						<th><small>Tarifs non-sportifs</small></th>
					</tr>
				</thead>

				<tbody>
					<?php foreach ($ecoles as $eid => $ecole) { ?>
					<tr>
						<td><?php echo stripslashes($ecole['nom']); ?></td>
						<td style="padding:0px">
							<input type="checkbox" <?php if ($ecole['ecole_lyonnaise']) echo 'checked '; ?>/>
							<label></label>
						</td>
						<td style="padding:0px">
							<input type="checkbox" <?php if ($ecole['format_long']) echo 'checked '; ?>/>
							<label class="format"></label>
						</td>
						<td><center><?php 
							$count = 0;
							$countSportif = 0;

							foreach ($ecole['tarifs'] as $tid => $data) {
								if (empty($tarifs[$tid]))
									continue;

								$count++;

								if ($tarifs[$tid]['sportif'])
									$countSportif++;
							} 
							echo $countSportif; ?></center></td>
						<td><center><?php echo $count - $countSportif; ?></center></td>
					</tr>
					<?php } if (!count($ecoles)) { ?>
					<tr class="vide">
						<td colspan="5">Aucune école</td>
					</tr>
					<?php } ?>
				</tbody>
			</table>

			<?php } ?>

<?php

//Inclusion du pied de page
require DIR.'templates/_footer.php';
