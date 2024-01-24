<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* includes/_functions.php *********************************/
/* Plusieurs fonctions très utilisées **********************/
/* *********************************************************/
/* Dernière modification : le 20/11/14 *********************/
/* *********************************************************/



/** 
 * Fonction relative à l'état de l'interface
 * 
 * Renvoie TRUE si le script est executé en ligne de commande
 * 
 * @return boolean TRUE si le script est executé en ligne de commande
 */
function isCLI() {
	return in_array(php_sapi_name(), array('cli', 'cli-server'));
}


/** 
 * Enlève tout ce qui n'est pas lettre, chiffres
 * 
 * et quelques caractères spéciaux d'une chaine de caractères
 * 
 * @param  string $string Chaine à nettoyer
 * @return string         Chaine nettoyée
 */
function onlyLetters($string) {
	return preg_replace('/[^\p{L}\p{N} \'.-]+/', '', $string);
}


/** 
 * Echappe une chaine de caractère pour préparer son insertion dans une base de données
 * 
 * @param  string $string Chaine à nettoyer
 * @return string         Chaine nettoyée
 */
function secure($string) {
	return trim(htmlspecialchars(addslashes(removeUnicode($string))));
}

/**
 * Supprime tous les caractères unicode non géré en base de donnée
 * 
 * Seuls les caractères UTF8 sont conservés
 * 
 * Cela est du au fait que la collection est utf8 et non utf8mb4 qui gère les caractères sur 4bytes
 * 
 * @param  string $string Chaine à nettoyer
 * @return string         Chaine nettoyée
 */
function removeUnicode($string) {
	return empty($string) ? "" : preg_replace('/[\x{10000}-\x{10FFFF}]/u', "\xEF\xBF\xBD", $string);
}

/**
 * 
 * Sécurise un chemin de fichier relatif, de sorte que l'on ne puisse pas accéder à des fichiers parents
 * 
 * On en profite pour nettoyer la chaine
 * 
 * @param  string $path Chemin à sécuriser
 * @return string       Chemin sécurisé
 */
function secureFile($path) {
	return preg_replace('/\/+/', '/', str_replace(['../', '/..'], '', $path));
}


function printDate($s, $sentence = true) {
	$t = time();
	if (empty($s)) return '';

	//Futur-Passé proche
	else if (abs($t - $s) < 60) return $sentence ? 'À l\'instant' : 'maintenant';

	//Futur
	else if ($s > $t && $s - $t < 3600) return ($sentence ? 'Dans ' : null).(int) (($s - $t) / 60).'min';
	else if ($s > $t && $s - $t < 86400 && mktime(0, 0, 0, date('m', $s), date('d', $s), date('Y', $s)) == 
		mktime(0, 0, 0, date('m'), date('d'), date('Y'))) return ($sentence ? 'Dans ' : null).(int) (($t - $s) / 3600).'h';
	else if ($s > $t && mktime(0, 0, 0, date('m', $s), date('d', $s) - 1, date('Y', $s)) == 
		mktime(0, 0, 0, date('m'), date('d'), date('Y'))) return 'Demain à '.date('H:i', $s);

	//Passé 
	else if ($s < $t && $t - $s < 3600) return ($sentence ? 'Il y a ' : null).(int) (($t - $s) / 60).'min';
	else if ($s < $t && $t - $s < 86400 && mktime(0, 0, 0, date('m', $s), date('d', $s), date('Y', $s)) == 
		mktime(0, 0, 0, date('m'), date('d'), date('Y'))) return ($sentence ? 'Il y a ' : null).(int) (($t - $s) / 3600).'h';
	else if ($s < $t && mktime(0, 0, 0, date('m', $s), date('d', $s) + 1, date('Y', $s)) == 
		mktime(0, 0, 0, date('m'), date('d'), date('Y'))) return 'Hier à '.date('H:i', $s);

	//Tous les autres cas
	else return ($sentence ? 'Le ' : 'le ').date('d\/m\/y à H:i', $s);
}

