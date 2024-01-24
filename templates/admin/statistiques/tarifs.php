<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* templates/admin/statistiques/tari.php *******************/
/* Template des stats sur les tarifs ***********************/
/* *********************************************************/
/* Dernière modification : le 23/01/15 *********************/
/* *********************************************************/


//Inclusion de l'entête de page
require DIR.'templates/admin/_header_admin.php';

?>	
				<style>
				.subnav li, 
				.subnav li ul { width:200px !important;}
				</style>
			
				<nav class="subnav">
					<h2>Statistiques sur les tarifs</h2>
					<ul>
						<li>
							<span>Ecoles non lyonnaises</span>
							<ul>
								<li><a href="?e=0&s=0">Tarifs non sportifs</a></li>
								<li><a href="?e=0&s=1">Tarifs sportifs</a></li>
							</ul>
						</li>

						<li>
							<span>Ecoles lyonnaises</span>
							<ul>
								<li><a href="?e=1&s=0">Tarifs non sportifs</a></li>
								<li><a href="?e=1&s=1">Tarifs sportifs</a></li>
							</ul>
						</li>
					</ul>
				</nav>

				<?php
				
				$totaux = [];

				foreach ($typesEcoles as $typeEcole => $labelEcole) {

					foreach ($typesSportifs as $typeSportif => $labelSportif) {

					$noprint = (!isset($_GET['e']) || 
						!isset($_GET['s']) ||
						$_GET['e'] != $typeEcole ||
						$_GET['s'] != $typeSportif);

				if (!$noprint) {

				?>

				<a name="tarifs_<?php echo $typeSportif.'_'.$typeEcole; ?>"></a>
				<h3><?php echo $labelEcole.' / '.$labelSportif; ?></h3>

				<a class="excel" href="?excel=&e=<?php echo $_GET['e']; ?>&s=<?php echo $_GET['s']; ?>">Télécharger en XLSX</a>
				<center>
				<table style="width:auto">
					<thead>
						<tr>
							<th>Ecole</th>

							<?php }
							
							$nbTarifs = 0;
							$total = 0;
							$totalNb = 0;

							foreach ($tarifs as $id => $tarif) {

								if ($typeSportif != $tarif['sportif'] ||
									(int) $tarif['ecole_lyonnaise'] != $typeEcole)
									continue;


									
								if (!isset($totaux[$id]))
									$totaux[$id] = 0;

								$nbTarifs++;

								if (!$noprint) {

							 ?>

							<th class="vertical"><span><?php echo stripslashes($tarif['nom']); ?></span></th>

							<?php } }


							if (!$noprint) { ?>

							<th style="width:0px !important"></th>

							<th class="vertical" style="background:#000; color:#FFF">
								<span>TOTAUX</span>
							</th>

						</tr>
					</thead>

					<tbody>

						<?php }

						$nbEcoles = 0;
						foreach ($ecoles as $ecole) {

							if ((int) $ecole['ecole_lyonnaise'] != $typeEcole)
								continue;

						$nbEcoles++;


						if (!$noprint) {

						?>

						<tr class="form">
							<td><center><?php echo stripslashes($ecole['nom']); ?></center></td>
							
							<?php }

							$tot = 0;
							$totNb = 0;

							foreach ($tarifs as $id => $tarif) {

								if ($typeSportif != $tarif['sportif'] ||
									(int) $tarif['ecole_lyonnaise'] != $typeEcole)
									continue;

								$pnb = empty($ecole['tarifs'][$id]) ? '' : $ecole['tarifs'][$id]; 

							if (!$noprint) {

							?>

							<td class="vertical"<?php echo !empty($ecole['tarifs'][$id]) ? ' style="background-color:#DFD"' : ''; ?>>
								<small style="line-height:0px; color:#000">
									<b><?php echo $pnb; ?></b><br />
									<?php echo $pnb ? printMoney($tarif['tarif'] * $pnb) : ''; ?>
								</small>
							</td>

							<?php }

							$pnb = (int) $pnb;
							$tot += $tarif['tarif'] * $pnb; 
							$totNb += $pnb; 
							$totaux[$id] += $pnb;

							} 

							if (!$noprint) { 

							?>

							<th style="width:0px !important"></th>

							<td class="vertical"<?php echo $tot ? ' style="background-color:#DDF"' : ''; ?>>
								<small style="line-height:0px; color:#000">
									<b><?php echo $totNb ? $totNb : ''; ?></b><br />
									<?php echo $tot ? printMoney($tot) : ''; ?>
								</small>
							</td>

						</tr>

						<?php } }

						if (!$nbEcoles && !$noprint) { ?>

						<tr class="vide">
							<td colspan="<?php echo 3 + $nbTarifs; ?>">Aucune école</td>
						</tr>

						<?php } else { 

						if (!$noprint) {
						?>

						<tr>
							<th colspan="<?php echo 3 + $nbTarifs; ?>"></th>
						</tr>

						<tr class="form">
							<th>TOTAUX</th>

							<?php }
							 foreach ($tarifs as $id => $tarif) { 

								if ($typeSportif != $tarif['sportif'] ||
									(int) $tarif['ecole_lyonnaise'] != $typeEcole)
									continue;

								if (!$noprint) {
							?>

							<td class="vertical"<?php echo $totaux[$id] ? ' style="background-color:#FDD"' : ''; ?>>
								<small style="line-height:0px; color:#000">
									<b><?php echo $totaux[$id]  ? $totaux[$id]  : ''; ?></b><br />
									<?php echo $totaux[$id]  ? printMoney($totaux[$id] * $tarif['tarif']) : ''; ?>
								</small>
							</td>

							<?php }

							$total += $totaux[$id] * $tarif['tarif'];
							$totalNb += $totaux[$id];

							} 


							if (!$noprint) {

							?>

							<th style="width:0px !important"></th>


							<td class="vertical" style="background:#999;">
								<small style="line-height:0px; color:#000">
									<b><?php echo $totalNb; ?></b><br />
									<?php echo printMoney($total); ?>
								</small>
							</td>

						</tr>

						<?php } } 

						if (!$noprint) { 
						?>
					</tbody>
				</table>
				</center>


				<?php } } }


				if (!isset($_GET['e']) ||
					!isset($_GET['s']) ||
					!in_array($_GET['e'], array_keys($typesEcoles)) ||
					!in_array($_GET['s'], array_keys($typesSportifs))) {

				$total = 0;
				$totalNb = 0;

				foreach ($tarifs as $id => $tarif) {
					$total += $totaux[$id] * $tarif['tarif'];
					$totalNb += $totaux[$id];
				}
				
				echo '<center>Totaux : <b>'.$totalNb.'</b> ('.
					printMoney($total).')';		
				
				}
				?>

<?php

//Inclusion du pied de page
require DIR.'templates/_footer.php';
