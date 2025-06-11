<?php
if (!defined('ABSPATH')) exit;

function lucidus_upgrade_log_dir() {
    $upload = wp_upload_dir();
    $dir = trailingslashit($upload['basedir']) . 'dbs-library/logs/';
    if (!file_exists($dir)) {
        wp_mkdir_p($dir);
    }
    return $dir;
}

function lucidus_insight_log_file() {
    return lucidus_upgrade_log_dir() . 'lucidus-insight-log.json';
}

function lucidus_record_insight($entry) {
    $file = lucidus_insight_log_file();
    $log = [];
    if (file_exists($file)) {
        $log = json_decode(file_get_contents($file), true);
        if (!is_array($log)) {
            $log = [];
        }
    }
    $log[] = $entry;
    if (count($log) > 50) {
        $log = array_slice($log, -50);
    }
    file_put_contents($file, wp_json_encode($log));
}

function lucidus_generate_insights() {
    $suggestions = [];

    // Check memory files for saturation
    $upload = wp_upload_dir();
    $memory_files = glob(trailingslashit($upload['basedir']) . 'lucidus-terminal/memory-*.json');
    foreach ($memory_files as $file) {
        if (filesize($file) > 1024 * 1024) {
            $suggestions[] = sprintf('Memory file %s exceeds 1MB.', basename($file));
        }
    }

    // Detect unused modules
    $modules_dir = plugin_dir_path(dirname(__DIR__)) . 'modules/';
    foreach (glob($modules_dir . '*', GLOB_ONLYDIR) as $folder) {
        if (!file_exists($folder . '/module-active.flag')) {
            $suggestions[] = sprintf('Module %s may be inactive.', basename($folder));
        }
    }

    // Prophecy error rate
    $archive_dir = trailingslashit($upload['basedir']) . 'dbs-library/memory-archive/';
    $errors = 0;
    foreach (glob($archive_dir . 'entry-*.json') as $file) {
        $data = json_decode(file_get_contents($file), true);
        if (isset($data['error'])) {
            $errors++;
        }
    }
    if ($errors > 5) {
        $suggestions[] = sprintf('%d archive entries contain errors.', $errors);
    }

    if (!$suggestions) {
        $suggestions[] = 'System stable. No issues detected.';
    }

    lucidus_record_insight([
        'time' => current_time('mysql'),
        'suggestions' => $suggestions
    ]);
}

add_action('lucidus_upgrade_insight', 'lucidus_generate_insights');

add_action('init', function() {
    if (!wp_next_scheduled('lucidus_upgrade_insight')) {
        wp_schedule_event(time(), 'hourly', 'lucidus_upgrade_insight');
    }
});

function lucidus_upgrade_unschedule() {
    $timestamp = wp_next_scheduled('lucidus_upgrade_insight');
    if ($timestamp) {
        wp_unschedule_event($timestamp, 'lucidus_upgrade_insight');
    }
}
