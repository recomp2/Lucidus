<?php
/*
Plugin Name: Lucidus Terminal Pro
Description: Terminal interface with voice options.
Version: 0.2
*/
if (!defined('ABSPATH')) exit;

require_once plugin_dir_path(__FILE__).'admin/lucidus-terminal-pro-admin.php';

function ltp_activate(){
    if(false === get_option('ltp_voice')){
        add_option('ltp_voice','0');
    }
}
register_activation_hook(__FILE__, 'ltp_activate');

function ltp_register_menu() {
    add_menu_page('Lucidus Terminal', 'Lucidus Terminal', 'manage_options', 'lucidus-terminal', 'ltp_admin_page');
}
add_action('admin_menu', 'ltp_register_menu');

function ltp_scripts(){
    wp_enqueue_script('lucidus-terminal-js', plugin_dir_url(__FILE__).'js/terminal.js', [], '1.0', true);
}
add_action('wp_enqueue_scripts','ltp_scripts');

function ltp_rest_init() {
    register_rest_route('lucidus/v1', '/ping', [
        'methods' => 'GET',
        'callback' => function(){ return ['message' => 'pong']; }
    ]);
    register_rest_route('lucidus/v1', '/settings', [
        'methods' => 'GET',
        'callback' => function(){
            return [
                'voice' => get_option('ltp_voice','0')==='1',
                'memory' => get_option('ltp_memory','0')==='1'
            ];
        },
        'permission_callback' => '__return_true'
    ]);
    register_rest_route('lucidus/v1', '/settings', [
        'methods' => 'POST',
        'callback' => function($request){
            update_option('ltp_voice', $request->get_param('voice') ? '1':'0');
            update_option('ltp_memory', $request->get_param('memory') ? '1':'0');
            return ['status'=>'saved'];
        },
        'permission_callback' => '__return_true'
    ]);
}
add_action('rest_api_init', 'ltp_rest_init');

function ltp_shortcode() {
    return '<div id="lucidus-terminal"></div>';
}
add_shortcode('lucidus_terminal', 'ltp_shortcode');
