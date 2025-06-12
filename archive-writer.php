<?php
if (!defined('ABSPATH')) exit;

function lucidus_archive_dir() {
    $upload = wp_upload_dir();
    $dir = trailingslashit($upload['basedir']) . 'dbs-library/memory-archive/';
    if (!file_exists($dir)) {
        wp_mkdir_p($dir);
    }
    return $dir;
}

function lucidus_write_archive($data) {
    $dir = lucidus_archive_dir();
    $file = $dir . 'entry-' . date('Ymd-His') . '.json';
    file_put_contents($file, wp_json_encode($data));
}

function lucidus_get_archive_entries($limit = 20) {
    $dir = lucidus_archive_dir();
    $files = glob($dir . 'entry-*.json');
    rsort($files);
    $files = array_slice($files, 0, $limit);
    $entries = [];
    foreach ($files as $file) {
        $data = json_decode(file_get_contents($file), true);
        if ($data) {
            $entries[] = $data;
        }
    }
    return $entries;
}
