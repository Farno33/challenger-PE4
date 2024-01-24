<?php

$links = [
	'centraliens'				=> [
		'id_participant'		=> 'participants',
		'id_utilisateur'		=> 'utilisateurs'],

	'chambres' 					=> [],
	
	'chambres_participants' 	=> [
		'id_chambre' 			=> 'chambres',
		'id_participant'		=> 'participants'],
	
	'concurrents' 				=> [
		'id_equipe' 			=> 'equipes',
		'id_sportif' 			=> 'sportifs',
		'id_sport' 				=> 'sports'],

	'configurations' 			=> [],

	'contacts' 					=> [
		'id_utilisateur' 		=> 'utilisateurs'],

	'droits_admin' 				=> [
		'id_utilisateur' 		=> 'utilisateurs'],

	'droits_ecoles' 			=> [
		'id_ecole' 				=> 'ecoles',
		'id_utilisateur' 		=> 'utilisateurs'],

	'ecoles' 					=> [
		'id_respo' 				=> 'utilisateurs',
		'id_image'				=> 'images'],

	'ecoles_sports' 			=> [
		'id_ecole' 				=> 'ecoles',
		'id_sport' 				=> 'sports'],

	'equipes' 					=> [
		'id_ecole_sport' 		=> 'ecoles_sports', 
		'id_capitaine' 			=> 'participants'],

	'erreurs' 					=> [
		'id_participant' 		=> 'participants'],

	'groupes' 					=> [
		'id_phase'				=> 'phases'],

	'images' 					=> [],

	'matchs' 					=> [
		'id_site' 				=> 'sites',
		'id_phase' 				=> 'phases',
		'id_concurrent_a'		=> 'concurrents',
		'id_concurrent_b'		=> 'concurrents'],

	'modeles' 					=> [],

	'paiements' 				=> [
		'id_ecole' 				=> 'ecoles'],
	
	'participants' 				=> [
		'id_ecole' 				=> 'ecoles',
		'id_tarif_ecole' 		=> 'tarifs_ecoles'], 
	
	'phases' 					=> [
		'id_sport' 				=> 'sports',
		'id_phase_suivante'		=> 'phases'],

	'phases_concurrents' 		=> [
		'id_phase'				=> 'phases',
		'id_groupe'				=> 'groupes',
		'id_concurrent'			=> 'concurrents'],

	'podiums' 					=> [
		'id_sport' 				=> 'sports',
		'id_concurrent1'		=> 'concurrents',
		'id_concurrent2'		=> 'concurrents',
		'id_concurrent3'		=> 'concurrents',
		'id_concurrent3ex'		=> 'concurrents'],

	'points' 					=> [
		'id_ecole' 				=> 'ecoles'],
	
	'quotas_ecoles' 			=> [
		'id_ecole' 				=> 'ecoles'],

	'sites' 					=> [],

	'sportifs' 					=> [
		'id_equipe' 			=> 'equipes',
		'id_participant' 		=> 'participants'],

	'sports' 					=> [
		'id_respo' 				=> 'utilisateurs'],

	'taches' 					=> [],

	'tarifs' 					=> [
		'id_sport_special'		=> 'sports',
		'id_ecole_for_special'	=> 'ecoles'],
	
	'tarifs_ecoles' 			=> [
		'id_tarif' 				=> 'tarifs',
		'id_ecole' 				=> 'ecoles'],
	
	'tentes' 					=> [
		'id_zone'				=> 'zones',
		'id_ecole' 				=> 'ecoles'],
	
	'tokens' 					=> [
		'id_utilisateur' 		=> 'utilisateurs'],
	
	'utilisateurs' 				=> [],
	
	'zones' 					=> []];


foreach ($links as $k => $l) {
	$links[$k]['_auteur'] = 'utilisateurs';
}

