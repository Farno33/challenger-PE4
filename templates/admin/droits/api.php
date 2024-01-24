<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* templates/admin/droits/liste.php ************************/
/* Template de la liste des organisateurs ******************/
/* *********************************************************/
/* Dernière modification : le 24/11/14 *********************/
/* *********************************************************/

$cql = "T|u:utilisateurs:
F|u.nom|u.prenom|u.private_token|u.public_token
C|u.private_token:<>:null";

//Inclusion de l'entête de page
require DIR.'templates/admin/_header_admin.php';

?>
			
				<form method="post" action="<?php url('admin/module/competition/extract'); ?>">
				<h2>
					Liste des accès API
					<input type="submit" value="CQL" />
				</h2>

				<input type="hidden" name="cql" value="<?php echo $cql; ?>" />
				</form>


				<?php
				if (isset($add) ||
					isset($modify) ||
					!empty($delete)) {
				?>

				<div class="alerte alerte-<?php echo !empty($add) || !empty($modify) || !empty($delete) ? 'success' : 'erreur'; ?>">
					<div class="alerte-contenu">
						<?php
						if (!empty($delete)) echo 'Les tokens ont bien été supprimé';
						else if (!empty($add)) echo 'Les tokens ont bien été créé';
						else echo 'Une erreur s\'est produite';
						?>
					</div>
				</div>

				<?php } ?>


				<form method="post">
					<table class="table-small">
						<thead>
							<tr class="form">
								<td>
									<select name="login[]">
										<option value="" selected>Responsable...</option>
										<?php 
										foreach ($admins as $id => $admin) { 
											if (!empty($admin['private_token']))
												continue;
										?>
										<option value="<?php echo $admin['login']; ?>"><?php echo stripslashes($admin['nom'].' '.$admin['prenom']); ?></option>
										<?php } ?>
									</select>
								</td>
								<td></td>
								<td></td>
								<td class="actions">
									<button type="submit" name="add">
										<img src="<?php url('assets/images/actions/add.png'); ?>" alt="Add" />
									</button>
								</td>
							</tr>

							<tr>
								<th>Nom / Prénom</th>
								<th style="color:red">Private token</th>
								<th style="color:green">Public token</th>
								<th class="actions">Actions</th>
							</tr>
						</thead>

						<tbody>

							<?php

							$count = 0;
							foreach ($admins as $admin) {
								if (empty($admin['private_token']))
									continue;

								$count++; 
							?>

							<tr class="form">
								<td>
									<input type="hidden" name="login[]" value="<?php echo stripslashes($admin['login']); ?>" />
									<div><?php echo stripslashes($admin['nom'].' '.$admin['prenom']); ?></div>
								</td>
								<td>
									<input type="text" disabled value="<?php echo $admin['private_token']; ?>" />
								</td>
								<td>
									<input type="text" disabled value="<?php echo $admin['public_token']; ?>" />
								</td>
								<td class="actions">								
									<button type="submit" name="delete" value="<?php echo stripslashes($admin['login']); ?>" />
										<img src="<?php url('assets/images/actions/delete.png'); ?>" alt="Delete" />
									</button>
								</td>
							</tr>

							<?php } if (empty($count)) { ?>

							<tr class="vide">
								<td colspan="2">Aucun accès API</td>
							</tr>

							<?php } ?>

						</tbody>
					</table>
				</form>
<?php

//Inclusion du pied de page
require DIR.'templates/_footer.php';
