<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* templates/admin/statistiques/ecoles.php *****************/
/* Template des stats sur les Ecoles ***********************/
/* *********************************************************/
/* Dernière modification : le 23/01/15 *********************/
/* *********************************************************/


//Inclusion de l'entête de page
require DIR.'templates/admin/_header_admin.php';

?>
			
				<h2>Statistiques sur les Ecoles</h2>

				<a class="excel" href="?excel">Télécharger en XLSX</a>
				<table>
					<thead>
						<tr>
							<th>Nom</th>
							<th style="width:150px"><small>Inscrip. / Quota</small></th>
							<th>Etat</th>
							<th style="width:60px"><small>Caution</small></th>
							<th style="width:60px"><small>C. Logement</small></th>
							<th style="width:60px">RI</th>
							<th style="width:60px"><small>Charte</small></th>
							<th>Connexion</th>
						</tr>
					</thead>

					<tbody>

						<?php if (!count($ecoles)) { ?> 

						<tr class="vide">
							<td colspan="8">Aucune école</td>
						</tr>

						<?php } foreach ($ecoles as $ecole) { ?>

						<tr>
							<td><?php echo stripslashes($ecole['nom']); ?></td>
							<td><center><?php echo $ecole['quota_inscriptions'].($ecole['quota_total'] !== null ? ' / <b>'.$ecole['quota_total'] : '').'</b>'; ?></center></td>
							<td><?php echo printEtatEcole($ecole['etat_inscription']); ?></td>
							<td style="padding:0px">													
								<input type="checkbox" <?php if ($ecole['caution_recue']) echo 'checked '; ?>/>
								<label></label>
							</td>
							<td style="padding:0px">													
								<input type="checkbox" <?php if ($ecole['caution_logement']) echo 'checked '; ?>/>
								<label></label>
							</td>
							<td><center><?php echo $ecole['quota_signatures'].' / '.$ecole['quota_inscriptions']; ?></center></td>
							<td style="padding:0px">													
								<input type="checkbox" <?php if ($ecole['charte_acceptee']) echo 'checked '; ?>/>
								<label></label>
							</td>
							<td><?php echo empty($ecole['connexion']) ? '<i>Aucune connexion</i>' : printDateTime($ecole['connexion']); ?></td>
						</tr>

						<?php } ?>

					</tbody>
				</table>


<?php

//Inclusion du pied de page
require DIR.'templates/_footer.php';
