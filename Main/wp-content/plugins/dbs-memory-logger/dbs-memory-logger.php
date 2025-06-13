<?php
/*
Plugin Name: DBS Memory Logger
Description: Records memory actions with viewable log and settings page.
Version: 0.3
*/
if (!defined('ABSPATH')) exit;

require_once plugin_dir_path(__FILE__).'admin/dbs-memory-logger-admin.php';

function dbs_memory_logger_activate(){
    $file = WP_CONTENT_DIR . '/dbs-library/system.json';
    if(!file_exists($file)){
        wp_mkdir_p(dirname($file));
        file_put_contents($file, json_encode([]));
    }
    if(false === get_option('dbs_memory_logger_enabled')){
        add_option('dbs_memory_logger_enabled','1');
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
