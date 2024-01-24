<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* templates/admin/logement/filles.php *********************/
/* Template des filles logées ******************************/
/* *********************************************************/
/* Dernière modification : le 16/02/15 *********************/
/* *********************************************************/

$cql = 'T|e:ecoles:|p:participants:|tarifs_ecoles:p|t:tarifs:
TL|cp:chambres_participants:|c:chambres:|sp:sportifs:|eq:equipes:sp|ec:ecoles_sports:eq|s:sports:ec
F|c.numero|s.sport|s.sexe|p.pompom|p.fanfaron|p.cameraman|p.nom|p.prenom|p.sexe|e.nom|p.telephone|t.logement|cp.respo
B|e.nom|p.sexe:DESC|t.logement:DESC|s.sport|s.sexe|p.nom|p.prenom'."\n".
(!empty($_GET['ecole']) ? 'C|e.id:'.$_GET['ecole']."\n" : '').
(!empty($_GET['filter']) ? 'C|UP|p.nom:like:"%'.secure($_GET['filter']).'%"|OR|p.prenom:like:"%'.secure($_GET['filter']).'%"|DOWN'."\n" : '').
'C|UP|p.sexe:"f"|t.logement:1|OR|c.id:notnull|DOWN
G|e';

//Inclusion de l'entête de page
require DIR.'templates/admin/_header_admin.php';

