<?php
/**
 * Lucidus Memory Loader
 * Initializes memory archive structure and hooks for selective memory tracking.
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define memory archive root
define('LUCIDUS_MEMORY_ARCHIVE', plugin_dir_path(__FILE__) . 'memory-archive/');

// Create required subdirectories if not present
function lucidus_initialize_memory_archive() {
    $dirs = [
        'users',
        'scrolls',
        'mood',
        'chat',
        'tags',
        'chapters',
        'images',
        'audio',
        'system'
    ];

    foreach ($dirs as $dir) {
        $path = LUCIDUS_MEMORY_ARCHIVE . $dir . '/';
        if (!file_exists($path)) {
            wp_mkdir_p($path);
        }
    }

    // Create index and system files if needed
    $system_files = [
        'index.json',
        'patch-log.json',
        'echo-engine.json'
    ];

    foreach ($system_files as $file) {
        $filepath = LUCIDUS_MEMORY_ARCHIVE . 'system/' . $file;
        if (!file_exists($filepath)) {
            file_put_contents($filepath, json_encode([], JSON_PRETTY_PRINT));
        }
    }
}
add_action('init', 'lucidus_initialize_memory_archive');

// Optional: log entry function
function lucidus_log_memory_event($type, $id, $data) {
    $folder = trailingslashit(LUCIDUS_MEMORY_ARCHIVE . $type);
    $filename = $folder . sanitize_file_name($id) . '.json';

    if (!file_exists($folder)) {
        wp_mkdir_p($folder);
    }

    file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT));
}
?>
