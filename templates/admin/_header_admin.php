<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* templates/admin/_header_admin.php ***********************/
/* Haut de page de l'administration ************************/
/* *********************************************************/
/* Dernière modification : le 20/11/14 *********************/
/* *********************************************************/


//Inclusion de l'entête de page
require DIR.'templates/_header.php';

?>

			<div class="menus noprint">
				<nav>
					<ul><!--

						--><li class="logout">
							<a href="<?php url('logout'); ?>" <?php if (isset($_SESSION['user']['cas'])) { ?>
								onclick="window.location.href='<?php url('logout'); ?>'+(confirm('Voulez-vous aussi vous déconnecter du CAS?') ? '?cas' : '');return false;"<?php } ?>>Déconnexion</a>
						</li><!--

						--><li class="profil">
							<a href="<?php url('profil'); ?>">Mon profil</a>
						</li><!--

						--><li>
							<a href="<?php url('admin'); ?>">Modules</a>
							<ul>

								<?php

								if (!empty($user['private_token']) &&
									!empty($user['public_token'])) { 

								?>

								<li><a href="<?php url('api'); ?>" target="_blank">API</a></li>

								<?php }

								foreach ($modulesAdmin as $_module => $_info) {
									list($_titre, $_desc) = $_info;
									if (in_array($_module, $_SESSION['user']['privileges']))
										echo '<li><a href="'.url('admin/module/'.$_module, false, false).'">'.$_titre.'</a></li>';
								}

								?>

							</ul>
						</li><!--
						
						<?php if (!empty($module)) { ?>

						--><li>
							<span><i><?php echo $modulesAdmin[$module][0]; ?></i></span>
							<ul>

								<?php
								foreach ($actionsModule as $_action => $_titre)
									echo '<li><a href="'.url('admin/module/'.$module.'/'.$_action, false, false).'">'.$_titre.'</a></li>';
								?>

							</ul>
						</li><!--

						<?php } if (!empty($user['cas'])) { ?>
						
						--><li class="centralien">
							<a href="<?php url('centralien'); ?>">Centralien</a>
							<ul>
								
								<?php foreach ($modulesCentralien as $_module => $_info) {
									list($_titre, $_desc) = $_info;
									echo '<li><a href="'.url('centralien/'.$_module, false, false).'">'.$_titre.'</a></li>';
								} ?>
								
							</ul>
						</li><!--

						<?php } ?>

					--></ul>
				</nav>
			</div>

			<script type="text/javascript">
			$(function() {
				$('nav > ul > li').click(function() {
					$(this).toggleClass('hover');
				}).mouseout(function() {
					$(this).removeClass('hover');
				});
			});
			</script>

			<div class="main">
