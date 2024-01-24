<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* templates/admin/competition/sportifs_ecoles_groupes.php */
/* Template des sportifs de la compétition *****************/
/* *********************************************************/
/* Dernière modification : le 18/12/14 *********************/
/* *********************************************************/

$cql = 'T|p:participants:|sp:sportifs:|eq:equipes:sp|ecoles_sports:eq|s:sports:
G|e:ecoles:p
S|eq
W|capitaine:eq.id_capitaine:p.id
F|s.sport|s.sexe|eq.label|p.nom|p.prenom|p.sexe|p.licence|p.telephone
B|e.nom|s.sport|s.sexe|eq.label|p.nom|p.prenom';


//Inclusion de l'entête de page
require DIR.'templates/admin/_header_admin.php';

?>


				<form method="post" action="<?php url('admin/module/competition/extract'); ?>">
				<nav class="subnav">
					<h2>
						Liste des Sportifs (par Ecole) groupés par Equipe
						<input type="submit" value="CQL" />
					</h2>

					<input type="hidden" name="cql" value="<?php echo $cql; ?>" />
	 					
					<ul>
						<li><a href="<?php url('admin/module/competition/sportifs'); ?>">Globale</a></li>
						<li>
							<span>Par Ecole</span>
							<ul>
								<li><a href="<?php url('admin/module/competition/sportifs_ecoles'); ?>">Non groupé</a></li>
								<li><a href="<?php url('admin/module/competition/sportifs_ecoles_sports'); ?>">% Sport</a></li>
								<li><a href="<?php url('admin/module/competition/sportifs_ecoles_equipes'); ?>">% Equipe</a></li>
							</ul>
						<li>
							<span>Par Sport</span>
							<ul>
								<li><a href="<?php url('admin/module/competition/sportifs_sports'); ?>">Non groupé</a></li>
								<li><a href="<?php url('admin/module/competition/sportifs_sports_ecoles'); ?>">% Ecole</a></li>
								<li><a href="<?php url('admin/module/competition/sportifs_sports_equipes'); ?>">% Equipe</a></li>
							</ul>
						</li>
					</ul>
				</nav>
				</form>

				<a class="excel_big" href="?excel">Télécharger en XLSX groupé</a>


				<?php

				foreach ($sportifs as $eid => $sportifs_ecole) {

					$sport = null;

				?>
				
				<h3><?php echo stripslashes($sportifs_ecole[0]['nom']); ?></h3>
				
				<a class="excel" href="?excel=<?php echo $eid; ?>">Télécharger en XLSX</a>
				<table>
					<thead>
						<tr>
							<td colspan="8">
								<center>
								<?php if ($sportifs_ecole[0]['quota_sportif'] !== null) { ?>
								Quota Sportif : <b><?php echo $sportifs_ecole[0]['quota_sportif']; ?></b>
								&nbsp; &nbsp; / &nbsp; &nbsp;
								<?php } ?>
								Sportifs :  <b><?php echo empty($sportifs_ecole[0]['pid']) ? 0 : count($sportifs_ecole); ?></b>
								</center>
							</td>
						</tr>

						<tr>
							<th>Sport</th>
							<th>Equipe</th>
							<th style="width:60px">Capitaine</th>
							<th>Nom</th>
							<th>Prenom</th>
							<th style="width:60px">Sexe</th>
							<th>Licence</th>
							<th>Téléphone</th>
						</tr>
					</thead>

					<tbody>

						<?php if (empty($sportifs_ecole[0]['pid'])) { ?> 

						<tr class="vide">
							<td colspan="8">Aucun sportif</td>
						</tr>

						<?php } 

						else foreach ($sportifs_ecole as $sportif) {

							if (!empty($equipe) &&
								$sportif['eqid'] != $equipe) { 

						?>

						<tr>
							<th colspan="8"></th>
						</tr>

						<?php }

						$equipe = $sportif['eqid'];
						
						?>

						<tr>
							<td><?php echo stripslashes($sportif['sport']).' '.printSexe($sportif['ssexe']); ?></td>
							<td><?php echo stripslashes($sportif['label']); ?></td>
							<td style="padding:0px">													
								<input type="checkbox" <?php if ($sportif['id_capitaine'] == $sportif['pid']) echo 'checked'; ?> />
								<label class="capitaine"></label>
							</td>
							<td><?php echo stripslashes(strtoupper($sportif['pnom'])); ?></td>
							<td><?php echo stripslashes($sportif['pprenom']); ?></td>
							<td style="padding:0px">													
								<input type="checkbox" <?php if ($sportif['psexe'] == 'h') echo 'checked'; ?> />
								<label class="sexe"></label>
							</td>
							<td><?php echo stripslashes($sportif['plicence']); ?></td>
							<td><?php echo stripslashes($sportif['ptelephone']); ?></td>
							
						</tr>

						<?php } ?>

					</tbody>
				</table>

				<?php } ?>


<?php

//Inclusion du pied de page
require DIR.'templates/_footer.php';
