<?php
/*
Plugin Name: Lucidus Terminal Pro
Description: Mythological OS plugin for WordPress.
Version: 0.1.1
Author: Dr.G and Lucidus Bastardo
*/

if (!defined('ABSPATH')) { exit; }

// Auto include scripts from /scripts/*/*.php
foreach (glob(__DIR__ . '/scripts/*/*.php') as $script) {
    include_once $script;
}

// Ensure required directories exist
function lucidus_terminal_pro_init_dirs() {
    $dirs = array(
        __DIR__ . '/logs',
        __DIR__ . '/dbs-library',
        __DIR__ . '/memory-archive/profiles',
        __DIR__ . '/canon',
        __DIR__ . '/assets',
        __DIR__ . '/core',
        __DIR__ . '/templates',
        __DIR__ . '/scripts',
    );
    foreach ($dirs as $dir) {
        if (!file_exists($dir)) {
            wp_mkdir_p($dir);
        }
    }
    if (!file_exists(__DIR__ . '/logs/script-executions.log')) {
        file_put_contents(__DIR__ . '/logs/script-executions.log', "");
    }
    if (!file_exists(__DIR__ . '/logs/upload-log.json')) {
        file_put_contents(__DIR__ . '/logs/upload-log.json', '[]');
    }
}
register_activation_hook(__FILE__, 'lucidus_terminal_pro_init_dirs');

// Load admin pages
add_action('admin_menu', function() {
    add_menu_page('Lucidus Status', 'Lucidus Status', 'manage_options', 'lucidus-status', function() { include __DIR__ . '/admin/status.php'; });
    add_submenu_page('lucidus-status', 'Scripts', 'Scripts', 'manage_options', 'lucidus-scripts', function() { include __DIR__ . '/admin/scripts.php'; });
    add_submenu_page('lucidus-status', 'Settings', 'Settings', 'manage_options', 'lucidus-settings', function() { include __DIR__ . '/admin/settings.php'; });
    add_submenu_page('lucidus-status', 'Memory', 'Memory', 'manage_options', 'lucidus-memory', function() { include __DIR__ . '/admin/memory.php'; });
    add_submenu_page('lucidus-status', 'Voice', 'Voice', 'manage_options', 'lucidus-voice', function() { include __DIR__ . '/admin/voice.php'; });
    add_submenu_page('lucidus-status', 'Uploads', 'Uploads', 'manage_options', 'lucidus-uploads', function() { include __DIR__ . '/admin/uploads.php'; });
    add_submenu_page('lucidus-status', 'Gospel', 'Gospel', 'manage_options', 'lucidus-gospel', function() { include __DIR__ . '/admin/gospel.php'; });
    add_submenu_page('lucidus-status', 'Diagnostics', 'Diagnostics', 'manage_options', 'lucidus-diagnostics', function() { include __DIR__ . '/admin/diagnostics.php'; });
});

// REST endpoints
add_action('rest_api_init', function() {
    register_rest_route('lucidus/v1', '/status', array(
        'methods' => 'GET',
        'callback' => function() {
            return array('status' => 'ok', 'version' => '0.1.0');
        }
    ));
    register_rest_route('lucidus/v1', '/scripts/list', array(
        'methods' => 'GET',
        'callback' => function() {
            $scripts = array();
            foreach (glob(__DIR__ . '/scripts/*/*.php') as $script) {
                $scripts[] = basename($script);
            }
            return $scripts;
        }
    ));
});
?>
