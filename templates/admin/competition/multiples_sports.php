<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* templates/admin/competition/sans_sport.php **************/
/* Template des sportifs sans sport ************************/
/* *********************************************************/
/* Dernière modification : le 20/01/15 *********************/
/* *********************************************************/


$cql = 'T|p:participants:|sp:sportifs:|e:ecoles:|eq:equipes:sp|ecoles_sports:eq|s:sports:
S2|p
W|capitaine:p.id:eq.id_capitaine
F|p.nom|p.prenom|p.sexe|p.licence|p.telephone|e.nom|s.sport|s.sexe|eq.label
B|p.nom|p.prenom';

//Inclusion de l'entête de page
require DIR.'templates/admin/_header_admin.php';

?>
				<form method="post" action="<?php url('admin/module/competition/extract'); ?>">
				<nav class="subnav">
					<h2>
						Liste des Multi-Sportifs
						<input type="submit" value="CQL" />
					</h2>

					<input type="hidden" name="cql" value="<?php echo $cql; ?>" />
	 					
					<ul>
						<li><a href="<?php url('admin/module/competition/multiples_sports'); ?>">Globale</a></li>
						<li><a href="<?php url('admin/module/competition/multiples_sports_ecoles'); ?>">Par Ecole</a></li>
					</ul>
				</nav>
				</form>
				
				<a class="excel" href="?excel">Télécharger en XLSX</a>
				<table class="table-small">
					<thead>
						<tr>
							<td colspan="7">
								<center>Multi-sportifs :  <b><?php echo count($multiples_sports); ?></b>
								</center>
							</td>
						</tr>

						<tr>
							<th><small>Nom / Prénom</small></th>
							<th><small>Licence / Téléphone</small></th>
							<th style="width:60px">Sexe</th>
							<th>Ecole</th>

							<th style="width:60px"><small>Capitaine</small></th>
							<th>Sport</th>
							<th>Equipe</th>
						</tr>
					</thead>

					<tbody>

						<?php 
						$first = true;
						if (!count($multiples_sports)) { ?> 

						<tr class="vide">
							<td colspan="7">Aucun multi-sportif</td>
						</tr>

						<?php 

						} else foreach ($multiples_sports as $sports_sportif) { 
							foreach ($sports_sportif as $k => $sportif) { 
						
						if (!$first && $k == 0) echo '<tr><th colspan="7"></th></tr>';
						$first = false;

						?>

						<tr>
							
							<?php if ($k == 0) { ?>

							<td rowspan="<?php echo count($sports_sportif); ?>">
								<?php echo stripslashes(strtoupper($sportif['nom'])); ?><br />
								<small><?php echo stripslashes($sportif['prenom']); ?></small>
							</td>
							<td rowspan="<?php echo count($sports_sportif); ?>">
								<?php echo stripslashes($sportif['licence']); ?><br />
								<small><?php echo stripslashes($sportif['telephone']); ?></small>
							</td>
							<td rowspan="<?php echo count($sports_sportif); ?>" style="padding:0px">													
								<input type="checkbox" <?php if ($sportif['sexe'] == 'h') echo 'checked'; ?> />
								<label class="sexe"></label>
							</td>
							<td rowspan="<?php echo count($sports_sportif); ?>"><?php echo stripslashes($sportif['enom']); ?></td>
								
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


<?php

//Inclusion du pied de page
require DIR.'templates/_footer.php';
