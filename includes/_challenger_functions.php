<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* includes/_challenger_functions.php **********************/
/* Fonctions relatives à l'application du Challenger *******/
/* *********************************************************/
/* Dernière modification : le 19/01/15 *********************/
/* *********************************************************/


function makeValue($str) {
	if ($str == 'false') return false;
	if ($str == 'true') return true;
	if (is_numeric($str)) return (float) $str;
	return $str;
}

function printEtatEnvois($commence, $participants, $envois, $echecs) {
	$etat = false;

	if (empty($commence) && empty($envois) && empty($echecs)) $etat = "a_envoyer";
	else if ($participants) {
		if ($echecs >= $participants) $etat = "echec";
		else if ($envois >= $participants) $etat = "envoyes";
		else if ($envois + $echecs >= $participants) $etat = "envoi_partiel";
		else $etat = "en_cours";
	}

	$etats = [
		'a_envoyer' => '<b style="color:var(--blue);">En attente</b>',
		'echec' => '<b style="color:var(--red);">Echec(s)</b>',
		'envoyes' => '<b style="color:var(--green);">Envoyé(s)</b>',
		'envoi_partiel' => '<b style="color:var(--orange);">Envoi partiel</b>',
		'en_cours' => '<b style="color:var(--purple);">En cours</b>',
	];

	return in_array($etat, array_keys($etats)) ? 
		$etats[$etat] : '<i>Inconnu</i>';
}

function printEtatEnvoi($commence, $envoi, $echec) {
	$etat = false;

	if (empty($commence) && empty($envoi) && empty($echec)) $etat = "a_envoyer";
	else if (!empty($envoi)) $etat = "envoye";
	else if (!empty($echec)) $etat = "echec";
	else $etat = "en_cours";

	$etats = [
		'a_envoyer' => '<b style="color:var(--blue);">En attente</b>',
		'echec' => '<b style="color:var(--red);">Echec</b>',
		'envoye' => '<b style="color:var(--green);">Envoyé</b>',
		'en_cours' => '<b style="color:var(--purple);">En cours</b>',
	];

	return in_array($etat, array_keys($etats)) ? 
		$etats[$etat] : '<i>Inconnu</i>';
}

function printActionItem($etat, $_ref, $etatBefore = null) {
	$action = empty($_ref) ? 'ajout' : 
		($etat == 'desactive' ? 'suppression' : 
			($etatBefore === 'desactive' ? 'restoration' : 'modification'));

	$actions = [
		'ajout' => '<b style="color:var(--green);">Ajout</b>',
		'modification' => '<b style="color:var(--orange);">Modification</b>',
		'suppression' => '<b style="color:var(--red);">Suppression</b>',
		'restoration' => '<b style="color:var(--purple);">Restoration</b>',
	];

	return in_array($action, array_keys($actions)) ? 
		$actions[$action] : '<i>Inconnu</i>';
}


function printEtatItem($etat) {
	$etats = [
		'revision' => '<b style="color:var(--blue);">Révision</b>',
		'active' => '<b style="color:var(--green);">Actuel</b>',
		'temp' => '<b style="color:var(--orange);">Temporaire</b>',
		'desactive' => '<b style="color:var(--purple);">Supprimé</b>',
	];

	return in_array($etat, array_keys($etats)) ? 
		$etats[$etat] : '<i>Inconnu</i>';
}

function printEtatTournoi($etat) {
	$etats = [
		'elimination' => '<b style="color:var(--red);">Élimination</b>',
		'poules' => '<b style="color:var(--green);">Poules</b>',
		'championnat' => '<b style="color:var(--orange);">Championnat</b>',
		'series' => '<b style="color:var(--blue);">Séries</b>',
	];

	return in_array($etat, array_keys($etats)) ? 
		$etats[$etat] : '<i>Inconnu</i>';
}

function printEtatPaiement($etat) {
	$etats = [
		'paye' => '<b style="color:var(--green);">Payé</b>',
		'refuse' => '<b style="color:var(--red);">Refusé</b>',
		'annule' => '<b style="color:var(--red);">Annulé</b>',
		'attente' => '<b style="color:var(--blue);">En attente</b>',
	];

	return in_array($etat, array_keys($etats)) ? 
		$etats[$etat] : '<i>Inconnu</i>';
}

