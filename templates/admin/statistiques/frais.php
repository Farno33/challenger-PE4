<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* templates/admin/statistiques/frais.php ******************/
/* Template des frais sur les Ecoles ***********************/
/* *********************************************************/
/* Dernière modification : le 16/02/15 *********************/
/* *********************************************************/


//Inclusion de l'entête de page
require DIR.'templates/admin/_header_admin.php';

?>				
				<style>
				td:not(:first-child) { text-align: center;}
				</style>
			
				<h2>Frais des Ecoles</h2>

				<a class="excel" href="?excel">Télécharger en XLSX</a>
				<table>
					<thead>
						<tr>
							<th>Nom</th>
							<th>Participants</th>
							<th>Retards</th>
							<th>Malus %</th>
							<th>Participation</th>
							<th>Prix gourde</th>
							<th>Frais</th>
							<th>Total</th>
							<th>Payé</th>
							<th>Restant</th>
						</tr>
					</thead>

					<tbody>

						<?php if (!count($ecoles)) { ?> 

						<tr class="vide">
							<td colspan="10">Aucune école</td>
						</tr>

						<?php } else {

							$totaux['amount_retards'] = 0;

							foreach ($ecoles as $ecole) { ?>

						<tr>
							<td><?php echo stripslashes($ecole['nom']); ?></td>
							<td><?php echo $ecole['quota_inscriptions']; ?></td>
							<td><?php echo $ecole['quota_retards']; ?></td>
							<td><?php echo $ecole['malus']; ?> %</td>
							<td><?php echo printMoney($ecole['sum_tarifs']); ?></td>
							<td><?php echo printMoney($ecole['sum_recharges']); ?></td>
							<td><?php echo printMoney($ecole['sum_retards'] * $ecole['malus'] / 100); ?></td>
							<td><b><?php echo printMoney($ecole['sum_tarifs'] + $ecole['sum_recharges'] + $ecole['sum_retards'] * $ecole['malus'] / 100); ?></b></td>
							<td><?php echo printMoney($ecole['sum_paiements']); ?></td>
							<td><?php echo printMoney($ecole['sum_tarifs'] + $ecole['sum_recharges'] + $ecole['sum_retards'] * $ecole['malus'] / 100 - $ecole['sum_paiements']); ?></td>
						</tr>

						<?php 

						$totaux['amount_retards'] += $ecole['sum_retards'] * $ecole['malus'] / 100;

						} ?>

						<tr>
							<th colspan="10"></th>
						</tr>

						<tr>
							<th>TOTAUX</th>
							<td><?php echo $totaux['quota_inscriptions']; ?></td>
							<td><?php echo $totaux['quota_retards']; ?></td>
							<td><i>Moy:</i> <?php echo round($totaux['malus'] / count($ecoles), 2); ?> %</td>
							<td><?php echo printMoney($totaux['sum_tarifs']); ?></td>
							<td><?php echo printMoney($totaux['sum_recharges']); ?></td>
							<td><?php echo printMoney($totaux['amount_retards']); ?></td>
							<td><b><?php echo printMoney($totaux['sum_tarifs'] + $totaux['sum_recharges'] + $totaux['amount_retards']); ?></b></td>
							<td><?php echo printMoney(empty($totaux['sum_paiements']) ? 0 : $totaux['sum_paiements']); ?></td>
							<td><?php echo printMoney($totaux['sum_tarifs'] + $totaux['sum_recharges'] + + $totaux['amount_retards'] - empty($totaux['sum_paiements']) ? 0 : $totaux['sum_paiements']); /* Pas bien genant mais ça cause des warning... donc tant qu'à faire on teste */ ?></td>
						</tr>

						<?php } ?>

					</tbody>
				</table>


<?php

//Inclusion du pied de page
require DIR.'templates/_footer.php';
