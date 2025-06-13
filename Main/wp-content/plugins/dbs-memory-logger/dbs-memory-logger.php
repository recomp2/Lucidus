<?php
/*
Plugin Name: DBS Memory Logger
Description: Records memory actions.
Version: 0.2
*/
if (!defined('ABSPATH')) exit;

function dbs_memory_logger_activate(){
    $file = WP_CONTENT_DIR . '/dbs-library/system.json';
    if(!file_exists($file)){
        wp_mkdir_p(dirname($file));
        file_put_contents($file, json_encode([]));
    }
}
register_activation_hook(__FILE__, 'dbs_memory_logger_activate');

function dbs_memory_logger($message) {
    $log_dir = WP_CONTENT_DIR . '/dbs-library';
    if (!is_dir($log_dir)) {
        wp_mkdir_p($log_dir);
    }
    $file = $log_dir . '/system.json';
    $data = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
    $data[] = ['time' => current_time('mysql'), 'message' => $message];
    file_put_contents($file, json_encode($data));
}

function dbs_memory_menu() {
    add_menu_page('DBS Memory', 'DBS Memory', 'manage_options', 'dbs-memory', 'dbs_memory_page');
    add_submenu_page('dbs-memory', 'Memory Log', 'Memory Log', 'manage_options', 'dbs-memory', 'dbs_memory_page');
}
add_action('admin_menu', 'dbs_memory_menu');

function dbs_memory_page() {
    echo '<div class="wrap"><h1>DBS Memory Log</h1>';
    $screen = get_current_screen();
    if($screen && method_exists($screen, 'add_help_tab')){
        $screen->add_help_tab([
            'id' => 'dbs_memory_help',
            'title' => __('Usage'),
            'content' => '<p>' . esc_html__('This page lists logged system messages.', 'dbs-memory-logger') . '</p>'
        ]);
    }
    $file = WP_CONTENT_DIR . '/dbs-library/system.json';
    $entries = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
    echo '<ul>';
    foreach($entries as $e){
        echo '<li>'.esc_html($e['time'].': '.$e['message']).'</li>';
    }
    echo '</ul></div>';
}

function dbs_memory_rest() {
    register_rest_route('dbs/v1', '/log', [
        'methods' => 'POST',
        'callback' => function($request){
            $msg = sanitize_text_field($request->get_param('message'));
            dbs_memory_logger($msg);
            return ['status' => 'logged'];
        },
        'permission_callback' => '__return_true'
    ]);
    register_rest_route('dbs/v1', '/log', [
        'methods' => 'GET',
        'callback' => function(){
            $file = WP_CONTENT_DIR . '/dbs-library/system.json';
            return file_exists($file) ? json_decode(file_get_contents($file), true) : [];
        },
        'permission_callback' => '__return_true'
    ]);
}
add_action('rest_api_init', 'dbs_memory_rest');
