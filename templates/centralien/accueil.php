<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* templates/admin/accueil.php *****************************/
/* Template de l'accueil de l'administration ***************/
/* *********************************************************/
/* Dernière modification : le 20/11/14 *********************/
/* *********************************************************/

require DIR.'templates/centralien/_header_centralien.php';

?>
			<h2>Modules centraliens</h2>

				<?php 

				foreach ($modulesCentralien as $_module => $_info) {
					list($_titre, $_desc) = $_info;

				?>

				<a class="module" href="<?php url('centralien/'.$_module); ?>">
					<h3><?php echo $_titre; ?></h3>

					<?php echo $_desc; ?>
				</a>

				<?php } ?>

<?php

//Inclusion du pied de page
require DIR.'templates/_footer.php';
