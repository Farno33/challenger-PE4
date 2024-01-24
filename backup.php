<?php

define('MULTIPLE_INSERT', 5);
define('MAX_LENGTH_INSERT', 100000);
define('BACKUP_DIR', DIR.'backups/');

if (!file_exists(BACKUP_DIR))
    mkdir(BACKUP_DIR, 0777, true);

function __($path, $str = '', $open = false) {
    $hash = md5($str);
    $str = chunk_split(base64_encode($str));
    file_put_contents($path, $hash . PHP_EOL . $str . PHP_EOL, $open ? 0 : FILE_APPEND);
}

function type($properties) {
    return preg_replace('/^([^\(]+)\(?(.*)/', '$1', $properties['Type']);
}

function quark($sql) {
    return '`'.$sql.'`';
}

function item($item) {
    return '('.implode(', ', $item).')';
}

function dataQuotes($data) {
    global $pdo;
    if ($data === null) return 'NULL';
    return $pdo->quote($data);
}

function dataNoQuotes($data) {
    if ($data === null) return 'NULL';
    return $data;
}

function __items($path, $items, $table, $columns) {
    if (empty($items))
        return;
    
    $keys = array_map('quark', array_keys($columns));
    $types = array_values($columns);

    $prefix = "INSERT INTO $table";
    $prefix .= ' ('.implode(', ', $keys).')';
    $prefix .= " VALUES\n";
    
    $NO_QUOTES = ['int', 'smallint', 'tinyint', 'mediumint', 'bigint', 'float', 'double', 'decimal', 'real'];
    foreach ($types as $i => $type) {
        if (in_array($type, $NO_QUOTES)) {
            foreach ($items as $j => $item) {
                $items[$j][$i] = dataNoQuotes($item[$i]);
            }
        } else {
            foreach ($items as $j => $item) {
                $items[$j][$i] = dataQuotes($item[$i]);
            }
        }
    }

    $items = array_map('item', $items);
    $subItems = [];
    $subLength = 0;
    foreach ($items as $item) {
        $length = strlen($item);
        
        if ($subLength + $length > MAX_LENGTH_INSERT && $subLength > 0) {
            __($path, $prefix . implode(",\n", $subItems));
            $subItems = [];
            $subLength = 0;
        }
        
        $subItems[] = $item;
        $subLength += $length;
    }

    if ($subLength > 0) 
        __($path, $prefix . implode(",\n", $subItems));
}

function __exec($hash, $str) {
    global $pdo;
    $str = base64_decode($str);

    if ($hash === md5($str)) {
        $pdo->exec($str);
    }
}

function __print($hash, $str) {
    $str = base64_decode($str);

    if ($hash === md5($str)) {
        echo "$str;\n";
    }
}

function __read($path, $callback) {
    $handle = fopen($path, "r");
    if ($handle) {
        $hash = '';
        $str = '';
        
        while (($line = fgets($handle)) !== false) {
            $line = trim($line);
            
            if (empty($line)) {
                $callback($hash, $str);
                $hash = '';
                $str = '';
            } else if (empty($hash)) {
                $hash = $line;
            } else {
                $str .= $line;
            }
        }

        if (!empty($hash) || !empty($str))
            $callback($hash, $str);
        
        fclose($handle);
    }
}

function __backup($mode = 'manual') {
    global $pdo;

    $path = BACKUP_DIR.'/backup_'.$mode.'_'.date('Ymd-His');
    __($path, "SET foreign_key_checks = 0", true);

    $tables = array_keys($pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_UNIQUE));
    foreach ($tables as $table) {
        $qtable = quark($table);
        list(,$schema) = $pdo->query("SHOW CREATE TABLE $qtable")->fetch(PDO::FETCH_NUM);
        $columns = $pdo->query("SHOW COLUMNS FROM $qtable")->fetchAll(PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);
        $columns = array_map('type', $columns);
        $hasEtat = in_array('_etat', array_keys($columns));
        $items = $pdo->query("SELECT * FROM $qtable".($mode == 'min' && $hasEtat ? ' WHERE _etat = "active" ' : ''))->fetchAll(PDO::FETCH_NUM);

        __($path, "DROP TABLE IF EXISTS $table");
        __($path, "$schema");
        __items($path, $items, $table, $columns);
    }

    __($path, "SET foreign_key_checks = 1");
}