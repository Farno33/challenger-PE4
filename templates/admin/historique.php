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


//Inclusion de l'entête de page
require DIR.'templates/admin/_header_admin.php';

?>

				<h2><?php echo $titre; ?></h2>

				<form method="post">
					<center>
						<input type="submit" value="Revenir à la page précédente" />
					</center>
				</form><br />

				<table class="table">
					<thead>
						<tr>
							<th>ID</th>
							<th>Date</th>
							<th>Auteur</th>
							<th>Action</th>
							<th>Message</th>
							<th>État</th>
						</tr>
					</thead>

					<tbody>

						<?php 

						$contenu = '';
						$history = array_reverse($history);
						$etatBefore = null;
						foreach ($history as $revision) { 
							ob_start();

						?>

						<tr class="form">
							<td class="content"><b><?php echo $revision['id']; ?></b></td>
							<td class="content"><?php echo printDateTime($revision['_date']); ?></td>
							<td class="content"><center><b><?php echo $revision['_auteur_nom'].' '.$revision['_auteur_prenom']; ?></b></center></td>
							<td class="content"><center><?php echo printActionItem($revision['_etat'], $revision['_ref'], $etatBefore); ?></center></td>
							<td><textarea readonly><?php echo stripslashes($revision['_message']); ?></textarea></td>
							<td class="content"><center><?php echo printEtatItem($revision['_etat']); ?></center></td>
						</tr>

						<?php 

							$ligne = ob_get_clean();
							$contenu = $ligne . $contenu;
							$etatBefore = $revision['_etat'];
						} 

						echo $contenu;

						?>

					</tbody>
				</table>

<?php

//Inclusion du pied de page
require DIR.'templates/_footer.php';
