<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* templates/admin/ecoles/liste.php ************************/
/* Template de la liste du module des Ecoles ***************/
/* *********************************************************/
/* Dernière modification : le 21/11/14 *********************/
/* *********************************************************/


$cql = 'T|e:ecoles:
TL|q:quotas_ecoles:
C|q.quota:"total"
C|OR
C|q.quota:null
F|e.nom|e.ecole_lyonnaise|e.format_long|q.valeur|e.etat_inscription';


//Inclusion de l'entête de page
require DIR.'templates/admin/_header_admin.php';

?>
			
				<form method="post" action="<?php url('admin/module/competition/extract'); ?>">
					<h2>
						Liste des Ecoles
						<input type="submit" value="CQL" />
					</h2>

					<input type="hidden" name="cql" value='<?php echo $cql; ?>' />
				</form>

				<?php if (isset($delete)) { ?>

				<div class="alerte alerte-success">
					<div class="alerte-contenu">
						L'école et toutes ses données ont bien été supprimées
					</div>
				</div>

				<?php } else if (isset($empty)) { ?>

				<div class="alerte alerte-success">
					<div class="alerte-contenu">
						L'école a bien été vidée de ses données, mais pas supprimée.
					</div>
				</div>

				<?php } ?>

				<form method="post" id="ajout_ecole">
					<center>
						<input type="submit" class="success" value="Ajouter une école" />
					</center>
				</form>
				<br />

				<form method="post">
					<table class="table-small">
						<thead>
							<tr>
								<th style="width:40px"></th>
								<th>Nom</th>
								<th style="width:40px"><small>Abrev.</small></th>
								<th style="width:60px"><small>Lyonnaise</small></th>
								<th style="width:60px"><small>Format</small></th>
								<th style="width:150px"><small>Inscrip. / Quota</small></th>
								<th>Etat</th>
								<th class="actions"><small>Historique</small></th>
							</tr>
						</thead>

						<tbody>

							<?php if (!count($ecoles)) { ?> 

							<tr class="vide">
								<td colspan="7">Aucune école</td>
							</tr>

							<?php } foreach ($ecoles as $ecole) { ?>

							<tr class="clickme form" onclick="window.location.href = '<?php url('admin/module/ecoles/'.$ecole['id']); ?>';">
								<td class="content" style="padding:1px !important; line-height:0; vertical-align:middle"><!--
									<?php if (!empty($ecole['token'])) { ?>
									--><center><img src="<?php url('image/'.$ecole['token']); ?>" style="<?php echo ($ecole['width'] && $ecole['width'] * 25 / $ecole['height'] > 35 ? 'width:35px' : 'height:25px'); ?>" /></center><!--
									<?php } ?>
								--></td>
								<td class="content"><?php echo stripslashes($ecole['nom']); ?></td>
								<td class="content"><?php echo stripslashes($ecole['abreviation']); ?></td>
								<td>
									<input type="checkbox" <?php if ($ecole['ecole_lyonnaise']) echo 'checked '; ?>/>
									<label></label>
								</td>
								<td>
									<input type="checkbox" <?php if ($ecole['format_long']) echo 'checked '; ?>/>
									<label class="format"></label>
								</td>
								<td class="content"><center><?php echo $ecole['nb_inscriptions'].(isset($quotas[$ecole['id']]['total']) ? ' / <b>'.$quotas[$ecole['id']]['total'] : '').'</b>'; ?></center></td>
								<td class="content"><?php echo printEtatEcole($ecole['etat_inscription']); ?></td>
								<td class="actions">
									<button type="submit" name="listing" value="<?php echo stripslashes($ecole['id']); ?>">
										<img src="<?php url('assets/images/actions/list.png'); ?>" alt="Listing" />
									</button>
								</td>
							</tr>

							<?php } ?>

						</tbody>
					</table>
				</form>

				<div id="modal-ajout-ecole" class="modal">
					<form method="post">
						<fieldset>
							<legend>Ajout d'une école</legend>

							<label for="form-nom" class="needed">
								<span>Nom</span>
								<input type="text" name="nom" id="form-nom" value="" />
							</label>

							<center>
								<input type="submit" class="success" value="Ajouter l'école" name="add_ecole" />
							</center>
						</fieldset>
					</form>
				</div>

				<script type="text/javascript">
				$(function() {
					$speed =  <?php echo APP_SPEED_ERROR; ?>;

			        $analysisAjout = function(elem, event, force) {
			            if (event.keyCode == 13 || force) {
			              	$parent = elem.children('fieldset');
			              	$first = $parent.children('label').first();
			  				$nom = $first.children('input');
			  				$erreur = false;
			  				
			                if (!$nom.val().trim()) {
			                	$erreur = true;
			                	$nom.addClass('form-error').removeClass('form-error', $speed).focus();
			                }

			                if ($erreur)
			                	event.preventDefault();   
			           
			            }
			        };

					$('#modal-ajout-ecole form').bind('submit', function(event) { $analysisAjout($(this), event, true); });
					$('form#ajout_ecole').bind('submit', function(e) { e.preventDefault(); $('#modal-ajout-ecole').modal(); });
				});
				</script>

<?php

//Inclusion du pied de page
require DIR.'templates/_footer.php';
