<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* templates/admin/competition/sans_sport_ecoles.php *******/
/* Template des sportifs sans sports par école**************/
/* *********************************************************/
/* Dernière modification : le 20/01/15 *********************/
/* *********************************************************/

$cql = 'T|p:participants:
TL|sp:sportifs:
G|ecoles
C|sp.id:null
C|p.sportif:1
F|p.nom|p.prenom|p.sexe|p.licence|p.telephone';


//Inclusion de l'entête de page
require DIR.'templates/admin/_header_admin.php';

?>

				
				<form method="post" action="<?php url('admin/module/competition/extract'); ?>">
				<nav class="subnav">
					<h2>
						Liste des Sportifs sans Sport (Par Ecole)
						<input type="submit" value="CQL" />
					</h2>

					<input type="hidden" name="cql" value="<?php echo $cql; ?>" />
	 					
					<ul>
						<li><a href="<?php url('admin/module/competition/sans_sport'); ?>">Globale</a></li>
						<li><a href="<?php url('admin/module/competition/sans_sport_ecoles'); ?>">Par Ecole</a></li>
					</ul>
				</nav>
				</form>

				<a class="excel_big" href="?excel">Télécharger en XLSX groupé</a>


				<?php

				foreach ($sans_sport as $eid => $sans_sport_ecole) {

				?>
				
				<h3><?php echo stripslashes($sans_sport_ecole[0]['enom']); ?></h3>
				
				<a class="excel" href="?excel=<?php echo $eid; ?>">Télécharger en XLSX</a>
				<table class="table-small">
					<thead>
						<tr>
							<td colspan="7">
								<center>Concernés :  <b><?php echo empty($sans_sport_ecole[0]['id']) ? 0 : count($sans_sport_ecole); ?></b></center>
							</td>
						</tr>

						<tr>
							<th>Nom</th>
							<th>Prenom</th>
							<th>Sexe</th>
							<th>Licence</th>
							<th>Téléphone</th>
						</tr>
					</thead>

					<tbody>

						<?php if (empty($sans_sport_ecole[0]['id'])) { ?> 

						<tr class="vide">
							<td colspan="7">Aucun participant</td>
						</tr>

						<?php } else foreach ($sans_sport_ecole as $participant) { ?>

						<tr>
							<td><?php echo stripslashes(strtoupper($participant['nom'])); ?></td>
							<td><?php echo stripslashes($participant['prenom']); ?></td>
							<td style="padding:0px">													
								<input type="checkbox" <?php if ($participant['sexe'] == 'h') echo 'checked'; ?> />
								<label class="sexe"></label>
							</td>
							<td><?php echo stripslashes($participant['licence']); ?></td>
							<td><?php echo stripslashes($participant['telephone']); ?></td>
							
						</tr>

						<?php } ?>

					</tbody>
				</table>

				<?php } ?>


<?php

//Inclusion du pied de page
require DIR.'templates/_footer.php';