function printDateTime($date, $sentence = true) {
	$date = new DateTime($date);
	return printDate($date->getTimestamp(), $sentence);
}

function printInterval($start, $end, $bigRound = true) {
	$dms = new DateTime($start);
	$dme = new DateTime($end);
	$s = $dme->getTimestamp() - $dms->getTimestamp();

	if ($s < 3600) return round($s / 60).'min';
	else if ($s < 86400) return round($s / 3600).'h';
	else return round($s / 86400).'j';
}

function url($route = '', $abs = false, $echo = true) {
	$server = !empty($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : $_SERVER['SERVER_ADDR'];
	$dir = DIR_APP.'/';
	$return = ($abs ? 'http'.(isSecure() ? 's' : '').'://'.$server : '').$dir.$route;
	if (!$echo)
		return $return;
	echo $return;
}

function isSecure() {
  return
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || !empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443;
}

function hashPass($string = '') {
	$seed = !defined('APP_SEED') || empty(APP_SEED) ? '' : APP_SEED;
	return hash('sha256', $string.$seed);
}

function printMoney($money = 0, $devise = '€') {
	return sprintf('%.2f '.$devise, $money);
}

function isValidEmail($email_address) {
	//Norme RFC 5322
    return preg_match('/^(?!(?>(?1)"?(?>\\\[ -~]|[^"])"?(?1)){255,})(?!(?>(?1)"?(?>\\\[ -~]|[^"])"?(?1)){65,}@)'.
		'((?>(?>(?>((?>(?>(?>\x0D\x0A)?[\t ])+|(?>[\t ]*\x0D\x0A)?[\t ]+)?)'.
		'(\((?>(?2)(?>[\x01-\x08\x0B\x0C\x0E-\'*-\[\]-\x7F]|\\\[\x00-\x7F]|(?3)))*(?2)\)))+(?2))|(?2))?)'.
		'([!#-\'*+\/-9=?^-~-]+|"(?>(?2)(?>[\x01-\x08\x0B\x0C\x0E-!#-\[\]-\x7F]|\\\[\x00-\x7F]))*(?2)")'.
		'(?>(?1)\.(?1)(?4))*(?1)@(?!(?1)[a-z\d-]{64,})(?1)(?>([a-z\d](?>[a-z\d-]*[a-z\d])?)'.
		'(?>(?1)\.(?!(?1)[a-z\d-]{64,})(?1)(?5)){0,126}|\[(?:(?>IPv6:(?>([a-f\d]{1,4})'.
		'(?>:(?6)){7}|(?!(?:.*[a-f\d][:\]]){8,})((?6)(?>:(?6)){0,6})?::(?7)?))|'.
		'(?>(?>IPv6:(?>(?6)(?>:(?6)){5}:|(?!(?:.*[a-f\d]:){6,})(?8)?::(?>((?6)(?>:(?6)){0,4}):)?))?'.
		'(25[0-5]|2[0-4]\d|1\d{2}|[1-9]?\d)(?>\.(?9)){3}))\])(?1)$/isD', $email_address);
}

/**
 * 
 * Fonctions pour le filtrage des fiches
 * 
 * On découpe en groupes séparés par des guillemets
 * 
 * Les groupes non entourés par les " sont découpés en mots
 * 
 * La recherche peut être limitée à une recherche stricte,
 * 
 * dès qu'un élement du filtrage est trouvé on le supprime de la chaine étudiée
 */
function filter_empty($str) {
	return $str != '';
}

function make_filtres($filtre) {

	//Découpe du filtre en sous-filtres
	$filtre = strtolower(preg_replace('/\s+/', ' ', secure($filtre)));
	$groupes = explode(secure('"'), $filtre);
	$filtres = array();
	
	foreach ($groupes as $i => $groupe) {
		$groupe = trim($groupe);

		//Si le groupe n'est pas entre guillemets ou si c'est le dernier groupe
		//On découpe alors le groupe à l'aide des espaces 
		if (!($i % 2) ||
			end($groupes) == $groupe)
			$filtres = array_merge($filtres, explode(' ', $groupe));


		//Sinon on conserve le groupe en entier
		else
			$filtres[] = $groupe;
	}

	//On supprime les filtres vides
	return array_filter($filtres, 'filter_empty');
}

function search_filtres($haystack, $filtres, $limitsearch = false) {
	$print = true;

	//Pour chacun des filtres...
	foreach ($filtres as $filtre) {

		//Si le filtre est non vide et qu'il n'est pas trouvé, alors on ne doit pas afficher l'élément concerné
		if ($filtre != '' &&
			strpos($haystack, $filtre) === false) {
			$print = false;
			break;
		}

		//Si le filtrage est limité en nombre d'occurence, on supprime la partie correspondant au filtre
		if ($limitsearch)
			$haystack = str_replace($filtre, '', $haystack);
	}

	return $print;
}

function in_array_multiple($needles, $haystack) {
	if (!count($needles)) return false;
	$needle = array_pop($needles);
	return in_array($needle, $haystack) && (
			!count($needles) || 
			in_array_multiple($needles, $haystack));
}

function http_post($url, $post = []) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true); //A cause des chaines contenant @
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
	$response = curl_exec($ch);
	return !curl_errno($ch) ? $response : curl_error($ch);
}

