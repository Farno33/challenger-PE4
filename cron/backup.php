<?php

define('DELETE_AFTER', 7 * 24 * 60); // 7 jours
$keeps = [30, 60, 120, 180, 240]; //Paliers

require DIR.'backup.php';

$autos = [];
$backups = array_diff(scandir(BACKUP_DIR), array('.', '..'));
foreach ($backups as $file) {
    @list(, $mode, $date) = explode('_', $file);
    if ($mode === 'auto') {
        $autos[$date] = [
            'file' => $file,
            'keep' => false
        ];
    }
}
krsort($autos);

$days = [];
$keeps = array_fill_keys($keeps, false);
$now = (new DateTime())->getTimestamp();
foreach ($autos as $dstr => $auto) {
    $date = date_create_from_format('Ymd-His', $dstr);
    $timestamp = $date->getTimestamp();
    $day = $date->format('Ymd');
    $minutes = round(($now - $timestamp) / 60) - 2; // On se donne 2min de plus 

    if ($minutes > DELETE_AFTER) {
        continue;
    }

    if (empty($days[$day])) {
        $autos[$dstr]['keep'] = true;
        $days[$day] = $dstr;
    }

    foreach ($keeps as $min => $keep) {
        if ($min >= $minutes) {
            if (!$keep) {
                $autos[$dstr]['keep'] = true;
                $keeps[$min] = $dstr;
            }

            continue 2;
        }
    }
}

foreach ($autos as $auto) {
    if (!$auto['keep']) {
        @unlink(BACKUP_DIR . '/' .$auto['file']);
    }
}

__backup('auto');