$tables = ['centraliens', 'chambres', 'chambres_participants', 'concurrents', 'configurations', 'contacts', 'droits_admin', 'droits_ecoles', 'ecoles', 'ecoles_sports', 'equipes', 'erreurs', 'groupes', 'images', 'matchs', 'modeles', 'paiements', 'participants', 'phases', 'phases_concurrents', 'podiums', 'points', 'quotas_ecoles', 'sites', 'sportifs', 'sports', 'taches', 'tarifs', 'tarifs_ecoles', 'tentes', 'utilisateurs', 'zones'];
$table = !empty($_GET['table']) && in_array($_GET['table'], $tables) ? $_GET['table'] : null;
$from = !empty($_GET['from']) ? new DateTime($_GET['from']) : null;
$to = !empty($_GET['to']) ? new DateTime($_GET['to']) : null;
$withModifs = !empty($_GET['modifs']);
$tid = isset($_GET['id']) && strlen($_GET['id']) ? intval($_GET['id']) : null;
$blame = !empty($_GET['blame']);
$xls = isset($_GET['excel']);
$restore = isset($_GET['restore']);

if (empty($table))
	$with = [];

else if ($tid !== null)
	$with = [$tid];

else {
	$with = $pdo->query('SELECT id FROM '.$table.' WHERE _ref IS NULL '.
		($to ? 'AND _date <= "'.$to->format(DateTime::W3C).'"' : ''))
		->fetchAll(PDO::FETCH_ASSOC);
	$with = array_column($with, 'id');
	$with = $xls || $restore ? array_slice($with, 0, 1) : $with;  
}

$colsBlame = ['_auteur', '_u_login', '_u_nom', '_u_prenom', '_u_email', '_u_telephone', '_message'];
$colsCommon = ['id', '_date', '_action'];
$colsOmits = ['_ref', '_etat'];
$colsNoDiff = ['id', '_date', '_action'];
$groups = [];
$header = [];  

searchHistory:
foreach ($with as $id) {
	$historyAfter = "SELECT ".
			"t.*, u.login AS _u_login, u.nom AS _u_nom, u.prenom AS _u_prenom, ".
			"u.email AS _u_email, u.telephone AS _u_telephone ".		
		"FROM (SELECT ".
				"@r AS _id, ".
		        "@r := (SELECT id FROM ".$table." WHERE _ref = _id LIMIT 1) ".
		    "FROM (SELECT @r := ".$id.") vars, ".$table." ".
		    "WHERE @r IS NOT NULL) i ".
	    "JOIN ".$table." t ON t.id = i._id ".
	    "LEFT JOIN utilisateurs u ON u.id = t._auteur ".
	    "ORDER BY t._date ASC";
	$historyAfter = $pdo->query($historyAfter) or die(print_r($pdo->errorInfo()));
	$historyAfter = $historyAfter->fetchAll(PDO::FETCH_ASSOC);
	array_shift($historyAfter);

	$historyBefore = "SELECT ".
			"t.*, u.login AS _u_login, u.nom AS _u_nom, u.prenom AS _u_prenom, ".
			"u.email AS _u_email, u.telephone AS _u_telephone ".
		"FROM (SELECT ".
				"@r AS _id, ".
		        "@r := (SELECT _ref FROM ".$table." WHERE id = _id LIMIT 1) ".
		    "FROM (SELECT @r := ".$id.") vars, ".$table." ".
		    "WHERE @r IS NOT NULL) i ".
	    "JOIN ".$table." t ON t.id = i._id ".
	    "LEFT JOIN utilisateurs u ON u.id = t._auteur ".
	    "ORDER BY t._date ASC";
	$historyBefore = $pdo->query($historyBefore)->fetchAll(PDO::FETCH_ASSOC);

	if (count($historyAfter) + count($historyBefore) < 1)
		continue;

	$history = array_merge($historyBefore, $historyAfter);
	if (empty($header)) {
		$cols = array_merge(array_keys($history[0]), ['_action']);
		foreach ($cols as $l) {
			if (in_array($l, $colsOmits) ||
				!in_array($l, $colsCommon) && (
					!$blame && in_array($l, $colsBlame) ||
					$blame && !in_array($l, $colsBlame)))
				continue;

			$header[$l] = $l;
		}
	}

	$count = count($history);
	$hasBefore = false;
	$items = [];
	$p = null;

	$etatBefore = null;
	foreach ($history as $k => $h) {
		$h['_action'] = printActionItem($h['_etat'], $h['_ref'], $etatBefore);
		$etatBefore = $h['_etat'];
		$h['_action'] = $xls ? strip_tags($h['_action']) : $h['_action'];
		$date = new DateTime($h['_date']);

		if ($to && $date > $to)
			break;

		if ($hasBefore && (!$from || $date >= $from)) {
			$items[] = $p;
			$hasBefore = false;
		}

		$h['@recent'] = in_array($h['_etat'], ['active', 'desactive']) ? $h['_etat'] : null;
		$h['@actual'] = $tid !== null && $id == $h['id'];

		if ($h['@recent'] && $count === 1 && $withModifs)
			break;

		if (!$from || $date >= $from) {
			$items[] = $h;
		} else {
			$hasBefore = true;
		}

		$p = $h;
	}

	if (count($items))
		$groups[$id] = $items;
}

