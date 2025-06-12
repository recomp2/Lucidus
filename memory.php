<?php
if (!defined('ABSPATH')) exit;

if (!defined('LUCIDUS_MEMORY_LIMIT')) {
    define('LUCIDUS_MEMORY_LIMIT', 50);
}

function lucidus_memory_file($user_id = null) {
    if (null === $user_id) {
        $user_id = get_current_user_id();
    }
    $upload = wp_upload_dir();
    $dir = trailingslashit($upload['basedir']) . 'lucidus-terminal/';
    if (!file_exists($dir)) {
        wp_mkdir_p($dir);
    }
    return $dir . 'memory-' . intval($user_id) . '.json';
}

function lucidus_load_memory($user_id = null) {
    $file = lucidus_memory_file($user_id);
    if (!file_exists($file)) return [];
    $json = file_get_contents($file);
    $data = json_decode($json, true);
    return is_array($data) ? $data : [];
}

function lucidus_save_memory($memory, $user_id = null) {
    $file = lucidus_memory_file($user_id);
    if (count($memory) > LUCIDUS_MEMORY_LIMIT) {
        $memory = array_slice($memory, -LUCIDUS_MEMORY_LIMIT);
    }
    file_put_contents($file, wp_json_encode($memory));
}

function lucidus_clear_memory($user_id = null) {
    $file = lucidus_memory_file($user_id);
    if (file_exists($file)) {
        file_put_contents($file, wp_json_encode([]));
    }
}