?>	
				<style type="text/css">
					.ui-autocomplete .ui-menu-item span {
						font-size:75%;
					}

					table tr.respo-chambre td {
						background-color:#FDF !important;
					}
				</style>
				
				<form method="post" action="<?php url('admin/module/competition/extract'); ?>">
					<h2>
						Liste des filles logées
						<input type="submit" value="CQL" />
					</h2>

					<input type="hidden" name="cql" value='<?php echo $cql; ?>' />
				</form>
				
				<center>
					<form method="get" action="#top">
						<fieldset>
							<label>
								<select name="ecole" style="width:300px" onchange="$(this).parent().parent().parent().submit();">
									<option value="" disabled <?php if (!isset($_GET['ecole']))
										echo 'selected'; ?>>Choisissez une école</option>

									<option value="0" <?php if (isset($_GET['ecole']) && !in_array($_GET['ecole'], array_keys($ecoles)))
										echo 'selected'; ?>>Toutes les écoles</option>

									<?php foreach ($ecoles as $id => $ecole) { ?>

									<option value="<?php echo $id; ?>" <?php if (!empty($_GET['ecole']) && $_GET['ecole'] == $id) 
										echo ' selected'; ?>><?php echo stripslashes($ecole['nom']); ?></option>

									<?php } ?>

								</select>
								<input type="submit" class="success" value="Choisir" style="margin: 0px !important; width:100px" /><br />
							</label>

							<label>
								<input name="filter" style="width:300px" type="text" value="<?php echo !empty($_GET['filter']) ? $_GET['filter'] : ''; ?>" placeholder="Filtrer sur les noms/prénoms/chambres" />
								<input type="submit" class="success" value="Filter" style="margin: 0px !important; width:100px" /><br />
							</label>
						</fieldset>
					</form>
				</center>
				

				<?php if (isset($_GET['ecole'])) { ?>

				<br />
				<br />

				<?php 

				$count = 0;
				foreach ($ecoles as $eid => $ecole) { 
					if (empty($filles[$eid]) && empty($other_loges[$eid]))
						continue; 

					$count++;
					$group = empty($filles[$eid]) ? [] : $filles[$eid];
					$data = isset($group[0]) ? $group[0] : [];
				?>
				<h3><?php echo stripslashes($ecole['nom']); ?></h3>

				<?php if ((empty($_GET['ecole']) || !in_array($_GET['ecole'], array_keys($ecoles))) && empty($group)) {} else { ?>
				<table>
					<thead>
						<tr>
							<td colspan="9">
								<center>Filles à loger :  <b><?php echo empty($data['id']) ? 0 : count($group); ?></b>
								</center>
							</td>
						</tr>

						<tr>
							<th style="width:150px !important">Chambre</th>
							<th>Sport</th>
							<th style="width:50px"><small>Pompom</small></th>
							<th style="width:50px"><small>Fanfaron</small></th>
							<th style="width:50px"><small>Cameraman</small></th>
							<th>Nom</th>
							<th>Prenom</th>
							<th style="width:200px !important">Téléphone</th>
							<th style="width:100px !important">Détails</th>
						</tr>
					</thead>

					<tbody>

						<?php 

						$phase = null;
						if (empty($data['id']) || !count($group)) { ?> 

						<tr class="vide">
							<td colspan="9">Aucune fille</td>
						</tr>

						<?php } else foreach ($group as $fille) {
							$numero = !empty($fille['cid']) ? $fille['numero'] : '';

							if ($phase === null || $fille['phase'] != $phase) {
								$phase = $fille['phase'];
								echo '<tr class="vide"><td colspan="13" style="background:#CCC">'.$labels_phases[$phase].'</td></tr>';
							}

						?>

						<tr class="form<?php if ($fille['respo'] && !empty($numero)) echo ' respo-chambre" data-respo="'.$numero; ?>">
							<td style="width:150px !important">
								<input style="background-color:<?php echo empty($fille['cid']) ? 'transparent' : colorChambre($numero); ?>; color:<?php echo empty($fille['cid']) ? 'black' : colorContrast(colorChambre($numero)); ?>" type="text" class="chambre-auto" value="<?php echo $numero; ?>" data-format="<?php echo $fille['format_long'] ? 'long' : 'court'; ?>"  />
								<input type="hidden" class="chambre-hidden" value="<?php echo $numero; ?>" />
								<input type="hidden" class="chambre-pid" value="<?php echo $fille['id']; ?>" />
							</td>
							<td><div><?php echo empty($fille['sid']) ? '<i>Sans sport</i>' : (stripslashes($fille['sport']).' '.printSexe($fille['sexe'])); ?></div></td>
							<td>
								<input type="checkbox" <?php if ($fille['pompom']) echo 'checked '; ?>/>
								<label class="extra extra-pompom"></label>
							</td>
							<td>
								<input type="checkbox" <?php if ($fille['fanfaron']) echo 'checked '; ?>/>
								<label class="extra extra-fanfaron"></label>
							</td>
							<td>
								<input type="checkbox" <?php if ($fille['cameraman']) echo 'checked '; ?>/>
								<label class="extra extra-video"></label>
							</td>
							<td><div><?php echo stripslashes(strtoupper($fille['nom'])); ?></div></td>
							<td><div><?php echo stripslashes($fille['prenom']); ?></div></td>
							<td><input class="chambre-telephone" type="text" value="<?php echo stripslashes($fille['telephone']); ?>" /></td>
							<td><?php if (!empty($numero)) { ?>
								<center>
									<img style="cursor:pointer; display:inline" onclick="$changeRespoChambre(<?php echo $fille['id']; ?>, '<?php echo $numero; ?>', $(this));" src="<?php url('assets/images/actions/captain.png'); ?>" />
									<a href="?filter=<?php echo $numero; ?>">Détails</a>
								</center><?php } ?></td>
						</tr>

						<?php } ?>

					</tbody>
				</table>
				<?php } ?>

				<?php if (!empty($_GET['ecole']) && in_array($_GET['ecole'], array_keys($ecoles))) { ?>

				<form method="post" id="ajout_other">
					<center>
						<input type="submit" class="success" value="Loger une autre personne" />
					</center>
				</form>
				<br />

				<?php } 
				
				$group = empty($other_loges[$eid]) ? [] : $other_loges[$eid];
				$data = isset($group[0]) ? $group[0] : [];

				if ((empty($_GET['ecole']) || !in_array($_GET['ecole'], array_keys($ecoles))) && empty($group)) {} else { ?>
				<a name="others<?php echo $eid; ?>"></a>
				<table>
					<thead>
						<tr>
							<td colspan="12">
								<center>Autres personnes logées :  <b><?php echo empty($data['id']) ? 0 : count($group); ?></b>
								</center>
							</td>
						</tr>

						<tr>
							<th style="width:150px !important">Chambre</th>
							<th>Nom</th>
							<th>Prenom</th>
							<th style="width:50px"><small>Sexe</small></th>
							<th style="width:50px"><small>Pompom</small></th>
							<th style="width:50px"><small>Fanfaron</small></th>
							<th style="width:50px"><small>Caméraman</small></th>
							<th>Sport</th>
							<th style="width:50px"><small>Logement</small></th>
							<th>Téléphone</th>
							<th style="width:100px !important">Détails</th>
							<th>Actions</th>
						</tr>
					</thead>

					<tbody>

						<?php if (empty($data['id']) || !count($group)) { ?> 

						<tr class="vide">
							<td colspan="12">Aucune autre personnes logée</td>
						</tr>

						<?php } else foreach ($group as $other) {
							$numero = !empty($other['cid']) ? $other['numero'] : '';
						?>

						<tr class="form<?php if ($other['respo'] && !empty($numero)) echo ' respo-chambre" data-respo="'.$numero; ?>">
							<td style="width:150px !important">
								<input style="background-color:<?php echo empty($other['cid']) ? 'transparent' : colorChambre($numero); ?>; color:<?php echo empty($other['cid']) ? 'black' : colorContrast(colorChambre($numero)); ?>" type="text" readonly value="<?php echo $numero; ?>" />
							</td>
							<td><div><?php echo stripslashes(strtoupper($other['nom'])); ?></div></td>
							<td><div><?php echo stripslashes($other['prenom']); ?></div></td>
							<td>
								<input type="checkbox" <?php if ($other['sexe'] == 'h') echo 'checked '; ?>/>
								<label class="sexe"></label>
							</td>
							<td>
								<input type="checkbox" <?php if ($other['pompom']) echo 'checked '; ?>/>
								<label class="extra extra-pompom"></label>
							</td>
							<td>
								<input type="checkbox" <?php if ($other['fanfaron']) echo 'checked '; ?>/>
								<label class="extra extra-fanfaron"></label>
							</td>
							<td>
								<input type="checkbox" <?php if ($other['cameraman']) echo 'checked '; ?>/>
								<label class="extra extra-video"></label>
							</td>
							<td><div><?php echo empty($other['sid']) ? '<i>Sans sport</i>' : (stripslashes($other['sport']).' '.printSexe($other['ssexe'])); ?></div></td>
							<td>
								<input type="checkbox" <?php if ($other['logement']) echo 'checked '; ?>/>
								<label class="package"></label>
							</td>
							<td><div><?php echo stripslashes($other['telephone']); ?></div></td>
							<td><?php if (!empty($numero)) { ?>
								<center>
									<img style="cursor:pointer; display:inline" onclick="$changeRespoChambre(<?php echo $other['id']; ?>, '<?php echo $numero; ?>', $(this));" src="<?php url('assets/images/actions/captain.png'); ?>" />
									<a href="<?php url('admin/module/logement/_'.substr($numero, 0, 1).'#'.$numero); ?>">Détails</a>
								</center><?php } ?></td>
							<td class="actions"><a href="?ecole=<?php echo $_GET['ecole'].(empty($_GET['filter']) ? '' : '&filter='.$_GET['filter']); ?>&del=<?php echo $other['id']; ?>#others<?php echo $eid; ?>"><img src="<?php url('assets/images/actions/delete.png'); ?>" alt="" /></a></td>
						</tr>

						<?php } ?>

					</tbody>
				</table>

				<?php }  }


				if (!empty($_GET['ecole']) && in_array($_GET['ecole'], array_keys($ecoles))) { ?>

				<div id="modal-ajout-other" class="modal">
					<form method="post" action="<?php url('admin/module/logement/filles?ecole='.$_GET['ecole']); ?>#others<?php echo $_GET['ecole']; ?>">
						<fieldset>
							<legend>Ajout d'une autre personne</legend>

							<label for="form-chambre" class="needed">
								<span>Chambre</span>
								<input id="form-chambre" style="background-color:transparent" type="text" class="chambre-auto" value="" data-other="true" data-format="<?php echo $ecoles[$_GET['ecole']]['format_long'] ? 'long' : 'court'; ?>" />
								<input type="hidden" class="chambre-hidden" value="" name="chambre" />
								<input type="hidden" class="chambre-pid" value=""  />
							</label>

							<label for="form-other" class="needed">
								<span>Personne</span>
								<select id="form-other" name="other">
									<option value="" disabled selected>Choisissez une personne</option>

									<?php foreach ($others as $other) { ?>

									<option value="<?php echo $other['id']; ?>"><?php echo stripslashes(strtoupper($other['nom']).' '.$other['prenom']); ?></option>

									<?php } ?>

								</select>
							</label>

							<center>
								<input type="submit" class="success" value="Ajouter la personne" name="add_other" />
							</center>
						</fieldset>
					</form>
				</div>

				<?php } else if (!$count && !empty($_GET['filter'])) { ?>

				<div class="alerte alerte-erreur">
					<div class="alerte-contenu">
						Aucune fille ou autre personne ne correspond au filtre spécifié.
					</div>
				</div>

				<?php } ?>


				<script type="text/javascript">
			   	$(function() {
			    	$speed =  <?php echo APP_SPEED_ERROR; ?>;

			    	$changeRespoChambre = function(id, chambre, elem) {
			    		elem = $(elem).parent().parent().parent(); //tr
						var was = $(elem).attr('data-respo');
						was = was != '0' && was != undefined && was != '';

						$(document).find('tr[data-respo="' + chambre + '"]').each(function() {
							$(this).attr('data-respo', '0');
							$(this).removeClass('respo-chambre');
						});

						if (!was) {
							$(elem).attr('data-respo', chambre+'');
							$(elem).addClass('respo-chambre');
						}

						$.ajax({
							url: "<?php url('admin/module/logement/_'); ?>" + chambre.substr(0, 1) + "?respo",
						  	method: "POST",
						  	cache: false,
							dataType: "json",
							data:{id: id}
						});
			    	};

			        $analysisAjout = function(elem, event, force) {
			            if (event.keyCode == 13 || force) {
			              	$parent = elem.children('fieldset');
			              	$first = $parent.children('label').first();
			  				$chambre = $first.children('input').first(); //chambre-hidden
			  				$other = $first.next().children('select');
			  				$erreur = false;
			  				
			                if (!$chambre.next().val().trim()) {
			                	$erreur = true;
			                	$chambre.addClass('form-error-important').removeClass('form-error-important', $speed).focus();
			                }

			                if (!$other.val().trim()) {
			                	$erreur = true;
			                	$other.addClass('form-error').removeClass('form-error', $speed).focus();
			                }

			                if ($erreur)
			                	event.preventDefault();   
			           
			            }
			        };

					$('#modal-ajout-other form').bind('submit', function(event) { $analysisAjout($(this), event, true); });
					$('form#ajout_other').bind('submit', function(e) { e.preventDefault();
						$('#modal-ajout-other').modal({
							focus:false,
							onClose: function () {
								//Fix car l'autocomplete ne se montre pas après avoir fermé puis réouvert le modal
								//Donc on actualise la page
								window.location.href="";
							}
						});
					});

			    	var canSearch = false;
			    	var onlyOnEnter = false;
				    $(".chambre-auto").autocomplete({
				        source: function( request, response ) {
							var $me = this.element;
							$.ajax({
								url: "<?php url('admin/module/logement/filles?ajax'); ?>",
							  	method: "POST",
							  	cache: false,
								dataType: "json",
								data:{
									other: typeof $me.data('other') === 'undefined' ? 0 : 1,
									filtre: request.term, 
									format: $me.data('format'),
									chambre: $me.parent().children('.chambre-hidden').val()},
								success: function(data) {
									response(data);
								}
							});
						},
				        minLength:0,
				        select: function(e, ui) {
				            e.preventDefault();
				            var previous = $(this).parent().children('.chambre-hidden').val();

				            $(this).parent().children('.chambre-hidden').val(ui.item.numero).trigger('change');
				            $(this).val(ui.item.numero);
				            $(this).css('background-color', ui.item.bgColor);
				            $(this).css('color', ui.item.color);
				            $(':focus').blur();
				          
				            if (typeof $(this).data('other') === 'undefined' &&
				            	ui.item.numero != previous) {
				            	var pid = $(this).parent().children('.chambre-pid').val();
				            	$(this).parent().parent().attr('data-respo', '0');
								$(this).parent().parent().removeClass('respo-chambre');

				  				$.ajax({
				  					url: "<?php url('admin/module/logement/filles?maj'); ?>",
				  					method: "POST",
								  	cache: false,
				  					data: {
				  						pid: pid,
				  						chambre: ui.item.numero
				  					}
				  				});

				  				if (ui.item.numero) {
				  					var batiment = ui.item.numero.substr(0, 1);
				  					$(this).parent().parent().find('td:last-of-type')
				  						.html('<center>' + 
				  							'<img style="cursor:pointer; display:inline" onclick="$changeRespoChambre(' + 
				  								pid + ', \'' + ui.item.numero + '\', $(this));" src="<?php url('assets/images/actions/captain.png'); ?>" /> ' +
											'<a href="?filter=' + ui.item.numero + '">Détails</a></center>');
				  				} else { 
				  					$(this).parent().parent().find('td:last-of-type').html('');
				  				}
				  			}
				        },
				        focus: function(e, ui) {
	            			/*$(this).val(ui.item.numero);
				            $(this).css('background-color', ui.item.bgColor);
				            $(this).css('color', ui.item.color);*/
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
			        	$(this).val($(this).parent().children('.chambre-hidden').val());
			        });
	                            
	                $.ui.autocomplete.prototype._renderItem = function(ul, item) {
	                    return $("<li>")
	                    	.append("<a>" + item.html + "</a>")
	                    	.appendTo(ul);
	       			 };

			        $('.chambre-telephone').change(function(event) {
			        	$elem = $(this);
						$parent = $(this).parent().parent();
						$first = $parent.children('td:first');
						$pid = $first.children('input.chambre-pid');
						$telephone = $(this);

		  				$.ajax({
		  					url: "<?php url('admin/module/logement/filles?maj'); ?>",
		  					method: "POST",
						  	cache: false,
		  					data: {
		  						pid: $pid.val(),
		  						telephone: $telephone.val()
		  					}, 
		  					success: function(data) {
		  						$elem.val(data);
		  					}
		  				});
					});
			    });
				</script>

				<?php } ?>


<?php

//Inclusion du pied de page
require DIR.'templates/_footer.php';
