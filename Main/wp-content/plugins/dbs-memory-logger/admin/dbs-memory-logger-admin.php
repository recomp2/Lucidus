<?php
if (!defined('ABSPATH')) {
    exit;
}

function dbs_memory_logger_settings_init() {
    register_setting('dbs_memory_logger', 'dbs_memory_logger_enabled');
    register_setting('dbs_memory_logger', 'dbs_memory_logger_voice');
}
add_action('admin_init', 'dbs_memory_logger_settings_init');

function dbs_memory_logger_menu() {
    add_menu_page('DBS Memory', 'DBS Memory', 'manage_options', 'dbs-memory', 'dbs_memory_logger_admin_page');
    add_submenu_page('dbs-memory', 'Memory Logs', 'Logs', 'manage_options', 'dbs-memory-logs', 'dbs_memory_logger_logs_page');
}
add_action('admin_menu', 'dbs_memory_logger_menu');

function dbs_memory_logger_admin_page() {
    if (!current_user_can('manage_options')) {
        return;
    }
    if (isset($_POST['dbs_memory_logger_settings'])) {
        check_admin_referer('dbs_memory_logger_settings');
        update_option('dbs_memory_logger_enabled', isset($_POST['dbs_memory_logger_enabled']) ? '1' : '0');
        update_option('dbs_memory_logger_voice', isset($_POST['dbs_memory_logger_voice']) ? '1' : '0');
        echo '<div class="updated notice"><p>Settings saved.</p></div>';
    }
    $enabled = get_option('dbs_memory_logger_enabled', '1');
    $voice = get_option('dbs_memory_logger_voice', '0');
    $screen = get_current_screen();
    if ($screen && method_exists($screen, 'add_help_tab')) {
        $screen->add_help_tab([
            'id' => 'dbs_memory_logger_help',
            'title' => __('Memory Logger Help', 'dbs-memory-logger'),
            'content' => '<p>' . esc_html__('Toggle memory logging and voice alerts.', 'dbs-memory-logger') . '</p>'
        ]);
    }
    echo '<div class="wrap"><h1>' . esc_html__('DBS Memory Logger', 'dbs-memory-logger') . '</h1>';
    echo '<h2 class="nav-tab-wrapper">';
    echo '<a href="' . admin_url('admin.php?page=dbs-memory') . '" class="nav-tab nav-tab-active">' . esc_html__('Settings', 'dbs-memory-logger') . '</a>';
    echo '<a href="' . admin_url('admin.php?page=dbs-memory-logs') . '" class="nav-tab">' . esc_html__('Logs', 'dbs-memory-logger') . '</a>';
    echo '</h2>';
    echo '<form method="post">';
    wp_nonce_field('dbs_memory_logger_settings');
    echo '<p><label><input type="checkbox" name="dbs_memory_logger_enabled" value="1"' . checked('1', $enabled, false) . ' /> ' . esc_html__('Enable Logging', 'dbs-memory-logger') . '</label></p>';
    echo '<p><label><input type="checkbox" name="dbs_memory_logger_voice" value="1"' . checked('1', $voice, false) . ' /> ' . esc_html__('Enable Voice Alerts', 'dbs-memory-logger') . '</label></p>';
    submit_button();
    echo '</form></div>';
}

function dbs_memory_logger_logs_page() {
    if (!current_user_can('manage_options')) {
        return;
    }
    $entries = dbs_memory_logger_get_entries();
    echo '<div class="wrap"><h1>' . esc_html__('Memory Log Entries', 'dbs-memory-logger') . '</h1>';
    echo '<h2 class="nav-tab-wrapper">';
    echo '<a href="' . admin_url('admin.php?page=dbs-memory') . '" class="nav-tab">' . esc_html__('Settings', 'dbs-memory-logger') . '</a>';
    echo '<a href="' . admin_url('admin.php?page=dbs-memory-logs') . '" class="nav-tab nav-tab-active">' . esc_html__('Logs', 'dbs-memory-logger') . '</a>';
    echo '</h2>';
    if (empty($entries)) {
        echo '<p>' . esc_html__('No entries logged yet.', 'dbs-memory-logger') . '</p>';
    } else {
        echo '<ul class="dbs-memory-log-entries">';
        foreach ($entries as $e) {
            echo '<li>' . esc_html($e['time'] . ': ' . $e['message']) . '</li>';
        }
        echo '</ul>';
    }
    echo '</div>';
}

function dbs_memory_logger_get_entries() {
    $file = WP_CONTENT_DIR . '/dbs-library/system.json';
    return file_exists($file) ? json_decode(file_get_contents($file), true) : [];
}
?>
