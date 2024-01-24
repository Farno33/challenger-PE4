<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* templates/admin/competition/sportifs.php ****************/
/* Template des sportifs de la compétition *****************/
/* *********************************************************/
/* Dernière modification : le 18/12/14 *********************/
/* *********************************************************/

$cql = "T|eq:equipes:|p:participants:|e:ecoles:|ecoles_sports:eq|s:sports:
F|p.nom|p.prenom|p.sexe|e.nom|s.sport|s.sexe|s.individuel|eq.label|p.licence|p.telephone
B|p.nom|p.prenom";

//Inclusion de l'entête de page
require DIR.'templates/admin/_header_admin.php';

?>
				
				<form method="post" action="<?php url('admin/module/competition/extract'); ?>">
				<nav class="subnav">
					<h2>
						Liste des Capitaines
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
				
				<a class="excel" href="?excel">Télécharger en XLSX</a>
				<table>
					<thead>
						<tr>
							<td colspan="9">
								<center>Capitaines :  <b><?php echo count($capitaines); ?></b>
								</center>
							</td>
						</tr>

						<tr>
							<th>Nom</th>
							<th>Prenom</th>
							<th>Sexe</th>
							<th>Ecole</th>
							<th>Sport</th>
							<th style="width:50px"><small>Individuel</small></th>
							<th>Equipe</th>
							<th>Licence</th>
							<th>Téléphone</th>
						</tr>
					</thead>

					<tbody>

						<?php if (!count($capitaines)) { ?> 

						<tr class="vide">
							<td colspan="9">Aucun capitaine</td>
						</tr>

						<?php } else foreach ($capitaines as $capitaine) { ?>

						<tr>
							<td><?php echo stripslashes(strtoupper($capitaine['pnom'])); ?></td>
							<td><?php echo stripslashes($capitaine['pprenom']); ?></td>
							<td style="padding:0px">
								<input type="checkbox" <?php if ($capitaine['psexe'] == 'h') echo 'checked' ?> />
								<label class="sexe"></label>
							</td>
							<td><?php echo stripslashes($capitaine['enom']); ?></td>
							<td><?php echo stripslashes($capitaine['sport']).' '.printSexe($capitaine['ssexe']); ?></td>
							<td style="padding:0px">
								<input type="checkbox" <?php if ($capitaine['sindividuel']) echo 'checked' ?> />
								<label></label>
							</td>
							<td><?php echo stripslashes($capitaine['label']); ?></td>
							<td><?php echo stripslashes($capitaine['plicence']); ?></td>
							<td><?php echo stripslashes($capitaine['ptelephone']); ?></td>
							
						</tr>

						<?php } ?>

					</tbody>
				</table>


<?php

//Inclusion du pied de page
require DIR.'templates/_footer.php';
