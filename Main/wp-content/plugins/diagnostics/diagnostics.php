<?php
/*
Plugin Name: diagnostics
Description: Placeholder for diagnostics plugin.
Version: 0.1
*/
if (!defined('ABSPATH')) exit;

function diagnostics_menu(){
    add_menu_page(ucwords(str_replace('-', ' ', 'diagnostics')), ucwords(str_replace('-', ' ', 'diagnostics')), 'manage_options', 'diagnostics', 'diagnostics_page');
}
add_action('admin_menu', 'diagnostics_menu');

function diagnostics_page(){
    echo '<div class="wrap"><h1>'.esc_html(ucwords(str_replace('-', ' ', 'diagnostics'))).'</h1></div>';
}

function diagnostics_rest(){
    register_rest_route('diagnostics/v1', '/ping', [
        'methods' => 'GET',
        'callback' => function(){ return ['message'=>'pong']; },
        'permission_callback' => '__return_true'
    ]);
}
add_action('rest_api_init', 'diagnostics_rest');

function diagnostics_shortcode(){
    return '<div class="diagnostics"></div>';
}
add_shortcode('diagnostics', 'diagnostics_shortcode');
