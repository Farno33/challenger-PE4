<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* templates/admin/droits/droits.php ***********************/
/* Template de la gestion des droits ***********************/
/* *********************************************************/
/* Dernière modification : le 24/11/14 *********************/
/* *********************************************************/


//Inclusion de l'entête de page
require DIR.'templates/admin/_header_admin.php';

?>
			
				<h2>Gestion des Droits</h2>


				<form method="post">
					<center><table style="width:auto">
						<thead>
							<tr>
								<th>Admin</th>

								<?php foreach ($modulesAdmin as $module => $titre) { ?>

								<th class="vertical"><span><?php echo $titre[0]; ?></span></th>

								<?php } ?>

							</tr>
						</thead>

						<tbody>

							<?php if (!count($admins)) { ?> 

							<tr class="vide">
								<td colspan="<?php echo 1 + count($modulesAdmin); ?>">Aucun administrateur</td>
							</tr>

							<?php } foreach ($admins as $admin) { ?>

							<tr class="form">
								<td><center><?php echo stripslashes(strtoupper($admin['nom']).' '.$admin['prenom']); ?></center></td>
								
								<?php foreach ($modulesAdmin as $module => $titre) { ?>

								<td class="vertical">

									<?php if ($_SESSION['user']['id'] != $admin['id']) { ?>

									<input data-module="<?php echo $module; ?>" data-utilisateur="<?php echo $admin['id']; ?>" type="checkbox" id="form-droit_<?php echo $admin['id'].'_'.$module; ?>" <?php if (in_array($module, $admin['modules'])) echo ' checked'; ?> />
									<label for="form-droit_<?php echo $admin['id'].'_'.$module; ?>"></label>

									<?php } ?>

								</td>

								<?php } ?>

							</tr>

							<?php } ?>

						</tbody>
					</table></center>
				</form>

			<script type="text/javascript">
			$(function() {
				$('input[type=checkbox]').on('change', function(e) {
					$.ajax({
						url: "<?php url('admin/module/droits/droits'); ?>",
					  	method: "POST",
					  	cache: false,
						data:{
							utilisateur:$(this).data('utilisateur'),
							module:$(this).data('module')
						},
						success: function(data) { if (data != '1') e.preventDefault(); },
						error: function() { e.preventDefault(); }
					});
				});
			});
			</script>

<?php

//Inclusion du pied de page
require DIR.'templates/_footer.php';
