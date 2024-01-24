<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* templates/admin/droits/liste.php ************************/
/* Template de la liste des organisateurs ******************/
/* *********************************************************/
/* Dernière modification : le 24/11/14 *********************/
/* *********************************************************/

//Inclusion de l'entête de page
require DIR.'templates/admin/_header_admin.php';

?>
				<style type="text/css">
				.subnav li, 
				.subnav li ul { width:200px !important;}
				</style>

				<nav class="subnav">
					<h2>Planning des sites</h2>

					<ul>
						<li><a href="?p=planning">Planning</a></li>
						<li><a href="?p=nonprog">Matchs non programmés</a></li>
					</ul>
				</nav>

			




<?php

//Inclusion du pied de page
require DIR.'templates/_footer.php';