function printEtatMatch($etat) {
	$etats = [
		'G' => '<b style="color:var(--green);">G</b>',
		'P' => '<b style="color:var(--red);">P</b>',
		'N' => '<b style="color:var(--blue);">N</b>',
		'F' => '<b style="color:var(--foreground);">F</b>',
		'' => '',
	];

	return in_array($etat, array_keys($etats)) ? 
		$etats[$etat] : '';
}

function printEtatEcole($etat) {
	$etats = [
		'fermee' => '<b style="color:var(--red);">Inscription non lancée</b>',
		'ouverte' => '<b style="color:var(--blue);">Inscription ouverte</b>',
		'limitee' => '<b style="color:var(--purple);">Inscription limitée</b>',
		'close' => '<b style="color:var(--orange);">Inscription close</b>',
		'validee' => '<b style="color:var(--green);">Inscription validée</b>',
	];

	return in_array($etat, array_keys($etats)) ? 
		$etats[$etat] : '<i>Inconnu</i>';
}

function printEtatErreur($etat) {
	$etats = [
		'specifiee' => '<b style="color:var(--orange);">Spécifiée</b>',
		'corrigee' => '<b style="color:var(--blue);">Corrigée</b>',
		'acceptee' => '<b style="color:var(--green);">Acceptée</b>',
		'refusee' => '<b style="color:var(--red);">Refusée</b>',
	];

	return in_array($etat, array_keys($etats)) ? 
		$etats[$etat] : '<i>Inconnu</i>';
}

function printCautionEcole($etat) {
	$etats = [
		'0' => '<b style="color:var(--red);">Caution non reçue</b>',
		'1' => '<b style="color:var(--blue);">Caution reçue</b>',
	];

	return in_array($etat, array_keys($etats)) ? 
		$etats[$etat] : '<i>Inconnu</i>';
}

function printMethodeConnexion($method) {
	$methods = [
		'remember' => '<b style="color:var(--red);">AUTO</b>',
		'db' => '<b style="color:var(--blue);">DB</b>',
		'cas' => '<b style="color:var(--orange);">CAS</b>',
	];

	return in_array($method, array_keys($methods)) ? 
		$methods[$method] : '<i>Inconnu</i>';
}

function printSaisiePaiement($saisie) {
	$saisies = [
		'automatique' => '<b style="color:var(--green);">Automatique</b>',
		'manuelle' => '<b style="color:var(--blue);">Manuelle</b>',
	];

	return in_array($saisie, array_keys($saisies)) ? 
		$saisies[$saisie] : '<i>Inconnu</i>';
}

function printTypePaiement($type) {
	$types = [
		'cb' => '<b>Carte-Bleue</b>',
		'cheque' => '<b>Chèque</b>',
		'virement' => '<b>Virement</b>',
		'especes' => '<b>Espèces</b>',
		'regulation' => '<b>Régulation</b>',
	];

	return in_array($type, array_keys($types)) ? 
		$types[$type] : '<i>Inconnu</i>';
}

function printLogementTarif($logement) {
	$types = [
		'1' => '<b style="color:var(--green);">Logement compris</b>',
		'0' => '<b style="color:var(--red);">Logement non compris</b>',
	];

	return in_array($logement, array_keys($types)) ? 
		$types[$logement] : '<i>Inconnu</i>';
}

function printTypeEcole($fanfare) {
	$types = [
		'1' => '<b style="color:var(--gold-fanfare);">Fanfare</b>',
		'0' => '<b style="color:var(--blue-bds);">BDS</b>',
	];

	return in_array($fanfare, array_keys($types)) ? 
		$types[$fanfare] : '<i>Inconnu</i>';
}

