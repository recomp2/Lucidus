<?php
if (!defined('ABSPATH')) exit;

function lucidus_error_log($entry) {
    $upload = wp_upload_dir();
    $file = trailingslashit($upload['basedir']) . 'lucidus-error-log.json';
    $log = [];
    if (file_exists($file)) {
        $log = json_decode(file_get_contents($file), true);
        if (!is_array($log)) {
            $log = [];
        }
    }
    $entry['time'] = current_time('mysql');
    $log[] = $entry;
    if (count($log) > 100) {
        $log = array_slice($log, -100);
    }
    file_put_contents($file, wp_json_encode($log));
}
