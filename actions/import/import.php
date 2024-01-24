<?php


$id = $args[1][0];
if (!(!empty($_SESSION['user']) && (
		!empty($_SESSION['user']['privileges']) &&
		in_array('ecoles', $_SESSION['user']['privileges']) ||
		!empty($_SESSION['user']['ecoles']) &&
		in_array($id, $_SESSION['user']['ecoles']))))
	die(header('location:'.url('accueil', false, false)));


$now = new DateTime();
$finPhase1 = new DateTime(APP_FIN_PHASE1);
$finPhase2 = new DateTime(APP_DATE_MALUS);
$finMalus = new DateTime(APP_FIN_MALUS);
$finInscrip = new DateTime(APP_FIN_INSCRIP);

$phase_actuelle = $now < $finPhase1 ? 'phase1' : (
	$now < $finPhase2 ? 'phase2' : (
		$now < $finMalus ? 'malus' : (
			$now < $finInscrip ? 'modif' : null)));


if (!empty($_SESSION['user']['privileges']) &&
	in_array('ecoles', $_SESSION['user']['privileges']))
	$accesAdmin = true;


if ((empty($phase_actuelle) || $phase_actuelle == 'modif') && empty($accesAdmin))
	die(header('location:'.url('accueil', false, false)));


$pdo->exec('DELETE FROM participants WHERE _etat = "temp" AND _auteur = '.(int) $_SESSION['user']['id']);
$pdo->exec('DELETE FROM equipes WHERE _etat = "temp" AND _auteur = '.(int) $_SESSION['user']['id']);
$pdo->exec('DELETE FROM sportifs WHERE _etat = "temp" AND _auteur = '.(int) $_SESSION['user']['id']);


$ecole = $pdo->query('SELECT '.
		'e.*, '.
		'(SELECT COUNT(p1.id) FROM participants AS p1 WHERE p1.id_ecole = e.id AND p1._etat = "active") AS nb_inscriptions, '.
		'(SELECT COUNT(p2.id) FROM participants AS p2 WHERE p2.id_ecole = e.id AND p2._etat = "active" AND p2.sportif = 1) AS nb_sportif, '.
		'(SELECT COUNT(p3.id) FROM participants AS p3 WHERE p3.id_ecole = e.id AND p3._etat = "active" AND p3.pompom = 1) AS nb_pompom, '.
		'(SELECT COUNT(p4.id) FROM participants AS p4 WHERE p4.id_ecole = e.id AND p4._etat = "active" AND p4.fanfaron = 1) AS nb_fanfaron, '.
		'(SELECT COUNT(p5.id) FROM participants AS p5 WHERE p5.id_ecole = e.id AND p5._etat = "active" AND p5.cameraman = 1) AS nb_cameraman, '.
		'(SELECT COUNT(p6.id) FROM participants AS p6 WHERE p6.id_ecole = e.id AND p6._etat = "active" AND p6.pompom = 1 AND p6.sportif = 0) AS nb_pompom_nonsportif, '.
		'(SELECT COUNT(p7.id) FROM participants AS p7 WHERE p7.id_ecole = e.id AND p7._etat = "active" AND p7.fanfaron = 1 AND p7.sportif = 0) AS nb_fanfaron_nonsportif, '.
		'(SELECT COUNT(p10.id) FROM participants AS p10 WHERE p10.id_ecole = e.id AND p10._etat = "active" AND p10.cameraman = 1 AND p10.sportif = 0) AS nb_cameraman_nonsportif, '.
		'(SELECT COUNT(p8.id) FROM participants AS p8 JOIN tarifs_ecoles AS te8 ON te8.id = p8.id_tarif_ecole AND te8._etat = "active" JOIN tarifs AS t8 ON t8.id = te8.id_tarif AND t8.logement = 1 AND t8._etat = "active" WHERE p8.id_ecole = e.id AND p8.sexe = "f" AND p8._etat = "active") AS nb_filles_logees, '.
		'(SELECT COUNT(p9.id) FROM participants AS p9 JOIN tarifs_ecoles AS te9 ON te9.id = p9.id_tarif_ecole AND te9._etat = "active" JOIN tarifs AS t9 ON t9.id = te9.id_tarif AND t9.logement = 1 AND t9._etat = "active" WHERE p9.id_ecole = e.id AND p9.sexe = "h" AND p9._etat = "active") AS nb_garcons_loges '.
	'FROM ecoles AS e '.
	'WHERE '.
		'e._etat = "active" AND '.
		'e.id = '.(int) $id)
	or DEBUG_ACTIVE && die(print_r($pdo->errorInfo()));
$ecole = $ecole->fetch(PDO::FETCH_ASSOC);


$quotas = $pdo->query('SELECT '.
		'quota, '.
		'valeur, '.
		'id '.
	'FROM quotas_ecoles '.
	'WHERE '.
		'id_ecole = '.(int) $id.' AND '.
		'_etat = "active"')
	->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);


foreach ($quotas as $quota => $valeur)
	$quotas[$quota] = $valeur['valeur'];


$quotas_reserves = $pdo->query('SELECT '.
		'es.id_sport, '.
		's.sport, '.
		's.sexe, '.
		'es.quota_reserves, '.
		'(SELECT COUNT(p.id) FROM participants AS p JOIN sportifs AS sp ON sp.id_participant = p.id AND sp._etat = "active" JOIN equipes AS eq ON eq._etat = "active" AND eq.id = sp.id_equipe WHERE p.id_ecole = es.id_ecole AND p._etat = "active" AND eq.id_ecole_sport = es.id) AS sportifs '.
	'FROM ecoles_sports AS es '.
	'JOIN sports AS s ON '.
		's._etat = "active" AND '.
		's.id = es.id_sport '.
	'WHERE '.
		'es.id_ecole = '.$ecole['id'].' AND '.
		'es.quota_reserves > 0 AND '.
		'es._etat = "active"')
	->fetchAll(PDO::FETCH_ASSOC);

if (isset($quotas['total'])) {
	$places_reservees = 0;

	foreach ($quotas_reserves as $quota_reserves) {
		if ($quota_reserves['sportifs'] < $quota_reserves['quota_reserves']) {
			$quotas['total'] -= $quota_reserves['quota_reserves'];
			$places_reservees += $quota_reserves['quota_reserves'];
		}
	}
}


if (empty($ecole) ||
	$ecole['etat_inscription'] == 'fermee' && (
		empty($_SESSION['user']['privileges']) ||
		!in_array('ecoles', $_SESSION['user']['privileges'])))
	die(header('location:'.url('', false, false)));


if ($ecole['etat_inscription'] != 'ouverte' && (
		empty($_SESSION['user']['privileges']) ||
		!in_array('ecoles', $_SESSION['user']['privileges'])))
	die(header('location:'.url('ecoles/'.$ecole['id'].'/recapitulatif', false, false)));


if (isset($quotas['total']) &&
	$quotas['total'] <= $ecole['nb_inscriptions'])
	die(header('location:'.url('ecoles/'.$ecole['id'].'/participants', false, false)));



$sports = $pdo->query('SELECT '.
		's.sport, '.
		's.sexe, '.
		'(SELECT COUNT(sp.id_participant) '.
			'FROM sportifs AS sp '.
			'JOIN equipes AS eq ON '.
				'eq.id = sp.id_equipe AND '.
				'eq._etat = "active" '.
			'WHERE '.
				'eq.id_ecole_sport = es.id AND '.
				'sp._etat = "active") AS nb, '.
		'(SELECT COUNT(spt.id_participant) '.
			'FROM sportifs AS spt '.
			'JOIN equipes AS eqt ON '.
				'eqt.id = spt.id_equipe AND '.
				'eqt._etat = "active" '.
			'JOIN ecoles_sports AS est ON '.
				'est.id = eqt.id_ecole_sport AND '.
				'est._etat = "active" '.
			'WHERE '.
				'est.id_sport = s.id AND '.
				'spt._etat = "active") AS nbt, '.
		'es.quota_max, '.
		'es.quota_equipes, '.
		's.quota_inscription '.
	'FROM ecoles_sports AS es '.
	'JOIN sports AS s ON '.
		's.id = es.id_sport AND '.
		's._etat = "active" '.
	'WHERE '.
		'es.id_ecole = '.(int) $id.' AND '.
		'es._etat = "active"')
	->fetchAll(PDO::FETCH_ASSOC);


if (!empty($_FILES['file']) && 
	!empty($_SESSION['csvPHP']) &&
	!empty($_POST['csvPHP']) &&
	$_POST['csvPHP'] == $_SESSION['csvPHP']) {
	$csvPHP = ['error' => false, 'datas' => []];

	if (!empty($_FILES['file']['error']) ||
		!isset($_FILES['file']['tmp_name']) ||
		!file_exists($_FILES['file']['tmp_name']))
		$csvPHP['error'] = 'chargement';

	else if (empty($_FILES['file']['size']) ||
		$_FILES['file']['size'] > 1024 * 1024)
		$csvPHP['error'] = 'size';

	else {
		$filename = basename($_FILES['file']['name']);
		$filedots = explode('.', $filename);
		$fileext = count($filedots) == 1 ? '' : '.'.array_pop($filedots);

		if (!in_array($fileext, ['.ods', '.xls', '.xlsx', '.csv']))
			$csvPHP['error'] = 'nonsupporte';

		else {
			require DIR.'includes/PHPExcel/PHPExcel/IOFactory.php';
			
			if ($fileext == '.csv') {
				$objReader = PHPExcel_IOFactory::createReader('CSV');
        		$objReader->setDelimiter(!empty($_POST['delimiter']) && 
        			strlen($_POST['delimiter']) ? $_POST['delimiter'] : ';');
        		$objPHPExcel = $objReader->load($_FILES['file']['tmp_name']);
			} 

			else
				$objPHPExcel = PHPExcel_IOFactory::load($_FILES['file']['tmp_name']);

			$nullValue = '';
			$calculateFormulas = true;
			$formatData = false;
			$returnCellRef = false;
			$csvPHP['datas'] = $objPHPExcel->getActiveSheet()->toArray($nullValue, $calculateFormulas, $formatData, $returnCellRef);

			$count = empty($csvPHP['datas']) ? 0 : count($csvPHP['datas'][0]);
			$columnToDelete = 0;
			for ($j = $count - 1; $j >= 0; $j--) {
				$column = array_column($csvPHP['datas'], $j);
				if (empty(implode('', $column)))
					$columnToDelete++;
			}

			foreach ($csvPHP['datas'] as $k => $data) {
				$csvPHP['datas'][$k] = array_slice($data, 0, $count - $columnToDelete);
			}
		}
	}
}

$_SESSION['csvPHP'] = rand();

?>