function printSexe($sexe, $parentheses = true) {
	$sexes = [
		'm' => '<b style="color:var(--foreground);">F/H</b>',
		'f' => '<b style="color:var(--soft-pink);">F</b>',
		'h' => '<b style="color:var(--soft-blue);">H</b>',
	];

	return ($parentheses ? '(' : '').
		(in_array($sexe, array_keys($sexes)) ? $sexes[$sexe] : '<i>?</i>').
		($parentheses ? ')' : '');
}

function printClassement($place, $exaequo = false) {
	return $place.'<span style="color:var(--grey7)"><sup>'.
		($place == 1 ? 'er' : 'e').'</sup>'.($exaequo ? ' ex.' : '').'</span>';
}

function makeSheetTitle($feuille, $id) {
	static $feuilles = [];

	if ($feuille == null) return $feuilles[] = 'Feuille '.($id + 1);
	$feuille = str_replace(str_split('*:/\\?[]'), '', $feuille);
	$feuille = substr($feuille, 0, 31);
	if (!in_array($feuille, $feuilles)) return $feuilles[] = $feuille;
	$feuille = substr($feuille, 0, 29);
	$copy = 2;
	while (in_array($feuille.' '.$copy, $feuilles))
		$copy++;
	return $feuilles[] = $feuille.' '.$copy;
}

function colorChambre($chambre) {
	$i1 = ord($chambre[0]) + 1;
	$i2 = ord($chambre[1]) + 1;
	$i3 = ord($chambre[2]) + 1;
	$i4 = ord($chambre[3]) + 1;

	$l1 = abs(($i4 + $i1) * ($i2 + $i3) % 16);
	$l2 = abs(($i3 + $i1) * ($i4 + $i2) % 16);
	$l3 = abs(($i1 + $i2) * ($i3 + $i4) % 16);

	return '#'.dechex($l1).dechex($l2).dechex($l3);
}

function colorContrast($hex, $dark = '#000', $light = '#FFF') {
    $hex = str_replace('#', '', $hex);
    if (strlen($hex) == 3)
    	$hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
    return (hexdec($hex) > 0xffffff/2) ? $dark : $light;
}

function exportXLSX($items, $fichier, $titre, $labels) {
	require_once DIR.'includes/XLSXWriter/xlsxwriter.class.php';

	// Redirect output to a client’s web browser (Excel2007)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="'.$fichier.'_'.date('d-m-Y_H-i').'.xlsx"');
	header('Cache-Control: max-age=1');

	// If you're serving to IE over SSL, then the following may be needed
	header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
	header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
	header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	header ('Pragma: public'); // HTTP/1.0
	
	$writer = new XLSXWriter();
	makeSheet($writer, 0, $items, $titre, $labels);
	$writer->writeToStdOut();
	exit;
}


function exportXLSXGroupe($items, $fichier, $feuilles, $titres, $labels) {
	require_once DIR.'includes/XLSXWriter/xlsxwriter.class.php';

	// Redirect output to a client’s web browser (Excel2007)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="'.$fichier.'_'.date('d-m-Y_H-i').'.xlsx"');
	header('Cache-Control: max-age=1');

	// If you're serving to IE over SSL, then the following may be needed
	header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
	header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
	header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	header ('Pragma: public'); // HTTP/1.0
	
	$writer = new XLSXWriter();

	$i = 0;
	foreach ($items as $j => $items_sheet)
		makeSheet($writer, $i++, $items_sheet, $titres[$j], $labels, $feuilles[$j]);

	$writer->writeToStdOut();
	exit;
}

