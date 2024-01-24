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
					<?php if (empty($id_conversation)) { ?>
					Liste des Conversations
					<?php } else { ?>
					<a href="<?php url('admin/module/communication'); ?>">Liste des Conversations</a>
					<?php } ?> <br />

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

				<?php if (empty($id_conversation)) { ?>


					<?php if (count($recus_unread)) { ?>
						<h3>Messages non lus</h3>

						<table class="table">
							<thead>
								<tr>
									<th style="width:60px">Type</th>
									<th>Résumé</th>
									<th>Auteur</th>
									<th>Date</th>
								</tr>
							</thead>

							<tbody>

								<?php foreach ($recus_unread as $rid => $recu) { ?>

								<tr class="form clickme" onclick="window.location.href = '<?php url('admin/module/communication/conversation_'.(empty($recu['pid']) ? 'recu_'.$rid : $recu['pid']).'#recu'.$rid); ?>';">
									<td>
										<input type="checkbox" name="type[]" <?php if ($recu['type'] == 'sms') echo ' checked'; ?> />
										<label class="type-message" />
									</td>
									<td>
										<textarea readonly onclick="event.stopPropagation();"><?php echo trim(strip_tags(str_replace('<br />', "\n", str_replace(['</p>', '</div>'], '<br /><br />', $recu['type'] == 'sms' ? $recu['message'] : $recu['titre']."\n\n".$recu['message'])))); ?> </textarea>
									</td>
									<td class="content"><?php echo stripslashes(ucname($recu['prenom'].' '.$recu['nom'])); ?></td>
									<td class="content"><?php echo printDateTime($recu['date']); ?></td>
								</tr>

								<?php } ?>

							</tbody>
						</table>

					<?php } ?>

					<h3>Conversations récentes<br />
						<small style="color:var(--grey7); font-weight:normal; font-size:0.75em">Les messages de publipostages ne sont pas pris en compte</small></h3>

					<style type="text/css">
					.unread td {
						background:#AAA !important;
					}
					</style>

					<table class="table" style="margin-bottom:10px">
						<thead>
							<tr class="form">
								<td colspan="6">
									<input placeholder="Rechercher une personne" class="search-autocomplete" />
								</td>
							</tr>

							<tr>
								<th>Exté</th>
								<th>Participant</th>
								<th>Email</th>
								<th>Téléphone</th>
								<th>Envois</th>
								<th>Reçus</th>
							</tr>
						</thead>

						<tbody>

							<?php foreach ($conversations as $conversation) { ?>

							<tr class="form clickme <?php if (!empty($conversation['unread'])) echo 'unread'; ?>" onclick="window.location.href = '<?php url('admin/module/communication/conversation_'.(empty($conversation['exte']) ? '' : (empty($conversation['recus']) ? 'envoi_' : 'recu_')).$conversation['cid']); ?>';">
								<td>
									<input type="checkbox" <?php if (!empty($conversation['exte'])) echo 'checked '; ?>/>
									<label></label>
								</td>
								<td class="content"><?php echo stripslashes(ucname($conversation['prenom'].' '.$conversation['nom'])); ?></td>
								<td class="content"><?php echo $conversation['email']; ?></td>
								<td class="content"><?php echo getPhone($conversation['telephone']); ?></td>
								<td class="content"><?php echo (int) $conversation['envois']; ?> <small><?php echo empty($conversation['last_envoi']) ? '' : printDateTime($conversation['last_envoi']); ?></small></td>
								<td class="content"><?php echo (int) $conversation['recus']; ?> <small><?php echo empty($conversation['last_recu']) ? '' : printDateTime($conversation['last_recu']); ?></small></td>
							</tr>

							<?php } if (!count($conversations)) { ?>

							<tr class="vide">
								<td colspan="6">Aucune conversation récente</td>
							</tr>

							<?php } ?>

						</tbody>
					</table>

					<script type="text/javascript">
					$(function() {
						var canSearch = false;
			    		var onlyOnEnter = false;
						$(".search-autocomplete").autocomplete({
					        source: function( request, response ) {
								var $me = this.element;
								$.ajax({
									url: "<?php url('admin/module/communication/conversations?ajax'); ?>",
								  	method: "POST",
								  	cache: false,
									dataType: "json",
									data:{filtre: request.term},
									success: function(data) {
										response(data);
									}
								});
							},
					        minLength:2,
					        select: function(e, ui) {
					            e.preventDefault();
					            window.location.href = "<?php url('admin/module/communication/conversation_'); ?>" + ui.item.url;
					        },
					        focus: function(e, ui) {
		            			return false;  
					    	},
					        search: function (e, ui) {
					        	var canTempSearch = canSearch;
					        	canSearch = false;
					        	return !onlyOnEnter || onlyOnEnter && canTempSearch;
					        }
					    }).bind('keyup', function(e) {
					    	if (e.keyCode == 13) {
					    		canSearch = true;
					    		$(this).autocomplete("search", $(this).val());
					    	}
					    }).focus(function(){
					    	if (!onlyOnEnter)
					    		$(this).autocomplete("search");        
				        }).blur(function() {
				        });
		                            
		                $.ui.autocomplete.prototype._renderItem = function(ul, item) {
		                    return $("<li>")
		                    	.append("<a>" + (item.exte ? "<i>(Exté)</i> " : '') + item.prenom + ' ' + item.nom + "<br /><small>" + 
		                    		item.email + (item.email && item.telephone ? " / " : "") + item.telephone + "</small></a>")
		                    	.appendTo(ul);
		       			 };
					});
					</script>

					<center><a href="?publipostage=<?php echo $withPublipostage ? 0 : 1; ?>"><?php echo $withPublipostage ? 'Cacher' : 'Afficher'; ?>
						les messages de publipostage</a></center>

				<?php } else { ?>

				<?php if (!empty($people)) { ?>

				<h3>Conversation avec <u><?php 
					echo stripslashes(!empty($people['prenom']) 
						? $people['prenom'].' '.$people['nom']
						: $people['email'].$people['telephone']); ?></u><br />
						<small style="color:gray; font-weight:normal; font-size:0.75em"><?php echo 
							(empty($people['email']) ? '' : '<a href="'.url('admin/module/communication/ecrire?'.
								(empty($id_participant) ? 'to='.$people['email'] : 'id='.$id_participant), false, false).'">'.$people['email'].'</a>').
							(!empty($people['email']) && !empty($people['telephone']) ? ' / ' : '').
							(empty($people['telephone']) ? '' : '<a href="'.url('admin/module/communication/ecrire?type=sms&'.
								(empty($id_participant) ? 'to='.$people['telephone'] : 'id='.$id_participant), false, false).'">'.$people['telephone'].'</a>'); ?></small></h3>
				
				<?php } ?>

				<style>	
				.conversation_message {
					border-radius:5px;
					margin:10px;
					background:#FFF;
					width:80%;
					clear:both;
					border:1px solid #999;
					box-shadow: 0px 0px 5px #999;
					padding:0;
				}
				.conversation_envoi {
					float:right;
				}
				.conversation_message small {
					color:gray;
					background:#EEE;
					display:block;
					padding:5px;
				}
				.conversation_message iframe {
					display:block;
					outline:none;
					border:none;
					width:100%;
					height:1px;
				}
				.conversation_email {
					border-top:10px solid var(--gold-mail);
				}
				.conversation_sms {
					border-top:10px solid var(--blue-sms);
				}
				.conversation_titre {
					font-weight:bold;
					color:var(--foreground);
					display:block;
					padding:0px 10px 5px;
				}
				.conversation_sms .conversation_titre {
					background:var(--blue-sms);
				}
				.conversation_email .conversation_titre {
					background:var(--gold-mail);
				}
				</style>

				<div style="max-width:1000px; margin:auto; background:var(--background); padding:5px 0px">
					
					<?php 

					$now = new DateTime();
					foreach ($conv as $message) { 
						$date = new DateTime($message['date']);
					
					?>

					<div class="conversation_message<?php echo ' '.($message['type'] == 'sms' ? 'conversation_sms' : 'conversation_email').' '.($message['direction'] == 'envoi' ? ' conversation_envoi' : ''); ?>">
						<a name="<?php echo $message['direction'].$message['id']; ?>"></a>

						<span class="conversation_titre"><?php if ($message['direction'] == 'recu') 
							echo ($message['type'] == 'email' ? stripslashes($message['titre']).' ' : '').'<a href="'.url('admin/module/communication/ecrire?recu='.$message['id'], false, false).'"><i>(Répondre)</i></a>'; ?></span>
						
						<small><?php echo ($message['type'] == 'sms' ? 'SMS' : 'Email').' '.($message['direction'] == 'envoi' ? '<a href="'.url('admin/module/communication/message_'.$message['mid'], false, false).'">soumis</a>' : 'reçu'); ?> <b><?php echo printDateTime($message['date']); ?></b><?php 
							if ($message['direction'] == 'envoi') echo ' ('.printEtatEnvoi($date <= $now, !empty($message['envoi']), !empty($message['echec'])).'), '.
								'par <b>'.stripslashes($message['prenom'].' '.$message['nom']).'</b>'.
								(!empty($message['to']) ? ', forcé pour <b>'.(isValidPhone($message['to']) ? getPhone($message['to']) : $message['to']).'</b>' : '').
								(!empty($message['echec']) ? '<br />'.$message['echec'] : ''); ?></small>
						<iframe src="<?php url('admin/module/communication/'.$message['direction'].'_'.$message['id']); ?>" onload="this.style.height = (36 + this.contentDocument.body.offsetHeight) + 'px'; loadTitle(this);" style="height:1px"></iframe>
					</div>

					<?php } ?>

					<div class="clearfix">
					</div>
				</div>

				<script type="text/javascript">
				function loadTitle(iframe) {
					var html = $(iframe).contents().find('html').html();
					var title = html.match(/<title>([^<]*)<\/title>/i);
					$(iframe).parent().find('.conversation_titre').append(title !== null && title[1] ? title[1] : '');
				}
				</script>

				<?php } ?>
<?php

//Inclusion du pied de page
require DIR.'templates/_footer.php';
