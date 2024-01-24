<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* templates/admin/competition/capitaines_sports.php *******/
/* Template des capitaines de la compétition ***************/
/* *********************************************************/
/* Dernière modification : le 19/12/14 *********************/
/* *********************************************************/


$cql = 'T|eq:equipes:|p:participants:|ecoles_sports:eq|e:ecoles:p
G|s:sports:
F|p.nom|p.prenom|p.sexe|e.nom|eq.label|p.licence|p.telephone
B|s.sport|s.sexe|p.nom|p.prenom';

//Inclusion de l'entête de page
require DIR.'templates/admin/_header_admin.php';

?>


				<form method="post" action="<?php url('admin/module/competition/extract'); ?>">
				<nav class="subnav">
					<h2>
						Liste des Capitaines (Par Sport)
						<input type="submit" value="CQL" />
					</h2>

					<input type="hidden" name="cql" value="<?php echo $cql; ?>" />
	 					
					<ul>
						<li><a href="<?php url('admin/module/competition/capitaines'); ?>">Globale</a></li>
						<li><a href="<?php url('admin/module/competition/capitaines_ecoles'); ?>">Par Ecole</a></li>
						<li><a href="<?php url('admin/module/competition/capitaines_sports'); ?>">Par Sport</a></li>
					</ul>
				</nav>
				</form>

				<a class="excel_big" href="?excel">Télécharger en XLSX groupé</a>


				<?php

				foreach ($capitaines as $sid => $capitaines_sport) {

				?>
				
				<h3><?php echo stripslashes($capitaines_sport[0]['sport']).' '.printSexe($capitaines_sport[0]['sexe']); ?></h3>
				
				<a class="excel" href="?excel=<?php echo $sid; ?>">Télécharger en XLSX</a>
				<table class="table-small">
					<thead>
						<tr>
							<td colspan="7">
								<center>Capitaines :  <b><?php echo empty($capitaines_sport[0]['pid']) ? 0 : count($capitaines_sport); ?></b>
								</center>
							</td>
						</tr>

						<tr>
							<th>Nom</th>
							<th>Prenom</th>
							<th>Sexe</th>
							<th>Ecole</th>
							<th>Equipe</th>
							<th>Licence</th>
							<th>Téléphone</th>
						</tr>
					</thead>

					<tbody>

						<?php if (empty($capitaines_sport[0]['pid'])) { ?> 

						<tr class="vide">
							<td colspan="7">Aucun capitaine</td>
						</tr>

						<?php } else foreach ($capitaines_sport as $capitaine) { ?>

						<tr>
							<td><?php echo stripslashes(strtoupper($capitaine['pnom'])); ?></td>
							<td><?php echo stripslashes($capitaine['pprenom']); ?></td>
							<td style="padding:0px">
								<input type="checkbox" <?php if ($capitaine['psexe'] == 'h') echo 'checked' ?> />
								<label class="sexe"></label>
							</td>
							<td><?php echo stripslashes($capitaine['enom']); ?></td>
							<td><?php echo stripslashes($capitaine['label']); ?></td>
							<td><?php echo stripslashes($capitaine['plicence']); ?></td>
							<td><?php echo stripslashes($capitaine['ptelephone']); ?></td>
							
						</tr>

						<?php } ?>

					</tbody>
				</table>

				<?php } ?>


<?php

//Inclusion du pied de page
require DIR.'templates/_footer.php';