function makeSheet(&$writer, $id, $items, $titre, $labels, $feuille = null) {
	$nbColumns = count($labels);
	
	$sheetName = makeSheetTitle($feuille, $id);
	$header = array_fill(0, $nbColumns, "string");
	$writer->writeSheetHeader($sheetName, $header, true);
	$writer->writeSheetRow($sheetName, array('Challenge, exporté le '.date('d\/m\/Y à H:i')));
	$writer->writeSheetRow($sheetName, array($titre));
	$label = array_keys($labels);
	$writer->writeSheetRow($sheetName, $label);

	$writer->markMergedCell($sheetName, 0, 0, 0, $nbColumns - 1);
	$writer->markMergedCell($sheetName, 1, 0, 1, $nbColumns - 1);

	foreach ($items as $item) {

		$row = array();
		foreach ($labels as $label => $indexSQL) {
			$row[] = unsecure($item[$indexSQL]);
		}
		$writer->writeSheetRow($sheetName, $row);
	}

	/*

	//Pour faire du style (avec le v2 de la librairie)
	//La v1 ne permet pas les styles
	//La v2 ne permet pas les merges et est foireuse
	
	$sheetName = makeSheetTitle($feuille, $id);
	
	$rows = [];
	$rows[] = array('Challenge, exporté le '.date('d\/m\/Y à H:i'));
	$rows[] = array($titre);
	$rows[] = array_keys($labels);


	foreach ($items as $item) {

		$row = array();
		foreach ($labels as $label => $indexSQL) {
			$row[] = unsecure($item[$indexSQL]);
		}
		$rows[] = $row;
 	}

 	$writer->writeSheet($rows, $sheetName, [], array(
        array( // in each style element you can use or 'cells', or 'rows' or 'columns'.
          'fill' => array(
            'color' => 'FFCC00'),
          'rows' => array('2')
          )
        )); 
    */
}



function api($post, $json_decode = true) {
	$return = http_post(URL_API_ECLAIR, $post);
	return $json_decode ? json_decode($return) : $return;
}


function isValidSexe($sexe) {
    $pattern = '/^(h(omme)?|f(emme)?|m(an)?|w(oman)?|0|1|o(ui)?|n(on?)?|y(es)?)$/i';
    return preg_match($pattern, $sexe);
}

function isValidSportif($sportif) {
    $pattern = '/^(|s(port(if)?)?|0|1|o(ui)?|n(on?)?|y(es)?)$/i';
    return preg_match($pattern, $sportif);
}

function isValidFanfaron($fanfaron) {
    $pattern = '/^(|f(anfar(|e|on(n?e)?)?)?|0|1|o(ui)?|n(on?)?|y(es)?)$/i';
    return preg_match($pattern, $fanfaron);
}

function isValidPompom($pompom) {
    $pattern = '/^(|p(om-?pom|im-?pim)?|0|1|o(ui)?|n(on?)?|y(es)?)$/i';
    return preg_match($pattern, $pompom);
}

function isValidCameraman($cameraman) {
    $pattern = '/^(|c(amera((wo)?man)?)?|p(hoto(graphe)?)?|0|1|o(ui)?|n(on?)?|y(es)?)$/i';
    return preg_match($pattern, $cameraman);
}

function isValidPhone($phone) {
    $pattern = '/^\s*(?:\+?(\d{1,3}))?([-. (]*\d[-. )]*)?([-. (]*(\d{2,4})[-. )]*(?:[-.x ]*(\d+))?)+\s*$/';
    return preg_match($pattern, $phone);
}

function isValidSmsPhone($phone) {
	$pattern = '/0(?:[1-7]|9) \d\d \d\d \d\d \d\d/';
	return preg_match($pattern, getPhone($phone));
}

function isValidLicence($licence) {
    $pattern = '/^([a-z0-9]{4}\s*)?[0-9]{6}$/i';
    return preg_match($pattern, $licence);
}

function isValidLogement($logement) {
    $pattern = '/^(|l(ight)?( p(ackage)?)?|f(ull)?( p(ackage)?)?|0|1|o(ui)?|n(on?)?|y(es)?)$/i';
    return preg_match($pattern, $logement);
}

function isValidRecharge($recharge) {
    $pattern = '/^\d*((\.|,)00?)?( ?€)?$/';
    return preg_match($pattern, $recharge);
}

function isValidCapitaine($capitaine) {
    $pattern = '/^(|c(aptain|apitaine)?|0|1|o(ui)?|n(on?)?|y(es)?)$/i';
    return preg_match($pattern, $capitaine);
}

function getSexe($sexe) {
    $pattern = '/^(h(omme)?|m(an)?|1|o(ui)?|y(es)?)$/i';
    if (!isValidSexe($sexe)) return '?';
    return preg_match($pattern, $sexe) ? 'h' : 'f';
}

