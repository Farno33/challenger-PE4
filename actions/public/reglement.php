<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/public/reglement.php ****************************/
/* Affichage du RI du Challenge ****************************/
/* *********************************************************/
/* Dernière modification : le 16/01/16 *********************/
/* *********************************************************/

$langs = ['en' => 'English version', 'fr' => 'Version française'];



$lang = !empty($args[1][0]) ? $args[1][0] : 'fr';
$fichierPrefixe = DIR.'templates/public/reglement_';
$lang = file_exists($fichierPrefixe.$lang.'.php') ? $lang : 'fr';


if (!file_exists($fichierPrefixe.$lang.'.php'))
	die(require DIR.'templates/_error.php');


//Inclusion de l'entête de page
require DIR.'templates/_header_nomenu.php';

?>
			
			<center>
				<?php foreach ($langs as $l => $c) { 
					echo $l == $lang ? '<b>'.$c.'</b>' : '<a href="'.url('reglement/'.$l, false, false).'">'.$c.'</a>';
					if (@end(array_keys($langs)) != $l) echo ' | ';
				} ?>
			</center>
			
			
				
				<div class="iframe">
					<center>
					<?php echo file_get_contents($fichierPrefixe.$lang.'.php'); ?>
				</center>
				</div>
				

<?php

//Inclusion du pied de page
require DIR.'templates/_footer.php';