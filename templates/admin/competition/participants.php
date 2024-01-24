<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* templates/admin/competition/participants.php ************/
/* Template des participants de la compétition *************/
/* *********************************************************/
/* Dernière modification : le 23/02/15 *********************/
/* *********************************************************/

$cql = 'T|p:participants:|tarifs_ecoles|t:tarifs:|e:ecoles:
F|p.nom|p.prenom|p.sexe|e.nom|p.sportif|p.fanfaron|p.pompom|p.cameraman|p.telephone|p.recharge|t.nom|t.logement';

//Inclusion de l'entête de page
require DIR.'templates/admin/_header_admin.php';

?>
				
				<form method="post" action="<?php url('admin/module/competition/extract'); ?>">
				<nav class="subnav">
					<h2>
						Liste des Participants
						<input type="submit" value="CQL" />
					</h2>

					<input type="hidden" name="cql" value="<?php echo $cql; ?>" />
	 				
					<ul>
						<li><a href="<?php url('admin/module/competition/participants'); ?>">Globale</a></li>
						<li><a href="<?php url('admin/module/competition/participants_ecoles'); ?>">Par Ecole</a></li>
					</ul>
				</nav>
				</form>
				
				<a class="excel" href="?excel">Télécharger en XLSX</a>

				<table>
					<thead>
						<tr>
							<td colspan="12">
								<center>Participants :  <b><?php echo count($participants); ?></b>
								</center>
							</td>
						</tr>

						<tr>
							<th>Nom</th>
							<th>Prenom</th>
							<th>Sexe</th>
							<th>Ecole</th>
							<th style="width:40px"><small>Sportif</small></th>
							<th style="width:40px"><small>F.</small></th>
							<th style="width:40px"><small>P.</small></th>
							<th style="width:40px"><small>C.</small></th>
							<th>Téléphone</th>
							<th>Gourde</th>
							<th>Tarif</th>
							<th style="width:60px">Logement</th>
						</tr>
					</thead>

					<tbody>

						<?php if (!count($participants)) { ?> 

						<tr class="vide">
							<td colspan="12">Aucun participant</td>
						</tr>

						<?php } else foreach ($participants as $participant) { ?>

						<tr>
							<td><?php echo stripslashes(strtoupper($participant['pnom'])); ?></td>
							<td><?php echo stripslashes($participant['pprenom']); ?></td>
							<td style="padding:0px">													
								<input type="checkbox" <?php if ($participant['psexe'] == 'h') echo 'checked'; ?> />
								<label class="sexe"></label>
							</td>
							<td><?php echo stripslashes($participant['enom']); ?></td>
							<td style="padding:0px">													
								<input type="checkbox" <?php if ($participant['sportif']) echo 'checked'; ?> />
								<label></label>
							</td>
							<td style="padding:0px">													
								<input type="checkbox" <?php if ($participant['fanfaron']) echo 'checked'; ?> />
								<label class="extra-fanfaron"></label>
							</td>
							<td style="padding:0px">													
								<input type="checkbox" <?php if ($participant['pompom']) echo 'checked'; ?> />
								<label class="extra-pompom"></label>
							</td>
							<td style="padding:0px">													
								<input type="checkbox" <?php if ($participant['cameraman']) echo 'checked'; ?> />
								<label class="extra-video"></label>
							</td>
							<td><?php echo stripslashes($participant['ptelephone']); ?></td>
							<td><?php echo printMoney($participant['recharge']); ?></td>
							<td><?php echo stripslashes($participant['tnom']); ?></td>
							<td style="padding:0px">					
								<input type="checkbox" <?php if ($participant['logement']) echo 'checked' ?> />
								<label class="package"></label>
							</td>
							
						</tr>

						<?php } ?>

					</tbody>
				</table>


<?php

//Inclusion du pied de page
require DIR.'templates/_footer.php';