function getSportif($sportif) {
    $pattern = '/^(s(port(if)?)?|1|o(ui)?|y(es)?)$/i';
    if (!isValidSportif($sportif)) return '?';
    return preg_match($pattern, $sportif) ? '1' : '0';
}

function getFanfaron($fanfaron) {
    $pattern = '/^(f(anfar(|e|on(n?e)?)?)?|1|o(ui)?|y(es)?)$/i';
    if (!isValidFanfaron($fanfaron)) return '?';
    return preg_match($pattern, $fanfaron) ? '1' : '0';
}

function getPompom($pompom) {
    $pattern = '/^(p(om-?pom|im-?pim)?|1|o(ui)?|y(es)?)$/i';
    if (!isValidPompom($pompom)) return '?';
    return preg_match($pattern, $pompom) ? '1' : '0';
}

function getCameraman($cameraman) {
    $pattern = '/^(c(amera((wo)?man)?)?|p(hoto(graphe)?)?|1|o(ui)?|y(es)?)$/i';
    if (!isValidCameraman($cameraman)) return '?';
    return preg_match($pattern, $cameraman) ? '1' : '0';
}

function getLogement($logement) {
    $pattern = '/^(f(ull)?( p(ackage)?)?|1|o(ui)?|y(es)?)$/i';
    if (!isValidLogement($logement)) return '?';
    return preg_match($pattern, $logement) ? '1' : '0';
}

function getCapitaine($capitaine) {
    $pattern = '/^(c(aptain|apitaine)?|1|o(ui)?|y(es)?)$/i';
    if (!isValidCapitaine($capitaine)) return '?';
    return preg_match($pattern, $capitaine) ? '1' : '0';
}

function getRecharge($recharge) {
    $pattern = '/^(\d*)((\.|,)00?)?( ?€)?$/';
    if (!isValidRecharge($recharge)) return 0;
    return preg_replace($pattern, $recharge, '$1');
}

function getPhone($phone) {
	$phone = trim($phone);
	$phone = preg_replace('/(\d)[ .\/-](\d)/', '$1$2', $phone);
	$phone = preg_replace('/^00/', '+', $phone);
	$phone = preg_replace('/^\+33\(?0?\)?/', '0', $phone);
	$phone = strlen($phone) == 9 && preg_match('/^[1-9]/', $phone) ? '0'.$phone : $phone;
	$phone = strlen($phone) == 10 && preg_match('/^0\d{9}$/', $phone) ? preg_replace('/^(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})$/', '$1 $2 $3 $4 $5', $phone) : $phone;
	return preg_replace('/^[ .\/-](\d+)/', '$1', $phone);
}

function formatLicence($licence) {
	$licence = explode(',', $licence);
	foreach ($licence as $k => $lic) {
		$lic = trim($lic);
		if (preg_match('/([A-Z0-9]{4})[^0-9A-Z]?([0-9]{6})/i', $lic, $matches))
			$licence[$k] = strtoupper($matches[1]).' '.$matches[2];
		else if (!empty($lic))
			$licence[$k] = "Certificat médical ou questionnaire";
		else
			unset($licence[$k]);
	}
	return implode(', ', $licence);
}

function getLicence($licence) {
	/*
	$licence = trim($licence);
	return preg_replace_callback('/^(([a-z0-9]{4})\s*)?([0-9]{6})$/i', function ($matches) {
            return strtoupper($matches[2].' '.$matches[3]);
        }, $licence);*/
	return formatLicence($licence);
}

function sendSms($number, $message) {
    $signature = md5(SMS_SALT.$number.$message);
    return http_post(SMS_URL, [
        'number' => $number,
        'message' => $message,
        'signature' => $signature]);
}

function sygma($data, $json_decode = true) {
	unset($data['signature']);

	ksort($data);
	$data['signature'] = sha1(http_build_query($data).'&'.SYGMA_SALT);
	$return = http_post(URL_SYGMA, $data);
	return $json_decode ? json_decode($return) : $return;
}