function http_get($url) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
	$response = curl_exec($ch);
	return !curl_errno($ch) ? $response : curl_error($ch);
}



function removeAccents($txt){
    $txt = str_replace('œ', 'oe', $txt);
    $txt = str_replace('Œ', 'Oe', $txt);
    $txt = str_replace('æ', 'ae', $txt);
    $txt = str_replace('Æ', 'Ae', $txt);
    mb_regex_encoding('UTF-8');
    $txt = mb_ereg_replace('[ÀÁÂÃÄÅĀĂǍẠẢẤẦẨẪẬẮẰẲẴẶǺĄ]', 'A', $txt);
    $txt = mb_ereg_replace('[àáâãäåāăǎạảấầẩẫậắằẳẵặǻą]', 'a', $txt);
    $txt = mb_ereg_replace('[ÇĆĈĊČ]', 'C', $txt);
    $txt = mb_ereg_replace('[çćĉċč]', 'c', $txt);
    $txt = mb_ereg_replace('[ÐĎĐ]', 'D', $txt);
    $txt = mb_ereg_replace('[ďđ]', 'd', $txt);
    $txt = mb_ereg_replace('[ÈÉÊËĒĔĖĘĚẸẺẼẾỀỂỄỆ]', 'E', $txt);
    $txt = mb_ereg_replace('[èéêëēĕėęěẹẻẽếềểễệ]', 'e', $txt);
    $txt = mb_ereg_replace('[ĜĞĠĢ]', 'G', $txt);
    $txt = mb_ereg_replace('[ĝğġģ]', 'g', $txt);
    $txt = mb_ereg_replace('[ĤĦ]', 'H', $txt);
    $txt = mb_ereg_replace('[ĥħ]', 'h', $txt);
    $txt = mb_ereg_replace('[ÌÍÎÏĨĪĬĮİǏỈỊ]', 'I', $txt);
    $txt = mb_ereg_replace('[ìíîïĩīĭįıǐỉị]', 'i', $txt);
    $txt = str_replace('Ĵ', 'J', $txt);
    $txt = str_replace('ĵ', 'j', $txt);
    $txt = str_replace('Ķ', 'K', $txt);
    $txt = str_replace('ķ', 'k', $txt);
    $txt = mb_ereg_replace('[ĹĻĽĿŁ]', 'L', $txt);
    $txt = mb_ereg_replace('[ĺļľŀł]', 'l', $txt);
    $txt = mb_ereg_replace('[ÑŃŅŇ]', 'N', $txt);
    $txt = mb_ereg_replace('[ñńņňŉ]', 'n', $txt);
    $txt = mb_ereg_replace('[ÒÓÔÕÖØŌŎŐƠǑǾỌỎỐỒỔỖỘỚỜỞỠỢ]', 'O', $txt);
    $txt = mb_ereg_replace('[òóôõöøōŏőơǒǿọỏốồổỗộớờởỡợð]', 'o', $txt);
    $txt = mb_ereg_replace('[ŔŖŘ]', 'R', $txt);
    $txt = mb_ereg_replace('[ŕŗř]', 'r', $txt);
    $txt = mb_ereg_replace('[ŚŜŞŠ]', 'S', $txt);
    $txt = mb_ereg_replace('[śŝşš]', 's', $txt);
    $txt = mb_ereg_replace('[ŢŤŦ]', 'T', $txt);
    $txt = mb_ereg_replace('[ţťŧ]', 't', $txt);
    $txt = mb_ereg_replace('[ÙÚÛÜŨŪŬŮŰŲƯǓǕǗǙǛỤỦỨỪỬỮỰ]', 'U', $txt);
    $txt = mb_ereg_replace('[ùúûüũūŭůűųưǔǖǘǚǜụủứừửữự]', 'u', $txt);
    $txt = mb_ereg_replace('[ŴẀẂẄ]', 'W', $txt);
    $txt = mb_ereg_replace('[ŵẁẃẅ]', 'w', $txt);
    $txt = mb_ereg_replace('[ÝŶŸỲỸỶỴ]', 'Y', $txt);
    $txt = mb_ereg_replace('[ýÿŷỹỵỷỳ]', 'y', $txt);
    $txt = mb_ereg_replace('[ŹŻŽ]', 'Z', $txt);
    $txt = mb_ereg_replace('[źżž]', 'z', $txt);
    return $txt;
}


