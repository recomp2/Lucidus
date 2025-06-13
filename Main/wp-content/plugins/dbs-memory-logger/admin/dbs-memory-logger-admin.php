<?php
if (!defined('ABSPATH')) {
    exit;
}

function dbs_memory_logger_settings_init() {
    register_setting('dbs_memory_logger', 'dbs_memory_logger_enabled');
    register_setting('dbs_memory_logger', 'dbs_memory_logger_voice');
    register_setting('dbs_memory_logger', 'dbs_memory_logger_microphone');
    register_setting('dbs_memory_logger', 'dbs_memory_logger_tts');
    register_setting('dbs_memory_logger', 'dbs_memory_logger_personality');
    register_setting('dbs_memory_logger', 'dbs_memory_logger_prompt');
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
    if(function_exists('dbs_update_user_memory')){
        dbs_update_user_memory('dbs-memory-logger','settings');
    }
    if (isset($_POST['dbs_memory_logger_settings'])) {
        check_admin_referer('dbs_memory_logger_settings');
        update_option('dbs_memory_logger_enabled', isset($_POST['dbs_memory_logger_enabled']) ? '1' : '0');
        update_option('dbs_memory_logger_voice', isset($_POST['dbs_memory_logger_voice']) ? '1' : '0');
        update_option('dbs_memory_logger_microphone', isset($_POST['dbs_memory_logger_microphone']) ? '1' : '0');
        update_option('dbs_memory_logger_tts', isset($_POST['dbs_memory_logger_tts']) ? '1' : '0');
        update_option('dbs_memory_logger_personality', sanitize_text_field($_POST['dbs_memory_logger_personality']));
        update_option('dbs_memory_logger_prompt', sanitize_textarea_field($_POST['dbs_memory_logger_prompt']));
        echo '<div class="updated notice"><p>Settings saved.</p></div>';
    }
    $enabled = get_option('dbs_memory_logger_enabled', '1');
    $voice = get_option('dbs_memory_logger_voice', '0');
    $microphone = get_option('dbs_memory_logger_microphone', '0');
    $tts = get_option('dbs_memory_logger_tts', '0');
    $personality = get_option('dbs_memory_logger_personality', 'dub');
    $prompt = get_option('dbs_memory_logger_prompt', '');
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
    echo '<p><label><input type="checkbox" name="dbs_memory_logger_microphone" value="1"' . checked('1', $microphone, false) . ' /> ' . esc_html__('Enable Microphone Input', 'dbs-memory-logger') . '</label></p>';
    echo '<p><label><input type="checkbox" name="dbs_memory_logger_tts" value="1"' . checked('1', $tts, false) . ' /> ' . esc_html__('Enable TTS Mode', 'dbs-memory-logger') . '</label></p>';
    echo '<p>' . esc_html__('AI Personality:', 'dbs-memory-logger') . '<select name="dbs_memory_logger_personality">'
        .'<option value="dub"'.selected('dub',$personality,false).'>Dub</option>'
        .'<option value="randall"'.selected('randall',$personality,false).'>Randall</option>'
        .'<option value="nasty"'.selected('nasty',$personality,false).'>Nasty P</option>'
    .'</select></p>';
    echo '<p><label>' . esc_html__('Prompt Builder', 'dbs-memory-logger') . '<br /><textarea name="dbs_memory_logger_prompt" rows="3" cols="50">' . esc_textarea($prompt) . '</textarea></label></p>';
    submit_button();
    echo '</form></div>';
}

function dbs_memory_logger_logs_page() {
    if (!current_user_can('manage_options')) {
        return;
    }
    if(function_exists('dbs_update_user_memory')){
        dbs_update_user_memory('dbs-memory-logger','logs');
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
    $file = WP_CONTENT_DIR . '/dbs-library/memory-archive/system.json';
    return file_exists($file) ? json_decode(file_get_contents($file), true) : [];
}
?>
