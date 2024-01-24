<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* templates/ecoles/recapitulatif.php **********************/
/* Template pour les récapitualtifs de l'inscription *******/
/* *********************************************************/
/* Dernière modification : le 12/12/14 *********************/
/* *********************************************************/


//Inclusion de l'entête de page
require DIR.'templates/ecoles/_header_ecoles.php';

?>
				
				<nav class="subnav">
					<h2><a href="?p=">Récapitulatif de l'école <i><?php echo stripslashes($ecole['nom']); ?></i></a></h2>

					<ul>
						<li><a href="?p=etat">Etat</a></li>
						<li><a href="?p=coords">Coordonnées</a></li>
						<li><a href="?p=extras">Extras</a></li>
						<li><a href="?p=sportifs">Sportifs</a></li>
						<li><a href="?p=listing">Listing</a></li>
						<li><a href="?p=alertes">Erreurs</a></li>
						<li><a href="?p=paiements">Paiements</a></li>
						<li><a href="?p=tarifs">Tarifs</a></li>
					</ul>
				</nav>

				<form class="form-table" method="post">
				
				<?php if (empty($_GET['p']) || $_GET['p'] == 'etat') { ?>

				<fieldset>
					<h3>Etat de l'inscription</h3>

					<label class="nomargin">
						<span>Etat</span>
						<div><?php echo printEtatEcole($ecole['etat_inscription']); ?></div>
					</label>

					<label class="nomargin">
						<span>Caution</span>
						<div><?php echo $ecole['caution_recue'] ? 'Recue' : 'Non recue'; ?></div>
					</label>

					<label class="nomargin">
						<span>Responsable</span>
						<div>
							
							<?php if (empty($ecole['ulogin'])) { ?>
							
							<i>Non défini</i>

							<?php } else { ?>

							<div class="clearfix contact">
								<img src="<?php echo empty($ecole['uphoto']) ? (URL_API_ECLAIR.'?type=photo&login='.$ecole['ulogin']) : 
									url($ecole['uphoto'], false, false); ?>" alt="<?php echo $ecole['ulogin']; ?>" />

								<h4 style="margin:0px; text-align:left; margin-top:20px"><?php echo stripslashes(strtoupper($ecole['unom']).' '.$ecole['uprenom']); ?></h4>
								<?php if (!empty($ecole['uposte'])) { ?>
								<i><?php echo stripslashes($ecole['uposte']); ?></i>
								<?php } ?><br /><br />
								<a href="mailto:<?php echo $ecole['uemail']; ?>"><?php echo $ecole['uemail']; ?></a><br />
								<a href="tel:<?php echo $ecole['utelephone']; ?>"><?php echo $ecole['utelephone']; ?></a><br />
							</div>

							<?php } ?>

						</div>
					</label>
				</fieldset>

				<fieldset>
					<h3>Quotas et données numériques</h3>

					<table class="table-small" style="margin-top:20px">
						<tr>
							<td><center><i>Inscriptions : </i> <?php echo '<b>'.$ecole['nb_inscriptions'].'</b>'.($inscription_on ? ' / '.
								($ecole['nb_inscriptions'] >= $quotas['total'] ? '<span class="full">'.(int) $quotas['total'].'</span>' : (int) $quotas['total']).
								($places_reservees > 0 ? ' (+'.$places_reservees.')' : '') : ''); ?></center></td>
							<td><center><i>Sportifs : </i>  <?php echo '<b>'.$ecole['nb_sportif'].'</b>'.($sportif_on ? ' / '.
								($ecole['nb_sportif'] >= $quotas['sportif'] ? '<span class="full">'.(int) $quotas['sportif'].'</span>' : (int) $quotas['sportif']) : ''); ?></center></td>
							<td><center><i>Non sportifs : </i>  <?php echo '<b>'.($ecole['nb_inscriptions'] - $ecole['nb_sportif']).'</b>'.($nonsportif_on ? ' / '.
								($ecole['nb_inscriptions'] - $ecole['nb_sportif'] >= $quotas['nonsportif'] ? '<span class="full">'.(int) $quotas['nonsportif'].'</span>' : (int) $quotas['nonsportif']) : ''); ?></center></td>
						</tr>
						<tr>
							<td><center><i>Participants logés : </i> <?php echo '<b>'.($ecole['nb_filles_logees'] + $ecole['nb_garcons_loges']).'</b> '.($logement_on ? ' / '.
								($ecole['nb_filles_logees'] + $ecole['nb_garcons_loges'] >= $quotas['logement'] ? '<span class="full">'.(int) $quotas['logement'].'</span>' : (int) $quotas['logement']) : ''); ?></center></td>
							<td><center><i>Filles logées : </i> <?php echo '<b>'.$ecole['nb_filles_logees'].'</b> '.($filles_on ? ' / '.
								($ecole['nb_filles_logees'] >= $quotas['filles_logees'] ? '<span class="full">'.(int) $quotas['filles_logees'].'</span>' : (int) $quotas['filles_logees']) : ''); ?></center></td>
							<td><center><i>Garcons logés : </i> <?php echo '<b>'.$ecole['nb_garcons_loges'].'</b> '.($garcons_on ? ' / '.
								($ecole['nb_garcons_loges'] >= $quotas['garcons_loges'] ? '<span class="full">'.(int) $quotas['garcons_loges'].'</span>' : (int) $quotas['garcons_loges']) : ''); ?></center></td>
						</tr>

						<tr>
							<td><center><i>Pompoms : </i> <?php echo '<b>'.$ecole['nb_pompom'].'</b> '.($pompom_on ? ' / '.
								($ecole['nb_pompom'] >= $quotas['pompom'] ? '<span class="full">'.(int) $quotas['pompom'].'</span>' : (int) $quotas['pompom']) : '').
								'<br />dont <b>'.$ecole['nb_pompom_nonsportif'].'</b> '.($pompom_nonsportif_on ? ' / '.
								($ecole['nb_pompom_nonsportif'] >= $quotas['pompom_nonsportif'] ? '<span class="full">'.(int) $quotas['pompom_nonsportif'].'</span>' : (int) $quotas['pompom_nonsportif']) : '').' non sportifs'; ?></center></td>
							<td><center><i>Caméramans : </i> <?php echo '<b>'.$ecole['nb_cameraman'].'</b> '.($cameraman_on ? ' / '.
								($ecole['nb_cameraman'] >= $quotas['cameraman'] ? '<span class="full">'.(int) $quotas['cameraman'].'</span>' : (int) $quotas['cameraman']) : '').
								'<br />dont <b>'.$ecole['nb_cameraman_nonsportif'].'</b> '.($cameraman_nonsportif_on ? ' / '.
								($ecole['nb_cameraman_nonsportif'] >= $quotas['cameraman_nonsportif'] ? '<span class="full">'.(int) $quotas['cameraman_nonsportif'].'</span>' : (int) $quotas['cameraman_nonsportif']) : '').' non sportifs'; ?></center></td>
							<td><center><i>Fanfarons : </i> <?php echo '<b>'.$ecole['nb_fanfaron'].'</b> '.($fanfaron_on ? ' / '.
								($ecole['nb_fanfaron'] >= $quotas['fanfaron'] ? '<span class="full">'.(int) $quotas['fanfaron'].'</span>' : (int) $quotas['fanfaron']) : '').
								'<br />dont <b>'.$ecole['nb_fanfaron_nonsportif'].'</b> '.($fanfaron_nonsportif_on ? ' / '.
								($ecole['nb_fanfaron_nonsportif'] >= $quotas['fanfaron_nonsportif'] ? '<span class="full">'.(int) $quotas['fanfaron_nonsportif'].'</span>' : (int) $quotas['fanfaron_nonsportif']) : '').' non sportifs'; ?></center></td>
						</tr>
					</table>

					<?php if (!empty($quotas_reserves) && isset($quotas['total'])) { ?>

					<table class="table-small" style="margin-top:20px">
						<tr>
							<th>Sport</th>
							<th style="width:100px"><small>Quota réservé</small></th>
							<th style="width:100px"><small>Sportifs</small></th>
							<th style="width:75px"><small>Si atteint</small></th>
						</tr>

						<?php foreach ($quotas_reserves as $quota_reserves) { ?>

						<tr>
							<td><?php echo stripslashes($quota_reserves['sport']).' '.printSexe($quota_reserves['sexe']); ?></td>
							<td><center><?php echo $quota_reserves['quota_reserves']; ?></center></td>
							<td><center><?php echo $quota_reserves['sportifs']; ?></center></td>
							<td><center><b><?php if ($quota_reserves['sportifs'] < $quota_reserves['quota_reserves']) echo '+'.$quota_reserves['quota_reserves']; ?></b></center></td>
						</tr>

						<?php } ?>

					</table>

					<?php } ?>

				</fieldset>

				<?php } else if ($_GET['p'] == 'coords') { ?>

				<fieldset>
					<h3>Coordonnées de l'école</h3>

					<center><img src="<?php url('image/'.$ecole['token']); ?>" style="<?php echo ($ecole['width'] && $ecole['width'] * 100 / $ecole['height'] > 200 ? 'width:200px' : 'height:100px'); ?>" /></center>

					<label class="nomargin">
						<span>Nom / <i>Type</i></span>
						<?php echo stripslashes($ecole['nom']); ?>
						 / <i><?php echo empty($ecole['ecole_lyonnaise']) ? 'Non Lyonnaise' : 'Lyonnaise'; ?></i>
					</label>

					<label class="nomargin">
						<span>Adresse</span>
						<div><?php echo nl2br(stripslashes($ecole['adresse'])); ?></div>
					</label>

					<label class="nomargin">
						<span>Code Postal</span>
						<?php echo stripslashes($ecole['code_postal']); ?>
					</label>

					<label class="nomargin">
						<span>Ville</span>
						<?php echo stripslashes($ecole['ville']); ?>
					</label>

					<label class="nomargin">
						<span>Email Ecole</span>
						<?php echo stripslashes($ecole['email_ecole']); ?>
					</label>

					<label class="nomargin">
						<span>Téléphone Ecole</span>
						<?php echo stripslashes($ecole['telephone_ecole']); ?>
					</label>
				</fieldset>

				<fieldset>
					<h3 class="show">Responsables administratif et organisation</h3>

					<div class="bloc">
						<h3 class="hide">Responsable administratif</h3>

						<label class="nomargin">
							<span>Nom</span>
							<?php echo stripslashes($ecole['nom_respo']); ?>
						</label>

						<label class="nomargin">
							<span>Prénom</span>
							<?php echo stripslashes($ecole['prenom_respo']); ?>
						</label>

						<label class="nomargin">
							<span>Email</span>
							<?php echo stripslashes($ecole['email_respo']); ?>
						</label>

						<label class="nomargin">
							<span>Téléphone</span>
							<?php echo stripslashes($ecole['telephone_respo']); ?>
						</label>
					</div>

					<div class="bloc">
						<h3 class="hide">Responsable organisation</h3>

						<label class="nomargin">
							<span>Nom</span>
							<?php echo stripslashes($ecole['nom_corespo']); ?>
						</label>

						<label class="nomargin">
							<span>Prénom</span>
							<?php echo stripslashes($ecole['prenom_corespo']); ?>
						</label>

						<label class="nomargin">
							<span>Email</span>
							<?php echo stripslashes($ecole['email_corespo']); ?>
						</label>

						<label class="nomargin">
							<span>Téléphone</span>
							<?php echo stripslashes($ecole['telephone_corespo']); ?>
						</label>
					</div>
				</fieldset>

				<?php } else if ($_GET['p'] == 'extras') { ?>

				<?php if (count($sans_sport)) { ?>
				
				<fieldset>
					<h3>Sportifs sans sport
						<a style="display:inline; font-size:14px; vertical-align:middle; color:#000" class="excel" href="?excel=sanssport">Télécharger en XLSX</a></h3>

					<table class="table-small">
						<thead>
							<tr>
								<th>Sportif</th>
								<th style="width:200px">Licence</th>
								<th style="width:80px">Sexe</th>
								<th style="width:80px">Logement</th>
							</tr>
						</thead>

						<tbody>

							<?php foreach ($sans_sport as $sportif) { ?>

							<tr class="form">
								<td class="content"><?php echo stripslashes(strtoupper($sportif['nom']).' '.$sportif['prenom']); ?></td>
								<td class="content"><?php echo stripslashes($sportif['licence']); ?></td>
								<td>
									<input type="checkbox" <?php echo $sportif['sexe'] == 'h' ? 'checked ' : ''; ?>/>
									<label class="sexe"></label>
								</td>
								<td>
									<input type="checkbox" <?php echo $sportif['logement'] ? 'checked ' : ''; ?>/>
									<label class="package"></label>
								</td>
							</tr>

							<?php } ?>

						</tbody>
					</table>
				</fieldset>

				<?php } ?>

				<?php 

				$has_sportif_multiple_sports = false;
				foreach ($sportifs_sports as $sportif_sports) {
					if (count($sportif_sports) > 1) {
						$has_sportif_multiple_sports = true;
						break;
					}
				}

				if ($has_sportif_multiple_sports) { ?>

				<fieldset>
					<h3>Sportifs avec plusieurs sports
						<a style="display:inline; font-size:14px; vertical-align:middle; color:#000" class="excel" href="?excel=multisports">Télécharger en XLSX</a></h3>
					
					<table class="table-small">
						<thead>
							<tr>
								<th>Sportif</th>
								<th style="width:80px">Capitaine</th>
								<th>Sport</th>
								<th>Equipe</th>
							</tr>
						</thead>

						<tbody>

							<?php 

							$premier = true;
							foreach ($sportifs_sports as $sportif_sports) { 
								if (count($sportif_sports) <= 1)
									continue;

									$data = $sportif_sports[0];

								
								if (!$premier) { 
							?>
								<tr><th colspan="4"></th></tr>

							<?php } else $premier = false;

									foreach ($sportif_sports as $key => $sport) { 

							?>

							<tr class="form">
								
								<?php if ($key == 0) { ?>

								<td rowspan="<?php echo count($sportif_sports); ?>">
									<div>
										<?php echo stripslashes(strtoupper($data['pnom']).' '.$data['pprenom']); ?><br />
										<small><?php echo stripslashes($data['plicence']); ?></small>
									</div>
								</td>
								
								<?php } ?>

								<td>
									<input type="checkbox" <?php echo $sport['cid'] == $data['pid'] ? 'checked ' : ''; ?>/>
									<label class="capitaine"></label>
								</td>
								<td class="content"><?php echo stripslashes($sport['sport']).' '.printSexe($sport['sexe']); ?></td>
								<td class="content"><?php echo stripslashes($sport['label']); ?></td>
							</tr>

							<?php } } ?>

						</tbody>
					</table>
				</fieldset>

				<?php } ?>

				<fieldset>
					<h3>Fanfarons
						<a style="display:inline; font-size:14px; vertical-align:middle; color:#000" class="excel" href="?excel=fanfarons">Télécharger en XLSX</a></h3>

					<table class="table-small">
						<thead>
							<tr>
								<th>Fanfaron</th>
								<th style="width:80px">Sportif</th>
								<th style="width:80px">Sexe</th>
								<th style="width:80px">Logement</th>
							</tr>
						</thead>

						<tbody>

							<?php if (!count($fanfarons)) { ?> 

							<tr class="vide">
								<td colspan="4">Aucun fanfaron</td>
							</tr>

							<?php } foreach ($fanfarons as $fanfaron) { ?>

							<tr class="form">
								<td class="content"><?php echo stripslashes(strtoupper($fanfaron['nom']).' '.$fanfaron['prenom']); ?></td>
								<td>
									<input type="checkbox" <?php echo $fanfaron['sportif'] ? 'checked ' : ''; ?>/>
									<label></label>
								</td>
								<td>
									<input type="checkbox" <?php echo $fanfaron['sexe'] == 'h' ? 'checked ' : ''; ?>/>
									<label class="sexe"></label>
								</td>
								<td>
									<input type="checkbox" <?php echo $fanfaron['logement'] ? 'checked ' : ''; ?>/>
									<label class="package"></label>
								</td>
							</tr>

							<?php } ?>

						</tbody>
					</table>
				</fieldset>

				<fieldset>
					<h3>Pompoms
						<a style="display:inline; font-size:14px; vertical-align:middle; color:#000" class="excel" href="?excel=pompoms">Télécharger en XLSX</a></h3>

					<table class="table-small">
						<thead>
							<tr>
								<th>Pompom</th>
								<th style="width:80px">Sportif</th>
								<th style="width:80px">Sexe</th>
								<th style="width:80px">Logement</th>
							</tr>
						</thead>

						<tbody>

							<?php if (!count($pompoms)) { ?> 

							<tr class="vide">
								<td colspan="4">Aucun pompom</td>
							</tr>

							<?php } foreach ($pompoms as $pompom) { ?>

							<tr class="form">
								<td class="content"><?php echo stripslashes(strtoupper($pompom['nom']).' '.$pompom['prenom']); ?></td>
								<td>
									<input type="checkbox" <?php echo $pompom['sportif'] ? 'checked ' : ''; ?>/>
									<label></label>
								</td>
								<td>
									<input type="checkbox" <?php echo $pompom['sexe'] == 'h' ? 'checked ' : ''; ?>/>
									<label class="sexe"></label>
								</td>
								<td>
									<input type="checkbox" <?php echo $pompom['logement'] ? 'checked ' : ''; ?>/>
									<label class="package"></label>
								</td>
							</tr>

							<?php } ?>

						</tbody>
					</table>
				</fieldset>

				<fieldset>
					<h3>Cameramans
						<a style="display:inline; font-size:14px; vertical-align:middle; color:#000" class="excel" href="?excel=cameramans">Télécharger en XLSX</a></h3>

					<table class="table-small">
						<thead>
							<tr>
								<th>Cameraman</th>
								<th style="width:80px">Sportif</th>
								<th style="width:80px">Sexe</th>
								<th style="width:80px">Logement</th>
							</tr>
						</thead>

						<tbody>

							<?php if (!count($cameramans)) { ?> 

							<tr class="vide">
								<td colspan="4">Aucun cameraman</td>
							</tr>

							<?php } foreach ($cameramans as $cameraman) { ?>

							<tr class="form">
								<td class="content"><?php echo stripslashes(strtoupper($cameraman['nom']).' '.$cameraman['prenom']); ?></td>
								<td>
									<input type="checkbox" <?php echo $cameraman['sportif'] ? 'checked ' : ''; ?>/>
									<label></label>
								</td>
								<td>
									<input type="checkbox" <?php echo $cameraman['sexe'] == 'h' ? 'checked ' : ''; ?>/>
									<label class="sexe"></label>
								</td>
								<td>
									<input type="checkbox" <?php echo $cameraman['logement'] ? 'checked ' : ''; ?>/>
									<label class="package"></label>
								</td>
							</tr>

							<?php } ?>

						</tbody>
					</table>
				</fieldset>

				<?php } else if ($_GET['p'] == 'sportifs') { ?>
				

				<?php if (!count($equipes_sportifs)) { ?>

				<fieldset>
					<h3>Liste des équipes</h3>

					<div class="alerte alerte-attention">
						<div class="alerte-contenu">
							Aucune équipe n'a encore été créée.<br />
							Dirigez-vous sur <a href="<?php url('ecoles/'.$ecole['id'].'/sportifs'); ?>">la page concernée</a> pour en ajouter !
						</div>
					</div>
				</fieldset>

				<?php } foreach ($equipes_sportifs as $sid => $equipes) {
					$data = $equipes[array_keys($equipes)[0]][0];

				?>

				<fieldset>
					<h3><?php echo stripslashes($data['sport']).' '.printSexe($data['sexe']); ?>
						<a style="display:inline; font-size:14px; vertical-align:middle; color:#000" class="excel" href="?excel=sportifs&sport=<?php echo $sid; ?>">Télécharger en XLSX</a></h3>

					<center>Responsable : <a href="<?php url('contact'); ?>"><b>
						<?php echo stripslashes(strtoupper($data['unom']).' '.$data['uprenom']); ?></b></a></center>
					

					<?php foreach ($equipes as $equipe) { ?>


					<?php if (count($equipes) > 1) { ?>

					<h4>Équipe : <?php echo stripslashes($equipe[0]['label']); ?></h4>

					<?php } else { ?> <br /> <?php } ?>

					<table>
						<thead>
							<tr>
								<th style="width:60px"><small>Capitaine</small></th>
								<th>Sportif</th>
								<th style="width:80px">Sexe</th>
								<th style="width:150px">Licence</th>
								<th>Tarif</th>
								<th style="width:80px">Logement</th>
							</tr>
						</thead>

						<tbody>

							<?php if (empty($equipe[0]['pid'])) { ?> 

							<tr class="vide">
								<td colspan="6">Aucun sportif</td>
							</tr>

							<?php } else { foreach ($equipe as $sportif) { ?>

							<tr class="form">
								<td>														
									<input type="checkbox" <?php echo $equipe[0]['cid'] == $sportif['pid'] ? 'checked ' : ''; ?>/>
									<label class="capitaine"></label>
								</td>
								<td class="content"><?php echo stripslashes(strtoupper($sportif['pnom']).' '.$sportif['pprenom']); ?></td>
								<td>
									<input type="checkbox" <?php echo $sportif['psexe'] == 'h' ? 'checked ' : ''; ?>/>
									<label class="sexe"></label>
								</td>
								<td class="content"><?php echo stripslashes($sportif['plicence']); ?></td>
								<td class="content"><?php echo stripslashes($sportif['ptarif']); ?></td>
								<td>
									<input type="checkbox" <?php echo $sportif['plogement'] ? 'checked ' : ''; ?>/>
									<label class="package"></label>
								</td>
							</tr>

							<?php } } ?>

						</tbody>
					</table>

					<?php } ?>

				</fieldset>

				<?php } ?>
				

				<?php } else if ($_GET['p'] == 'listing') { ?>

				<fieldset>
					<h3>Liste des participants</h3>

					<a href="?excel=participants" class="excel">Télécharger en XLSX</a>
					<table>
						<thead>
							<tr>
								<th>Participant</th>
								<th>Email</th>
								<th>Tarif</th>
								<th>Gourde</th>
								<th>Montant</th>
								<th style="width:60px">Retard</th>
								<th style="width:60px"><small>Logement</small></th>

							</tr>
						</thead>

						<tbody>

							<?php if (!count($participants)) { ?> 

							<tr class="vide">
								<td colspan="6">Aucun participant</td>
							</tr>

							<?php } foreach ($participants as $participant) { ?>

							<tr class="form">
								<td class="content"><?php echo stripslashes(strtoupper($participant['nom']).' '.$participant['prenom']); ?></td>
								<td class="content"><?php echo stripslashes($participant['email']); ?></td>
								<td class="content"><?php echo stripslashes($participant['tarif']); ?></td>
								<td class="content"><?php echo printMoney($participant['recharge']); ?></td>
								<td class="content"><?php echo printMoney($participant['montant']); ?></td>
								<td>
									<input type="checkbox" <?php if ($participant['retard']) echo 'checked '; ?>/>
									<label class="retard<?php if ($participant['hors_malus']) echo '-excuse'; ?>"></label>
								</td>

								<td>
									<input type="checkbox" <?php if ($participant['logement']) echo 'checked '; ?>/>
									<label class="package"></label>
								</td>
							</tr>

							<?php } ?>

						</tbody>
					</table>
				</fieldset>

				<?php } else if ($_GET['p'] == 'paiements') { ?>

				<fieldset>
					<h3>Etat du paiement</h3>

					<label class="nomargin">
						<span>Prix participants</span>
						<div><?php echo printMoney($montant_inscriptions['montant']); ?></div>
					</label>

					<label class="nomargin">
						<span>Prix gourde</span>
						<div><?php echo printMoney($montant_recharges['montant']); ?></div>
					</label>

					<?php 

					$montant = $montant_inscriptions['montant'] + $montant_recharges['montant']; 
					$malus = (float) $ecole['malus'] / 100 *  $inscriptions_enretard['montant'];
					
					if ($inscriptions_enretard['nbretards'] > 0 ) { ?>

					<label class="nomargin">
						<span>Malus</span>
						<div><?php echo (float) $ecole['malus']; ?> %</div>
					</label>

					<label class="nomargin">
						<span>Pénalités</span>
						<div><?php echo printMoney($malus); ?></div>
						<small><b><?php echo $inscriptions_enretard['nbretards']; ?></b> inscriptions après <?php echo printDateTime(APP_DATE_MALUS, false); ?></small>
					</label>

					<?php } ?>

					<hr />

					<label class="nomargin">
						<span>Montant total</span>
						<?php echo printMoney($montant + $malus); ?>
					</label>

					<label class="nomargin">
						<span>Montant payé</span>
						<?php echo printMoney($montant_paye['montant']); ?>
					</label>

					<label class="nomargin">
						<span>Montant restant</span>
						<?php echo printMoney($montant + $malus - $montant_paye['montant']); ?>
					</label>
				</fieldset>


				<fieldset>
					<h3>Liste des paiements</h3>

					<a class="excel" href="?excel=paiements">Télécharger en XLSX</a></h3>
					<table>
						<thead>
							<tr>
								<th>Date</th>
								<th>Type</th>
								<th>Montant</th>
								<th>Etat</th>
							</tr>
						</thead>

						<tbody>

							<?php if (!count($paiements)) { ?> 

							<tr class="vide">
								<td colspan="4">Aucun paiement</td>
							</tr>

							<?php } foreach ($paiements as $paiement) { ?>

							<tr>
								<td><?php echo printDateTime($paiement['_date']); ?></td>
								<td><center><?php echo printTypePaiement($paiement['type']); ?></center></td>
								<td><center><?php echo printMoney($paiement['montant']); ?></center></td>
								<td><center><?php echo printEtatPaiement($paiement['etat']); ?></center></td>
							</tr>

							<?php } ?>

						</tbody>
					</table>
				</fieldset>

				<?php } else if ($_GET['p'] == 'tarifs') { ?>

				<style>
				.tarifs { 
					float:right; 
					width:400px;
				}

				@media screen and (max-width: 1024px) {
					h4 {
						margin-left:0px !important;
						text-align: center !important;
					}

					.tarifs { 
						float:none; 
						width:inherit;
						margin-top:10px;
						margin-left:100px;
						margin-right:100px;
					}

					.nomargin {
						margin:0 !important;
					}
				}

				@media screen and (max-width: 600px) {

					.tarifs { 
						margin-left:0px;
						margin-right:0px;
					}
				}



				</style>

				<?php foreach ($tarifs_groupes as $groupe => $tarifs) { ?>
			
				<fieldset>
					<ul>

						<li style="margin-bottom:20px; list-style-type:none;">
							<h3>Tarifs "<?php echo $groupe ? 'Sportifs' : 'Non-sportifs'; ?>"</h3>
							<ul style="list-style-type:none">

								<?php foreach ($tarifs as $tarif) { ?>
								
								<li style="margin-bottom:25px; line-height:1.5em">
									<h4 style="text-align:left; margin-bottom:0; margin-left:150px"><?php echo $tarif['nom']; ?></h4> 
									


									<div class="tarifs">
										<input type="checkbox" readonly <?php if ($tarif['logement']) echo 'checked '; ?>/>
										<label class="package"></label>
										
										<div class="triple">									
										<input type="checkbox" readonly <?php if ($tarif['for_pompom'] != 'no') echo 'checked '; ?>/>
										<label class="extra-pompom<?php echo $tarif['for_pompom'] == 'or' ? '-or' : ''; ?>"></label>
										
										<input type="checkbox" readonly <?php if ($tarif['for_cameraman'] != 'no') echo 'checked '; ?>/>
										<label class="extra-video<?php echo $tarif['for_cameraman'] == 'or' ? '-or' : ''; ?>"></label>

										<input type="checkbox" readonly <?php if ($tarif['for_fanfaron'] != 'no') echo 'checked '; ?>/>
										<label class="extra-fanfaron<?php echo $tarif['for_fanfaron'] == 'or' ? '-or' : ''; ?>"></label>
										</div>
									</div>

									<label class="nomargin">
										<span>Prix</span>
										<?php echo sprintf('%.2f €', $tarif['tarif']); ?> 
									</label>

									<?php if (!empty($tarif['id_sport_special'])) { ?>
									
									<label class="nomargin">
										<span>Sport Spécial</span>
										<?php echo stripslashes($tarif['sport']).' '.printSexe($tarif['sexe']); ?>
									</label>

									<?php } if (!empty($tarif['description'])) { ?>
									
									<label class="nomargin" style="margin-right:450px">
										<span>Description</span>
										<div><?php echo nl2br($tarif['description']); ?></div>
									</label>

									<?php } ?>
								</li>

								<?php } ?>

							</ul>
						</li>

					</ul>
				</fieldset>

				<?php } } 

				else if ($_GET['p'] == 'alertes') { ?>

				<h3>Erreurs soumises
					<a style="display:inline; font-size:14px; vertical-align:middle; color:#000" class="excel" href="?excel=err_soumises">Télécharger en XLSX</a></h3>

				<table>
					<thead>
						<tr>
							<th>Date</th>
							<th>Nom</th>
							<th>Prénom</th>
							<th style="width:60px">Sexe</th>
							<th style="width:60px">Sportif</th>
							<th>Message</th>
							<th>Etat</th>
						</tr>
					</thead>

					<tbody>

						<?php if (!count($erreurs)) { ?> 

						<tr class="vide">
							<td colspan="7">Aucune erreur</td>
						</tr>

						<?php } foreach ($erreurs as $erreur) { ?>

						<tr class="form">
							<td><div><?php echo printDateTime($erreur['_date']); ?></div></td>
							<td><div><?php echo stripslashes($erreur['nom']); ?></div></td>
							<td><div><?php echo stripslashes($erreur['prenom']); ?></div></td>
							<td>
								<input type="checkbox" <?php echo $erreur['sexe'] == 'h' ? 'checked ' : ''; ?>/>
								<label class="sexe"></label>
							</td>
							<td>
								<input type="checkbox" <?php echo $erreur['sportif'] ? 'checked ' : ''; ?>/>
								<label></label>
							</td>
							<td><textarea readonly><?php echo stripslashes($erreur['message']); ?></textarea></td>
							<td><center><?php echo printEtatErreur($erreur['etat']); ?></center></td>
						</tr>

						<?php } ?>

					</tbody>
				</table>
				<br />

				<h3>Erreurs liées aux données</h3>

				<h4>Noms/prénoms en doublon</h4>

				<table class="table-small">
					<thead>
						<tr>
							<th>Nom-Prénom</th>
							<th>Nom</th>
							<th>Prénom</th>
							<th style="width:60px">Sexe</th>
						</tr>
					</thead>

					<tbody>

						<?php 

						$doublons = false;
						for ($j = 0; $j < count($noms_prenoms); $j++) { //Do not a foreach loop (because of live modification)
							$nom_prenom = array_keys($noms_prenoms)[$j];
							$ids = $noms_prenoms[$nom_prenom];

							if (count($ids) == 1)
								continue;

							list($nom, $prenom) = explode(' ', $nom_prenom);
							if (!empty($noms_prenoms[$prenom.' '.$nom])) {
								$noms_prenoms[$prenom.' '.$nom] = array_filter($noms_prenoms[$prenom.' '.$nom], function($id) {
									global $ids;
									return false;
								});
							}

							$doublons = true;
							$i = 0;

							foreach ($ids as $id) {
								$participant = $participants[$id];
						  ?>

							<tr class="form">
								<?php if ($i++ == 0) { ?>
								<td rowspan="<?php echo count($ids); ?>"><div><b><?php echo ucname(stripslashes($nom_prenom)); ?></b></div></td>
								<?php } ?>

								<td><div><?php echo stripslashes($participant['nom']); ?></div></td>
								<td><div><?php echo stripslashes($participant['prenom']); ?></div></td>
								<td>
									<input type="checkbox" <?php echo $participant['sexe'] == 'h' ? 'checked ' : ''; ?>/>
									<label class="sexe"></label>
								</td>
							</tr>

						<?php } } if (!$doublons) { ?>

						<tr class="vide">
							<td colspan="4">Aucun doublon</td>
						</tr>

						<?php } ?>

					</tbody>
				</table>

				<h4>Emails en doublon ou invalides<br /><small style="color:gray">Des emails, bien que valides, présentant des divergences avec la globalité sont aussi notées ici</small></h4>

				<table class="table-small">
					<thead>
						<tr>
							<th>Email</th>
							<th>Nom</th>
							<th>Prénom</th>
							<th style="width:60px">Sexe</th>
						</tr>
					</thead>

					<tbody>

						<?php 

						$doublons = false;
						foreach ($emails as $email => $ids) {
							$domaine = explode('@', $email);

							if (count($ids) == 1 && isValidEmail($email) && $domaine[1] == $domaine_ecole && (
									levenshtein($domaine[0], str_replace(' ', '', $participants[$ids[0]]['nom_prenom'])) <= 5 ||
									levenshtein($domaine[0], str_replace(' ', '', $participants[$ids[0]]['prenom_nom'])) <= 5))
								continue;

							$doublons = true;
							$i = 0;

							foreach ($ids as $id) {
								$participant = $participants[$id];
						  ?>

							<tr class="form">
								<?php if ($i++ == 0) { ?>
								<td rowspan="<?php echo count($ids); ?>"><div><b><?php echo stripslashes($email); ?></b></div></td>
								<?php } ?>

								<td><div><?php echo stripslashes($participant['nom']); ?></div></td>
								<td><div><?php echo stripslashes($participant['prenom']); ?></div></td>
								<td>
									<input type="checkbox" <?php echo $participant['sexe'] == 'h' ? 'checked ' : ''; ?>/>
									<label class="sexe"></label>
								</td>
							</tr>

						<?php } } if (!$doublons) { ?>

						<tr class="vide">
							<td colspan="4">Aucun doublon</td>
						</tr>

						<?php } ?>

					</tbody>
				</table>


				<h4>Licences en doublon ou invalides<br /><small style="color:gray">Certains cas d'exception, notamment avec des noms composés, peuvent être listés ici</small></h4>

				<table class="table-small">
					<thead>
						<tr>
							<th>Licence</th>
							<th>Nom</th>
							<th>Prénom</th>
							<th style="width:60px">Sexe</th>
						</tr>
					</thead>

					<tbody>

						<?php 

						$doublons = false;
						foreach ($licences as $licence => $ids) {
							$licence_clean = strtoupper(str_replace(' ', '', strlen($licence) < 10 && count($asCodes) == 1 ? $asCodes[0].$licence : $licence));
							$licence_nom_prenom = empty($licences_sql[$licence_clean]) ? '' : clean($licences_sql[$licence_clean]['nom']).' '.clean($licences_sql[$licence_clean]['prenom']);
							if (count($ids) <= 1 && isValidLicence($licence) && isLicenceEcole($licence, $asCodes) &&
								!empty($licence_nom_prenom) &&
									(levenshtein($licence_nom_prenom, $participants[$ids[0]]['nom_prenom']) <= 5 ||
									levenshtein($licence_nom_prenom, $participants[$ids[0]]['prenom_nom']) <= 5))
								continue;

							$doublons = true;
							$i = 0;

							foreach ($ids as $id) {
								$participant = $participants[$id];
						  ?>

							<tr>
								<?php if ($i++ == 0) { ?>
								<td rowspan="<?php echo count($ids); ?>"><b><?php echo stripslashes($licence); ?></b>
									<?php if (in_array(substr($licence_clean, 0, 4), $asCodes) && !empty($licence_nom_prenom)) 
										echo '<br /><small>'.ucname($licences_sql[$licence_clean]['nom'].' '.$licences_sql[$licence_clean]['prenom']).'</small>'; ?>
								</td>
								<?php } ?>

								<td><?php echo stripslashes($participant['nom']); ?></td>
								<td><?php echo stripslashes($participant['prenom']); ?></td>
								<td style="padding:0px">
									<input type="checkbox" <?php echo $participant['sexe'] == 'h' ? 'checked ' : ''; ?>/>
									<label class="sexe"></label>
								</td>
							</tr>

						<?php } } if (!$doublons) { ?>

						<tr class="vide">
							<td colspan="4">Aucun doublon</td>
						</tr>

						<?php } ?>

					</tbody>
				</table>


				<h4>Téléphones en doublon ou invalides<br /><small style="color:gray">Quelques cas particuliers, notamment les numéros étrangers, peuvent être listés ici</small></h4>

				<table class="table-small">
					<thead>
						<tr>
							<th>Téléphone</th>
							<th>Nom</th>
							<th>Prénom</th>
							<th style="width:60px">Sexe</th>
						</tr>
					</thead>

					<tbody>

						<?php 

						$doublons = false;
						foreach ($telephones as $telephone => $ids) {
							if (count($ids) <= 1 && isValidPhone($telephone))
								continue;

							$doublons = true;
							$i = 0;

							foreach ($ids as $id) {
								$participant = $participants[$id];
						  ?>

							<tr class="form">
								<?php if ($i++ == 0) { ?>
								<td rowspan="<?php echo count($ids); ?>"><div><b><?php echo stripslashes($telephone); ?></b></div></td>
								<?php } ?>

								<td><div><?php echo stripslashes($participant['nom']); ?></div></td>
								<td><div><?php echo stripslashes($participant['prenom']); ?></div></td>
								<td>
									<input type="checkbox" <?php echo $participant['sexe'] == 'h' ? 'checked ' : ''; ?>/>
									<label class="sexe"></label>
								</td>
							</tr>

						<?php } } if (!$doublons) { ?>

						<tr class="vide">
							<td colspan="4">Aucun doublon</td>
						</tr>

						<?php } ?>

					</tbody>
				</table>


				<?php } ?>

				</form>	

<?php

//Inclusion du pied de page
require DIR.'templates/_footer.php';
