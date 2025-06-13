<?php
/*
Plugin Name: Lucidus Terminal Pro
Description: Minimal skeleton for Lucidus terminal.
Version: 0.1
*/
if (!defined('ABSPATH')) exit;

function ltp_register_menu() {
    add_menu_page('Lucidus Terminal', 'Lucidus Terminal', 'manage_options', 'lucidus-terminal', 'ltp_terminal_page');
}
add_action('admin_menu', 'ltp_register_menu');

function ltp_scripts(){
    wp_enqueue_script('lucidus-terminal-js', plugin_dir_url(__FILE__).'js/terminal.js', [], '1.0', true);
}
add_action('wp_enqueue_scripts','ltp_scripts');

function ltp_terminal_page() {
    echo '<div class="wrap"><h1>Lucidus Terminal</h1></div>';
}

function ltp_rest_init() {
    register_rest_route('lucidus/v1', '/ping', [
        'methods' => 'GET',
        'callback' => function(){ return ['message' => 'pong']; }
    ]);
}
add_action('rest_api_init', 'ltp_rest_init');

function ltp_shortcode() {
    return '<div id="lucidus-terminal"></div>';
}
add_shortcode('lucidus_terminal', 'ltp_shortcode');