function isConnected() {
    $connected = @fsockopen("www.google.fr", 80); 
    if ($connected)
        fclose($connected);

    return $connected;
}

function unsecure($string) {
	return stripslashes(html_entity_decode($string.""));
}

function ucname($string) {
	// mb_strtolower() ne gere pas les strings vides
	if(empty($string)){
		return '';
	}

    $string = ucwords(mb_strtolower($string, 'UTF-8'));
    $delimiters = ['-', '\''];

    foreach ($delimiters as $delimiter) {
        if (strpos($string, $delimiter) !== false)
            $string = implode($delimiter, array_map('ucfirst', explode($delimiter, $string)));
    }
    
    return $string;
}

function getClientIp() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';

    return $ipaddress == '::1' ? '127.0.0.1' : $ipaddress;
}

/*********************************
*    *    *    *    *    *
-    -    -    -    -    -
|    |    |    |    |    |
|    |    |    |    |    + year [optional]
|    |    |    |    +----- day of week (0 - 7) (Sunday=0 or 7)
|    |    |    +---------- month (1 - 12)
|    |    +--------------- day of month (1 - 31)
|    +-------------------- hour (0 - 23)
+------------------------- min (0 - 59)
*********************************/
function isDueCronTab($frequency = '* * * * *', $time = false) {
	if (!isValidCronTab($frequency))
		return false;

    $time = is_string($time) ? strtotime($time) : ($time === false ? time() : $time);
    $time = array_map('intval', explode(' ', date('i G j n w Y', $time)));
    $crontab = explode(' ', $frequency);

    foreach ($crontab as $k => &$v) {
        $v = explode(',', $v);
        $regexps = array(
            '/^\*$/', # every 
            '/^\d+$/', # digit 
            '/^(\d+)\-(\d+)$/', # range
            '/^\*\/(\d+)$/' # every digit
        );
        $content = array(
            "true", # every
            "{$time[$k]} === $0", # digit
            "($1 <= {$time[$k]} && {$time[$k]} <= $2)", # range
            "{$time[$k]} % $1 === 0" # every digit
        );
        foreach ($v as &$v1)
            $v1 = preg_replace($regexps, $content, $v1);
        $v = '('.implode(' || ', $v).')';
    }
    $crontab = implode(' && ', $crontab);
    return eval("return {$crontab};");
}

function isValidCronTab($frequency) {
	return preg_match('/^((?:[1-9]?\d|\*)\s*(?:(?:[\/-][1-9]?\d)|(?:,[1-9]?\d)+)?\s*){5,6}$/', $frequency);
}