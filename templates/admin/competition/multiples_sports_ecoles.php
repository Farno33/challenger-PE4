<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* templates/admin/competition/pompoms_ecoles.php **********/
/* Template des pompoms de la compétition *****************/
/* *********************************************************/
/* Dernière modification : le 18/12/14 *********************/
/* *********************************************************/


$cql = 'T|p:participants:|sp:sportifs:|eq:equipes:sp|ecoles_sports:eq|s:sports:
S2|p
G|e:ecoles:p
W|capitaine:p.id:eq.id_capitaine
F|p.nom|p.prenom|p.sexe|p.licence|p.telephone|s.sport|s.sexe|eq.label
B|e.nom|p.nom|p.prenom';


//Inclusion de l'entête de page
require DIR.'templates/admin/_header_admin.php';

?>

				<form method="post" action="<?php url('admin/module/competition/extract'); ?>">
				<nav class="subnav">
					<h2>
						Liste des Multi-Sportifs (Par Ecole)
						<input type="submit" value="CQL" />
					</h2>

					<input type="hidden" name="cql" value="<?php echo $cql; ?>" />
	 					
					<ul>
						<li><a href="<?php url('admin/module/competition/multiples_sports'); ?>">Globale</a></li>
						<li><a href="<?php url('admin/module/competition/multiples_sports_ecoles'); ?>">Par Ecole</a></li>
					</ul>
				</nav>
				</form>

				<a class="excel_big" href="?excel">Télécharger en XLSX groupé</a>


				<?php

				foreach ($multiples_sports as $eid => $multiples_sports_ecole) {
					$data = $multiples_sports_ecole[array_keys($multiples_sports_ecole)[0]][0];
				?>
				
				<h3><?php echo stripslashes($data['nom']); ?></h3>
				
				<a class="excel" href="?excel=<?php echo $eid; ?>">Télécharger en XLSX</a>
				<table class="table-small">
					<thead>
						<tr>
							<td colspan="6">
								<center>
								Multi-Sportifs :  <b><?php echo empty($data['pid']) ? 0 : count($multiples_sports_ecole); ?></b>
								</center>
							</td>
						</tr>

						<tr>
							<th><small>Nom / Prénom</small></th>
							<th><small>Licence / Téléphone</small></th>
							<th style="width:60px">Sexe</th>

							<th style="width:60px"><small>Capitaine</small></th>
							<th>Sport</th>
							<th>Equipe</th>
						</tr>
					</thead>

					<tbody>

						<?php 
						$first = true;
						if (empty($data['pid'])) { ?> 

						<tr class="vide">
							<td colspan="6">Aucun multi-sportif</td>
						</tr>

						<?php } else foreach ($multiples_sports_ecole as $sports_sportif) { 
							foreach ($sports_sportif as $k => $sportif) { 

						if (!$first && $k == 0) echo '<tr><th colspan="6"></th></tr>';
						$first = false;

						?>

						<tr>

							<?php if ($k == 0) { ?>

							<td rowspan="<?php echo count($sports_sportif); ?>">
								<?php echo stripslashes(strtoupper($sportif['pnom'])); ?><br />
								<small><?php echo stripslashes($sportif['pprenom']); ?></small>
							</td>
							<td rowspan="<?php echo count($sports_sportif); ?>">
								<?php echo stripslashes($sportif['licence']); ?><br />
								<small><?php echo stripslashes($sportif['ptelephone']); ?></small>
							</td>
							<td rowspan="<?php echo count($sports_sportif); ?>" style="padding:0px">													
								<input type="checkbox" <?php if ($sportif['psexe'] == 'h') echo 'checked'; ?> />
								<label class="sexe"></label>
							</td>
								
							<?php } ?>

							<td style="padding:0px">
								<input type="checkbox" <?php if ($sportif['capitaine']) echo 'checked'; ?> />
								<label class="capitaine"></label>
							</td>
							<td><?php echo stripslashes($sportif['sport']).' '.printSexe($sportif['ssexe']); ?></td>
							<td><?php echo stripslashes($sportif['label']); ?></td>
						</tr>

						<?php } } ?>

					</tbody>
				</table>

				<?php } ?>


<?php

//Inclusion du pied de page
require DIR.'templates/_footer.php';
