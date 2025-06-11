<?php
if (!defined('ABSPATH')) exit;

function tier_access_log($user_id, $feature, $allowed) {
    $upload = wp_upload_dir();
    $dir = trailingslashit($upload['basedir']) . 'lucidus-terminal/';
    if (!file_exists($dir)) {
        wp_mkdir_p($dir);
    }
    $file = $dir . 'tier-access.log';
    $entry = sprintf("%s\t%d\t%s\t%s\n", current_time('mysql'), $user_id, $feature, $allowed ? 'allowed' : 'denied');
    file_put_contents($file, $entry, FILE_APPEND);

    $json_file = $dir . 'tier-denial-log.json';
    $log = [];
    if (file_exists($json_file)) {
        $log = json_decode(file_get_contents($json_file), true);
        if (!is_array($log)) $log = [];
    }
    $log[] = [
        'time' => current_time('mysql'),
        'user' => $user_id,
        'feature' => $feature,
        'allowed' => $allowed
    ];
    file_put_contents($json_file, wp_json_encode($log));
}
