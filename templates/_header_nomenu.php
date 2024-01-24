<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* templates/_heade_rnomenu.php ****************************/
/* Haut de page sans menu **********************************/
/* *********************************************************/
/* Dernière modification : le 20/11/14 *********************/
/* *********************************************************/


//Inclusion de l'entête de page
require DIR.'templates/_header.php';

?>

			<div class="menus noprint">
				<nav>
					<ul><!--

						<?php if (!empty($_SESSION['user'])) { ?>

						--><li class="logout">
							<a href="<?php url('logout'); ?>"<?php if (isset($_SESSION['user']['cas'])) { ?>
								onclick="window.location.href='<?php url('logout'); ?>'+(confirm('Voulez-vous aussi vous déconnecter du CAS?') ? '?cas' : '');return false;"<?php } ?>>Déconnexion</a>
						</li><!--

						--><li class="profil">
							<a href="<?php url('profil'); ?>">Mon profil</a>
						</li><!--

						<?php if (!empty($user['cas'])) { ?>
						
						--><li class="centralien">
							<a href="<?php url('centralien'); ?>">Centralien</a>
							<ul>
								
								<?php foreach ($modulesCentralien as $_module => $_info) {
									list($_titre, $_desc) = $_info;
									echo '<li><a href="'.url('centralien/'.$_module, false, false).'">'.$_titre.'</a></li>';
								} ?>

							</ul>
						</li><!--

						<?php } } ?>
						
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
