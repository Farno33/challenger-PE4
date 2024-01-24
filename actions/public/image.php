<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* actions/public/login_ecoles.php *************************/
/* Gére la connexion pour les écoles ***********************/
/* *********************************************************/
/* Dernière modification : le 20/11/14 *********************/
/* *********************************************************/

$token = $args[1][0];

$image = $pdo->query('SELECT image '.
	'FROM images '.
	'WHERE '.
		'token = "'.secure($token).'" AND '.
		'_etat = "active"')
	->fetch(PDO::FETCH_ASSOC);


header('Pragma: public');
header('Cache-Control: max-age=86400, public');
header('Expires: '. gmdate('D, d M Y H:i:s \G\M\T', time() + 86400));
header('Content-Type: image/png');

//1x1 transparent pixel
if (empty($image))
	die(base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAC0lEQVQYV2NgAAIAAAUAAarVyFEAAAAASUVORK5CYII='));

die(base64_decode($image['image']));
