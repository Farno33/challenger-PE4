<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* templates/admin/competition/pompoms.php *****************/
/* Template des pompoms de la compétition ******************/
/* *********************************************************/
/* Dernière modification : le 18/12/14 *********************/
/* *********************************************************/
	
$cql = 'T|p:participants:|e:ecoles:
C|p.pompom:1
F|p.nom|p.prenom|p.sexe|p.sportif|e.nom|p.telephone';



//Inclusion de l'entête de page
require DIR.'templates/admin/_header_admin.php';

?>

				<form method="post" action="<?php url('admin/module/competition/extract'); ?>">
				<nav class="subnav">
					<h2>
						Liste des Pompoms
						<input type="submit" value="CQL" />
					</h2>

					<input type="hidden" name="cql" value="<?php echo $cql; ?>" />
	 					
					<ul>
						<li><a href="<?php url('admin/module/competition/pompoms'); ?>">Globale</a></li>
						<li><a href="<?php url('admin/module/competition/pompoms_ecoles'); ?>">Par Ecole</a></li>
					</ul>
				</nav>
				</form>
				
				<a class="excel" href="?excel">Télécharger en XLSX</a>
				<table class="table-small">
					<thead>
						<tr>
							<td colspan="6">
								<center>Pompoms :  <b><?php echo count($pompoms); ?></b>
								</center>
							</td>
						</tr>

						<tr>
							<th>Nom</th>
							<th>Prenom</th>
							<th>Sexe</th>
							<th>Sportif</th>
							<th>Ecole</th>
							<th>Téléphone</th>
						</tr>
					</thead>

					<tbody>

						<?php if (!count($pompoms)) { ?> 

						<tr class="vide">
							<td colspan="6">Aucun pompom</td>
						</tr>

						<?php } else foreach ($pompoms as $pompom) { ?>

						<tr>
							<td><?php echo stripslashes(strtoupper($pompom['pnom'])); ?></td>
							<td><?php echo stripslashes($pompom['pprenom']); ?></td>
							<td style="padding:0px">
								<input type="checkbox" <?php if ($pompom['psexe'] == 'h') echo 'checked' ?> />
								<label class="sexe"></label>
							</td>
							<td style="padding:0px">
								<input type="checkbox" <?php if ($pompom['psportif']) echo 'checked' ?> />
								<label></label>
							</td>
							<td><?php echo stripslashes($pompom['enom']); ?></td>
							<td><?php echo stripslashes($pompom['ptelephone']); ?></td>
							
						</tr>

						<?php } ?>

					</tbody>
				</table>


<?php

//Inclusion du pied de page
require DIR.'templates/_footer.php';
