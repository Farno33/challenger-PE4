<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* templates/admin/droits/liste.php ************************/
/* Template de la liste des organisateurs ******************/
/* *********************************************************/
/* Dernière modification : le 24/11/14 *********************/
/* *********************************************************/


//Inclusion de l'entête de page
require DIR.'templates/admin/_header_admin.php';

?>
			

				<h2>
					<?php if (empty($id_message)) { ?>

					Liste des Envois <br />

					<?php } else { ?>

					<a href="<?php url('admin/module/communication/envois'); ?>">Liste des Envois</a><br />

					<?php } ?>

					<?php if (empty($mailOk)) { ?>
					<a style="font-size:14px;display:inline-block" class="excel_big">Emails inactifs</a>
					<?php } else { ?>
					<a style="font-size:14px;display:inline-block" class="excel">Emails actifs</a>
					<?php } ?>

					<?php if (empty($smsOk)) { ?>
					<a style="font-size:14px;display:inline-block" class="excel_big">SMS inactifs</a>
					<?php } else { ?>
					<a style="font-size:14px;display:inline-block" class="excel">SMS actifs</a>
					<?php } ?>

				</h2>

				<?php if (empty($id_message)) { ?>

					<table class="table table-small" style="margin-bottom:10px">
						<thead>
							<tr>
								<th style="width:60px">Type</th>
								<th>Résumé</th>
								<th>Auteur</th>
								<th>Date</th>
								<th style="width:40px"><small>Envois</small></th>
								<th style="width:40px"><small>Echecs</small></th>
								<th>Etat</th>
							</tr>
						</thead>

						<tbody>

							<?php if (!count($envois)) { ?> 

							<tr class="vide">
								<td colspan="7">Aucune conversation</td>
							</tr>

							<?php } 

							$now = new DateTime();
							foreach ($envois as $eid => $envoi) { 
							?>

							<tr class="form clickme" onclick="window.location.href = '<?php url('admin/module/communication/message_'.$eid); ?>';">
								<td>
									<input type="checkbox" name="type[]" <?php if ($envoi['type'] == 'sms') echo ' checked'; ?> />
									<label class="type-message" />
								</td>
								<td>
									<textarea readonly onclick="event.stopPropagation();"><?php echo simplifyText(html_entity_decode($envoi['type'] == 'sms' ? $envoi['contenu'] : $envoi['titre']."\n\n".$envoi['contenu'])); ?> </textarea>
								</td>
								<td class="content"><?php echo stripslashes(ucname($envoi['prenom'].' '.$envoi['nom'])); ?></td>
								<td class="content"><?php echo empty($envoi['date']) ? '' : printDateTime($envoi['date']); ?></td>
								<td class="content"><?php echo $envoi['envois'].' / '.$envoi['participants']; ?></td>
								<td class="content"><?php echo $envoi['echecs']; ?></td>
								<td class="content"><?php 
									$date = new DateTime($envoi['date']);
									echo printEtatEnvois($date <= $now, $envoi['participants'], $envoi['envois'], $envoi['echecs']);

									if ($date > $now && $envoi['participants'] > $envoi['envois'] + $envoi['echecs']) { ?>

									<a href="?cancel=<?php echo $eid; ?>"><img src="<?php url('assets/images/actions/delete.png'); ?>" alt="D" /></a>

									<?php } ?></td>
							</tr>

							<?php } ?>

						</tbody>
					</table>



				<center><a href="?publipostage=<?php echo $onlyPublipostage ? 0 : 1; ?>"><?php 
					echo empty($onlyPublipostage) ? 'Afficher seulement les publipostages' : 'Affiches tous les messages'; ?></a></center>

				<?php } else { ?>

				<style>
				.conversation_iframe {
					width:100%;
					max-width:800px;
					margin:auto;
				}
				iframe {
					display:block;
					outline:none;
					border:1px solid #999;
					width:100%;
					height:1px;
				}
				.conversation_message {
					border-radius-top:5px;
					color:#FFF;
					padding:5px;
				}
				.conversation_email {
					background:#EF8B26;
				}
				.conversation_sms {
					background:#38C0E2;
				}
				</style>


				<h3>Prévisualisation du message</h3>
				<div class="conversation_iframe">
					<div class="conversation_message conversation_<?php echo $envois[$id_message]['type']; ?>">
						<?php echo $envois[$id_message]['type'] == 'email' ? '<b>Email : </b>'.simplifyText(html_entity_decode($envois[$id_message]['titre']), 0) : '<b>SMS</b>'; ?>
					</div>
					<iframe src="<?php url('admin/module/communication/message_'.$id_message.'?iframe'); ?>" onload="this.style.height=(this.contentDocument.body.scrollHeight + 2) +'px';"></iframe>
				</div>
				<br />
				<br />
				<h3>Liste des destinataires</h3>
				<table class="table table-small">
						<thead>
							<tr>
								<th>Nom</th>
								<th>Prénom</th>
								<th style="width:40px"><small>Tentatives</small></th>
								<th>Echec</th>
								<th>Etat</th>
								<th>Date</th>
							</tr>
						</thead>

						<tbody>

							<?php if (!count($persons)) { ?> 

							<tr class="vide">
								<td colspan="6">Aucun destinataire</td>
							</tr>

							<?php } 

							$now = new DateTime();
							foreach ($persons as $enid => $person) { 
								$date = new DateTime($person['date']);
							?>

							<tr class="form clickme" onclick="window.location.href = '<?php url('admin/module/communication/conversation_'.(empty($person['id']) ? 'envoi_'.$enid : $person['id']).'#envoi'.$enid); ?>';">
								<td class="content"><?php echo stripslashes(ucname($person['nom'])); ?></td>
								<td class="content"><?php echo stripslashes(ucname($person['prenom'])); ?></td>
								<td class="content"><?php echo $person['tentatives']; ?></td>
								<td><textarea readonly onclick="event.stopPropagation();"><?php echo unsecure($person['echec']); ?></textarea></td>
								<td class="content"><?php echo printEtatEnvoi($date <= $now, !empty($person['envoi']), !empty($person['echec'])); 

								if ($date > $now && empty($person['envoi']) && empty($person['echec'])) { ?>

									<a href="?cancel_envoi=<?php echo $enid; ?>"><img src="<?php url('assets/images/actions/delete.png'); ?>" alt="D" /></a>

								<?php } ?></td>
								<td class="content"><?php echo empty($person['envoi']) ? '' : printDateTime($person['envoi']); ?></td>
							</tr>

							<?php } ?>

						</tbody>
					</table





				<?php } ?>

<?php

//Inclusion du pied de page
require DIR.'templates/_footer.php';
