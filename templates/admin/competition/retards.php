<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* templates/admin/competition/fanfarons.php ***************/
/* Template des fanfarons de la compétition ****************/
/* *********************************************************/
/* Dernière modification : le 18/12/14 *********************/
/* *********************************************************/

$cql = 'T|p:participants:|e:ecoles:|tarifs_ecoles|t:tarifs:
F|p.nom|p.prenom|p.sexe|p.sportif|t.nom|t.logement|p.hors_malus|e.nom|p.telephone
C|p.date_inscription:>:"'.APP_DATE_MALUS.'"';

//Inclusion de l'entête de page
require DIR.'templates/admin/_header_admin.php';

?>
				
				<form method="post" action="<?php url('admin/module/competition/extract'); ?>">
				<nav class="subnav">
					<h2>
						Liste des Retards
						<input type="submit" value="CQL" />
					</h2>

					<input type="hidden" name="cql" value='<?php echo $cql; ?>' />
	 					
					<ul>
						<li><a href="<?php url('admin/module/competition/retards'); ?>">Globale</a></li>
						<li><a href="<?php url('admin/module/competition/retards_ecoles'); ?>">Par Ecole</a></li>
					</ul>
				</nav>
				</form>
				
				<a class="excel" href="?excel">Télécharger en XLSX</a>
				<table>
					<thead>
						<tr>
							<td colspan="9">
								<center>Retards :  <b><?php echo count($retards); ?></b>
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
							<th>Ecole</th>
							<th>Téléphone</th>
						</tr>
					</thead>

					<tbody>

						<?php if (!count($retards)) { ?> 

						<tr class="vide">
							<td colspan="9">Aucun retard</td>
						</tr>

						<?php } else foreach ($retards as $retard) { ?>

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
							<td><?php echo stripslashes($retard['enom']); ?></td>
							<td><?php echo stripslashes($retard['ptelephone']); ?></td>
							
						</tr>

						<?php } ?>

					</tbody>
				</table>


<?php

//Inclusion du pied de page
require DIR.'templates/_footer.php';
