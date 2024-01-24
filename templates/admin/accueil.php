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


//Inclusion de l'entête de page
require DIR . 'templates/admin/_header_admin.php';
?>

<h2>Modules administration</h2>

<?php

foreach ($modulesAdmin as $_module => $_info) {
	list($_titre, $_desc) = $_info;
	if (in_array($_module, $_SESSION['user']['privileges'])) { ?>

		<a class="module" href="<?php url('admin/module/' . $_module); ?>">
			<h3><?php echo $_titre; ?></h3>

			<?php echo $_desc; ?>
		</a>

<?php }
} ?>

<?php

//Inclusion du pied de page
require DIR . 'templates/_footer.php';
