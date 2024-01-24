<?php

require DIR.'backup.php';

if (!empty($_GET['backup']) &&
    !empty($_SESSION['backup']) &&
    $_GET['backup'] === $_SESSION['backup']) {
    __backup();
    __backup('min');
    $add = true;
}

if (!empty($_GET['restore']) &&
    !empty($_SESSION['backup']) &&
    $_GET['restore'] === $_SESSION['backup']) {
    $restore = true;
}

$_SESSION['backup'] = uniqid();

if (!empty($_POST['delete']) || !empty($_POST['restore']) || !empty($_POST['save']) || !empty($_POST['sql'])) {
    $file = empty($_POST['delete']) ? 
        (empty($_POST['restore']) ? 
            (empty($_POST['save']) ? $_POST['sql'] : $_POST['save']) : $_POST['restore']) : $_POST['delete'];
        
    if (strpos($file, '/') !== false ||
        !file_exists(BACKUP_DIR . '/' . $file))
        unset($file);
}

if (isset($file) &&
	!empty($_POST['delete'])) {

	@unlink(BACKUP_DIR . '/' . $file);
	$delete = true;
}

else if (isset($file) &&
	!empty($_POST['save'])) {

    header("Content-Type: application/octet-stream");
    header("Content-Transfer-Encoding: Binary");
    header("Content-disposition: attachment; filename=\"$file\""); 
    echo readfile(BACKUP_DIR . '/' . $file);
    die;
}

else if (isset($file) &&
	!empty($_POST['sql'])) {

    header("Content-Type: application/octet-stream");
    header("Content-Transfer-Encoding: Binary");
    header("Content-disposition: attachment; filename=\"$file.sql\""); 
    __read(BACKUP_DIR . '/' . $file, '__print');
    die;
}

else if (!empty($_POST['restore']) && 
    isset($file)) {
    __backup('pre-restore');
    __read(BACKUP_DIR . '/' . $file, '__exec');
    die(header('location:'.url('admin/module/configurations/backup?restore='.$_SESSION['backup'], false, false)));
}

$backups = array_diff(scandir(BACKUP_DIR), array('.', '..'));
$backups = array_map(function($file) {
    @list(, $mode, $date) = explode('_', $file);
    return [
        'file' => $file,
        'mode' => $mode, 
        'date' => $date,
        'size' => round(filesize(BACKUP_DIR . '/' . $file) / 1024 / 1024, 2) . 'Mio',
    ];
}, $backups);

//CRON

//Inclusion du bon fichier de template
require DIR.'templates/admin/configurations/backup.php';
