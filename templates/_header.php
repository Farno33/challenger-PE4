<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* templates/_header.php ***********************************/
/* Haut de page ********************************************/
/* *********************************************************/
/* Dernière modification : le 20/11/14 *********************/
/* *********************************************************/


?>

<!DOCTYPE html>
<html>
	<head>
		<title>Challenger - Gestion de l'organisation du Challenge</title>
		
		<!-- Balises Meta -->
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
		<meta name="author" content="Raphael Kichot' Moulin" />
		<meta name="description" content="Challenger V3 - Gestion de l'organisation du Challenge" />
		<meta name="keywords" content="challenge, challenger, organisation, sport, navette" />
			
		<!-- Feuilles de style CSS -->
		<link rel="stylesheet" media="all" href="<?php url('assets/css/global.css'); ?>" />
		<link rel="stylesheet" media="all" href="<?php url('assets/css/challenger.css'); ?>" />
		<link rel="stylesheet" media="all" href="<?php url('assets/css/autocomplete.css'); ?>" />
		<link rel="stylesheet" media="print" href="<?php url('assets/css/print.css'); ?>" />

		<!-- Icones -->
		<link rel="shortcut icon" href="<?php url('assets/images/ico/favicon.ico'); ?>" type="image/x-icon" />
		<link rel="icon" href="<?php url('assets/images/ico/favicon.ico'); ?>" type="image/x-icon" />
	
		<!-- Scripts Javascript -->
		<script src="<?php url('assets/js/jquery.min.js'); ?>"></script>
        <script src="<?php url('assets/js/jquery-ui.min.js'); ?>"></script>
        <script src="<?php url('assets/js/thememanagement.js'); ?>"></script>
		<script type="text/javascript" src="<?php url('assets/js/jquery.simplemodal.js'); ?>"></script>

		<?php if (defined('WYSIWYG')) { ?>
		<script src="<?php url('assets/tinymce/tinymce.min.js'); ?>"></script>
		<?php } ?>
		
	</head>

	<body>
		<noscript><div>Le site nécessite JavaScript pour fonctionner</div></noscript>
		<div class="container nojs">
			<header class="noprint">
				<a href="<?php url('accueil') ?>">
					Challenger 
				</a>
                <label class="ThemeSwitch">
                    <input type="checkbox" id="ThemeSwitch">
                    <span class="slider"></span>
                </label>
				<a class="presentation" target="_blank" href="<?php echo APP_URL_CHALLENGE; ?>">
					Site de présentation
				</a>
				<a class="contact" href="<?php url('contact'); ?>">
					Nous contacter
				</a>
			</header>
			