function isValidLicence56($licence) {
    $pattern = '/^([a-z0-9]{4}\s*)?[0-9]{5,6}$/i';
    return preg_match($pattern, $licence);
}

function getLicences($licence, $asCodes) {
	if (!isValidLicence56($licence))
		return [$licence]; 

	$as = '';
	$licence = trim($licence);

	if (strlen($licence) >= 10) {
		$as = substr($licence, 0, 4);
		$licence = sprintf("%06d", trim(substr($licence, 4)));
		return [$as.' '.$licence];
	}

	$licence = sprintf("%06d", $licence);

	if (count($asCodes) > 1)
		$return = [$licence];

	foreach ($asCodes as $as) {
		$return[] = $as.' '.$licence;
	}

	return $return;
}

function getEmail($email) {
	return strtolower(trim($email));
}

function getTelephone($telephone) {
	return getPhone($telephone);
}

function isLicenceEcole($licence, $asCodes) {
	$licence = trim($licence);

	if (!isValidLicence($licence) ||
		strlen($licence) < 10 && count($asCodes) > 1 ||
		strlen($licence) >= 10 && !in_array(strtoupper(substr($licence, 0, 4)), $asCodes))
		return false;

	return true;
}

function clean($string) {
	return strtolower(removeAccents(preg_replace('/[- ]/', '', $string)));
}

//mcrypt will be deleted in PHP 7.2 (deprecated in PHP 7.1)
function encrypt($key, $data, $iv = null) {
	$iv = $iv !== null ? $iv : @mcrypt_create_iv(
	    @mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC),
	    MCRYPT_DEV_URANDOM
	);

	return [
		'iv' => 	base64_encode($iv),
		'data' => 	base64_encode(@mcrypt_encrypt(
	        MCRYPT_RIJNDAEL_256,
	        hash('sha256', $key, true),
	        $data,
	        MCRYPT_MODE_CBC,
	        $iv
	    ))];
}

function decrypt($key, $iv, $data) {
	return rtrim(
	    @mcrypt_decrypt(
	        MCRYPT_RIJNDAEL_256,
	        hash('sha256', $key, true),
	        base64_decode($data),
	        MCRYPT_MODE_CBC,
	        base64_decode($iv)
	    ),
	    "\0"
	);
}

function sign($content){
	$signature = '';
	if (openssl_sign($content, $signature, openssl_get_privatekey(defined('APP_PKEY_COOKIE') ? APP_PKEY_COOKIE : 'file:///etc/apache2/pkey.pem'), OPENSSL_ALGO_SHA512))
		return base64_encode($signature);
	else throw new Exception("Crypto error", 1);
}

function verify($content, $signature){
	$pkey = openssl_get_publickey(defined('APP_PKEY_COOKIE') ? APP_PKEY_COOKIE : 'file:///etc/apache2/pkey.pem'); // Condition supprimable au prochain déploiement (c'est juste pour eviter des problemes avec la version actuelle)
	if ($pkey == NULL) return false;

	$res = openssl_verify($content, base64_decode($signature), $pkey, OPENSSL_ALGO_SHA512);
	if ($res == 1) return true;
	else if ($res == 0) return false;
	else throw new Exception("Crypto error", $res);
}

function replaceChallenger($matches) {
	global $data;
	global $data_out; 

	$key = str_replace('.', '_', $matches[2]);
	$value = '';
	
	if (isset($data[$key])) $value = $data[$key];
	else if ($key == '@date') $value = date('j/n/Y');
	else if ($key == '@annee') $value = date('Y');
	else if ($key == '@edition') $value = date('Y') - 1982;
	else if ($key == '@heure') $value = date('H:i');
	else if ($key == '@url') $value = preg_replace('#/*$#', '', BASE_URL).'/';
	else if ($key == '@url_app') $value = preg_replace('#/*$#', '', BASE_URL_APP).'/';
 
	$data_out[$key] = $value;
	$style = $matches[1].preg_replace('/(?:.*)style="([^"]*)"(?:.*)/', '$1', $matches[3]);
	$style = preg_replace('/challenger: ?0 ?;? ?/', '', $style);

	return !empty($style) ? '<span style="'.$style.'">'.$value.'</span>' : $value;
}