<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<title>Challenger - Importation en masse</title>

		<link href="<?php url('assets/css/bootstrap.min.css'); ?>" rel="stylesheet" />
        <link href="<?php url('assets/css/import.css'); ?>" rel="stylesheet"  />
        <link href="<?php url('assets/css/autocomplete.css'); ?>" rel="stylesheet"  />

   		<!-- Icones -->
		<link rel="shortcut icon" href="<?php url('assets/images/ico/favicon.ico'); ?>" type="image/x-icon" />
		<link rel="icon" href="<?php url('assets/images/ico/favicon.ico'); ?>" type="image/x-icon" />

		<!--[if lt IE 9]>
		<script src="<?php url('assets/js/html5shiv.min.js'); ?>"></script>
		<script src="<?php url('assets/js/respond.min.js'); ?>"></script>
		<![endif]-->
	</head>

	<body>
		<a id="header" onclick="return $returnStart(this);" href="<?php url('ecoles/'.$id.'/participants'); ?>">Challenger<span>Import</span></a>
		<noscript><div>Le site nécessite JavaScript pour fonctionner</div></noscript>

		<div class="over" id="step1">
			<form method="post" enctype="multipart/form-data">
				<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo 1024 * 1024; ?>" />
				<input type="hidden" name="delimiter" value="" />
				<input type="hidden" name="csvPHP" value="<?php echo $_SESSION['csvPHP']; ?>" />
				<input name="file" type="file" accept=".csv,.xls,.xlsx,.ods" />
			</form>

			<div class="actions">
				<div class="help">
					<span class="glyphicon glyphicon-question-sign"></span>
					<span class="title">Consulter<br />le tutoriel</span>
				</div>

				<div class="choose">
					<span class="glyphicon glyphicon-folder-open"></span>
					<span class="title">Sélectionner<br />CSV / XLS / ODS</span>
				</div>

				<div class="scratch">
					<span class="glyphicon glyphicon-th-list"></span>
					<span class="title">Saisir<br />manuellement</span>
				</div>
			</div>


			<h2>Importation en masse</h2>
			<section>
				<table>
					<thead>
						<tr>
							<th></th>
							<th>Inscrits</th>
							<th>Quota</th>
						</tr>
					</thead>

					<tbody>
						<tr>
							<td>Participants</td>
							<td><?php echo $ecole['nb_inscriptions']; ?></td>
							<td><?php echo isset($quotas['total']) ? (
								(int) $quotas['total'] <= $ecole['nb_inscriptions'] ? 
								'<span class="full">'.(int) $quotas['total'].'</span>' : (int) $quotas['total']).
								($places_reservees > 0 ? ' (+'.$places_reservees.')' : '') : ''; ?></td>
						</tr>
						<tr>
							<td>Sportifs <small>(non sportifs)</small></td>
							<td><?php echo $ecole['nb_sportif']; ?> <small>(<?php echo $ecole['nb_inscriptions'] - $ecole['nb_sportif']; ?>)</small></td>
							<td><?php echo isset($quotas['sportif']) ? (
								(int) $quotas['sportif'] <= $ecole['nb_sportif'] ? 
								'<span class="full">'.(int) $quotas['sportif'].'</span>' : (int) $quotas['sportif']) : ''; ?>
								<?php echo isset($quotas['nonsportif']) ? '<small>('.(
									(int) $quotas['nonsportif'] <= $ecole['nb_inscriptions'] - $ecole['nb_sportif'] ? 
									'<span class="full">'.(int) $quotas['nonsportif'].'</span>' : (int) $quotas['nonsportif']).')</small>' : ''; ?></td>
						</tr>
						<tr>
							<td>Logés</td>
							<td><?php echo $ecole['nb_filles_logees'] + $ecole['nb_garcons_loges']; ?></td>
							<td><?php echo isset($quotas['logement']) ? (
								(int) $quotas['logement'] <= $ecole['nb_filles_logees'] + $ecole['nb_garcons_loges'] ? 
								'<span class="full">'.(int) $quotas['logement'].'</span>' : (int) $quotas['logement']) : ''; ?></td>
						</tr>
						<tr>
							<td>Filles logées</td>
							<td><?php echo $ecole['nb_filles_logees']; ?></td>
							<td><?php echo isset($quotas['filles_logees']) ? (
								(int) $quotas['filles_logees'] <= $ecole['nb_filles_logees'] ? 
								'<span class="full">'.(int) $quotas['filles_logees'].'</span>' : (int) $quotas['filles_logees']) : ''; ?></td>
						</tr>
						<tr>
							<td>Garçons logés</td>
							<td><?php echo $ecole['nb_garcons_loges']; ?></td>
							<td><?php echo isset($quotas['garcons_loges']) ? (
								(int) $quotas['garcons_loges'] <= $ecole['nb_garcons_loges'] ? 
								'<span class="full">'.(int) $quotas['garcons_loges'].'</span>' : (int) $quotas['garcons_loges']) : ''; ?></td>
						</tr>
						<tr>
							<td>Fanfarons <small>(non sportifs)</small></td>
							<td><?php echo $ecole['nb_fanfaron']; ?> <small>(<?php echo $ecole['nb_fanfaron_nonsportif']; ?>)</small></td>
							<td><?php echo isset($quotas['fanfaron']) ? (
								(int) $quotas['fanfaron'] <= $ecole['nb_fanfaron'] ? 
								'<span class="full">'.(int) $quotas['fanfaron'].'</span>' : (int) $quotas['fanfaron']) : ''; ?>
								<?php echo isset($quotas['fanfaron_nonsportif']) ? '<small>('.(
									(int) $quotas['fanfaron_nonsportif'] <= $ecole['nb_fanfaron_nonsportif'] ? 
									'<span class="full">'.(int) $quotas['fanfaron_nonsportif'].'</span>' : (int) $quotas['fanfaron_nonsportif']).')</small>' : ''; ?></td>
						</tr>
						<tr>
							<td>Pompoms <small>(non sportifs)</small></td>
							<td><?php echo $ecole['nb_pompom']; ?> <small>(<?php echo $ecole['nb_pompom_nonsportif']; ?>)</small></td>
							<td><?php echo isset($quotas['pompom']) ? (
								(int) $quotas['pompom'] <= $ecole['nb_pompom'] ? 
								'<span class="full">'.(int) $quotas['pompom'].'</span>' : (int) $quotas['pompom']) : ''; ?>
								<?php echo isset($quotas['pompom_nonsportif']) ? '<small>('.(
									(int) $quotas['pompom_nonsportif'] <= $ecole['nb_pompom_nonsportif'] ? 
									'<span class="full">'.(int) $quotas['pompom_nonsportif'].'</span>' : (int) $quotas['pompom_nonsportif']).')</small>' : ''; ?></td>
						</tr>
						<tr>
							<td>Cameramans <small>(non sportifs)</small></td>
							<td><?php echo $ecole['nb_cameraman']; ?> <small>(<?php echo $ecole['nb_cameraman_nonsportif']; ?>)</small></td>
							<td><?php echo isset($quotas['cameraman']) ? (
								(int) $quotas['cameraman'] <= $ecole['nb_cameraman'] ? 
								'<span class="full">'.(int) $quotas['cameraman'].'</span>' : (int) $quotas['cameraman']) : ''; ?>
								<?php echo isset($quotas['cameraman_nonsportif']) ? '<small>('.(
									(int) $quotas['cameraman_nonsportif'] <= $ecole['nb_cameraman_nonsportif'] ? 
									'<span class="full">'.(int) $quotas['cameraman_nonsportif'].'</span>' : (int) $quotas['cameraman_nonsportif']).')</small>' : ''; ?></td>
						</tr>
					</tbody>
				</table>
			</section>


			<section>
				<table>
					<thead>
						<tr>
							<th></th>
							<th>Inscrits <small>(Total)</small></th>
							<th>Quota <small>(Max)</small></th>
						</tr>
					</thead>

					<tbody>
						
						<?php foreach ($sports as $sport) { ?>

						<tr>
							<td><?php echo stripslashes($sport['sport']).' '.printSexe($sport['sexe']); ?></td>
							<td><?php echo $sport['nb'].($sport['quota_inscription'] ? ' <small>('.$sport['nbt'].')</small>' : ''); ?></td>
							<td><?php echo ($sport['quota_max'] <= $sport['nb'] ? 
								'<span class="full">'.$sport['quota_max'].'</span>' : $sport['quota_max']).
								($sport['quota_inscription'] ? ' <small>('.($sport['quota_inscription'] <= $sport['nbt'] ? 
								'<span class="full">'.$sport['quota_inscription'].'</span>' : $sport['quota_inscription']).')</small>' : ''); ?></td>
						</tr>

						<?php } if (empty($sports)) { ?>

						<tr class="vide">
							<td colspan="3">Aucun sport</td>
						</tr>

						<?php } ?>

					</tbody>
				</table>
			</section>
		</div>

		<div id="hints">
			<div class="hint hint-model">
				<span class="glyphicon glyphicon-remove" aria-hidden="true" onclick="$(this).parent().slideUp(200, function() { $(this).remove(); }); event.cancelBubble=true;"></span>
				<span></span>
			</div>
		</div>

		<table id="table-excel">
            <thead>
                <tr>
                    <th onclick="return $addColumn()" title="Ajouter une colonne"><div><span class="glyphicon glyphicon-plus"></span></div></th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>

		<script src="<?php url('assets/js/jquery.min.js'); ?>"></script>
		<script src="<?php url('assets/js/js.cookie.js'); ?>"></script>
		<script src="<?php url('assets/js/jquery-ui.autocomplete.min.js'); ?>"></script>
		<script src="<?php url('assets/js/papaparse.min.js'); ?>"></script>
		<script type="text/javascript">
		$(function() {
			var forcePHP = false;
			var csvPHP = null;
			var ecole_lyonnaise = <?php echo $ecole['ecole_lyonnaise'] ? 'true' : 'false'; ?>;

			//Toutes les 30 secondes on fait une requete dans le vide pour conserver la session
			setInterval(function() {
				$.ajax({
					url: "<?php url('ecoles/'.$id.'/import/ajax'); ?>",
				  	method: "POST",
				  	cache: false,
					data:{load:"null"}
				});
			}, 30000);


			<?php if (!empty($csvPHP)) { ?>

			csvPHP = {
				error: <?php echo !empty($csvPHP['error']) ? '"'.$csvPHP['error'].'"' : 'false'; ?>,
				datas: <?php echo !empty($csvPHP['error']) ? '[]' : json_encode($csvPHP['datas']) ?>
			};

			<?php } ?>

			$('table#table-excel').scrollTop(0).scrollLeft(0);

		    //Décalage des numéros de lignes et des identification de colonnes lors du scroll dans le tableau
		    var busy = false;
		    var top;
		    var left;
		    $('table#table-excel').on('scroll', function() {
		    	if(busy)
			        return;

			    busy = true;
		        
		        $('table#table-excel tr th:first-of-type div').css({'left':$('#table-excel').scrollLeft()});
		        $('table#table-excel thead tr th div').css({'top':$('#table-excel').scrollTop()});
		        
		        if ($('input[autocomplete="off"]').length) {
		        	var inputAutoComplete = $('input[autocomplete="off"]').first();
		        	
		        	$('.ui-autocomplete').css({'left': inputAutoComplete.offset().left + $('.ui-autocomplete').outerWidth() > $(window).width() ? 
		        		inputAutoComplete.offset().left + inputAutoComplete.outerWidth() - $('.ui-autocomplete').outerWidth() : inputAutoComplete.offset().left,
		        		'top': inputAutoComplete.offset().top + inputAutoComplete.outerHeight() + $('.ui-autocomplete').outerHeight() > $(window).height() ?
		        		inputAutoComplete.offset().top - $('.ui-autocomplete').outerHeight() : inputAutoComplete.offset().top + inputAutoComplete.outerHeight()});
		        }

		        busy = false;
		    });

		    $.fn.removeClassPrefix = function(prefix) {
			    this.each(function(i, el) {
			        var classes = el.className.split(" ").filter(function(c) {
			            return c.lastIndexOf(prefix, 0) !== 0;
			        });
			        el.className = $.trim(classes.join(" "));
			    });
			    return this;
			};

			$returnStart = function(header) {
				if (datas.length) {
					$hideHint('message');
					$hideHint('error');
					$hideHint('danger');
					$hideHint('warning');
					$hideHint('success');
					$hideHint('info');
					$addHint('Si tu veux vraiment revenir à l\'accueil de l\'outil d\'importation en masse, ' +
						'<a href="#" onclick="location.reload();event.cancelBubble=true;return false;">clique-ici</a>. ' +
						'Pour revenir à la gestion des participants, <a href="#" onclick="window.location.href=\'' + 
							$(header).attr('href') + '\';event.cancelBubble=true;return false;">clique-ici</a>.', 'message', 0);
					return false;
				}

				return true;
			};

			$addHint = function(content, classPlus, duration, numCol, deleteRows) {
				var hint = $('.hint-model').clone().removeClass('hint-model').prependTo('#hints');
				hint.children('span:not(.glyphicon)').html(content + 
					(classPlus == "warning" || classPlus == "danger" ? '<br />' : '') +
					(classPlus == "warning" && numCol !== null ? ('<br /><a href="#" onclick="$clickOnColumn(\'' + numCol + '\', true); ' +
						'event.cancelBubble=true;return false;">Forcer ce choix malgré les alertes</a>') : '') + 
					(classPlus == "danger" && numCol !== null && deleteRows === true ? ('<br /><a href="#" onclick="$deleteEmptyRows();$clickOnColumn(\'' + numCol + '\'); ' +
						'event.cancelBubble=true;return false;">Supprimer les lignes vides et réessayer</a>') : '') + 
					(classPlus == "danger" && numCol !== null && 
						$('table#table-excel thead tr:first th[data-num-col="' + numCol + '"]').length > 0 ? ('<br /><a href="#" onclick="$clickOnColumn(\'' + numCol + '\'); ' +
						'event.cancelBubble=true;return false;">Réessayer avec cette colonne</a>') : '') + 
					(classPlus == "warning" || classPlus == "danger" ? ('<br /><a href="#" onclick="$goToNext(\'' + classPlus + '\'); ' +
						'event.cancelBubble=true;return false;">Montrer l\'élément suivant concerné</a>') : ''));
				
				if (classPlus !== null) hint.addClass('hint-' + classPlus);
				if (classPlus == "required" ||
					classPlus == "global") hint.children('span.glyphicon').remove();
				
				hint.fadeIn(500);

				if (classPlus == "required") {
					var second = $('#hints .hint-required:not(:first-of-type)');
					second.slideUp(500, function(){ second.remove(); });
				}

				if (duration > null)
					setTimeout(function() { hint.slideUp(500, function() { hint.remove(); }); }, duration);
			};

			$hideHint = function(hintName) {
				if (hintName == 'model')
					return;

				var hint = hintName ? $('.hint.hint-' + hintName) : $('.hint:not(.hint-model)');
				hint.slideUp(500, function(){ hint.remove(); });
			};

			$removeColumn = function(numCol) {
				var text = $('table#table-excel thead tr th[data-num-col="' + numCol + '"]').text();

				if ($('table#table-excel thead tr th[data-num-col]').length <= 1) {
					$addHint('Le tableau ne peut pas être vide, la suppression de la colonne "' + text + '" est impossible.', 'error', 2000);
					return false;
				}

				setTimeout(function() {
					$addHint('La colonne "' + text + '" vient d\'être supprimée', 'success', 2000); }, 250);

				$('table#table-excel tr th[data-num-col="' + numCol + '"], '+
					'table#table-excel tr td[data-num-col="' + numCol + '"]').remove();

				return false;
			};

			$removeRow = function(numRow, noAlert) {
				$hideHint('message');

				if ($('table#table-excel tbody tr[data-num-row]').length <= 1) {
					setTimeout(function() {
						$addHint('Le tableau ne peut pas être vide, la suppression de la ligne est impossible.', 'error', 2000);
					}, 100);
					return false;
				}

				if (!noAlert)
					setTimeout(function() {
						$addHint('Une ligne vient d\'être supprimée', 'success', 2000); }, 250);

				$('table#table-excel tbody tr[data-num-row="' + numRow + '"], '+
					'table#table-excel tbody tr td[data-num-row="' + numRow + '"]').remove();

				$('table#table-excel tbody tr th').each(function(i, n) {
					$(this).html('<div>' + (i + 1) + '<span class="glyphicon glyphicon-remove" title="Supprimer cette ligne"></span></div>');
				});

				return true;
			};

			$addColumn = function(beforeCol, defaultValue) {
				$hideHint('message');

				if (typeof beforeCol === "undefined") {
					var str = 'Où veux-tu ajouter cette nouvelle colonne ?<br />' + 
						'<ul><li><a href="#" onclick="$addColumn(false);event.cancelBubble=true;return false;"><b>À la fin</b></a><br /><br /></li>';
					
					$('table#table-excel thead tr th[data-num-col]').each(function() {
						str += '<li><a href="#" onclick="$addColumn(' + $(this).attr('data-num-col') + ');event.cancelBubble=true;return false;">Avant &quot;<b>' + $(this).text() + '</b>&quot;</a></li>';
					});

					str += '</ul>';

					$addHint(str, 'message', 0);
					return;
				}

				var nbCols = $('table#table-excel thead tr th[data-num-col]').length;
				var nbRows = $('table#table-excel tbody tr[data-num-row]').length;

				if (nbCols >= 50) {
					$addHint('Ce tableau contient trop de colonnes, le maximum est de 50.', 'error', 2000);
					return false;
				}

				if ((nbCols + 1) * nbRows >= 2500) {
					$addHint('Ce tableau contient trop de cellules, le maximum est de 2500.', 'error', 2000);
					return false;
				}

				setTimeout(function() {
					$addHint('Une colonne vient d\'être ajoutée', 'success', 2000); }, 250);

				var numCol = 0;

				$('table#table-excel thead tr th').each(function() {
					if (parseInt($(this).attr('data-num-col')) > numCol)
						numCol = parseInt($(this).attr('data-num-col'));
				});

				numCol = numCol + 1;
				$('table#table-excel thead tr th' + (beforeCol ? '[data-num-col=' + beforeCol + ']' : ':last-of-type')).each(function(i, n) {
					var strHead = '<th data-num-col="' + numCol + '"><div>?' + 
							'<span class="glyphicon glyphicon-remove" title="Supprimer cette colonne"></span></div></th>';

					if (beforeCol)
						$(this).before(strHead);

					else
						$(this).after(strHead);
				});

				$('table#table-excel tbody tr ' + (nbCols ? 'td' + (beforeCol ? '[data-num-col=' + beforeCol + ']' : ':last-of-type') : 'th:first-of-type')).each(function(i, n) {
					var strCell = '<td data-num-row="' + i + '" data-num-col="' + numCol + '">' + 
							'<input type="text" value="' + (defaultValue == null ? '' : defaultValue) + '" /></td>';

					if (beforeCol && nbCols)
						$(this).before(strCell);

					else 
						$(this).after(strCell);
				});

				$('table#table-excel thead tr th div').css({'top':$('#table-excel').scrollTop()});

				return numCol;
			};

			$deleteEmptyRows = function() {
				var numRow;
				var estVide;
				var toRemove = [];
				var count = 0;

				$('table#table-excel tbody tr').each(function() {
					estVide = true;
					numRow = $(this).attr('data-num-row');

					$(this).find('td[data-num-col] input').each(function() {
						if ($(this).val().replace(/^\s+|\s+$/g, "").length > 0) {
							estVide = false;
							return false;
						}
					});

					if (estVide)
						toRemove.push(numRow);
				});

				for (var i in toRemove)
					count += $removeRow(toRemove[i], true) ? 1 : 0;

				if (count > 0) {
					setTimeout(function() {
						$addHint('Une ligne (ou plus) vient d\'être supprimée.', 'success', 2000); }, 250);
				}
			};

			$ucname = function(str){
			    str = str.toLowerCase();
			    return str.replace(/(^([a-zA-Z\p{M}]))|([ -][a-zA-Z\p{M}])/g,
				    function(firstLetter){ return firstLetter.toUpperCase(); });
			};

			$removeAccents = function(s){
			    var r = s.toLowerCase();
			    non_asciis = {'a': '[àáâãäå]', 'ae': 'æ', 'c': 'ç', 'e': '[èéêë]', 'i': '[ìíîï]', 'n': 'ñ', 'o': '[òóôõö]', 'oe': 'œ', 'u': '[ùúûűü]', 'y': '[ýÿ]'};
			    for (i in non_asciis) { r = r.replace(new RegExp(non_asciis[i], 'g'), i); }
			    return r;
			};

			$escapeHtml = function(text) {
				return text
					.replace(/&/g, "&amp;")
					.replace(/</g, "&lt;")
					.replace(/>/g, "&gt;")
					.replace(/"/g, "&quot;")
					.replace(/'/g, "&#039;");
			};

			$isValidEmail = function(email) {
			    var pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
			    return pattern.test(email);
			};

			$isValidSexe = function(sexe) {
				var pattern = /^(h(omme)?|f(emme)?|m(an)?|w(oman)?|0|1|o(ui)?|n(on?)?|y(es)?)$/i;
			    return pattern.test(sexe);
			};

			$isValidSportif = function(sportif) {
				var pattern = /^(|s(port(if)?)?|0|1|o(ui)?|n(on?)?|y(es)?)$/i;
			    return pattern.test(sportif);
			};

			$isValidFanfaron = function(sportif) {
				var pattern = /^(|f(anfar(|e|on(n?e)?)?)?|0|1|o(ui)?|n(on?)?|y(es)?)$/i;
			    return pattern.test(sportif);
			};

			$isValidPompom = function(sportif) {
				var pattern = /^(|p(om-?pom|im-?pim)?|0|1|o(ui)?|n(on?)?|y(es)?)$/i;
			    return pattern.test(sportif);
			};

			$isValidCameraman = function(sportif) {
				var pattern = /^(|c(amera((wo)?man)?)?|p(hoto(graphe)?)?|0|1|o(ui)?|n(on?)?|y(es)?)$/i;
			    return pattern.test(sportif);
			};

			$isValidPhone = function(phone) {
				var pattern = /^\s*(?:\+?(\d{1,3}))?([-. (]*\d[-. )]*)?([-. (]*(\d{2,4})[-. )]*(?:[-.x ]*(\d+))?)+\s*$/g;
				return pattern.test(phone);
			};

			$isValidLicence = function(licence) {
				var pattern = /^([a-z0-9]{4}\s*)?[0-9]{6}$/i;
				return pattern.test(licence);
			};

			$isCMLicence = function(licence) {
				var pattern = /^(cm|certificat m(e|é|&eacute;)dical)$/i;
				return pattern.test(licence);
			};

			$isValidLogement = function(logement) {
				var pattern = /^(|l(ight)?( p(ackage)?)?|f(ull)?( p(ackage)?)?|0|1|o(ui)?|n(on?)?|y(es)?)$/i;
			    return pattern.test(logement);
			};

			$isValidRecharge = function(recharge) {
				var pattern = /^\d*((\.|,)00?)?( ?€)?$/;
				return pattern.test(recharge);
			};

			$isValidCapitaine = function(capitaine) {
				var pattern = /^(|c(aptain|apitaine)?|0|1|o(ui)?|n(on?)?|y(es)?)$/i;
			    return pattern.test(capitaine);
			};

			$getSexe = function(sexe) {
				var pattern = /^(h(omme)?|m(an)?|1|o(ui)?|y(es)?)$/i;
				if (!$isValidSexe(sexe)) return '?';
				return pattern.test(sexe) ? 'h' : 'f';
			};

			$getSportif = function(sportif) {
				var pattern = /^(s(port(if)?)?|1|o(ui)?|y(es)?)$/i;
				if (!$isValidSportif(sportif)) return '?';
				return pattern.test(sportif) ? '1' : '0';
			};

			$getFanfaron = function(fanfaron) {
				var pattern = /^(f(anfar(|e|on(n?e)?)?)?|1|o(ui)?|y(es)?)$/i;
				if (!$isValidFanfaron(fanfaron)) return '?';
				return pattern.test(fanfaron) ? '1' : '0';
			};

			$getPompom = function(pompom) {
				var pattern = /^(p(om-?pom|im-?pim)?|1|o(ui)?|y(es)?)$/i;
				if (!$isValidPompom(pompom)) return '?';
				return pattern.test(pompom) ? '1' : '0';
			};

			$getCameraman = function(cameraman) {
				var pattern = /^(c(amera((wo)?man)?)?|p(hoto(graphe)?)?|1|o(ui)?|y(es)?)$/i;
				if (!$isValidCameraman(cameraman)) return '?';
				return pattern.test(cameraman) ? '1' : '0';
			};

			$getLogement = function(logement) {
				var pattern = /^(f(ull)?( p(ackage)?)?|1|o(ui)?|y(es)?)$/i;
				if (!$isValidLogement(logement)) return '?';
				return pattern.test(logement) ? '1' : '0';
			};

			$getCapitaine = function(capitaine) {
				var pattern = /^(c(aptain|apitaine)?|1|o(ui)?|y(es)?)$/i;
				if (!$isValidCapitaine(capitaine)) return '?';
				return pattern.test(capitaine) ? '1' : '0';
			};

			$levenshtein = function(s, t) {
			    var d = []; //2d matrix

			    // Step 1
			    var n = s.length;
			    var m = t.length;

			    if (n == 0) return m;
			    if (m == 0) return n;

			    //Create an array of arrays in javascript (a descending loop is quicker)
			    for (var i = n; i >= 0; i--) d[i] = [];

			    // Step 2
			    for (var i = n; i >= 0; i--) d[i][0] = i;
			    for (var j = m; j >= 0; j--) d[0][j] = j;

			    // Step 3
			    for (var i = 1; i <= n; i++) {
			        var s_i = s.charAt(i - 1);

			        // Step 4
			        for (var j = 1; j <= m; j++) {

			            //Check the jagged ld total so far
			            if (i == j && d[i][j] > 4) return n;

			            var t_j = t.charAt(j - 1);
			            var cost = (s_i == t_j) ? 0 : 1; // Step 5

			            //Calculate the minimum
			            var mi = d[i - 1][j] + 1;
			            var b = d[i][j - 1] + 1;
			            var c = d[i - 1][j - 1] + cost;

			            if (b < mi) mi = b;
			            if (c < mi) mi = c;

			            d[i][j] = mi; // Step 6

			            //Damerau transposition
			            if (i > 1 && j > 1 && s_i == t.charAt(j - 2) && s.charAt(i - 2) == t_j) {
			                d[i][j] = Math.min(d[i][j], d[i - 2][j - 2] + cost);
			            }
			        }
			    }

			    // Step 7
			    return d[n][m];
			};

			$getLicences = function() {
				$.ajax({
					url: "<?php url('ecoles/'.$id.'/import/ajax'); ?>",
				  	method: "POST",
				  	cache: false,
					dataType: "json",
					data:{load:"licences"},
					success: function(data) {
						licences = [];
						for (var i in data['licences']) 
							licences[data['licences'][i]['licence']] = data['licences'][i];

						for (var i in data['as']) {
							if (data['as'][i].match(/^[a-z\d]{4}$/i))
								asCode.push(data['as'][i].toUpperCase());
						}
					}
				});
			};

			$getTarifs = function() {
				$.ajax({
					url: "<?php url('ecoles/'.$id.'/import/ajax'); ?>",
				  	method: "POST",
				  	cache: false,
					dataType: "json",
					data:{load:"tarifs"},
					success: function(data) {
						tarifs = data;
					}
				});
			};

			$getSports = function() {
				$.ajax({
					url: "<?php url('ecoles/'.$id.'/import/ajax'); ?>",
				  	method: "POST",
				  	cache: false,
					dataType: "json",
					data:{load:"sports"},
					success: function(data) {
						sports = data;
					}
				});
			};

			$filtreTarifs = function(logement, sportif, fanfaron, pompom, cameraman) {
				var filtre = [];
				var tarif;
				
				for (var i in tarifs) {
					tarif = tarifs[i];
					
					if (tarif['logement'] == '0' && logement == '1' ||
						tarif['logement'] == '1' && logement == '0' ||
						tarif['sportif'] == '1' && !sportif ||
						tarif['sportif'] == '0' && sportif ||
						tarif['for_fanfaron'] == 'no' && fanfaron ||
						tarif['for_cameraman'] == 'no' && cameraman ||
						tarif['for_pompom'] == 'no' && pompom ||
						tarif['for_fanfaron'] == 'yes' && !fanfaron ||
						tarif['for_cameraman'] == 'yes' && !cameraman ||
						tarif['for_pompom'] == 'yes' && !pompom)
						continue; 

					filtre.push(tarif);
				}
				return filtre;
			};

			$filtreSports = function(sexe, id_sport_special) {
				var filtre = [];
				var sport;
				
				for (var i in sports) {
					sport = sports[i];

					if (sport['special'] == '1' && sport['id'] != id_sport_special ||
						sport['sexe'] == 'f' && sexe == 'h' ||
						sport['sexe'] == 'h' && sexe == 'f')
						continue; 

					filtre.push(sport);
				}
				return filtre;
			};


			var waitFor = ["nom", "prenom", "sexe", "sportif", "licence", "fanfaron", "pompom", "cameraman", "logement", "tarif", "logeur", "recharge", "sport", "capitaine", "telephone", "email"];
			var waitName = ["Nom", "Prénom", "Sexe", "Sportif", "Licence", "Fanfaron", "Pompom / Pimpim", "Caméraman / Photographe", "Logement", "Tarif", "Logeur", "Recharge", "Sport", "Capitaine", "Téléphone", "Email"];
			var did = [];
			var didName = [];

			var links = {};
			var fictive = {};
			var error = warning = false;
			var types = ["sportif", "fanfaron", "pompom", "cameraman"];
			var licences = {};
			var tarifs = {};
			var sports = {};
			var colTarif = 0;
			var colSport = 0;
			var asCode = [];
			var timer;
			var datas = [];


			$fictiveColumn =  function(value) {
				if (waitFor.length) {
					$('.cell-warning, .cell-error').removeClass('cell-error').removeClass('cell-warning').
						removeAttr('data-message').removeAttr('data-error');
					$('table#table-excel tbody tr').removeClass('row-danger').removeClass('row-danger-shown').
						removeClass('row-warning').removeClass('row-warning-shown');
					error = false;

					if (waitFor[0] == "cameraman") {
						var nbTypes = 0;

						$('table#table-excel tbody tr[data-num-row]').each(function() {
							nbTypes = 0;
							
							for (var i in types) {
								if (types[i] != "cameraman" && (
										types[i] in fictive && fictive[types[i]] == 1 ||
										types[i] in links &&
										$(this).find('td[data-num-col="' + links[types[i]] + '"] input').val() === 
											(types[i] == "sportif" ? 'O' : 
												(types[i] == "fanfaron" ? 'F' : 'P'))))
									nbTypes++;
							}
							
							if (nbTypes + (value ? 1 : 0) == 0) {
								error = true;
								$(this).addClass('row-danger');
							}
						});
					}

					else if (waitFor[0] == "logeur") {
						value = prompt('Comment sont logées ces personnes ?');
						if (value === null || value.length == 0)
							error = true;
					}

					$hideHint('error');
					$hideHint('danger');
					$hideHint('warning');

					if (!error) {
						$hideHint('global');
						fictive[waitFor[0]] = value + '';
						did.push(waitFor.shift());
						didName.push(waitName.shift());
						var content = '';
						
						for (var i in fictive) {
							var name;

							if ((i == "licence" || 
								i == "sport" ||
								i == "capitaine") &&
								"sportif" in fictive &&
								fictive["sportif"] == 0)
								continue;
							
							for (var j = did.length - 1; j >= 0; j--) {
								if (did[j] == i) {
									name = didName[j];
									break;
								}
							} 

							content += '<li><b onclick="if(confirm(\'Veux-tu vraiment recommencer depuis \\\'' + name + '\\\' ?\')) $restartFrom(\'' + i + '\'); event.cancelBubble=true;">';
							if (i == "sportif") content += fictive[i] == 1 ? 'Sportif' : 'Non sportif';
							else if (i == "fanfaron") content += fictive[i] == 1 ? 'Fanfaron' : 'Non fanfaron';
							else if (i == "pompom") content += fictive[i] == 1 ? 'Pompom / pimpim' : 'Non pompom / pimpim';
							else if (i == "cameraman") content += fictive[i] == 1 ? 'Cameraman / photographe' : 'Non cameraman / photographe';
							else if (i == "logement") content += 'Logement : <i>' + (fictive[i] == 1 ? 'Full package' : 'Light package') + '</i>';
							else if (i == "logeur") content += "Logeur : <i>" + (fictive[i].length > 20 ? fictive[i].substr(0, 20) + '...' : fictive[i]) + "</i>";
							else if (i == "recharge") content += "Recharge de  <i>" + fictive[i] + "€</i>";
							else if (i == "telephone") content += "Aucune téléphone";
							else if (i == "sport") content += "Sports renseignés plus tard";
							else if (i == "capitaine") content += "Capitaines renseignés plus tard";
							content += '</b></li>';
						}

						$addHint('Certaines données sont communes à tout le monde : <ul>' + content + '</ul>', 'global', 0);
						$askNextColumn();
					}

					else {
						if (waitFor[0] == "cameraman") $addHint('Il y a des participants sans statut (sportif, fanfaron, pompom, cameraman)', 'danger', 0);
						else if (waitFor[0] == "logeur") $addHint('Les informations de logement pour quelques non logés n\‘ont pas été renseignées.', 'error', 0);
					}
				}
			};


			$askNextColumn = function() {
				if (waitFor.length) {
					var noSportif = "sportif" in fictive && fictive["sportif"] == 0 ||
						"sportif" in links &&
						$.grep($('table#table-excel tbody tr td[data-num-col="' + links["sportif"] + '"] input'), function(n, i) {
							return $(n).val() === "O"; }).length == 0;

					var noCapitaine = "capitaine" in fictive && fictive["capitaine"] == 0 ||
						"capitaine" in links &&
						$.grep($('table#table-excel tbody tr td[data-num-col="' + links["capitaine"] + '"] input'), function(n, i) {
							return $(n).val() === "O"; }).length == 0;

					var near = null;
					var nearDist = 0;
					var label;
					var dist; 

					$('table#table-excel thead th:not(:first-of-type)').each(function() {
						label = $removeAccents($(this).text()).replace(/^\s+|\s+$/g, "").toLowerCase();
						var pattern = /#\d+|\?/;
						if (pattern.test(label))
							return true;

						for (var i in links) {
							if (links[i] == $(this).attr('data-num-col')) {
								return true;
							}
						}

						dist = $levenshtein(label, waitFor[0]);
						if (near === null || dist < nearDist) {
							near = $(this);
							nearDist = dist;
						}
					});

					var maybe = near !== null && nearDist < 5 ? 
						('Il est probable que ce soit la colonne "' + near.text() + '", ' + 
						'<a href="#" onclick="$clickOnColumn(' + near.attr('data-num-col') + ');event.cancelBubble=true;return false;">' + 
						'clique ici pour confirmer</a> !') : '';

					if (waitFor[0] == "logeur") {
						var tousLoges = !("logement" in fictive) || fictive['logement'] == 1;

						if (tousLoges && 
							"logement" in links &&
							!ecole_lyonnaise) {
							$('table#table-excel tbody tr td[data-num-col="' + links["logement"] + '"] input').each(function() {
								if ($(this).val() != 'N') {
									tousLoges = false;
									return false;
								}
							});
						}
					}

					else if (waitFor[0] == "capitaine") {
						var withoutSport = "sport" in fictive && fictive["sport"] == '' || !("sport" in links);

						if (!withoutSport) {
							withoutSport = true; 

							$('table#table-excel tbody tr td[data-num-col="' + links["sport"] + '"] input').each(function() {
								if ($(this).attr('data-id-sport') > 0) {
									withoutSport = false;
									return false;
								}
							});
						}
					}

					$hideHint('required');

					if ((waitFor[0] == "licence" || waitFor[0] == "capitaine" || waitFor[0] == "sport") && noSportif ||
						waitFor[0] == "logeur" && tousLoges ||
						waitFor[0] == "capitaine" && withoutSport) {
						fictive[waitFor[0]] = '';
						did.push(waitFor.shift());
						didName.push(waitName.shift());
						$askNextColumn();
					} 

					else if ($.inArray(waitFor[0], types) >= 0)
						setTimeout(function() {
							$addHint('Sélectionne la colonne correspondant à <b>' + waitName[0] + '</b>. ' + maybe + '<br /><br />'+
								'<a href="#" onclick="$fictiveColumn(1);event.cancelBubble=true;return false;">Tout le monde possède ce statut.</a><br />'+
								'<a href="#" onclick="$fictiveColumn(0);event.cancelBubble=true;return false;">Personne ne possède ce statut</a>', 'required'); }, 500);

					else if (waitFor[0] == "telephone" &&
						noCapitaine) 
						setTimeout(function() {
							$addHint('Sélectionne la colonne correspondant à <b>' + waitName[0] + '</b>.' + (maybe.length ? ' ' + maybe + '<br />' : '') + 
								'<br /><a href="#" onclick="$fictiveColumn(\'\');event.cancelBubble=true;return false;">' + 
								'Ne renseigner aucun téléphone</a> (ceci n\'est pas recommandé)', 'required'); }, 500);

					else if (waitFor[0] == "recharge")
						setTimeout(function() {
							$addHint('Sélectionne la colonne correspondant à <b>' + waitName[0] + '</b>.' + (maybe.length ? ' ' + maybe + '<br />' : '') + 
								'<br />Recharger tout le monde de ' + 
								'<a href="#" onclick="$fictiveColumn(\'0\');event.cancelBubble=true;return false;">0€</a>, ' + 
								'<a href="#" onclick="$fictiveColumn(\'5\');event.cancelBubble=true;return false;">5€</a>, ' + 
								'<a href="#" onclick="$fictiveColumn(\'10\');event.cancelBubble=true;return false;">10€</a>, ' + 
								'<a href="#" onclick="$fictiveColumn(\'20\');event.cancelBubble=true;return false;">20€</a>.', 'required'); }, 500);

					else if (waitFor[0] == "sport" || waitFor[0] == 'capitaine')
						setTimeout(function() {
							$addHint('Sélectionne la colonne correspondant à <b>' + waitName[0] + '</b>. ' + maybe + '<br /><br />'+
								'<a href="#" onclick="$fictiveColumn(\'\');event.cancelBubble=true;return false;">Renseigner les ' + waitName[0] + 's plus tard.</a>', 'required'); }, 500);

					else if (waitFor[0] == "logement")
						setTimeout(function() {
							$addHint('Sélectionne la colonne correspondant à <b>' + waitName[0] + '</b>. ' + maybe + '<br /><br />'+
								'<a href="#" onclick="$fictiveColumn(0);event.cancelBubble=true;return false;">Tous sont en <i>Light package</i> (non logés)</a><br />'+
								'<a href="#" onclick="$fictiveColumn(1);event.cancelBubble=true;return false;">Tous sont en <i>Full package</i> (logés)</a>', 'required'); }, 500); 

					else if (waitFor[0] == "logeur")
						setTimeout(function() {
							$addHint('Sélectionne la colonne correspondant à <b>' + waitName[0] + '</b>. ' + maybe /* + '<br /><br />'+
								'<a href="#" onclick="$fictiveColumn();event.cancelBubble=true;return false;">Mettre la même donnée pour les non logés</a>'*/, 'required'); }, 500); 

					else if (waitFor.length)
						setTimeout(function() {
							$addHint('Sélectionne la colonne correspondant à <b>' + waitName[0] + '</b>. ' + maybe, 'required'); }, 500);
				}

				else {
					var cols = [];
					var deplace = false;
					var numCol;
					for (var i in links)
						cols.push(""+links[i]);

					$('table#table-excel thead tr th').each(function() {
						$(this).find('div span').remove();
						$(this).unbind('click').removeAttr('onclick');
						numCol = $(this).attr('data-num-col');
						if ($.inArray(numCol, cols) < 0)
							$('table#table-excel tr [data-num-col="' + numCol + '"]').remove();
					});

					$('table#table-excel tbody tr').addClass('row-waiting').each(function() {
						if ("capitaine" in links &&
							$(this).find('td[data-num-col="' + links["capitaine"] + '"] input').val() == "O") {
							$(this).detach().prependTo('table#table-excel tbody');
							deplace = true;
						}

						$(this).children('th').click(function() {
							if ($(this).parent().attr('data-message')) {
								$hideHint('message');
								$addHint($(this).parent().attr('data-message'), 'message', 2000);
							}

							$sendNextRow(true, true, $(this).parent().attr('data-num-row'));
						}).find('div span').remove();
					});

					$hideHint('global');
					$('table#table-excel').scrollTop(0).scrollLeft(0);


					$addHint('Toutes les données ont été correctement saisies, bien joué !<br />' + 
						'Essayons à présent de rajouter ces données dans la base, il est possible que certaines erreurs se produisent. Ainsi choisis l\'un des processus suivant : <br /><br />' + 
						'<a href="#" onclick="$initSend(\'one\');event.cancelBubble=true;return false;">Envoyer les participants un à un</a><br />' +
						'<a href="#" onclick="$initSend(\'all_stop\');event.cancelBubble=true;return false;">Envoyer tous les participants jusqu\'à la première erreur</a><br />' + 
						'<a href="#" onclick="$initSend(\'all\');event.cancelBubble=true;return false;">Envoyer tous les participants sans s\'arreter</a>', 'required', 0);
				

					if (deplace) {
						$('table#table-excel tbody tr th').each(function(i, n) {
							$(this).html('<div>' + (i + 1) + '</div>');
						});

						$addHint('Les capitaines ont été placés en tête de tableau', 'info', 2000);
					}
				}
			};

			$initSend = function(method) {
				if (method == "one")
					$sendNextRow(true, true);
				
				else if (method == "all_stop")
					$sendNextRow(false, true);
				
				else if (method == "all")
					$sendNextRow(false, false);
			};

			$sendNextRow = function(showSuccess, showError, numRowForce) {
				var errorSend = true;
				var numRow;
				var datas = {};
				var errorMessage = '';
				var rowsWaiting = $('table#table-excel tbody tr.row-waiting');

				if (rowsWaiting.length == 0 ||
					numRowForce && $('table#table-excel tbody tr[data-num-row="' + numRowForce + '"].row-waiting').length == 0)
					return -1;

				$hideHint();

				numRow = numRowForce ? numRowForce : rowsWaiting.first().attr('data-num-row');
				
				for (var i in links)
					datas[i] = $('table#table-excel tbody tr[data-num-row="' + numRow + '"] td[data-num-col="' + links[i] + '"] input').val();
				
				for (var i in fictive)
					datas[i] = fictive[i];

				datas["tarif"] = "tarif" in links &&
					$('table#table-excel tbody tr[data-num-row="' + numRow + '"] td[data-num-col="' + links["tarif"] + '"] input').attr('data-id-tarif') ? 
					$('table#table-excel tbody tr[data-num-row="' + numRow + '"] td[data-num-col="' + links["tarif"] + '"] input').attr('data-id-tarif') : '';

				datas["sport"] = "sport" in links &&
					$('table#table-excel tbody tr[data-num-row="' + numRow + '"] td[data-num-col="' + links["sport"] + '"] input').attr('data-id-sport') ? 
					$('table#table-excel tbody tr[data-num-row="' + numRow + '"] td[data-num-col="' + links["sport"] + '"] input').attr('data-id-sport') : '';

				datas['load'] = 'send';
				datas['temp'] = true;
				$.ajax({
					url: "<?php url('ecoles/'.$id.'/import/ajax'); ?>",
				  	method: "POST",
				  	cache: false,
					dataType: "json",
					data:datas,
					success: function(data) {
						errorSend = data.error !== false;
						errorMessage = errorSend && data.message ? data.message : '';

						$('table#table-excel tbody tr[data-num-row="' + numRow + '"]').
							removeClass('row-waiting').addClass(errorSend ? 'row-sent-error' : 'row-sent-success');

						if (errorSend)
							$('table#table-excel tbody tr[data-num-row="' + numRow + '"]').attr('data-message', 
								errorMessage ? errorMessage : 'Une erreur inconnue s\'est produite');

						if (errorSend && showError || !errorSend && showSuccess) {
							$addHint(("sexe" in datas && datas["sexe"] == 'H' ? 'Le participant' : 'La participante') + 
								' <b>' + $escapeHtml(("nom" in datas ? datas["nom"] : '<i>???</i>') + ' ' + ("prenom" in datas ? datas["prenom"] : '<i>???</i>')) + '</b>' +
								(errorSend ? ' n\'a pas été ajouté' : ' a bien été ajouté') + ("sexe" in datas && datas["sexe"] == 'H' ? '' : 'e') + 
								(errorSend ? '...' + (errorMessage.length ? '<br /><i>' + errorMessage + '</i>' : '') : ' !') + 
								(rowsWaiting.length > 1 ? ('<br /><br />' + 
									'<a href="#" onclick="$initSend(\'one\');event.cancelBubble=true;return false;">Envoyer les données suivantes une à une</a><br />' + 
									'<a href="#" onclick="$initSend(\'all_stop\');event.cancelBubble=true;return false;">Envoyer les données suivantes jusqu\'à erreur</a><br />' + 
									'<a href="#" onclick="$initSend(\'all\');event.cancelBubble=true;return false;">Envoyer toutes les données suivantes sans s\'arreter</a>') : ''),
								errorSend ? 'error' : 'success', rowsWaiting.length > 1 ? 0 : 2000); 
						}

						if (rowsWaiting.length <= 1)
							$endSend();

						else if (!showSuccess && !errorSend ||
							!showError && errorSend)
							$sendNextRow(showSuccess, showError);
					},
					error: function() {
						$('table#table-excel tbody tr[data-num-row="' + numRow + '"]').
							removeClass('row-waiting').addClass('row-sent-error').
							attr('data-message', 'Une erreur inconnue s\'est produite');

						if (showError) 
							$addHint(("sexe" in datas && datas["sexe"] == 'H' ? 'Le participant' : 'La participante') + 
								' <b>' + $escapeHtml(("nom" in datas ? datas["nom"] : '<i>???</i>') + ' ' + ("prenom" in datas ? datas["prenom"] : '<i>???</i>')) + '</b>' +
								' n\'a pas été ajouté' + ("sexe" in datas && datas["sexe"] == 'H' ? '' : 'e') + '...<br />' + 
								'<i>Une erreur inconnue s\'est produite</i>' + 
								(rowsWaiting.length > 1 ? ('<br /><br />' + 
									'<a href="#" onclick="$initSend(\'one\');event.cancelBubble=true;return false;">Envoyer les données suivantes une à une</a><br />' + 
									'<a href="#" onclick="$initSend(\'all_stop\');event.cancelBubble=true;return false;">Envoyer les données suivantes jusqu\'à erreur</a><br />' + 
									'<a href="#" onclick="$initSend(\'all\');event.cancelBubble=true;return false;">Envoyer toutes les données suivantes sans s\'arreter</a>') : ''),
								'error', rowsWaiting.length > 1 ? 0 : 2000); 

						
						if (rowsWaiting.length <= 1)
							$endSend();

						else if (!showError && rowsWaiting.length > 1)
							$sendNextRow(showSuccess, showError);

					}
				});
			};

			$confirmSend = function(hasWarnings) {
				$.ajax({
					url: "<?php url('ecoles/'.$id.'/import/ajax'); ?>",
				  	method: "POST",
				  	cache: false,
					dataType: "json",
					data:{load:"confirm"},
					success: function(data) {
						$hideHint('attention');
						$hideHint('required');

						if (hasWarnings)
							$addHint('Il reste encore quelques données dont la validation a été forcée après un message d\'alerte.' + 
								'Tu pourras évidemment retrouver et modifier ces éléments sur l\'interface classique du Challenger. '+
								'Attention à ne pas en avoir trop, cela pourrait ralentir le travail de l\'équipe organisatrice du Challenge', 'attention', 0);

						$addHint('Toutes les données ont été envoyées et correctement enregistrées ! Bravo :) <br />' +
							'Il te suffit à présent de revenir à : <ul>' +
							'<li><a href="#" onclick="location.reload();event.cancelBubble=true;return false;">L\'outil d\’importation en masse</a></li>' +
							'<li><a href="#" onclick="window.location.href=\'' + 
								$(header).attr('href') + '\';event.cancelBubble=true;return false;">La gestion des participants</a></li></ul>', 'success', 0);
					}
				});
			};

			$endSend = function() {
				var hasErrors = $('table#table-excel tbody tr.row-sent-error').length > 0;
				var hasSuccess = $('table#table-excel tbody tr.row-sent-success').length > 0;
				var hasWarnings = $('table#table-excel tbody tr td.row-sent-success input.cell-force').length > 0;

				if (!hasErrors)
					$confirmSend(hasWarnings);

				if (hasErrors) {
					$addHint('Toutes les données ont été envoyées mais il semble qu\'il y ait eu quelques erreurs. ' +
						'Pour t\'aider à corriger ces dernières, tu peux télécharger <a href="#" onclick="$downloadErrors();event.cancelBubble=true;return false;">ce fichier CSV</a> ' +
						'qui regroupe l\'ensemble des données concernées et accompagnées d\'une colonne apportant des informations sur les erreurs rencontrées.', 'attention', 0);

					if (hasSuccess) 
						$addHint('Pour envoyer malgré tout les données valides, <b><a href="#" onclick="$confirmSend(' + (hasWarnings ? '1' : '') + ');' + 
							'event.cancelBubble=true;return false;">clique-ici</a></b> !<br />', 'required'); 
				}	
			};

			$downloadErrors = function() {
				$addHint('Fonctionnalité pas encore disponible', 'error', 2000);
			};

			$goToNext = function(type) {
				var ref = $('.row-' + type + '-shown').length == 0 ? $('.row-' + type).last() : $('.row-' + type + '-shown').first();
				if (ref.length == 0)
					return;

				var next = ref.nextAll('.row-' + type);
				next = next.length == 0 ? $('.row-' + type).first() : next.first();
				$('.row-' + type).removeClass('row-' + type + '-shown');
				next.addClass('row-' + type + '-shown');

				var cell = next.find('.cell-warning,.cell-error').first().parent();
				$('table#table-excel').scrollTop(next.position().top - $('table#table-excel').position().top + $('table#table-excel').scrollTop() - next.outerHeight() - ($('table#table-excel').height() - 2 * next.outerHeight()) * (next.attr('data-num-row') - 1) / ($('table#table-excel tbody tr').length + 1) );
				
				if (cell !== null) {
					var numCol = cell.attr('data-num-col');
					$('table#table-excel').scrollLeft(cell.position().left - $('table#table-excel').position().left + $('table#table-excel').scrollLeft() - cell.outerWidth() - ($('table#table-excel').width() - 2 * cell.outerWidth()) * (numCol - 1) / ($('table#table-excel thead tr:first th').length + 1) );
					cell.children('input').focus();
				}
			};

			$restartFrom = function(from) {
				if ($.inArray(from, waitFor) >= 0)
					return;

				var todo;
				var numCol;
				var header;
				$hideHint('error');
				$hideHint('danger');
				$hideHint('warning');
				$hideHint('global');

				for (var i = did.length - 1; i >= 0; i--) {
					todo = did.splice(i, 1)[0];
					waitFor.unshift(todo);
					waitName.unshift(didName.splice(i, 1)[0]); 

					if (todo in links) {
						numCol = links[todo];

						header = $('table#table-excel thead tr th[data-num-col="' + numCol + '"]');
						header.removeClass('linked').children('div').
							text(header.attr('data-initial') ? header.attr('data-initial') : header.html()).
							append('<span class="glyphicon glyphicon-remove" title="Supprimer cette colonne"></span>');

						$('table#table-excel tbody tr td[data-num-col="' + numCol + '"] input').
							removeClassPrefix('cell-').removeAttr('readonly').each(function() {
								$(this).val($(this).attr('data-initial'));
							});
					}

					delete fictive[todo];
					delete links[todo];

					if (todo == from)
						break;
				}

				$askNextColumn();
			};

			$loadAutocomplete = function(input) {
				if (waitFor.length) {
					var wait = waitFor[0];
					
					if (wait == "sexe") {
						$(input).autocomplete({
							source: function(request, response) {
								var data = $getSexe(request.term);
						        response(data == '?' ? ["Homme", "Femme"] : (data == 'h' ? ['Homme'] : ['Femme']));
						    },
					        focus: function(e, ui) {
		            			return false;  
					    	},
					    	select: function(e, ui) {
					    		$(this).trigger('change');
					    		return true;
					    	},
						    minLength : 0,
    						position: {  collision: "flip"  }
						}).bind('focus', function(event){
    						$(this).autocomplete("search");
    						$(this).unbind(event);
						});
						
					}

					else if (wait == "sportif") {
						$(input).autocomplete({
							source: function(request, response) {
								var data = $getSportif(request.term);
						        response(data == '?' || request.term == '' ? ["Oui", "Non"] : (data == '1' ? ['Oui'] : ['Non']));
						    },
					        focus: function(e, ui) {
		            			return false;  
					    	},
					    	select: function(e, ui) {
					    		$(this).trigger('change');
					    		return true;
					    	},
						    minLength : 0,
    						position: {  collision: "flip"  }
						}).bind('focus', function(event){
    						$(this).autocomplete("search");
    						$(this).unbind(event);
						});
					}

					else if (wait == "fanfaron") {
						$(input).autocomplete({
							source: function(request, response) {
								var data = $getFanfaron(request.term);
						        response(data == '?' || request.term == '' ? ["Oui", "Non"] : (data == '1' ? ['Oui'] : ['Non']));
						    },
					        focus: function(e, ui) {
		            			return false;  
					    	},
					    	select: function(e, ui) {
					    		$(this).trigger('change');
					    		return true;
					    	},
						    minLength : 0,
    						position: {  collision: "flip"  }
						}).bind('focus', function(event){
    						$(this).autocomplete("search");
    						$(this).unbind(event);
						});
					}

					else if (wait == "pompom") {
						$(input).autocomplete({
							source: function(request, response) {
								var data = $getPompom(request.term);
						        response(data == '?' || request.term == '' ? ["Oui", "Non"] : (data == '1' ? ['Oui'] : ['Non']));
						    },
					        focus: function(e, ui) {
		            			return false;  
					    	},
					    	select: function(e, ui) {
					    		$(this).trigger('change');
					    		return true;
					    	},
						    minLength : 0,
    						position: {  collision: "flip"  }
						}).bind('focus', function(event){
    						$(this).autocomplete("search");
    						$(this).unbind(event);
						});
					}

					else if (wait == "logement") {
						$(input).autocomplete({
							source: function(request, response) {
								var data = $getLogement(request.term);
						        response(data == '?' || request.term == ''  ? ["Oui", "Non"] : (data == '1' ? ['Oui'] : ['Non']));
						    },
					        focus: function(e, ui) {
		            			return false;  
					    	},
					    	select: function(e, ui) {
					    		$(this).trigger('change');
					    		return true;
					    	},
						    minLength : 0,
    						position: {  collision: "flip"  }
						}).bind('focus', function(event){
    						$(this).autocomplete("search");
    						$(this).unbind(event);
						}).data('uiAutocomplete')._renderItem = function( ul, item ) {
                			return $("<li class='ui-menu-item'></li>")
		                    	.append(item['value'] + '<br /><small>' + (item['value'] == 'Oui' ? 'Full package' : 'Light package') + '</small>')
		                    	.appendTo(ul);
		                };
					}

					else if (wait == "cameraman") {
						$(input).autocomplete({
							source: function(request, response) {
								var data = $getCameraman(request.term);
						        response(data == '?' || request.term == '' ? ["Oui", "Non"] : (data == '1' ? ['Oui'] : ['Non']));
						    },
					        focus: function(e, ui) {
		            			return false;  
					    	},
					    	select: function(e, ui) {
					    		$(this).trigger('change');
					    		return true;
					    	},
						    minLength : 0,
    						position: {  collision: "flip"  }
						}).bind('focus', function(event){
    						$(this).autocomplete("search");
    						$(this).unbind(event);
						});
					}

					else if (wait == "capitaine") {
						$(input).autocomplete({
							source: function(request, response) {
								var data = $getCapitaine(request.term);
						        response(data == '?' || request.term == ''  ? ["Oui", "Non"] : (data == '1' ? ['Oui'] : ['Non']));
						    },
					        focus: function(e, ui) {
		            			return false;  
					    	},
					    	select: function(e, ui) {
					    		$(this).trigger('change');
					    		return true;
					    	},
						    minLength : 0,
    						position: {  collision: "flip"  }
						}).bind('focus', function(event){
    						$(this).autocomplete("search");
    						$(this).unbind(event);
						});
					}

					else if (wait == "tarif") {
						$(input).autocomplete({
							source: function(request, response) {
						        var is = [];
						        var logement;
						        var filtresReturn = [];
						        var tarifName;
						        var val;
						        var correspond;

						        val = $removeAccents(request.term).replace(/^\s+|\s+$/g, "").toLowerCase();
						        val = val.replace(/[^a-z\d ]+/gi, "").toLowerCase().split(' ');

						        logement = "logement" in fictive && fictive["logement"] == 1 ||
						        	"logement" in links && 
						        	$('table#table-excel tbody tr td[data-num-col="' + links["logement"] + '"]' +
										'[data-num-row="' + this.element.parent().attr('data-num-row') + '"] input').val() == 'O';

								for (var i in types) {
									is[types[i]] = types[i] in fictive && fictive[types[i]] == 1 ||
										types[i] in links &&
										$('table#table-excel tbody tr td[data-num-col="' + links[types[i]] + '"]' +
											'[data-num-row="' + this.element.parent().attr('data-num-row') + '"] input').val() === 
											(types[i] == "sportif" ? 'O' : 
												(types[i] == "fanfaron" ? 'F' : 
													(types[i] == "cameraman" ? 'C' : 'P')));
								}

								tarifsFiltres = $filtreTarifs(logement, is['sportif'], is['fanfaron'], is['pompom'], is['cameraman']);
								
								
								for (var i in tarifsFiltres) {
									tarifName = $removeAccents(tarifsFiltres[i]['nom']).replace(/[^a-z\d ]+/gi, '');
									tarifName = tarifName.toLowerCase(); 
									correspond = true;

									for (var j in val) {
										if (tarifName.indexOf(val[j]) < 0) {
											correspond = false;
											break;
										}
									}

									if (correspond)
										filtresReturn.push(tarifsFiltres[i]['nom']);
								}

								item = [];
								item['value'] = '...';
								item['plus'] = 0;
								item['count'] = filtresReturn.length;
								item['total'] = tarifsFiltres.length;
								
								if (filtresReturn.length)
									filtresReturn.push(item);

								response(filtresReturn);
						    },
					        select: function(e, ui) {
					        	if (ui.item['value'] == '...' && ui.item['total'] !== undefined)
					        		return false;

								$(this).trigger('change');
								return true;
					        },
					        focus: function(e, ui) {
		            			return false;  
					    	},
						    minLength : 0,
    						position: {  collision: "flip"  }
						}).bind('focus', function(event){
    						$(this).autocomplete("search");
    						$(this).unbind(event);
						}).data('uiAutocomplete')._renderItem = function( ul, item ) {
							if (item['value'] == '...' && item['total'] !== undefined) 
            					return $('<li class="ui-state-disabled">' + (item['plus'] ? ('... et ' + item['plus'] + ' autre'+(item['plus'] > 1 ? 's' : '')+'<br />') : '') +
            						'(' + item['count'] + ' sur '+ item['total'] +')</li>').appendTo(ul);

                			return $("<li class='ui-menu-item'></li>")
		                    	.append(item['value'])
		                    	.appendTo(ul);
		                };
					}

					else if (wait == "sport") {
						$(input).autocomplete({
							source: function(request, response) {
						        var is = [];
						        var filtresReturn = [];
						        var sportName;
						        var val;

						        val = $removeAccents(request.term).replace(/^\s+|\s+$/g, "").toLowerCase();
						        val = val.replace(/[^a-z\d ]/g, '');
						        val = val.replace(/ (f|g|f\/?g)$/, '');
						        val = val.replace(/(foot|basket|hand|volley)-?ball/i, "$1");

						        sportsFiltres = $filtreSports("sexe" in links &&
										$('table#table-excel tbody tr td[data-num-col="' + links["sexe"] + '"]' +
											'[data-num-row="' + this.element.parent().attr('data-num-row') + '"] input').val() == "F" ? 'f' : 'h',
										"tarif" in links ? $('table#table-excel tbody tr td[data-num-col="' + links["tarif"] + '"]' +
											'[data-num-row="' + this.element.parent().attr('data-num-row') + '"] input').attr('data-id-sport-special') : '');
						        
								for (var i in sportsFiltres) {
									sportName = $removeAccents(sportsFiltres[i]['sport']).replace(/[^a-z\d ]+/gi, ''); 
									sportName = sportName.toLowerCase();

									if (sportName.indexOf(val) >= 0)
										filtresReturn.push(sportsFiltres[i]['sport'] + ' (' + 
											(sportsFiltres[i]['sexe'] == 'm' ? 'F/G' : 
												(sportsFiltres[i]['sexe'] == 'h' ? 'H' : 'F')) + ')');
								}

								item = [];
								item['value'] = '...';
								item['plus'] = 0;
								item['count'] = filtresReturn.length;
								item['total'] = sportsFiltres.length;
								
								if (filtresReturn.length)
									filtresReturn.push(item);

								response(filtresReturn);
						    },
					        select: function(e, ui) {
					        	if (ui.item['value'] == '...' && ui.item['total'] !== undefined)
					        		return false;

								$(this).trigger('change');
								return true;
					        },
					        focus: function(e, ui) {
		            			return false;  
					    	},
						    minLength : 0,
    						position: {  collision: "flip"  }
						}).bind('focus', function(event){
    						$(this).autocomplete("search");
    						$(this).unbind(event);
						}).data('uiAutocomplete')._renderItem = function( ul, item ) {
							if (item['value'] == '...' && item['total'] !== undefined)
            					return $('<li class="ui-state-disabled">' + (item['plus'] ? ('... et ' + item['plus'] + ' autre'+(item['plus'] > 1 ? 's' : '')+'<br />') : '') +
            						'(' + item['count'] + ' sur '+ item['total'] +')</li>').appendTo(ul);

                			return $("<li class='ui-menu-item'></li>")
		                    	.append(item['value'])
		                    	.appendTo(ul);
		                };
					}

					else if (wait == "licence") {
						$(input).autocomplete({
							source: function(request, response) {
						        var is = [];
						        var filtresReturn = [];
						        var val;
						        var valnp;
						        var item;
						        var count = 0;
						        var countPrint = 0;
						        var nom = null;
						        var prenom = null;
						        var prenom_nom_l;
						        var nom_prenom_l;
						        var dist = 0;
						        var best = false;

						        if ("prenom" in links &&
						        	"nom" in links) {
									prenom = $removeAccents($(input).parent().parent().find('td[data-num-col="' + links["prenom"] + '"] input').val()).replace(/[- ]/g, '').toLowerCase();
									nom = $removeAccents($(input).parent().parent().find('td[data-num-col="' + links["nom"] + '"] input').val()).replace(/[- ]/g, '').toLowerCase();
								}

						        val = (request.term).replace(/[^a-z\d]/gi, '').toUpperCase();
					        	valnp = $removeAccents(request.term).replace(/[- ]/g, '').toLowerCase();

								for (var i in licences) {
									item = licences[i];
									item['lui'] = false;
									item['licence'] = item['licence'].replace(/ /g, '');
									item['licence'] = item['licence'].substring(0, 4) + ' ' + item['licence'].substring(4);
									nom_prenom_l = $removeAccents(item['nom']+' '+item['prenom']).replace(/[- ]/g, '').toLowerCase();
									prenom_nom_l = $removeAccents(item['prenom']+' '+item['nom']).replace(/[- ]/g, '').toLowerCase();

									if (item['licence'].replace(/ /g, '').indexOf(val) >= 0 ||
										nom_prenom_l.indexOf(valnp) >= 0 ||
										prenom_nom_l.indexOf(valnp) >= 0) {
										count++;

										if (prenom !== null &&
											nom !== null &&
											(dist = $levenshtein(prenom, item['prenom'].replace(/[- ]/g, '').toLowerCase()) + 
											$levenshtein(nom, item['nom'].replace(/[- ]/g, '').toLowerCase())) < (best === false ? 5 : best)) {
											
											if (best !== false)
												filtresReturn[0]['lui'] = false;
											
											best = dist;
											item['lui'] = true;
											filtresReturn.unshift(item);

											if (countPrint >= 20)
												filtresReturn.pop();

											else
												countPrint++;
										}

										else if (countPrint < 20) {
											countPrint++;
											filtresReturn.push(item);
										}
									}
								}

								if (valnp.length == 0 ||
						        	("certificatmedical").indexOf(valnp) >= 0 ||
						        	("cm").indexOf(valnp) >= 0) {
									filtresReturn.push({cm:true});
						        }

								item = [];
								item['value'] = '...';
								item['plus'] = count - countPrint;
								item['count'] = count;
								item['total'] = Object.keys(licences).length;
								

								if (count)
									filtresReturn.push(item);


								response(filtresReturn);
						    },
					        select: function(e, ui) {
					        	if (ui.item['value'] == '...' && item['total'] !== undefined)
					        		return false;

								$(this).val(ui.item['cm'] ? 'Certificat médical ou questionnaire' : ui.item['licence']).trigger('change');
								return false;
					        },
					        focus: function(e, ui) {
		            			return false;  
					    	},
						    minLength : 0,
    						position: {  collision: "flip"  }
						}).bind('focus', function(event){
    						$(this).autocomplete("search");
    						$(this).unbind(event);
						}).data('uiAutocomplete')._renderItem = function( ul, item ) {
							if (item['value'] == '...' && item['total'] !== undefined)
            					return $('<li class="ui-state-disabled">' + (item['plus'] ? ('... et ' + item['plus'] + ' autre'+(item['plus'] > 1 ? 's' : '')+'<br />') : '') +
            						'(' + item['count'] + ' sur '+ item['total'] +')</li>').appendTo(ul);

            				if (item['cm'])
	        					return $("<li class='ui-menu-item cm'></li>")
			                    	.append('Certificat médical ou questionnaire')
			                    	.appendTo(ul);

                			return $("<li class='ui-menu-item'></li>")
		                    	.append($escapeHtml(item['licence']) + '<br /><small' + (item['lui'] === true ? ' class="lui"' : '') + '>' + $escapeHtml(item['prenom'] + ' ' + item['nom']) + '</small>')
		                    	.appendTo(ul);
		                };
					}
				}
			};


			$clickOnColumn = function(numCol, force) { 
				error = false;
				warning = false;
				var sportsFiltres;
				var tarifsFiltres;

				if (waitFor.length) {
					var wait = waitFor[0];

					if ($('table#table-excel thead tr:first th[data-num-col="' + numCol + '"]').length == 0) {
						error = true; 
						$hideHint('error');
						$hideHint('message');
						$hideHint('danger');
						$addHint('Cette colonne n\'existe plus', 'error', 3000);
					}

					$('.cell-error, .cell-warning').
						removeClass('cell-error').removeClass('cell-warning').
						removeAttr('data-message').removeAttr('data-error');
					$('table#table-excel tbody tr').removeClass('row-danger').removeClass('row-danger-shown').
						removeClass('row-warning').removeClass('row-warning-shown');

					$('table#table-excel tbody tr td input').each(function() {
						if ($(this).data('uiAutocomplete'))
							$(this).autocomplete("destroy");
					});
					

					for (var i in links) {
						if (links[i] == numCol) {
							error = true; 
							$hideHint('error');
							$hideHint('message');
							$hideHint('danger');
							$addHint('Cette colonne est déjà affiliée à une donnée<br />' +
								'<a href="#" onclick="$restartFrom(\'' + i + '\');event.cancelBubble=true;return false;">Recommencer à partir de cette donnée</a>', 'error', 0);
							break;
						}
					}						

					if (!error) {
						if (wait == "email") {
							var emails = [];
							var doublons = [];
							var domains = {};
							var maxEmailsInDomain = 0;
							
							$('table#table-excel tbody tr td[data-num-col="' + numCol + '"] input').each(function() {
								var val = $(this).val().replace(/^\s+|\s+$/g, "").toLowerCase();
								var domain = val.split('@');

								if (domain[1] !== null)
									domains[domain[1]] = 1 + (domains[domain[1]] > 0 ? domains[domain[1]] : 0);

								if ($.inArray(val, emails) >= 0 &&
									$.inArray(val, doublons) < 0)
									doublons.push(val);

								emails.push(val);
							});

							for (var i in domains) {
								if (domains[i] > maxEmailsInDomain)
									maxEmailsInDomain = domains[i];
							}
						}

						else if (wait == "prenom" &&
							"nom" in links) {
							var noms_prenoms = [];
							var doublons_np = [];
							
							$('table#table-excel tbody tr td[data-num-col="' + numCol + '"] input').each(function() {
								var val = $(this).val().replace(/^\s+|\s+$/g, "");
								var nom = $('table#table-excel tbody tr td[data-num-col="' + links["nom"] + '"]' +
										'[data-num-row="' + $(this).parent().attr('data-num-row') + '"] input').val().replace(/^\s+|\s+$/g, "");
								var nom_prenom = $removeAccents(nom + ' ' + val).toLowerCase();
								
								if ($.inArray(nom_prenom, noms_prenoms) >= 0 &&
									$.inArray(nom_prenom, doublons_np) < 0)
									doublons_np.push(nom_prenom);

								noms_prenoms.push(nom_prenom);
							});
						}

						else if (wait == "licence") {
							var licences_ = [];
							var doublons_l = [];
							
							$('table#table-excel tbody tr td[data-num-col="' + numCol + '"] input').each(function() {
								var val = $(this).val().replace(/\s+/g, '').toUpperCase();

								if ($isValidLicence(val) &&
									$.inArray(val, licences_) >= 0 &&
									$.inArray(val, doublons_l) < 0)
									doublons_l.push(val);

								licences_.push(val);
							});
						}

						else if (wait == "telephone") {
							var telephones = [];
							var doublons_t = [];
							
							$('table#table-excel tbody tr td[data-num-col="' + numCol + '"] input').each(function() {
								var val = $(this).val();
								val = val.replace(/(\d)[ .\/-](\d)/g, '$1$2');
								val = val.replace(/^00/, '+');
								val = val.replace(/^\+33\(?0?\)?/, '0');
								val = val.length == 9 && val.match(/^[1-9]/) ? '0' + val : val;
								val = val.length == 10 && val.match(/^0\d{9}$/) ? val.replace(/^(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})$/, '$1 $2 $3 $4 $5') : val;
								val = val.replace(/^[ .\/-](\d+)/, '$1');

								if ($.inArray(val, telephones) >= 0 &&
									$.inArray(val, doublons_t) < 0)
									doublons_t.push(val);

								telephones.push(val);
							});
						}


						$('table#table-excel tbody tr td[data-num-col="' + numCol + '"] input').each(function() {
							var val = $(this).val().replace(/^\s+|\s+$/g, "");

							if (wait == "licence" ||
								wait == "sport" ||
								wait == "capitaine") {
								var sportif = true; 
								
								if ("sportif" in fictive && fictive["sportif"] == 0 ||
									"sportif" in links &&
									$('table#table-excel tbody tr td[data-num-col="' + links["sportif"] + '"]' +
										'[data-num-row="' + $(this).parent().attr('data-num-row') + '"] input').val() === "N")
									sportif = false;


								if (!sportif)
									$(this).attr('data-nonsportif', true);

								else if (wait == "capitaine" &&
									"sport" in links) {
									var id_sport = $(this).parent().parent().find('td[data-num-col="' + links["sport"] + '"] input').attr('data-id-sport');
									if (id_sport !== null)
										$(this).attr('data-id-sport', id_sport);
								}
							}

							if (wait == "cameraman") {
								var nbTypes = 0;
								
								for (var i in types) {
									if (types[i] != "cameraman" && (
											types[i] in fictive && fictive[types[i]] == 1 ||
											types[i] in links &&
											$('table#table-excel tbody tr td[data-num-col="' + links[types[i]] + '"]' +
												'[data-num-row="' + $(this).parent().attr('data-num-row') + '"] input').val() === 
												(types[i] == "sportif" ? 'O' : 
													(types[i] == "fanfaron" ? 'F' : 'P'))))
										nbTypes++;
								}
							}

							else if (sportif &&
								wait == "sport") {
								var errorSport = false;
								var sportSelected;
								var compare = $removeAccents(val).toLowerCase();
								compare = compare.replace(/(foot|basket|hand|volley)-?ball/i, "$1");
								compare = compare.replace(/ \((f|g|f\/g)\)/i, '');
								
								sportsFiltres = $filtreSports("sexe" in links &&
									$('table#table-excel tbody tr td[data-num-col="' + links["sexe"] + '"]' +
										'[data-num-row="' + $(this).parent().attr('data-num-row') + '"] input').val() == "F" ? 'f' : 'h',
									"tarif" in links ? $('table#table-excel tbody tr td[data-num-col="' + links["tarif"] + '"]' +
										'[data-num-row="' + $(this).parent().attr('data-num-row') + '"] input').attr('data-id-sport-special') : '');

								if (sportsFiltres.length == 0) {
									errorSport = true;
									$(this).attr('data-message', 'Aucun sport n\'est disponible');
								}

								else if (compare.length > 0) {
									var near = null; 
									var nearDist = 0;
									var nom; 
										
									for (var i in sportsFiltres) {
										nom = $removeAccents(sportsFiltres[i]['sport']).replace(/^\s+|\s+$/g, "");
										nom = nom.replace(/(foot|basket|hand|volley)-?ball/i, "$1").toLowerCase();
										var dist = compare == nom ? 0 : $levenshtein(nom, compare);

										if (near === null && dist < 5 || 
											nearDist > dist) {
											near = sportsFiltres[i];
											nearDist = dist;
										}
									}

									errorSport = (near === null);

									if (!errorSport)
										sportSelected = near;

									else
										$(this).attr('data-message', 'Aucun sport ne correspond à cette donnée');
								}

								if (!errorSport && sportSelected)
									$(this).attr('data-id-sport', sportSelected['id_ecole_sport']);
							}

							else if (wait == "tarif") {
								var is = [];
								var errorTarif = false;
								var logement;
								var tarifSelected;

								logement = "logement" in fictive && fictive["logement"] == 1 ||
						        	"logement" in links && 
									$('table#table-excel tbody tr[data-num-row="' + $(this).parent().attr('data-num-row') + '"] ' +
										'td[data-num-col="' + links["logement"] + '"] input').val() == "O";

								for (var i in types) {
									is[types[i]] = types[i] in fictive && fictive[types[i]] == 1 ||
										types[i] in links &&
										$('table#table-excel tbody tr[data-num-row="' + $(this).parent().attr('data-num-row') + '"] ' +
											'td[data-num-col="' + links[types[i]] + '"] input').val() === 
											(types[i] == "sportif" ? 'O' : 
												(types[i] == "fanfaron" ? 'F' : 
													(types[i] == "cameraman" ? 'C' : 'P')));
								}

								tarifsFiltres = $filtreTarifs(logement, is['sportif'], is['fanfaron'], is['pompom'], is['cameraman']);

								if (tarifsFiltres.length <= 1) {
									errorTarif = tarifsFiltres.length == 0;
									
									if (!errorTarif)
										tarifSelected = tarifsFiltres[0];

									else 
										$(this).attr('data-message', 'Aucun tarif n\'est disponible');
								}

								else {
									var near = null; 
									var nearDist = 0;
									var compare = $removeAccents(val).toLowerCase();
									var words = compare.split(' ');
									var nom; 

									for (var i in tarifsFiltres) {
										nom = $removeAccents(tarifsFiltres[i]['nom']).replace(/^\s+|\s+$/g, "").toLowerCase();
										var dist = compare == nom ? 0 : $levenshtein(nom, compare);

										if (near === null || nearDist > dist) {
											near = [tarifsFiltres[i]];
											nearDist = dist;
										}

										else if (nearDist == dist && near !== null)
											near.push = tarifsFiltres[i];
									}

									errorTarif = near === null || near.length != 1 || nearDist >= 5;

									if (!errorTarif)
										tarifSelected = near[0];

									else
										$(this).attr('data-message', near !== null ? 'Pas assez de terme pour filtrer les tarifs' : 'Aucun tarif ne correspond au filtre');
								}

								if (!errorTarif && tarifSelected) {
									$(this).attr('data-id-tarif', tarifSelected['id_tarif_ecole']);
									$(this).attr('data-id-sport-special', tarifSelected['id_sport_special']);
								}
							}


							else if (wait == "logeur") {
								var logement;
								var needLogeur;

								logement = $getLogement($('table#table-excel tbody tr td[data-num-col="' + links["logement"] + '"]' +
										'[data-num-row="' + $(this).parent().attr('data-num-row') + '"] input').val());
								needLogeur = !ecole_lyonnaise && logement == '0';
							}

							else if (wait == "prenom") {
								var nom_prenom = $removeAccents(("nom" in links ? 
									$('table#table-excel tbody tr[data-num-row="' + $(this).parent().attr('data-num-row') + '"] ' +
									'td[data-num-col="' + links["nom"] + '"] input').val() : '') + ' ' + val).replace(/^\s+|\s+$/g, "").toLowerCase();
							}

							if (wait == "sexe" && !$isValidSexe(val) ||
								(wait == "nom" || wait == "prenom") && val.length == 0 ||
								wait == "prenom" && $.inArray(nom_prenom, doublons_np) >= 0 ||
								wait == "email" && (!$isValidEmail(val) || $.inArray(val.toLowerCase(), doublons) >= 0) ||
								wait == "sportif" && !$isValidSportif(val) ||
								wait == "licence" && (sportif && val.length == 0 || $isValidLicence(val) && $.inArray(val.replace(/\s+/g, '').toUpperCase(), doublons_l) >= 0) ||
								wait == "fanfaron" && !$isValidFanfaron(val) ||
								wait == "pompom" && !$isValidPompom(val) ||
								wait == "cameraman" && (!$isValidCameraman(val) || nbTypes + ($getCameraman(val) == '1' ? 1 : 0) == 0) ||
								wait == "logement" && !$isValidLogement(val) ||
								wait == "recharge" && !$isValidRecharge(val) ||
								wait == "tarif" && errorTarif ||
								wait == "sport" && sportif && val.length > 0 && errorSport ||
								wait == "logeur" && needLogeur && val.length == 0 ||
								wait == "capitaine" && sportif && !$isValidCapitaine(val) ||
								wait == "telephone" && val.length == 0 && "capitaine" in links && 
									$(this).parent().parent().find('td[data-num-col="' + links["capitaine"] + '"] input').val() == "O") {
								
								$(this).removeClass('cell-warning').addClass('cell-error').attr('data-error', wait).parent().parent().addClass('row-danger').addClass('row-warning');
								
								if (wait == "email" && $isValidEmail(val)) $(this).attr('data-message', 'Une même adresse ne peut pas être utilisée plusieurs fois');
								else if (wait == "licence" && $isValidLicence(val)) $(this).attr('data-message', 'Une même licence ne peut pas être utilisée plusieurs fois');
								else if (wait == "prenom" && val.length > 0) $(this).attr('data-message', 'Plusieurs personnes ne peuvent avoir les mêmes nom et prénom');
								else if (wait == "cameraman" && $isValidCameraman(val)) $(this).attr('data-message', 'Un participant doit avoir au moins un statut');
								
								error = true;
							}

							else if (wait == "email") {
								var domain = val.split('@');

								if (domain[1] !== null &&
									maxEmailsInDomain / domains[domain[1]] > 5) {
									$(this).addClass('cell-warning').attr('data-error', wait).attr('data-message', 'L\'email ne semble pas correspondre au modèle des autres').parent().parent().addClass('row-warning');
									warning = true;
								}

								else if (domain[0] !== null &&
									domain[1] !== null &&
									maxEmailsInDomain == domains[domain[1]] &&
									maxEmailsInDomain > 10 &&
									"nom" in links &&
									"prenom" in links) {
									domain[0] = domain[0].replace(/[.-_]/g, '').toLowerCase();
									var nom = $('table#table-excel tbody tr td[data-num-col="' + links['nom'] + '"]' +
										'[data-num-row="' + $(this).parent().attr('data-num-row') + '"] input').val().toLowerCase();
									var prenom = $('table#table-excel tbody tr td[data-num-col="' + links['prenom'] + '"]' +
										'[data-num-row="' + $(this).parent().attr('data-num-row') + '"] input').val().toLowerCase();
									nom = $removeAccents(nom).replace(/[- ]/g, '');
									prenom = $removeAccents(prenom).replace(/[- ]/g, '');

									if ($levenshtein(domain[0], nom + prenom) > 5 &&
										$levenshtein(domain[0], prenom + nom) > 5) {
										$(this).addClass('cell-warning').attr('data-error', wait).attr('data-message', 'L\'email ne semble pas correspondre au modèle des autres').parent().parent().addClass('row-warning');
										warning = true;
									}
								}
							}

							else if (wait == "telephone" && 
								val.length > 0 &&
								!$isValidPhone(val)) {
								$(this).addClass('cell-warning').attr('data-error', wait).parent().parent().addClass('row-warning');
								warning = true;
							}

							else if (wait == "telephone" &&
								val.length > 0 &&
								$isValidPhone(val)) {
								var telephone = $(this).val();
								telephone = telephone.replace(/(\d)[ .\/-](\d)/g, '$1$2');
								telephone = telephone.replace(/^00/, '+');
								telephone = telephone.replace(/^\+33\(?0?\)?/, '0');
								telephone = telephone.length == 9 && telephone.match(/^[1-9]/) ? '0' + telephone : telephone;
								telephone = telephone.length == 10 && telephone.match(/^0\d{9}$/) ? telephone.replace(/^(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})$/, '$1 $2 $3 $4 $5') : telephone;
								telephone = telephone.replace(/^[ .\/-](\d+)/, '$1');

								if ($.inArray(telephone, doublons_t) >= 0) {
									$(this).addClass('cell-warning').attr('data-error', wait).attr('data-message', 'Ce téléphone semble être un doublon').parent().parent().addClass('row-warning');
									warning = true;
								}
							}

							else if (wait == "licence") { 
								if (val.length > 0 &&
									//sportif && 
									!$isValidLicence(val)) {
									if (!$isCMLicence(val)) {
										$(this).addClass('cell-warning').attr('data-error', wait).parent().parent().addClass('row-warning');
										warning = true;
									}
								}

								else if (val.length > 0 &&
									//sportif &&
									asCode.length > 0 && 
									$.inArray(val.substr(0, 4).toUpperCase(), asCode) < 0) {
									$(this).addClass('cell-warning').attr('data-message', 'La licence, bien que correcte, n\'est pas associée à cette école').
										attr('data-error', wait).parent().parent().addClass('row-warning');
									warning = true;
								}

								else if (Object.keys(licences).length > 0 &&
									//sportif && 
									val.length > 0 &&
									$isValidLicence(val) &&
									val.replace(/\s+/g, '').toUpperCase() in licences) {
									var lic = licences[val.replace(/\s+/g, '').toUpperCase()];
									var nom = $('table#table-excel tbody tr td[data-num-col="' + links['nom'] + '"]' +
										'[data-num-row="' + $(this).parent().attr('data-num-row') + '"] input').val().toLowerCase();
									var prenom = $('table#table-excel tbody tr td[data-num-col="' + links['prenom'] + '"]' +
										'[data-num-row="' + $(this).parent().attr('data-num-row') + '"] input').val().toLowerCase();
									nom = $removeAccents(nom).replace(/[- ]/g, '');
									prenom = $removeAccents(prenom).replace(/[- ]/g, '');
									var nom_l  = $removeAccents(lic['nom'].replace(/[- ]/g, '')).toLowerCase();
									var prenom_l = $removeAccents(lic['prenom'].replace(/[- ]/g, '')).toLowerCase();

									if ($levenshtein(prenom, prenom_l) + $levenshtein(nom, nom_l) > 5) {
										$(this).addClass('cell-warning').attr('data-error', wait).attr('data-message', 'La licence appartient à <b>' +
											$ucname(lic['prenom']) + ' ' + $ucname(lic['nom']) + '</b>').parent().parent().addClass('row-warning');
										warning = true;
									}
								}

								else if (Object.keys(licences).length &&
									//sportif && 
									val.length > 0 &&
									$isValidLicence(val) &&
									!(val.replace(/\s+/g, '').toUpperCase() in licences)) {
									$(this).addClass('cell-warning').attr('data-message', 'La licence, bien que correcte, n\'est pas dans les bases de la FFSU').
										attr('data-error', wait).parent().parent().addClass('row-warning');
									warning = true;
								}
							}
						});
	
						//Messages d'Attention
						if (warning && (force !== true || error)) {
							$hideHint('error');
							$hideHint('danger');
							$hideHint('message');
							$hideHint('warning');
							if (wait == "email") $addHint('Certains emails, bien que corrects, semblent ne pas correspondre au modèle des autres, corrige-les ou force ce choix.', 'warning', 0, numCol);
							else if (wait == "telephone") $addHint('Certains téléphones ne semblent pas être valides ou sont des doublons, corrige-les ou force ce choix.', 'warning', 0, numCol);
							else if (wait == "licence") $addHint('Certaines licences semblent invalides (format incorrect, mauvaise correspondance, suffixe d\'une autre école). Corrige-les ou force ce choix.', 'warning', 0, numCol);
						}

						else {
							warning = false;
							$('table#table-excel tbody tr td[data-num-col="' + numCol + '"] input.cell-warning').
								removeClass('cell-warning').
								addClass('cell-force'); //On conserve data-message et data-error
							$('table#table-excel tbody tr').removeClass('row-warning').removeClass('row-warning-shown');
						}


						//Message d'Erreur
						if (error) {
							$hideHint('danger');
							$hideHint('message');
							$hideHint('error');
							$('table#table-excel tbody tr td[data-num-col="' + numCol + ']"] input.cell-force').
								removeClass('cell-force').removeAttr('data-message').removeAttr('data-error');

							if (wait == "nom") $addHint('Au moins un nom n\'est pas renseigné', 'danger', 0, numCol, true);
							if (wait == "prenom") $addHint('Les prénoms sont obligatoires et le couple (nom, prénom) doit être unique', 'danger', 0, numCol);
							else if (wait == "sexe") $addHint('Quelques valeurs pour le sexe ne sont pas correctes', 'danger', 0, numCol);
							else if (wait == "email") $addHint('Les emails doivent êtres renseignés, être valides et être uniques entre eux', 'danger', 0, numCol);
							else if (wait == "sportif") $addHint('Quelques données ne permettent pas de savoir le caractère sportif ou non des participants', 'danger', 0, numCol);
							else if (wait == "licence") $addHint('Tous les sportifs doivent avoir une licence; et si celle-ci est valide elle doit être unique', 'danger', 0, numCol);
							else if (wait == "fanfaron") $addHint('Quelques valeurs pour le statut de fanfaron ne sont pas correctes', 'danger', 0, numCol);
							else if (wait == "pompom") $addHint('Quelques valeurs pour le statut de pompom ne sont pas correctes', 'danger', 0, numCol);
							else if (wait == "cameraman") $addHint('Quelques valeurs pour le statut de cameraman ne sont pas correctes, ou alors il y a des participants sans statut (sportif, fanfaron, pompom, cameraman)', 'danger', 0, numCol);
							else if (wait == "logement") $addHint('Quelques valeurs pour le logement/package ne sont pas correctes', 'danger', 0, numCol);
							else if (wait == "recharge") $addHint('Quelques valeurs pour la recharge ne sont pas correctes (ce doit être des nombres positifs)', 'danger', 0, numCol);
							else if (wait == "tarif") $addHint('Le tarif de quelques participants n\'a pas été déterminé', 'danger', 0, numCol);
							else if (wait == "logeur") $addHint('Les informations de logement pour quelques non logés n\‘ont pas été renseignées.', 'danger', 0, numCol);
							else if (wait == "capitaine") $addHint('Il est impossible pour quelques participants de savoir s\'ils sont ou non capitaine.', 'danger', 0, numCol);
							else if (wait == "telephone") $addHint('Les téléphones de quelques capitaines ne sont pas renseignés.', 'danger', 0, numCol);
							else if (wait == "sport") $addHint('Le sport de quelques participants n\'a pas été déterminé', 'danger', 0, numCol);
						}

						if (!error && !warning) {
							$hideHint('error');
							$hideHint('danger');
							$hideHint('warning');
							$hideHint('success');
							$hideHint('message');

							$addHint('La colonne correspondant à <b>' + waitName[0] + '</b> a bien été sélectionnée', 'success', 2000);
							
							var header = $('table#table-excel thead tr th[data-num-col="' + numCol + '"]');
							header.attr('data-initial', header.text());
							header.addClass('linked').html('<div>' + waitName[0] + '</div>');

							$('table#table-excel thead tr th div').css({'top':$('#table-excel').scrollTop()});
							$('table#table-excel tbody tr td[data-num-col="' + numCol + '"] input').attr('readonly', true);
							
							$('table#table-excel tbody tr td[data-num-col="' + numCol + '"] input').each(function() {
								$(this).attr('data-initial', $(this).val());

								if (wait == "nom" || wait == "prenom") {
									var val = $(this).val().replace(/^\s+|\s+$/g, "");
									$(this).val($ucname(val));
								}

								else if (wait == "sexe") {
									var val = $getSexe($(this).val().replace(/^\s+|\s+$/g, ""));
									$(this).val(val == 'h' ? 'H' : 'F').addClass(val == 'h' ? 'cell-sexe-h' : 'cell-sexe-f');
								} 

								else if (wait == "sportif") {
									var val = $getSportif($(this).val().replace(/^\s+|\s+$/g, ""));
									$(this).val(val == '1' ? 'O' : 'N').addClass(val == '1' ? 'cell-sportif-1' : 'cell-sportif-0');
								} 

								else if (wait == "fanfaron") {
									var val = $getFanfaron($(this).val().replace(/^\s+|\s+$/g, ""));
									$(this).val(val == '1' ? 'F' : '').addClass(val == '1' ? 'cell-fanfaron' : '');
								} 

								else if (wait == "pompom") {
									var val = $getPompom($(this).val().replace(/^\s+|\s+$/g, ""));
									$(this).val(val == '1' ? 'P' : '').addClass(val == '1' ? 'cell-pompom' : '');
								} 

								else if (wait == "cameraman") {
									var val = $getCameraman($(this).val().replace(/^\s+|\s+$/g, ""));
									$(this).val(val == '1' ? 'C' : '').addClass(val == '1' ? 'cell-cameraman' : '');
								}

								else if (wait == "logement") {
									var val = $getLogement($(this).val().replace(/^\s+|\s+$/g, ""));
									$(this).val(val == '1' ? 'O' : 'N').addClass(val == '1' ? 'cell-fullpackage' : 'cell-lightpackage');
								}

								else if (wait == "recharge") {
									var val = $(this).val().replace(/^\s+|\s*€\s+$/g, "");
									$(this).val(val.length == 0 ? '' : parseFloat(val) + ' €');
								}

								else if (wait == "tarif") {
									if ($(this).attr('data-id-tarif') != null) {
										for (var i in tarifs) {
											if (tarifs[i]['id_tarif_ecole'] == $(this).attr('data-id-tarif')) {
												$(this).val(tarifs[i]['nom']);
												break;
											}
										}
									}
								}

								else if (wait == "telephone") {
									var val = $(this).val();
									val = val.replace(/(\d)[ .\/-](\d)/g, '$1$2');
									val = val.replace(/^00/, '+');
									val = val.replace(/^\+33\(?0?\)?/, '0');
									val = val.length == 9 && val.match(/^[1-9]/) ? '0' + val : val;
									val = val.length == 10 && val.match(/^0\d{9}$/) ? val.replace(/^(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})$/, '$1 $2 $3 $4 $5') : val;
									$(this).val(val.replace(/^[ .\/-](\d+)/, '$1'));
								}

								else if (wait == "email") {
									$(this).val($(this).val().toLowerCase());
								}

								else if (wait == "sport") {
									if ($(this).attr('data-id-sport') != null &&
										$(this).attr('data-nonsportif') == null) {
										for (var i in sports) {
											if (sports[i]['id_ecole_sport'] == $(this).attr('data-id-sport')) {
												$(this).val(sports[i]['sport'] + ' (' + 
													(sports[i]['sexe'] == 'm' ? 'F/G' : 
														sports[i]['sexe'] == 'h' ? 'H' : 'F') + ')');
												break;
											}
										}
									} else 
										$(this).val('');
								}

								else if (wait == "capitaine") {
									var val = $(this).attr('data-nonsportif') == null && $(this).attr('data-id-sport') > 0 ? 
										$getCapitaine($(this).val().replace(/^\s+|\s+$/g, "")) : '0';
									$(this).val(val == '1' ? 'O' : '').addClass(val == '1' ? 'cell-capitaine' : '');
								} 

								else if (wait == "licence") {
									var val = $(this).val().replace(/^\s+|\s+$/g, "");
									$(this).val($(this).val().replace(/^(([a-z0-9]{4})\s*)?([0-9]{6})$/i, function(a, x, y, z) {
										return (y + ' ' + z).toUpperCase();
									}));

									/*if ($(this).attr('data-nonsportif'))
										$(this).attr('data-licence', $(this).val()).val('');*/
								} 
							});

							links[wait] = numCol;
							did.push(waitFor.shift());
							didName.push(waitName.shift());
							$askNextColumn();
						}
					}
				}
			};

			$cleanDatas = function() {
				var row, cell;
				var estVide;
				var emptyCols = {};
				var maxCols = 0;
				var table = [];

				//On enlève les lignes vides 
				//On identifie les colonnes vides
				for (var i in datas) {
					row = datas[i];
					emptyRow = true;

					for (var j in row) {
						cell = (row[j]+'').replace(/^\s+|\s+$/g, "");

						if (!(j in emptyCols))
							emptyCols[j] = true;

						if (cell != "") {
							emptyRow = false;
							emptyCols[j] = false; 
						}
					}

					if (!emptyRow) {
						table.push(row);

						if (row.length > maxCols)
							maxCols = row.length;
					}
				}

				//On enlève les colonnes vides
				for (var k in emptyCols) {
					if (emptyCols[k] == true) {
						maxCols--;
						
						for (var i in table)
							table[i].splice(k, 1);
					}
				}

				//On remplie les lignes avec des cellules manquantes
				for (var i in table) {
					if (table[i].length < maxCols) {
						for (var l = maxCols - table[i].length; l >= 1; l--)
							table[i].push('');
					}
				}

				return {table: table, maxCols: maxCols};
			};


			$loadDatas = function(hasHeader) {
				var maxCols = datas[0].length;

				if (!hasHeader) {
					for (var j = 1; j <= maxCols; j++)
						$('table#table-excel thead tr').
							append('<th data-num-col="' + j + '"><div><i>?</i>' + 
								'<span class="glyphicon glyphicon-remove" title="Supprimer cette colonne"></span></div></th>');
				}

				else {
					var header = datas.shift();
					for (var j in header) {
						$('table#table-excel thead tr').
							append('<th data-num-col="' + (parseInt(j) + 1) + '"><div>' + 
								((header[j]+'').replace(/^\s+|\s+$/g, "").length > 0 ? $escapeHtml(header[j]+'') : '<i>?</i>') +
								'<span class="glyphicon glyphicon-remove" title="Supprimer cette colonne"></span></div></th>');
					}
				}

				for (var i in datas) {
					$('table#table-excel tbody').append('<tr data-num-row="' + (parseInt(i) + 1) + '">' + 
						'<th><div>' + (parseInt(i) + 1) + '<span class="glyphicon glyphicon-remove" title="Supprimer cette ligne"></span></div></th></tr>');

                    for (var j in datas[i]) {
                    	$('table#table-excel tbody tr:last-of-type').append('<td data-num-row="' + (parseInt(i) + 1) + '" data-num-col="' + (parseInt(j) + 1) + '">' + 
                    		'<input type="text" value="' + (datas[i][j]+'').replace(/"/g, '&quot;') + '" /></td>');
                    }
				}

				setTimeout(function() {
					$hideHint('error');
					$hideHint('info');
					$hideHint('success');
					$addHint('Les données ont bien été chargées, à toi de jouer !', 'success', 2000);
				}, 500);

				setTimeout(function() {
					$('#step1').fadeOut(500, function() {
						$askNextColumn();
					});
				}, 1000);
			};

			$launchLoad = function(hasHeader) {
				if (datas.length - (hasHeader ? 1 : 0) <= 0) {
					$hideHint('success');
					$addHint('Ce fichier ne contient aucune ligne.', 'error', 2000);
				}

				else {
					$hideHint('success');
					$addHint('Chargement des données...', 'info', 0);

					setTimeout(function() {
						$loadDatas(hasHeader);
					}, 500);
				}
			};

			$parseResults = function() {
				if (datas.length > 250) 
					$addHint('Ce tableau contient trop de lignes vides ou non, le maximum est de 250.', 'error', 2000);

				else {
					var clean = $cleanDatas();
					var maxCols = clean.maxCols;
					datas = clean.table; 

					if (datas.length > 200)
						$addHint('Ce tableau contient trop de lignes, le maximum est de 200.', 'error', 2000);

					else if (maxCols > 50)
						$addHint('Ce tableau contient trop de colonnes, le maximum est de 50.', 'error', 2000);
					
					else if (maxCols * datas.length > 2500)
						$addHint('Ce tableau contient trop de cellules, le maximum est de 2500.', 'error', 2000);
					
					else
						$addHint('Ok les données ont bien été récupérées ! Pour continuer l\'ajout en groupe sur le Challenger, clique sur l\'option qui correspond à ton fichier : <br /><br />' + 
							'<a href="#" onclick="$launchLoad(true);event.cancelBubble=true;return false">Mon fichier possède un entête (noms des colonnes)</a><br />' + 
							'<a href="#" onclick="$launchLoad(false);event.cancelBubble=true;return false">Mon fichier ne possède pas d\'entête</a>', 'success', 0);
				}
			};

			$fromScratch = function() {
				var nbRows = 0;
				var model = ["Nom","Prénom","Sexe","Logement","Tarif","Email"];

				nbRows = prompt('Nombre de lignes à afficher ?');

				if (!$.isNumeric(nbRows) || $.inArray(nbRows, [null, '']) >= 0 || parseInt(nbRows) <= 0) {
					if (nbRows !== null) {
						$hideHint('info');
						$addHint('Le nombre de lignes n\'est pas correct', 'error', 2000);
					}

					return;
				}

				nbRows = parseInt(nbRows);

				$hideHint('info');

				if (nbRows > 200) {
					$addHint('Ce tableau contient trop de lignes, le maximum est de 200.', 'error', 2000);
					return;
				}

				if (nbRows * model.length > 2500) {
					$addHint('Ce tableau contient trop de cellules, le maximum est de 2500.', 'error', 2000);
					return;
				}

				datas = [model];
				model = $.map(model, function() { return ''; });

				for (var i = nbRows; i >= 1; i--)
					datas.push(model);

				$launchLoad(true);
			};

			$showHelp = function() {
				//$hideHint();
				$hideHint('attention');
				$addHint('Le tutoriel n\'est pas encore disponible pour le moment, merci de patienter quelques temps!', 'attention', 2000);
			};

			$.fn.getCursorPosition = function() {  
		        var el = $(this).get(0);  
		        var pos = 0;  
		        if ('selectionStart' in el) {  
		            pos = el.selectionStart;  
		        } else if ('selection' in document) {  
		            el.focus();  
		            var Sel = document.selection.createRange();  
		            var SelLength = document.selection.createRange().text.length;  
		            Sel.moveStart('character', -el.value.length);  
		            pos = Sel.text.length - SelLength;  
		        }  
		        return pos;  
		    }

			$(document).on('focus', '.cell-error,.cell-warning,.cell-force', function() {
				$hideHint('message');
				var type = $(this).attr('data-error');
				if (type !== null) {
					if ($(this).attr('data-message'))
						$addHint($(this).attr('data-message'), 'message', 2000);

					else if (type == "nom") $addHint('Le nom doit être renseigné', 'message', 2000);
					else if (type == "prenom") $addHint('Le prénom doit être renseigné', 'message', 2000);
					else if (type == "sexe") $addHint('Doit valider la regex <b>^(h(omme)?|f(emme)?|m(an)?|w(oman)?|0|1|o(ui)?|n(on?)?|y(es)?)$</b>', 'message', 2000);
					else if (type == "sportif") $addHint('Doit valider la regex <b>^(|s(port(if)?)?|0|1|o(ui)?|n(on?)?|y(es)?)$</b>', 'message', 2000);
					else if (type == "fanfaron") $addHint('Doit valider la regex <b>^(|f(anfar(|e|on(n?e)?)?)?|0|1|o(ui)?|n(on?)?|y(es)?)$</b>', 'message', 2000);
					else if (type == "pompom") $addHint('Doit valider la regex <b>^(|p(om-?pom|im-?pim)?|0|1|o(ui)?|n(on?)?|y(es)?)$</b>', 'message', 2000);
					else if (type == "cameraman") $addHint('Doit valider la regex <b>^(|c(amera((wo)?man)?)?|p(hoto(graphe)?)?|0|1|o(ui)?|n(on?)?|y(es)?)$</b>', 'message', 2000);
					else if (type == "telephone") $addHint('Le téléphone ne correspond pas à un format correct', 'message', 2000);
					else if (type == "email") $addHint('L\'email ne correspond pas à un format correct', 'message', 2000);
					else if (type == "licence") $addHint('Les licences françaises sont sous la forme <b>^([a-z0-9]{4}\\s*)?[0-9]{6}$</b>', 'message', 2000);
					else if (type == "recharge") $addHint('Doit valider la regex <b>^\\d*((\\.|,)00?)?( ?€)?$</b>', 'message', 2000);
					else if (type == "logement") $addHint('Doit valider la regex <b>^(|l(ight)?( p(ackage)?)?|f(ull)?( p(ackage)?)?|0|1|o(ui)?|n(on?)?|y(es)?)$</b>', 'message', 2000);
					else if (type == "capitaine") $addHint('Doit valider la regex <b>^(|c(aptain|apitaine)?|0|1|o(ui)?|n(on?)?|y(es)?)$</b>', 'message', 2000);
				}
			});

			$(document).on('change', '.cell-error, .cell-warning', function() {
				$(this).removeClass('cell-error').removeClass('cell-warning').removeAttr('data-error').removeAttr('data-message');
				$(this).parent().parent().removeClass('row-warning').removeClass('row-danger').removeClass('row-warning-shown').removeClass('row-danger-shown');
			});

			$(document).on('focus', 'table#table-excel tbody tr td input:not([readonly])', function() {
				$loadAutocomplete(this);
				/*var save_this = $(this);
			    setTimeout (function(){ save_this.select(); }, 0);*/
			});

			var keyCodes = {};

			$(document).keydown(function (e) {
			    keyCodes[e.keyCode || e.which] = true;
			});

			$(document).keyup(function (e) {
			    delete keyCodes[e.keyCode || e.which];
			});

			$.fn.focusTextToEnd = function(){
		        this.focus();
		        var $thisVal = this.val();
		        this.val('').val($thisVal);
		        return this;
		    }

			$(document).on('keydown', 'table#table-excel tbody tr td input:not([readonly])', function(e) {
				var numCol = $(this).parent().attr('data-num-col');
			    keyCodes[e.keyCode || e.which] = true;

				if (9 in keyCodes && 16 in keyCodes ||
					38 in keyCodes && (!$(this).attr('autocomplete') || 16 in keyCodes)) { //UP (or SHIFT + UP lors d'un autocomplete)
					$(this).parent().parent().prev().find('td[data-num-col="' + numCol + '"] input:not([readonly])').focusTextToEnd();
					e.preventDefault();
				}
				
				else if (9 in keyCodes && !(16 in keyCodes) || //TAB without SHIFT
					40 in keyCodes && (!$(this).attr('autocomplete') || 16 in keyCodes)) { //DOWN (or SHIFT + DOWN lors d'un autocomplete)
					$(this).parent().parent().next().find('td[data-num-col="' + numCol + '"] input:not([readonly])').focusTextToEnd();
					e.preventDefault();
				}

				else if (39 in keyCodes && ($(this).getCursorPosition() == $(this).val().length || 16 in keyCodes)) { //RIGHT
					$(this).parent().nextAll().each(function() {
						if ($(this).has('input:not([readonly])').length) {
							$(this).find('input:not([readonly])').focusTextToEnd();
							e.preventDefault();
							return false;
						}
					});
				}

				else if (37 in keyCodes && ($(this).getCursorPosition() == 0 || 16 in keyCodes)) { //LEFT
					$(this).parent().prevAll().each(function() {
						if ($(this).has('input:not([readonly])').length) {
							$(this).find('input:not([readonly])').focusTextToEnd();
							e.preventDefault();
							return false;
						}
					});
				}
			});

			$(document).on('blur', 'table#table-excel tbody tr td input:not([readonly])', function() {
				if ($(this).data('uiAutocomplete'))
					$(this).autocomplete("destroy").removeData('uiAutocomplete').removeAttr('autocomplete');
			});

			$(document).on('click', 'table#table-excel thead tr th[data-num-col]', function() {
				var numCol = $(this).attr('data-num-col');
				$clickOnColumn(numCol);
			});

			$(document).on('click', 'table#table-excel thead tr th[data-num-col] span.glyphicon', function() {
    			var numCol = $(this).parent().parent().attr('data-num-col');
				$removeColumn(numCol);
				return false;
			});

			$(document).on('click', 'table#table-excel tbody tr th span.glyphicon', function() {
    			var numRow = $(this).parent().parent().parent().attr('data-num-row');
				$removeRow(numRow);
				return false;
			});
			
			$getLicences();
			$getTarifs();
			$getSports();

			$closeNewPage = function() {
				$('#hints').unbind('click');
				$('#hints .hint:not(.hint-model)').slideUp(200, function() {
					$('#hints').fadeOut(200, function() {
						$(this).removeClass('hints-new');
						$(this).fadeIn(200);
						$('#step1 .help').addClass('help-animated');
						$beginPage();
					});
				});
			};

			$beginPage = function() {
				$('#hints').click(function() {
					if (!$(this).hasClass('hints-right'))
						$(this).fadeOut(200, function() {
							$(this).addClass('hints-right');
							$(this).fadeIn(200);
						});
					

					else
						$(this).fadeOut(200, function() {
							$(this).removeClass('hints-right');
							$(this).fadeIn(200);
						});

					return false;
				});

				if (csvPHP != null) {
					if (csvPHP['error'] == 'chargement') 	$addHint('Une erreur s\'est produite lors du chargement du fichier', 'error', 2000);
					if (csvPHP['error'] == 'size') 			$addHint('Le fichier sélectionné est trop volumineux (1Mio max)', 'error', 2000);
					if (csvPHP['error'] == 'nonsupporte') 	$addHint('Le fichier n\'est pas conforme, un document CSV ou une feuille de calcul est attendu', 'error', 2000);
					else { 
						datas = csvPHP['datas'];
						$parseResults();
					}
				}

				else 
					$addHint('Pour commencer à utiliser l\'outil d\'ajout groupé de données sur le Challenger, il te suffit de sélectionner ' +
						'<a href="#" onclick="$(\'#step1 input[type=file]\').click();event.cancelBubble=true;return false">un fichier CSV ou une feuille de calcul</a>, ' +
						' ou alors commencer à partir d\'<a href="#" onclick="$fromScratch();event.cancelBubble=true;return false;">un fichier vide</a><br /><br />' +
						'Si tu veux découvrir l\'outil et identifier les bonnes informations à avoir, tu peux te diriger ' +
						'<a href="#" onclick="$showHelp();event.cancelBubble=true;return false;">vers le tutoriel</a> !', 'info', 0);
			};

			if (Cookies.get('challenger_import') === undefined) {
				$('#hints').click($closeNewPage);
				$('#hints').addClass('hints-new');

				setTimeout(function() {
					$addHint('Bonjour <b><?php echo preg_replace('`\\?\'`', '\\\'', $ecole['nom']); ?></b> !<br /><br /> Il semblerait que ce soit ta première visite sur cet outil. Ce dernier te permet d\'inscrire en masse des participants dans les bases du Challenger, et te permettra ainsi de gagner beaucoup de temps. <br />' +
						'<br />Avant de te lancer dans l\’aventure, il t\'est vivement recommandé de prendre connaissance du tutoriel, tu apprendras toutes les astuces pour profiter de l\'ensemble des fonctionnalités proposées.<br />' +
						'<br /><center><a href="#" onclick="$closeNewPage();event.cancelBubble=true;return false;">Pour commencer, clique ici !</a></center>', 'required', 0); }, 200);

				Cookies.set('challenger_import', true, {expires : 365});
			}

			else
				setTimeout($beginPage, 100);



			$('#step1 .choose').click(function() {
				$(this).parent().parent().find('input[type="file"]').click();
			});

			$('#step1 .help').click(function() {
				$showHelp();
			});

			$('#step1 .scratch').click(function() {
				$fromScratch();
			});

			$('#step1 input[type="file"]').val('').change(function() {
				$hideHint();

				var files = $(this)[0].files;
				var file;
				var filename; 
				var filedots; 
				var fileext; 

				if (files.length == 0)
					return;

				if (files.length > 1) {
					$(this).val('');
					$addHint('Tu ne dois sélectionner qu\'un seul fichier', 'error', 2000);
					return;
				}

				if (files[0].size >= 1024 * 1024) {
					$(this).val('');
					$addHint('Le fichier sélectionné est trop volumineux (1Mio max)', 'error', 2000);
					return; 
				}

				file = files[0];
				filename = "name" in file ? file.name : file.fileName;
				filedots = filename.split('.');
				fileext = filedots.length == 1 ? '' : '.' + filedots.pop();

				if (forcePHP ||
					fileext != '.csv' &&
					$.inArray(file.type, ['text/csv', 'application/csv']) < 0) {
					
					if ($.inArray(fileext, ['.csv', '.xls', '.xlsx', '.ods']) >= 0) {
						/*$addHint('Le fichier n\'est pas un document CSV mais semble être une feuille de calcul. ' +
							'Tu peux essayer de convertir cette feuille mais la conformité des données n\'est pas assurée.<br />' + 
							'<a href="#" onclick="$(\'#step1 form\').submit();event.cancelBubble=true;return false">Tenter la conversion de la feuille</a>', 'attention', 0);*/
						
						if (fileext == '.csv')
							$('#step1 form input[name=delimiter]').val(prompt('Quel est le délimiteur du fichier "' + filename + '" ?'));

						$addHint('Chargement et analyse du fichier en cours...', 'info', 0);
						$('#step1 form').submit();
						return;
					}
					
					else {
						$(this).val('');
						$addHint('Le fichier n\'est pas conforme, un document CSV ou une feuille de calcul est attendu', 'error', 2000);
						return;
					}
				}

				$(this).parse({
					config: {
						delimiter: "",	// auto-detect
						newline: "",	// auto-detect
						header: false,
						dynamicTyping: false,
						preview: 0,
						encoding: "",
						worker: false,
						comments: false,
						step: undefined,
						complete: function(results, file) {
							clearTimeout(timer);
							$hideHint('info');
							datas = results.data;
							$parseResults();							
						},
						error: function(err, file) {
							$addHint('Une erreur s\'est produite lors de la lecture du fichier', 'error', 2000);
						},
						download: false,
						skipEmptyLines: true,
						chunk: undefined,
						fastMode: false,
						beforeFirstChunk: undefined,
						withCredentials: undefined
					},
					before: function(file, inputElem) {
						$('#step1 input[type="file"]').val('');
						timer = setTimeout(function() {
							$addHint('Chargement et analyse du fichier en cours...', 'info', 0);
						}, 200);
					},
					error: function(err, file, inputElem, reason) {
						$addHint('Une erreur s\'est produite lors du chargement du fichier', 'error', 2000);
					},
					complete: function() {
						
					}
				});
			});
		});
		</script>
	</body>
</html>