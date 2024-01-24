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
require DIR.'templates/ecoles/_header_ecoles.php';

?>

				<h2><?php echo $titre; ?></h2>

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

						<?php foreach ($history as $revision) { ?>

						<tr class="form">
							<td class="content"><b><?php echo $revision['id']; ?></b></td>
							<td class="content"><?php echo printDateTime($revision['_date']); ?></td>
							<td class="content"><center><b><?php echo $revision['_auteur_nom'].' '.$revision['_auteur_prenom']; ?></b></center></td>
							<td class="content"><center><?php echo printActionItem($revision['_etat'], $revision['_ref']); ?></center></td>
							<td><textarea readonly><?php echo stripslashes($revision['_message']); ?></textarea></td>
							<td class="content"><center><?php echo printEtatItem($revision['_etat']); ?></center></td>
						</tr>

						<?php } ?>

					</tbody>
				</table>

<?php

//Inclusion du pied de page
require DIR.'templates/_footer.php';
