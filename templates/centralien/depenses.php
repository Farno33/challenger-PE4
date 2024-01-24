<?//php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* templates/ecoles/accueil.php ****************************/
/* Template de l'accueil pour les écoles *******************/
/* *********************************************************/
/* Dernière modification : le 11/12/14 *********************/
/* *********************************************************/

require DIR.'templates/centralien/_header_centralien.php';
?>

				<?//php if($uid && $uid != ""){?>
				<h2>
					<!--<a href="http://rechargement_challenge.eclair.ec-lyon.fr/<?//php echo $uid.'/'.$user['prenom'].'/'.$user['nom'];?>">Recharge ta carte participant ici</a>-->
				</h2>
				<?//php }?>

				<!--<h2>
					<?//php echo $user['prenom'].' '.$user['nom']; ?> : 
					<span style="color:green"><?//php
						//$total = 0;
						//foreach ($data->solde as $solde)
						//	$total += $solde->solde;
						//echo sprintf("%.02f€", $total/100); ?></span>
				</h2>

				<table class="table-small">
					<tr>
						<th>Date/Heure</th>
						<th>Produit</th>
						<th>Bar</th>
						<th>Quantité</th>
						<th>Total</th>
					</tr>

					<?//php foreach ($data->ventes as $depense) { ?>

					<tr>
						<td><?//php echo printDateTime($depense->datetime); ?></td>
						<td><?//php echo $depense->produit; ?></td>
						<td><?//php echo $depense->bar; ?></td>
						<td><?//php echo $depense->quantity; ?></td>
						<td style="color:red"><?//php echo printMoney($depense->prix_total/100); ?></td>
					</tr>

					<?//php } if (!count($data->ventes)) { ?>

					<tr class="vide">
						<td colspan="5">Aucune dépense pour le moment</td>
					</tr>

					<?//php } ?>

				</table>

				<table class="table-small">
					<tr>
						<th>Date/Heure</th>
						<th>Recharge</th>
					</tr>

					<?//php foreach ($data->recharges as $recharge) { ?>

					<tr>
						<td><?//php echo printDateTime($recharge->datetime); ?></td>
						<td style="color:green"><?//php echo printMoney($recharge->montant/100); ?></td>
					</tr>

					<?//php } if (!count($data->recharges)) { ?>

					<tr class="vide">
						<td colspan="2">Aucune recharge pour le moment</td>
					</tr>

					<?//php } ?>

				</table>-->

<?//php

//Inclusion du pied de page
require DIR.'templates/_footer.php';
