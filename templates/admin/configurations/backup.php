<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* templates/admin/configurations/liste.php ****************/
/* Template de la liste du module des constantes ***********/
/* *********************************************************/
/* Dernière modification : le 23/11/14 *********************/
/* *********************************************************/


//Inclusion de l'entête de page
require DIR.'templates/admin/_header_admin.php';

?>
			
				<h2>Liste des Backups</h2>

                <center>
                    <a class="excel" href="?backup=<?php echo $_SESSION['backup']; ?>">Faire un Backup manuel</a>
                </center>

				<?php
				if (isset($add) ||
					isset($restore) ||
					!empty($delete)) {
				?>

				<div class="alerte alerte-<?php echo !empty($add) || !empty($restore) || !empty($delete) ? 'success' : 'erreur'; ?>">
					<div class="alerte-contenu">
						<?php
						if (!empty($restore)) echo 'Le backup a bien été restauré';
						else if (!empty($delete)) echo 'Le backup a bien été supprimé';
						else if (!empty($add)) echo 'Le backup a bien été ajouté';
						else echo 'Une erreur s\'est produite, veuillez réessayer.';
						?>
					</div>
				</div>

				<?php } ?>


				<form method="post">
					<table class="table-small">
						<thead>
							<tr>
								<th>Date</th>
								<th>Mode</th>
								<th>Taille</th>
								<th class="actions" style="width:120px">Actions</th>
							</tr>
						</thead>

						<tbody>

							<?php if (!count($backups)) { ?> 

							<tr class="vide">
								<td colspan="4">Aucun backup</td>
							</tr>

							<?php } foreach ($backups as $backup) { ?>

							<tr class="form">
                                <td><div><?php echo stripslashes($backup['date']); ?></div></td>
                                <td><div><?php echo stripslashes($backup['mode']); ?></div></td>
                                <td><div><?php echo stripslashes($backup['size']); ?></div></td>
								<td class="actions">					
									<button type="submit" name="restore" value="<?php echo stripslashes($backup['file']); ?>" />
										<img src="<?php url('assets/images/actions/change.png'); ?>" alt="Restore" />
									</button>

									<button type="submit" name="delete" value="<?php echo stripslashes($backup['file']); ?>" />
										<img src="<?php url('assets/images/actions/delete.png'); ?>" alt="Delete" />
									</button>

									<button type="submit" name="save" value="<?php echo stripslashes($backup['file']); ?>" />
										<img src="<?php url('assets/images/actions/save.png'); ?>" alt="Save" />
									</button>

									<button type="submit" name="sql" value="<?php echo stripslashes($backup['file']); ?>" />
										<img src="<?php url('assets/images/actions/sql.png'); ?>" alt="SQL" />
									</button>
								</td>
							</tr>

							<?php } ?>

						</tbody>
					</table>
				</form>

				<script type="text/javascript">
				$(function() {
					$speed =  <?php echo APP_SPEED_ERROR; ?>;
				});
				</script>

<?php

//Inclusion du pied de page
require DIR.'templates/_footer.php';
