<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* templates/ecoles/_header_ecoles.php *********************/
/* Haut de page de les écoles ******************************/
/* *********************************************************/
/* Dernière modification : le 21/11/14 *********************/
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
						
						<?php

						if (!empty($ecole['etat_inscription']) && 
							in_array($ecole['etat_inscription'], array('ouverte', 'limitee', 'close')) ||
							!empty($_SESSION['user']['privileges']) &&
							in_array('ecoles', $_SESSION['user']['privileges'])) {

						?>

						--><li>
							<a href="<?php url('ecoles/'.$ecole['id'].'/accueil'); ?>">Coordonnées</a>
						</li><!--
						
						<?php if (in_array($ecole['etat_inscription'], ['limitee', 'ouverte']) ||
							!empty($_SESSION['user']['privileges']) &&
							in_array('ecoles', $_SESSION['user']['privileges'])) { ?>

						--><li>
							<a href="<?php url('ecoles/'.$ecole['id'].'/participants'); ?>">Participants</a>
						</li><!--

						<?php } ?>

						--><li>
							<a href="<?php url('ecoles/'.$ecole['id'].'/sportifs'); ?>">Sportifs</a>
						</li><!--

						<?php } ?>

						--><li>
							<a href="<?php url('ecoles/'.$ecole['id'].'/recapitulatif'); ?>">Récapitulatif</a>
						</li><!--
					--></ul>
				</nav>
			</div>

			<div class="main">
