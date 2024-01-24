<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* templates/admin/competition/fanfarons_ecoles.php ********/
/* Template des fanfarons de la compétition ****************/
/* *********************************************************/
/* Dernière modification : le 18/12/14 *********************/
/* *********************************************************/


$cql = 'T|p:participants:
G|ecoles
C|p.fanfaron:1
F|p.nom|p.prenom|p.sexe|p.sportif|p.telephone';


//Inclusion de l'entête de page
require DIR.'templates/admin/_header_admin.php';

?>
				
				<form method="post" action="<?php url('admin/module/competition/extract'); ?>">
				<nav class="subnav">
					<h2>
						Liste des Fanfarons (Par Ecole)
						<input type="submit" value="CQL" />
					</h2>

					<input type="hidden" name="cql" value="<?php echo $cql; ?>" />
	 					
					<ul>
						<li><a href="<?php url('admin/module/competition/fanfarons'); ?>">Globale</a></li>
						<li><a href="<?php url('admin/module/competition/fanfarons_ecoles'); ?>">Par Ecole</a></li>
					</ul>
				</nav>
				</form>

				<a class="excel_big" href="?excel">Télécharger en XLSX groupé</a>


				<?php

				foreach ($fanfarons as $eid => $fanfarons_ecole) {

				?>
				
				<h3><?php echo stripslashes($fanfarons_ecole[0]['nom']); ?></h3>
				
				<a class="excel" href="?excel=<?php echo $eid; ?>">Télécharger en XLSX</a>
				<table class="table-small">
					<thead>
						<tr>
							<td colspan="5">
								<center>
								<?php if ($fanfarons_ecole[0]['quota_fanfaron'] !== null) { ?>
								Quota Fanfaron : <b><?php echo $fanfarons_ecole[0]['quota_fanfaron']; ?></b>
								&nbsp; &nbsp; / &nbsp; &nbsp;
								<?php } ?>
								Fanfarons :  <b><?php echo empty($fanfarons_ecole[0]['pid']) ? 0 : count($fanfarons_ecole); ?></b>
								</center>
							</td>
						</tr>

						<tr>
							<th>Nom</th>
							<th>Prenom</th>
							<th style="width:60px">Sexe</th>
							<th style="width:60px">Sportif</th>
							<th>Téléphone</th>
						</tr>
					</thead>

					<tbody>

						<?php if (empty($fanfarons_ecole[0]['pid'])) { ?> 

						<tr class="vide">
							<td colspan="5">Aucun fanfaron</td>
						</tr>

						<?php } else foreach ($fanfarons_ecole as $fanfaron) { ?>

						<tr>
							<td><?php echo stripslashes(strtoupper($fanfaron['pnom'])); ?></td>
							<td><?php echo stripslashes($fanfaron['pprenom']); ?></td>
							<td style="padding:0px">
								<input type="checkbox" <?php if ($fanfaron['psexe'] == 'h') echo 'checked' ?> />
								<label class="sexe"></label>
							</td>
							<td style="padding:0px">
								<input type="checkbox" <?php if ($fanfaron['psportif']) echo 'checked' ?> />
								<label></label>
							</td>
							<td><?php echo stripslashes($fanfaron['ptelephone']); ?></td>
							
						</tr>

						<?php } ?>

					</tbody>
				</table>

				<?php } ?>


<?php

//Inclusion du pied de page
require DIR.'templates/_footer.php';
