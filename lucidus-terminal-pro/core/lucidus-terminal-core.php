<?php
/*
 * Core logic for Lucidus Terminal Pro
 */

// Ensure directories exist on init
add_action('init', 'lucidus_ensure_directories_exist');
add_action('init', 'lucidus_load_script_engine');
add_action('rest_api_init', 'lucidus_register_rest');
add_action('admin_menu', 'lucidus_register_menus');
add_action('admin_enqueue_scripts', 'lucidus_admin_assets');

function lucidus_admin_assets() {
    wp_enqueue_style('lucidus-admin-css', LUCIDUS_TERMINAL_PRO_URL . 'admin/assets/lucidus-admin.css');
    wp_enqueue_script('lucidus-admin-js', LUCIDUS_TERMINAL_PRO_URL . 'admin/assets/lucidus-admin.js', [], false, true);
}

function lucidus_ensure_directories_exist() {
    $base = WP_CONTENT_DIR . '/dbs-library/';
    $folders = [
        'scripts/prophecy', 'scripts/badges', 'scripts/rank', 'scripts/triggers',
        'scripts/town', 'scripts/map', 'scripts/gospel', 'scripts/media',
        'memory-archive/profiles', 'memory-archive/geo', 'memory-archive/scrolls/archive',
        'memory-archive/ledger', 'memory-archive/canon', 'memory-archive/logs',
        'memory-archive/backups', 'memory-archive/seeds',
        'assets/audio', 'assets/images', 'assets/poster-templates'
    ];
    foreach ($folders as $folder) {
        $full_path = $base . $folder;
        if (!file_exists($full_path)) {
            wp_mkdir_p($full_path);
        }
    }
}

function lucidus_load_script_engine() {
    $script_root = WP_CONTENT_DIR . '/dbs-library/scripts/';
    if (!is_dir($script_root)) {
        return;
    }
    $folders = scandir($script_root);
    foreach ($folders as $folder) {
        if ($folder === '.' || $folder === '..') {
            continue;
        }
        $dir = $script_root . $folder;
        if (is_dir($dir)) {
            foreach (glob("$dir/*.php") as $file) {
                if (current_user_can('manage_options')) {
                    include_once $file;
                }
                lucidus_log_script_load($file);
            }
        }
    }
}

function lucidus_log_script_load($file) {
    $log_path = WP_CONTENT_DIR . '/dbs-library/memory-archive/logs/script-executions.log';
    $entry = '[' . date('Y-m-d H:i:s') . '] Loaded: ' . basename($file) . PHP_EOL;
    if (!file_exists(dirname($log_path))) {
        wp_mkdir_p(dirname($log_path));
    }
    file_put_contents($log_path, $entry, FILE_APPEND);
}

function lucidus_register_rest() {
    register_rest_route('lucidus/v1', '/status', [
        'methods'  => 'GET',
        'callback' => function () {
            return [
                'status'  => 'Lucidus Terminal Core active',
                'version' => '1.0',
                'scripts_loaded' => true
            ];
        },
        'permission_callback' => '__return_true'
    ]);

    register_rest_route('lucidus/v1', '/scripts/list', [
        'methods'  => 'GET',
        'callback' => function () {
            $script_root = WP_CONTENT_DIR . '/dbs-library/scripts/';
            $results = [];
            foreach (glob($script_root . '*/*.php') as $file) {
                $results[] = basename($file);
            }
            return $results;
        },
        'permission_callback' => function () {
            return current_user_can('manage_options');
        }
    ]);
}

function lucidus_register_menus() {
    $cap = 'manage_options';
    add_menu_page('Lucidus Terminal', 'Lucidus Terminal', $cap, 'lucidus-terminal-status', 'lucidus_render_status_page');
    add_submenu_page('lucidus-terminal-status', 'Status', 'Status', $cap, 'lucidus-terminal-status', 'lucidus_render_status_page');
    add_submenu_page('lucidus-terminal-status', 'Scripts', 'Scripts', $cap, 'lucidus-terminal-scripts', 'lucidus_render_scripts_page');
    add_submenu_page('lucidus-terminal-status', 'Settings', 'Settings', $cap, 'lucidus-terminal-settings', 'lucidus_render_settings_page');
    add_submenu_page('lucidus-terminal-status', 'Memory', 'Memory', $cap, 'lucidus-terminal-memory', 'lucidus_render_memory_page');
    add_submenu_page('lucidus-terminal-status', 'Voice Engine', 'Voice Engine', $cap, 'lucidus-terminal-voice', 'lucidus_render_voice_page');
    add_submenu_page('lucidus-terminal-status', 'Uploads', 'Uploads', $cap, 'lucidus-terminal-uploads', 'lucidus_render_uploads_page');
    add_submenu_page('lucidus-terminal-status', 'Gospel', 'Gospel', $cap, 'lucidus-terminal-gospel', 'lucidus_render_gospel_page');
    add_submenu_page('lucidus-terminal-status', 'Diagnostics', 'Diagnostics', $cap, 'lucidus-terminal-diagnostics', 'lucidus_render_diagnostics_page');
}

function lucidus_render_status_page() {
    include LUCIDUS_TERMINAL_PRO_PATH . 'admin/templates/status.php';
}
function lucidus_render_scripts_page() {
    include LUCIDUS_TERMINAL_PRO_PATH . 'admin/templates/scripts.php';
}
function lucidus_render_settings_page() {
    include LUCIDUS_TERMINAL_PRO_PATH . 'admin/templates/settings.php';
}
function lucidus_render_memory_page() {
    include LUCIDUS_TERMINAL_PRO_PATH . 'admin/templates/memory.php';
}
function lucidus_render_voice_page() {
    include LUCIDUS_TERMINAL_PRO_PATH . 'admin/templates/voice.php';
}
function lucidus_render_uploads_page() {
    include LUCIDUS_TERMINAL_PRO_PATH . 'admin/templates/uploads.php';
}
function lucidus_render_gospel_page() {
    include LUCIDUS_TERMINAL_PRO_PATH . 'admin/templates/gospel.php';
}
function lucidus_render_diagnostics_page() {
    include LUCIDUS_TERMINAL_PRO_PATH . 'admin/templates/diagnostics.php';
}
?>