if ($xls) {
	foreach ($groups as $id => $history) {
		$titre = 'Liste '.($blame ? 'blamée ' : '').'des modifications ('.$table.' / '.$id.')';
		$fichier = 'liste_'.($blame ? 'blamee_' : '').'modifs_'.$table.'_'.$id;
		$labels = $header;
		$items = array_reverse($history);
		exportXLSX($items, $fichier, $titre, $labels);
	}
}

if ($restore && count($with) === 1 && count($history) > 0) {
	$last = array_reverse($history)[0];
	$toRestore = $last['id'];
	$isDesactive = $last['_etat'] === 'desactive';
	$ref = pdoRevision($table, $toRestore, null, $isDesactive ? 'desactive' : 'revision');
	$pdo->exec('UPDATE '.$table.' SET '.
			'_date = NOW(), '.
			'_etat = "'.($isDesactive ? 'active' : 'desactive').'", '.
			'_ref = '.$ref.', '.
			'_message = "'.($isDesactive ? 'Restoration' : 'Suppression').' de l\'élément" '.
		'WHERE '.
			'id = '.$toRestore);
	unset($_GET['restore']);
	$query = http_build_query($_GET); 
	header('location:'.url('history', true, false).'?'.$query); 
}

?>

<style>
* { font-family: Arial; }
form, input, select { font-size:11px; }
table { border-collapse: collapse; margin-right: 100px; font-size:11px; }
th, td { min-width: 30px; }
th { height: 110px; text-transform: uppercase; font-size: 10px; position: relative; font-weight: bold;}
td { background: #F3F3F3; border: 1px solid #CCC; padding: 0;}
tr:nth-of-type(2n) td { background: #FAFAFA; }
td.inter { color: #CCC; }
td.desactive { background: #FDD !important; }
td.active { background: #DFD !important; }
td.actual { background: #DDF !important; color: #000; }
td.diff { background: #333 !important; color: #FFF; }
td.sep { background: transparent; height: 2em; border: none; } 
th > div { width: 140px; transform: rotate(-45deg); position:absolute; left: -12px; text-align: left; overflow: hidden; margin-top: 2px; }
th > div > span { display:block; width:140px; border-top: 1px solid #ccc; padding: 5px 0 5px 26px }
tr:hover td { background: #FFE !important; cursor: pointer; color: #000; }
tr:hover td.diff { background: #FE3 !important; }
th.down { height: auto; text-align: left; padding-left: 100px; padding-top: 3em; }
th.down a { text-decoration: none; font-weight: bold; color: #FFF; padding: 5px 10px; background: green; border:3px solid #3C3; }
td.data div { height: 1.25em; overflow: auto; padding:5px; }
td.link div { color:blue; text-decoration: underline; text-shadow:0px 0px 5px #F3F3F3; }
form { background: #CCC; padding: 10px; }
img { height: 30px; } 
</style>

<form method="get" action="#">
	<select name="table">
		<option value=""></option>

		<?php foreach ($tables as $t) { ?>

		<option value="<?php echo $t; ?>" <?php if ($table === $t) echo 'selected'; ?>><?php echo $t; ?></option>

		<?php } ?>

	</select>
	<input type="text" name="id" value="<?php echo $tid; ?>" placeholder="ID" />
	<br />
	
	<label for="blame">
		<input type="checkbox" id="blame" name="blame" value="1" <?php if ($blame) echo 'checked'; ?> />
		Afficher les données sur les auteurs
	</label><br />
	
	<label for="modifs">
		<input type="checkbox" id="modifs" name="modifs" value="1" <?php if ($withModifs) echo 'checked'; ?> />
		Afficher uniquement les éléments modifiés
	</label><br />

	<input type="text" name="from" value="<?php echo ($from ? $from->format(DateTime::W3C) : null); ?>" placeholder="De" />
	<input type="text" name="to" value="<?php echo ($to ? $to->format(DateTime::W3C) : null); ?>" placeholder="Jusque" />
	<br />

	<br />
	<input type="submit" value="Afficher" />
</form>

<table>

	<?php foreach ($groups as $id => $items) { ?>

	<tr>
		<th colspan="<?php echo count($header); ?>" class="down">
			<a href="<?php echo 
				'?blame='.(int) $blame.
				'&modifs='.(int) $withModifs.
				'&table='.$table.
				'&id='.$id.
				'&from='.($from ? urlencode($from->format(DateTime::W3C)) : null).
				'&to='.($to ? urlencode($to->format(DateTime::W3C)) : null).
				'&excel' ?>">Télécharger</a>

			<a href="<?php echo 
				'?blame='.(int) $blame.
				'&modifs='.(int) $withModifs.
				'&table='.$table.
				'&id='.$id.
				'&from='.($from ? urlencode($from->format(DateTime::W3C)) : null).
				'&to='.($to ? urlencode($to->format(DateTime::W3C)) : null).
				'&restore' ?>">Restorer</a>
		</th>
	</tr>

	<tr>

		<?php foreach ($header as $l) { ?>
		
		<th><div><span><?php echo $l; ?></span></div></th>

		<?php } ?>

	</tr>

	<?php 

	$output = '';
	$p = null;
	foreach ($items as $id => $h) { 
		ob_start();
	
	?>

	<tr onclick="window.location.href='<?php echo 
		'?table='.$table.
		'&id='.$h['id'].
		'&blame='.($tid && $h['id'] == $tid ? (int) !$blame : (int) $blame).
		'#'.$h['id']; ?>'">

		<?php 

		foreach ($header as $l) { 
			$v = $h[$l];
			$first = end($items) === $h;
			$diff = !$blame && !in_array($l, $colsNoDiff) && !empty($p) && $p[$l] != $v;
			$inter = !$blame && !empty($p) && $p[$l] == $v && !$first;
			
			switch ($l) {
				case '_date':
					$value = printDateTime($v);
					break;
				case 'sexe':
					$value = printSexe($v, false);
					break;
				default;
					$value = $v;
			}

			$link = !empty($links[$table][$l]) ? $links[$table][$l] : null;

		?>
		
		<td class="data <?php echo 
			($first ? $h['@recent'].' ' : '').
			($h['@actual'] ? 'actual ' : '').
			($link ? 'link ' : '').
			($diff ? 'diff ' : '').
			($inter ? 'inter ' : ''); ?>"
			<?php if ($link) { ?>
			onclick="window.location.href='<?php echo 
				'?table='.$link.
				'&id='.$v.
				'&blame=0'.
				'#'.$v; ?>'; event.stopPropagation()"
			<?php } ?>>
			<?php echo 
				($l === 'id' ? '<a name="'.$v.'"></a>' : '').
				($l === 'image' && !empty($value) ? '<img src="data:image/png;base64, '.$value.'" />' : '<div>'.$value.'</div>'); ?>
		</td>

		<?php } ?>

	</tr>

	<?php 
		
		$content = ob_get_clean();
		$output = $content . $output;
		$p = $h;
	}

	echo $output;
}

?>

</table>