function replaceChallengerSmall($matches) {
	global $data;
	global $data_out; 
	
	$key = str_replace('.', '_', $matches[1]);
	$value = '';
	
	if (isset($data[$key])) $value = $data[$key];
	else if ($key == '@date') $value = date('j/n/Y');
	else if ($key == '@annee') $value = date('Y');
	else if ($key == '@edition') $value = date('Y') - 1982;
	else if ($key == '@heure') $value = date('H:i');

	$data_out[$key] = $value;
	return $value;
}

function simplifyChallenger($matches) {
	$style = $matches[1].preg_replace('/(?:.*)style="([^"]*)"(?:.*)/', '$1', $matches[3]);
	$style = preg_replace('/challenger: ?0 ?;? ?/', '', $style);
	$return = '{{'.$matches[2].'}}';

	return !empty($style) ? '<span style="'.$style.'">'.$return.'</span>' : $return;
}

function trimCond($str) {
	return trim(str_replace("&nbsp;", ' ', $str));
}

function simplifyText($str, $limit = 200, $html = false) {
	$conds_tags = ['if', 'op(0|1|)', 'then', 'else', 'fi'];
	$conds_tags = array_map(function($value) {
		return '(?:<span data-challenger(?:="")? data-cond="'.$value.'"(?:[^>]*)><\/span>)';
	}, $conds_tags);
	$conds_tags = implode('(.*?)', $conds_tags);
	
	$str = preg_replace_callback('#<span (?:style="([^"]*)" )?data-challenger(?:="")? data-field="([@a-zA-Z._]*?)"([^>]*)><\/span>#', 'simplifyChallenger', $str);
	$str = preg_replace_callback('#'.$conds_tags.'#s', function($matches) {	
		$c1 = trimCond(strip_tags($matches[1]));
		$c2 = trimCond(strip_tags($matches[3]));
		$op = $matches[2];
		$then = $matches[4];
		$else = $matches[5];

		return '[[SI]]'.$c1.($op === '1' ? '[[=]]' : ($op === '0' ? '[[≠]]' : '')).$c2.'[[ALORS]]'.$then.'[[SINON]]'.$else.'[[IS]]';
	}, $str);

	if ($html) {
		$str = preg_replace('#<span data-space(?:="")?(?:[^>]*)>&nbsp;</span>#', '', $str);
		$str = preg_replace('#(?:&nbsp;)*<span data-challenger(.*?)><\/span>(?:&nbsp;)*#', '', $str);
		return '<!DOCTYPE html><html><head><meta charset="utf-8" /></head>'.
			'<body style="font-size:12pt;font-family:Century Gothic,Tahoma,Arial,sans-serif">'.$str.'</body></html>';
	}

	$str = strip_tags($str);
	return $limit ? substr(trimCond($str), 0, $limit) : trimCond($str);
}

function cleanText($str, $title = '') {
	$conds_tags = ['if', 'op(0|1|)', 'then', 'else', 'fi'];
	$conds_tags = array_map(function($value) {
		return '(?:<span data-challenger(?:="")? data-cond="'.$value.'"(?:[^>]*)><\/span>)';
	}, $conds_tags);
	$conds_tags = implode('(.*?)', $conds_tags);
	
	$str = preg_replace('#<span data-space(?:="")?(?:[^>]*)>&nbsp;</span>#', '', $str);
	$str = preg_replace_callback('#<span (?:style="([^"]*)" )?data-challenger(?:="")? data-field="([@a-zA-Z._]*?)"([^>]*)><\/span>#', 'replaceChallenger', $str);
	$str = preg_replace_callback('#{{([@a-zA-Z._]*?)}}#', 'replaceChallengerSmall', $str);
	$str = preg_replace_callback('#'.$conds_tags.'#s', function($matches) {	
		$c1 = trimCond(strip_tags($matches[1]));
		$c2 = trimCond(strip_tags($matches[3]));
		$op = $matches[2];
		$then = $matches[4];
		$else = $matches[5];

		if ($op == '1') //Egalité
			return $c1 == $c2 ? $then : $else;

		else if ($op == '0') //Différence
			return $c1 != $c2 ? $then : $else;

		else //Existence
			return $c1.$c2 ? $then : $else;
	}, $str);

	$str = preg_replace('#(?:&nbsp;)*<span data-challenger(.*?)><\/span>(?:&nbsp;)*#', '', $str);
	return '<!DOCTYPE html><html><head>'.(!empty($title) ? '<title>'.$title.'</title>' : '').
		'<meta charset="utf-8" /></head>'.
		'<body style="font-size:12pt;font-family:Century Gothic,Tahoma,Arial,sans-serif">'.$str.'</body></html>';
}

