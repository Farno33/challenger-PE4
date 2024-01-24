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

$cql = 'T|p:participants:|tarifs_ecoles|t:tarifs:
G|ecoles:p
F|p.nom|p.prenom|p.sexe|p.sportif|t.nom|t.logement|p.hors_malus|p.telephone
C|p.date_inscription:>:"'.APP_DATE_MALUS.'"';


//Inclusion de l'entête de page
require DIR.'templates/admin/_header_admin.php';

?>
				
				<form method="post" action="<?php url('admin/module/competition/extract'); ?>">
				<nav class="subnav">
					<h2>
						Liste des Retards (Par Ecole)
						<input type="submit" value="CQL" />
					</h2>

					<input type="hidden" name="cql" value='<?php echo $cql; ?>' />

					<ul>
						<li><a href="<?php url('admin/module/competition/retards'); ?>">Globale</a></li>
						<li><a href="<?php url('admin/module/competition/retards_ecoles'); ?>">Par Ecole</a></li>
					</ul>
				</nav>
				</form>

				<a class="excel_big" href="?excel">Télécharger en XLSX groupé</a>


				<?php

				foreach ($retards as $eid => $retards_ecole) {

				?>
				
				<h3><?php echo stripslashes($retards_ecole[0]['nom']); ?></h3>
				
				<a class="excel" href="?excel=<?php echo $eid; ?>">Télécharger en XLSX</a>
				<table>
					<thead>
						<tr>
							<td colspan="8">
								<center>
									Retards :  <b><?php echo empty($retards_ecole[0]['pid']) ? 0 : count($retards_ecole); ?></b>
								</center>
							</td>
						</tr>

						<tr>
							<th>Nom</th>
							<th>Prenom</th>
							<th style="width:60px">Sexe</th>
							<th style="width:60px">Sportif</th>
							<th>Tarif</th>
							<th style="width:60px">Logement</th>
							<th style="width:60px">Excuse</th>
							<th>Téléphone</th>
						</tr>
					</thead>

					<tbody>

						<?php if (empty($retards_ecole[0]['pid'])) { ?> 

						<tr class="vide">
							<td colspan="8">Aucun retard</td>
						</tr>

						<?php } else foreach ($retards_ecole as $retard) { ?>

						<tr>
							<td><?php echo stripslashes(strtoupper($retard['pnom'])); ?></td>
							<td><?php echo stripslashes($retard['pprenom']); ?></td>
							<td style="padding:0px">
								<input type="checkbox" <?php if ($retard['psexe'] == 'h') echo 'checked' ?> />
								<label class="sexe"></label>
							</td>
							<td style="padding:0px">
								<input type="checkbox" <?php if ($retard['psportif']) echo 'checked' ?> />
								<label></label>
							</td>
							<td><?php echo stripslashes($retard['tarif']); ?></td>
							<td style="padding:0px">
								<input type="checkbox" <?php if ($retard['logement']) echo 'checked '; ?>/>
								<label class="package"></label>
							</td>
							<td style="padding:0px">
								<input type="checkbox" <?php if ($retard['hors_malus']) echo 'checked '; ?>/>
								<label class="retard-excuse"></label>
							</td>
							<td><?php echo stripslashes($retard['ptelephone']); ?></td>
							
						</tr>

						<?php } ?>

					</tbody>
				</table>

				<?php } ?>


<?php

//Inclusion du pied de page
require DIR.'templates/_footer.php';
