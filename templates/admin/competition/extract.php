<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* templates/admin/competition/sportifs_ecoles_groupes.php */
/* Template des sportifs de la compétition *****************/
/* *********************************************************/
/* Dernière modification : le 18/12/14 *********************/
/* *********************************************************/


//Inclusion de l'entête de page
require DIR.'templates/admin/_header_admin.php';

?>
				
				<form class="form-table" method="post">
					<fieldset>
						<h2>Extracteur de données</h2>

						<?php if (!empty($cql) && empty($erreur)) { ?>

						<label class="nomargin">
							<span>Tables</span>
							<div><?php 

							$linked_ = [];
							foreach ($datas_from as $alias => $data) {
								$linked_[] = ($alias == $data[0] ? '' : $data[0].' <i>AS</i> ').'<b>'.$alias.'</b>'.
									(!empty($data[1]) && $data[1] != '#' ? ' <i>WITH</i> '.$data[1].'.'.$links[$data[0]][$datas_from[$data[1]][0]][1].' = '.
										$alias.'.'.$links[$data[0]][$datas_from[$data[1]][0]][0] : '');
							}

							echo implode('<br />', $linked_);

							?></div>
						</label>

						<label class="nomargin">
							<span>Champs</span>
							<div><?php 

							$_selects_ = [];
							foreach ($selects_ as $select) {
								list($alias, $field) = explode('.', $select);
								if (!empty($group_table) && $alias == $group_alias)
									continue;

								if (!in_array($select, $selects))
									$_selects_[] = '<i style="text-decoration:line-through; font-style:normal">'.(!empty($alias) ? '<b>'.$alias.'</b>.' : '').$field.'</i>';

								else
									$_selects_[] = (!empty($alias) ? '<b>'.$alias.'</b>.' : '').$field;
							}

							echo implode(', ', $_selects_);

							?></div>
						</label>

						<?php if (count($orderbys)) { ?>

						<label class="nomargin">
							<span>Ordres</span>
							<div><?php 

							$_orders = [];
							foreach ($orderbys as $order) {
								$desc = preg_match('/ desc$/i', $order);
								list($alias, $field) = explode('.', preg_replace('/ desc$/i', '', $order));
								$_orders[] = '<b>'.$alias.'</b>.'.$field.' <i>'.($desc ? 'DESC' : 'ASC').'</i>';
							}

							echo implode(', ', $_orders);

							?></div>
						</label>

						<?php } if (!empty($group_table)) { ?>
						
						<label class="nomargin">
							<span>Groupe</span>
							<div><?php echo ($group_table != $group_alias ? $group_table.' <i>AS</i> ' : '').'<b>'.$group_alias.'</b>'.
								(!empty($group_link_alias) ? ' <i>WITH</i> '.$group_link_alias : '').
								' | <i>Lien '.(!empty($group_weak) ? 'faible' : 'fort').'</i> | '.
								(empty($havingOne) ? 'Au moins deux éléments' : (empty($havingZero) ? 'Au moins un élément' : 'Toutes cardinalités, même nulle')); ?></div>
						</label>

						<?php } if (!empty($subgroup)) { ?>
						
						<label class="nomargin">
							<span>Sous-groupe</span>
							<div><?php echo '<b>'.$subgroup[0].'</b>.'.$subgroup[1].' | '.
								(empty($havingSubOne) ? 'Au moins deux éléments' : 'Au moins un élément'); ?></div>
						</label>

						<?php } if (count($constraints)) { ?>

						<label class="nomargin">
							<span>Contraintes</span>
							<div><?php foreach ($constraints as $constraint) { ?>
								<?php echo $constraint; ?><br /><?php } ?></div>
						</label>

						<?php } if (count($cases)) { ?>

						<label class="nomargin">
							<span>Calculs</span>
							<div><?php foreach ($cases as $as => $case) { ?>
								<?php echo $case; ?><!--<i>AS</i> <?php echo $as; ?>--><br /><?php } ?></div>
						</label>

						<?php } ?>

						<label class="nomargin">
							<span>Jointure</span>
							<div><i>Jointure <?php echo $left ? 'faible' : 'forte'; ?></i></div> 
						</label>

						<?php } if ($erreur !== false && !empty($cql)) { ?>

						<div class="alerte alerte-erreur">
							<div class="alerte-contenu">
								<?php echo $erreur; ?>
							</div>
						</div>

						<?php } ?> 

						<label class="nomargin" for="form-null">
							<span>
								CQL (<a target="_blank" href="<?php url('assets/pdf/manuel_cql.pdf'); ?>">Aide</a>)<br />
								<input type="submit" value="Executer" />
							</span>
							<textarea <?php if (!empty($sql) && $erreur === false) echo 'class="fourtwo_input" '; ?>name="cql"><?php echo $cql; ?></textarea>
							<?php if (!empty($sql) && $erreur === false) { ?>
							<textarea class="four_input" style="background:#EEE" readonly>SQL en <?php echo round($time, 4)."s\n\n".$sql; ?></textarea>
							<?php } ?>
						</label>
					</fieldset>
				</form>


				<?php if (!empty($datas)) { ?>

				<?php if (!empty($group)) { ?>

				<a class="excel_big" href="?excel">Télécharger en XLSX groupé</a>

				<?php }	

				foreach ($datas as $_group_ => $sous_datas) {
					$data_group = count($sous_datas) ? $sous_datas[array_keys($sous_datas)[0]] : [];
					
					if (!empty($group)) {
				?>

				<h3>
					<?php 

					if (empty($_group_))
						echo '<i>Groupe non défini</i>';

					else {
						$values = [];
						foreach ($orders[$group_table] as $item) {
							if (in_array($item, $fields[$group_table])) {
								$value = $data_group[array_search($group_alias.'.'.$item, $selects) + 1 + (!empty($subgroup) ? 1 : 0)];

								if (in_array($item, $sexe))
									$value = printSexe($value);

								if (in_array($item, $money))
									$value = printMoney($value);

								$values[] = empty($value) ? '' : $value;
							}
						}
						$values = implode(' ', $values);
						echo empty($values) ? '<i>Nom vide</i>' : $values;
					}

					?>
				</h3>

				<a class="excel" href="?excel=<?php echo $_group_; ?>">Télécharger en XLSX</a>


				<?php } else { ?>
				
				<a class="excel" href="?excel">Télécharger en XLSX</a>
				
				<?php } ?>
				
				<table>
					<thead>
						<tr>
							<td colspan="<?php echo $columns; ?>">
								<center>
								Items :  <b><?php echo !isset($data_group[0]) || $data_group[0] === null ? 0 : array_sum(array_map(function($b) {return !isset($b[0]) || $b[0] === null ? 0 : 1;}, $sous_datas)); ?></b>
								<?php if (!empty($subgroup)) { ?> &nbsp; / &nbsp; Sous-groupes : <b><?php echo array_sum(array_map(function($b) { return 1; }, array_unique(array_filter(array_map(function($b) {return !isset($b[0]) || $b[0] === null ? null : '_sb_'.$b[0];}, $sous_datas))))); ?><b><?php } ?>
								</center>
							</td>
						</tr>

						<tr>
							<?php 

							foreach ($selects as $select) { 
								list($alias, $field) = explode('.', $select);

								if (!empty($group) && $alias == $group_alias)
									continue;

							?>

							<th<?php if (in_array($field, array_keys($forms)) || preg_match('/^(was|is|in|had|has)_/', $field)) echo ' style="width:60px"'; ?>>
								<?php echo '<small>'.$alias.'</small><br />'.$field; ?></th>
							
							<?php } ?>
					
						</tr>
					</thead>

					<tbody>

						<?php 

						if (count($sous_datas) == 0 || !isset($data_group[0]) || $data_group[0] === null) {

						?>

						<tr class="vide">
							<td colspan="<?php echo $columns; ?>">Aucun élément</td>
						</tr>

						<?php } else { foreach ($sous_datas as $item_data) {

							if (!empty($subgroup) && (!isset($item_data[0]) || $item_data[0] === null))
								continue;

							if (!empty($subgroup) &&
								isset($_subgroup_) &&
								$_subgroup_ != $item_data[0]) { 

						?>

						<tr>
							<th colspan="<?php echo $columns; ?>"></th>
						</tr>

						<?php }

						if (!empty($subgroup))
							$_subgroup_ = $item_data[0];
						
						?>

						<tr class="form">
							<?php 

							foreach ($selects as $num => $select) { 
								list($alias, $field) = explode('.', $select);
								$item = stripslashes($item_data[$num + 1 + (!empty($subgroup) ? 1 : 0)]);
								$form = in_array($field, array_keys($forms)) || preg_match('/^(was|is|in|had|has)_/', $field);

								if (!empty($group) && $alias == $group_alias)
									continue;
							?>

							<td<?php if (!$form) echo ' class="content"'; ?>>
								
								<?php 

								if ($form && $item != null) { 
									$checked = $item;
									if (in_array($field, $sexe)) 
										$checked = $item != 'f';

								?>
									<input type="checkbox" <?php if (!empty($checked)) echo 'checked'; ?> />
									<label class="<?php echo (!empty($forms[$field]) ? $forms[$field] : '').
										(in_array($field, $sexe) && $item == 'm' ? ' sexe-m' : ''); ?>"></label>
								
								<?php 

								} else if (in_array($field, $money) && $item != null) { 
									echo printMoney($item); 
								} else { 
									echo $item;
								}
								
								?>

							</td>

							<?php } ?>
						</tr>

						<?php } } ?>

					</tbody>
				</table>

				<?php } } ?>

<?php

//Inclusion du pied de page
require DIR.'templates/_footer.php';