function plainText($str) {
	$str = cleanText($str);
	$str = preg_replace( "/\r|\n/", "", $str);
	$str = preg_replace("/<p[^>]*?>/", "", $str);
	$str = str_replace("</p>", "<br />", $str); 
	return str_replace('&nbsp;', ' ', strip_tags(preg_replace('/\<br(\s*)?\/?\>/i', "\n", $str)));
}

function strip_quotes_from_message($message)
{
    $els_to_remove = [
        //'blockquote',                           // Standard quote block tag
        'div.moz-cite-prefix',                  // Thunderbird
        'div.gmail_extra',
        'div.gmail_signature',
        'div.gmail_quote',   					// Gmail
        'div.yahoo_quoted',                      // Yahoo
        'blockquote.challenger_quote',       // Challenger
        'blockquote[type="cite"]',
        'div[data-marker="__QUOTED_TEXT__"]',
        'hr[data-marker="__DIVIDER__"]',
        'div[data-marker="__HEADERS__"]',		//Zimbra
    ];

    $dom = str_get_html($message);

    foreach ($els_to_remove as $el) {
        foreach ($dom->find($el) as $f) {
            $f->outertext = '';
            unset($f);
        }
    }
    // Outlook doesn't respect
    // http://www.w3.org/TR/1998/NOTE-HTMLThreading-0105#Appendix%20B
    // We need to detect quoted replies "by hand"
    //
    // Example of Outlook quote:
    //
    // <div>
    //      <hr id="stopSpelling">
    //      Date: Fri. 20 May 2016 17:40:24 +0200<br>
    //      Subject: Votre facture Selon devis DEV201605201<br>
    //      From: xxxxxx@microfactures.com<br>
    //      To: xxxxxx@hotmail.fr<br>
    //      Lorem ipsum dolor sit amet consectetur adipiscing...
    // </div>
    //
    // The idea is to delete #stopSpelling's parent...
    $hr  = $dom->find('#stopSpelling');
    foreach ($hr as $f) {
        $f->parent()->outertext = '';
        unset($f);
    }
    // Roundcube adds a <p> with a sentence like this one, just
    // before the quote:
    // "Le 21-05-2016 02:25, AB Prog - Belkacem Alidra a écrit :"
    // Let's remove it
    $pattern = '/Le [0-9]{2}-[0-9]{2}-[0-9]{4} [0-9]{2}:[0-9]{2}, [^:]+ a &eacute;crit&nbsp;:/';
    $ps = $dom->find('p');
    foreach ($ps as $p) {
        if (preg_match($pattern, $p->plaintext)) {
            $p->outertext = '';
            unset($p);
        }
    }

    // Même chose un peu différemment
    $pattern = '/Le [0-9]{2} [A-Za-z]+ [0-9]{4} à [0-9]{2}:[0-9]{2}, [^:]+ a écrit&nbsp;:/';
    $ps = $dom->find('div');
    foreach ($ps as $p) {
        if (preg_match($pattern, $p->plaintext)) {
            $p->outertext = '';
            unset($p);
        }
    }

    // Let's remove empty tags like <p> </p>...
    $els = $dom->find('p,span,b,strong,div');
    foreach ($els as $e) {
        $html = trim($e->innertext);
        if (empty($html) || $html == "&nbsp;" || $html == '<br />' || $html == '<br>') {
            $e->outertext = '';
            unset($e);
        }
    }

    return ''.$dom;
}
