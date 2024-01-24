<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* templates/admin/logement/recensement_batiment.php *******/
/* Template des chambres du batiment ***********************/
/* *********************************************************/
/* Dernière modification : le 19/01/15 *********************/
/* *********************************************************/

$cql = 'T|c:chambres:
F|c.numero|c.nom|c.prenom|c.surnom|c.telephone|c.email|c.places|c.etat|c.lit_camp|c.bracelet|c.commentaire
C|c.numero:like:"'.$batiment.'%"';

//Inclusion de l'entête de page
require DIR.'templates/admin/_header_admin.php';

?>
				<form method="post" action="<?php url('admin/module/competition/extract'); ?>">
					<h2>
						Recensement du Bâtiment <?php echo $batiment; ?>
						<input type="submit" value="CQL" />
					</h2>

					<input type="hidden" name="cql" value='<?php echo $cql; ?>' />
				</form>
								
				<?php

				$etages = in_array($batiment, array('A', 'B', 'C')) ? 4 : 6;
				
				?>

				<a href="<?php url('admin/module/logement/recensement'); ?>" class="excel_big">Retourner aux bâtiments</a>
				<a href="?excel" class="excel">Télécharger en XLSX</a>

				<form method="post">
				<table>
					<tbody>

						<?php 

						foreach (range(0, $etages) as $etage) {
							$chambres = $etage == 0 ? 
								($etages == 6 ? 8 : 20) : 
								($etages == 6 ? 16 : 17);

						?>

						<tr>
							<th style="width:100px;">Chambre</th>
							<th style="width:120px">Nom</th>
							<th style="width:120px">Prenom</th>
							<th style="width:120px">Surnom</th>
							<th style="width:120px">Telephone</th>
							<th>Email</th>
							<th style="width:80px">Places</th>
							<th style="width:120px">Etat</th>
							<th style="width:120px">Format</th>
							<th style="width:90px">Lit de Camp</th>
							<th style="width:100px">Bracelet</th>
							<th>Commentaire</th>
							<th style="width:50px"></th>
						</tr>

							<?php
							
							foreach (range(1, $chambres) as $numero) {
								$chambre = sprintf('%s%d%02d', $batiment, $etage, $numero);
								$color = colorChambre($chambre);
								$proprio = isset($proprios[$chambre]) ? $proprios[$chambre] : null;
						
							?>

						<tr class="form">
							<td style="background-color:<?php echo $color; ?>;color:<?php echo colorContrast($color); ?>;text-align:center;font-weight:bold;">
								<a name="<?php echo $chambre; ?>"></a>
								<input type="hidden" value="<?php echo $chambre; ?>" />
								<?php echo $chambre; ?>
							</td>
							<td><input type="text" value="<?php echo $proprio == null ? '' : stripslashes($proprio['nom']); ?>" /></td>
							<td><input type="text" value="<?php echo $proprio == null ? '' : stripslashes($proprio['prenom']); ?>" /></td>
							<td><input type="text" value="<?php echo $proprio == null ? '' : stripslashes($proprio['surnom']); ?>" /></td>
							<td><input type="text" value="<?php echo $proprio == null ? '' : stripslashes($proprio['telephone']); ?>" /></td>
							<td><input type="text" value="<?php echo $proprio == null ? '' : stripslashes($proprio['email']); ?>" /></td>
							<td><input type="number" step="1" min="0" value="<?php echo $proprio == null ? 0 : (int) $proprio['places']; ?>" /></td>
							<td style="background-color:<?php echo $proprio == null || empty($proprio['etat']) ? $colorsEtatChambre['noncontacte'] : $colorsEtatChambre[$proprio['etat']]; ?>">
								<select style="background-color:transparent" onchange="$(this).css('background-color', $(this).children('option:selected').attr('color'));">
									
									<?php foreach ($labelsEtatChambre as $label => $description) { ?>

									<option value="<?php echo $label; ?>" color="<?php echo $colorsEtatChambre[$label]; ?>"<?php
										if (($proprio == null || empty($proprio['etat'])) && $label == 'noncontacte' ||
										 	!empty($proprio['etat']) && $proprio['etat'] == $label) echo ' selected'; ?>><?php echo $description; ?></option>

									<?php } ?>

								</select>
							</td>
							<td style="background-color:<?php echo $proprio == null || empty($proprio['format']) ? $colorsFormatChambre['nonrenseigne'] : $colorsFormatChambre[$proprio['format']]; ?>">
								<select style="background-color:transparent" onchange="$(this).css('background-color', $(this).children('option:selected').attr('color'));">
									
									<?php foreach ($labelsFormatChambre as $label => $description) { ?>

									<option value="<?php echo $label; ?>" color="<?php echo $colorsFormatChambre[$label]; ?>"<?php
										if (($proprio == null || empty($proprio['format'])) && $label == 'nonrenseigne' ||
										 	!empty($proprio['format']) && $proprio['format'] == $label) echo ' selected'; ?>><?php echo $description; ?></option>

									<?php } ?>

								</select>
							</td>
							<td style="background-color:<?php echo $proprio['lit_camp'] > 0 ? '#FAA' : 'transparent'; ?>">
								<input onchange="$(this).parent().css('background-color', $(this).val() > 0 ? '#FAA' : 'transparent');" type="number" step="1" min="0" value="<?php echo !empty($proprio['lit_camp']) ? $proprio['lit_camp'] : '0'; ?>" />
							</td>
							<td>
								<input id="bracelet_<?php echo $chambre; ?>" type="checkbox" <?php if (!empty($proprio['bracelet'])) echo 'checked '; ?>/>
								<label for="bracelet_<?php echo $chambre; ?>"></label>
							</td>
							<td><input type="text" value="<?php echo $proprio == null ? '' : stripslashes($proprio['commentaire']); ?>" /></td>
							<td class="actions">
								<button type="submit" name="listing" value="<?php echo stripslashes($proprio['id']); ?>">
									<img src="<?php url('assets/images/actions/list.png'); ?>" alt="Listing" />
								</button>
							</td>
						</tr>

						<?php 

							}
						}

						?>

					</tbody>
				</table>
			</form>

				<script type="text/javascript">
				$(function() {
					var block = false;
					$('td input[type=text], td input[type=number], td input[type=checkbox], td input[type=password], td select').change(function(event) {
						if (block == true)
							return;

						$parent = $(this).parent().parent();
						$first = $parent.children('td:first');
						$chambre = $first.children('input');
						$nom = $first.next().children('input');
						$prenom = $first.next().next().children('input');
		  				$surnom = $first.next().next().next().children('input');
		  				$telephone = $first.next().next().next().next().children('input');
		  				$email = $first.next().next().next().next().next().children('input');
		  				$places = $first.next().next().next().next().next().next().children('input');
		  				$etat = $first.next().next().next().next().next().next().next().children('select');
		  				$format = $first.next().next().next().next().next().next().next().next().children('select');
		  				$lit = $first.next().next().next().next().next().next().next().next().next().children('input');
		  				$bracelet = $first.next().next().next().next().next().next().next().next().next().next().children('input');
		  				$commentaire = $first.next().next().next().next().next().next().next().next().next().next().next().children('input');

		  				$.ajax({
		  					url: "<?php url('admin/module/logement/'.$batiment.'?maj'); ?>",
		  					method: "POST",
						  	cache: false,
						  	dataType: 'json',
		  					data: {
		  						chambre: $chambre.val(),
		  						nom: $nom.val(),
		  						prenom: $prenom.val(),
		  						surnom: $surnom.val(),
		  						telephone: $telephone.val(),
		  						email: $email.val(),
		  						places: $places.val(),
		  						etat: $etat.val(),
		  						format: $format.val(),
		  						lit: $lit.val(),
		  						bracelet: $bracelet.is(':checked') ? '1' : '0',
		  						commentaire: $commentaire.val()
		  					},
		  					success: function(data) { 
								if (data.error == '1') {
									alert('Des personnes sont logées dans cette chambre, vous ne pouvez changer l\'état ou le format ni le nombre de places');
									block = true;
									$etat.val(data.etat).change();
									$format.val(data.format).change();
									block = false;
								} else {
									$nom.val(data.nom);
									$prenom.val(data.prenom);
									$telephone.val(data.telephone);
								}
		  					}
		  				});
					});
	
				});
				</script>


<?php

//Inclusion du pied de page
require DIR.'templates/_footer.php';
