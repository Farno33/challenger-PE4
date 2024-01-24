<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* templates/admin/logement/recensement.php ****************/
/* Template des bâtiments pour le recensement **************/
/* *********************************************************/
/* Dernière modification : le 19/01/15 *********************/
/* *********************************************************/


//Inclusion de l'entête de page
require DIR.'templates/admin/_header_admin.php';

?>
				
				<h2>Liste des Bâtiments</h2>
				
				<center style="max-width: 1000px; margin: auto;">

				<?php

				foreach (array('U', 'V', 'T', 'X', 'A', 'B', 'C') as $batiment) {

				?>

				<a href="<?php url('admin/module/logement/_'.$batiment); ?>" class="square">
					<div class="square-titre">Bâtiment <?php echo $batiment; ?></div>
					<div class="square-stats">
						Chambres disponibles : <b><?php echo $batiments[$batiment]['nb_active']; ?></b><br />
						Filles logées : <b><?php echo $batiments[$batiment]['nb_filles']; ?></b><br />
						Places restantes : <b><?php echo $batiments[$batiment]['nb_places'] - $batiments[$batiment]['nb_filles']; ?></b>
					</div>
				</a>

				<?php } ?>

				</center>


<?php

//Inclusion du pied de page
require DIR.'templates/_footer.php';
