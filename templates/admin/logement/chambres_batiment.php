<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* templates/admin/logement/chambres_batiment.php **********/
/* Template des chambres du batiment ***********************/
/* *********************************************************/
/* Dernière modification : le 19/01/15 *********************/
/* *********************************************************/

$cql = 'T|c:chambres:|chambres_participants|p:participants:|e:ecoles:
TL|sp:sportifs:|eq:equipes:sp|ecoles_sports:eq|s:sports:
S|c
C|c.numero:like:"'.$batiment.'%"
B|c.numero
F|c.numero|c.nom|c.prenom|c.surnom|c.etat|c.etat_clef|c.lit_camp|p.nom|p.prenom|p.telephone|e.nom|s.sport|s.sexe';


//Inclusion de l'entête de page
require DIR.'templates/admin/_header_admin.php';

?>
				
				<form method="post" action="<?php url('admin/module/competition/extract'); ?>">
					<h2>
						Chambres du Bâtiment <?php echo $batiment; ?>
						<input type="submit" value="CQL" />
					</h2>

					<input type="hidden" name="cql" value='<?php echo $cql; ?>' />
				</form>

				<?php

				$etages = in_array($batiment, array('A', 'B', 'C')) ? 4 : 6;
				$nbfilles = max(1, $nbmaxfilles);

				?>

				<a href="<?php url('admin/module/logement/chambres'); ?>" class="excel_big">Retourner aux bâtiments</a>
				<a href="?excel" class="excel">Télécharger en XLSX</a>

				<table>
					<tbody>

						<?php 

						foreach (range(0, $etages) as $etage) {
							$chambres = $etage == 0 ? 
								($etages == 6 ? 8 : 20) : 
								($etages == 6 ? 16 : 17);


						ob_start();

						?>

						<tr>
							<th style="width:100px;"><?php echo $batiment.$etage; ?></th>
							<th>Proprio</th>
							<th style="width:120px">Etat</th>
							<th style="width:120px">Format</th>
							<?php for($i = 1; $i <= $nbfilles; $i++) { ?>
							<th>Fille <?php echo $i; ?></th>
							<?php } ?>
							<th style="width:150px">Clefs</th>
							<th style="width:100px">Lit de Camp</th>
						</tr>

						<?php

						$header = ob_get_clean();
						
						$nb = 0;
						foreach (range(1, $chambres) as $numero) {
							$chambre = sprintf('%s%d%02d', $batiment, $etage, $numero);
							$color = colorChambre($chambre);
							$proprio = isset($proprios[$chambre]) ? $proprios[$chambre] : null;
						
						if ($proprio == null)
							continue;

						if (!$nb)
							echo $header;

						$nb++;

						?>

						<tr class="form" data-format="<?php echo $proprio['format']; ?>">
							<td style="background-color:<?php echo $color; ?>;color:<?php echo colorContrast($color); ?>;text-align:center;font-weight:bold;">
								<input type="hidden" value="<?php echo $chambre; ?>" />
								<?php echo $chambre; ?><br />
								<small style="color:inherit"><a style="font-weight:normal;" href="<?php url('admin/module/logement/'.$batiment.'#'.$chambre); ?>"><i>Détails</i></a></small>
							</td>
							<td>
								<center><?php echo stripslashes($proprio['prenom'].' '.$proprio['nom'].'<br /><i>'.$proprio['surnom'].'</i>'); ?></center>
							</td>
							<td style="background-color:<?php echo empty($proprio['etat']) ? $colorsEtatChambre['noncontacte'] : $colorsEtatChambre[$proprio['etat']]; ?>">
								<a name="<?php echo $chambre; ?>"></a>
								<div style="background-color:<?php echo empty($proprio['etat']) ? $colorsEtatChambre['noncontacte'] : $colorsEtatChambre[$proprio['etat']]; ?>"><?php echo $labelsEtatChambre[!empty($proprio['etat']) ? $proprio['etat'] : 'noncontacte']; ?></div>
							</td>
							<td style="background-color:<?php echo empty($proprio['format']) ? $colorsFormatChambre['nonrenseigne'] : $colorsFormatChambre[$proprio['format']]; ?>">
								<div style="background-color:<?php echo empty($proprio['format']) ? $colorsFormatChambre['nonrenseigne'] : $colorsFormatChambre[$proprio['format']]; ?>"><?php echo $labelsFormatChambre[!empty($proprio['format']) ? $proprio['format'] : 'nonrenseigne']; ?></div>
							</td>

							<?php

							$participants_proprio = explode(',', $proprio['filles']);
							foreach ($participants_proprio as $key => $particpant) {
								if (empty($participants[$particpant]))
									unset($participants_proprio[$key]);
							}
							$participants_proprio = array_values($participants_proprio);

							?>

							<?php 

							for($i = 0; $i < $nbfilles; $i++) { ?>								
								<?php 

								if (isset($participants_proprio[$i])) { 
									$participant = $participants[$participants_proprio[$i]];

								?>
								
								<td<?php if ($participants_proprio[$i] == $proprio['respo']) echo ' style="background-color:#FDF" data-respo="1" '; ?> data-participant="<php echo $participant['id']; ?>"><center style="height:auto; padding:0px 5px; line-height:20px;">
								<b>
									<img style="cursor:pointer;" onclick="$changeRespoChambre(<?php echo $participant['id']; ?>, '<?php echo $chambre; ?>', $(this));" src="<?php url('assets/images/actions/captain.png'); ?>" />
									<?php echo stripslashes(strtoupper($participant['nom']).' '.$participant['prenom']); ?>
									<img style="cursor:pointer;" onclick="$deleteParticipant(<?php echo $participant['id']; ?>, '<?php echo $chambre; ?>');" src="<?php url('assets/images/actions/delete.png'); ?>" /></b><br />
								<small><?php echo stripslashes($participant['enom']).' / '; 
									echo empty($participant['sid']) ? '<i>Sans sport</i>' :
										($participant['sport'].' '.printSexe($participant['sexe'])); ?><br />
									<?php echo stripslashes($participant['telephone']); ?></small>
								</center></td>

								<?php } else if ($i < $proprio['places']) {
									$search = true;
								 ?>

								<td style="padding:0px" onclick="$(this).children('input').focus()">
									<input style="height:100% !important" data-chambre="<?php echo $chambre; ?>" type="text" placeholder="Ajouter une fille" class="fille-auto" value="" />
								</td>

								<?php } else { ?>

								<td></td>

								<?php } ?>

								
							
							<?php } ?>

							<td style="background-color:<?php echo empty($proprio['etat_clef']) ? $colorsEtatClef['pas_recue'] : $colorsEtatClef[$proprio['etat_clef']]; ?>">
								<select style="background-color:transparent;" onchange="$(this).parent().css('background-color', $(this).children('option:selected').attr('color'));">
									
									<?php foreach ($labelsEtatClef as $label => $description) { ?>

									<option value="<?php echo $label; ?>" color="<?php echo $colorsEtatClef[$label]; ?>"<?php
										if (empty($proprio['etat_clef']) && $label == 'pas_recue' ||
										 	!empty($proprio['etat_clef']) && $proprio['etat_clef'] == $label) echo ' selected'; ?>><?php echo $description; ?></option>

									<?php } ?>

								</select>
							</td>

							<td style="background-color:<?php echo $proprio['lit_camp'] > 0 ? '#FAA' : 'transparent'; ?>">
								<input style="height:100% !important" onchange="$(this).parent().css('background-color', $(this).val() > 0 ? '#FAA' : 'transparent');"type="number" step="1" min="0" value="<?php echo !empty($proprio['lit_camp']) ? $proprio['lit_camp'] : '0'; ?>" />
							</td>

						</tr>

						<?php 

						}						

						}

						?>

					</tbody>
				</table>

				

				<center>
					<img style="height:200px;" src="<?php url('assets/images/form/etatsClefs.png'); ?>" alt="" />
				</center>

				<script type="text/javascript">
				$(function() {
					var block = false;
					$('td input[type=text], td input[type=number], td input[type=checkbox], td input[type=password], td select').change(function(event) {
						if (block == true)
							return;

						$parent = $(this).parent().parent();
						$first = $parent.children('td:first');
						$chambre = $first.children('input');
						$clef = $first<?php echo str_repeat('.next()', 4+$nbfilles); ?>.children('select');
						$lit = $first<?php echo str_repeat('.next()', 4+$nbfilles); ?>.next().children('input');

		  				$.ajax({
		  					url: "<?php url('admin/module/logement/_'.$batiment.'?maj'); ?>",
		  					method: "POST",
						  	cache: false,
		  					data: {
		  						chambre: $chambre.val(),
		  						clef: $clef.val(),
		  						lit: $lit.val()
		  					}
		  				});
					});

					$deleteParticipant = function(id, chambre) {
						$.ajax({
							url: "<?php url('admin/module/logement/_'.$batiment.'?delete'); ?>",
						  	method: "POST",
						  	cache: false,
							dataType: "json",
							data:{id: id},
							success: function() { 
									window.location.href = '<?php url('admin/module/logement/_'.$batiment); ?>';
									window.location.reload(true);
							}
						});			       		
					};

					$changeRespoChambre = function(id, chambre, elem) {
						elem = $(elem).parent().parent().parent(); //td
						var was = $(elem).data('respo') == '1';

						$(elem).parent().find('td[data-participant]').css('background-color', 'inherit').data('respo', '0');

						if (!was)
							elem.css('background-color', '#FDF').data('respo', '1');

						$.ajax({
							url: "<?php url('admin/module/logement/_'.$batiment.'?respo'); ?>",
						  	method: "POST",
						  	cache: false,
							dataType: "json",
							data:{id: id}
						});
					};

					var canSearch = false;
			    	var onlyOnEnter = false;
				    $(".fille-auto").autocomplete({
				        source: function( request, response ) {
							var $me = this.element;
							var $color = $($me).parent().css('background-color');
							$.ajax({
								url: "<?php url('admin/module/logement/_'.$batiment.'?ajax'); ?>",
							  	method: "POST",
							  	cache: false,
								dataType: "json",
								data: {
									filtre: request.term,
									format: $me.parent().parent().data('format')
								},
								success: function(data) {
									response(data);
									if ($.isEmptyObject(data))
										$($me).parent().animate({backgroundColor:'#FBB'}, 100, function() {
		                					$(this).animate({backgroundColor:$color}, 1000); });
								}
							});
						},
				        select: function(e, ui) {
				            e.preventDefault();

				            $(this).val(ui.item.nom + ' ' + ui.item.prenom);
				            $(':focus').blur();
				          
			  				$.ajax({
			  					url: "<?php url('admin/module/logement/_'.$batiment.'?add'); ?>",
			  					method: "POST",
							  	cache: false,
								dataType: "json",
			  					data: {
			  						pid: ui.item.id,
			  						chambre: $(this).data('chambre')
			  					},
								success: function() { 
									window.location.href = '<?php url('admin/module/logement/_'.$batiment); ?>#' + $(this).data('chambre');
									window.location.reload(true);
								}
			  				});

			  			
				        },
				        minLength:0,
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
			        });
	                            
	                $.ui.autocomplete.prototype._renderItem = function(ul, item) {
	                    return $("<li>")
	                    	.append("<a style='line-height:1em !important'>" + item.nom + ' ' + item.prenom + 
	                    		'<br /><small>(' + item.enom + " / " + 
	                    		(item.sid == null ? "<i>Sans sport</i>" : item.sport) + ")</small></a>")
	                    	.appendTo(ul);
	       			 };
	
				});
				</script>


<?php

//Inclusion du pied de page
require DIR.'templates/_footer.php';
