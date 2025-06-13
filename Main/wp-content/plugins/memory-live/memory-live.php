<?php
/*
Plugin Name: memory live
Description: Placeholder for memory-live plugin.
Version: 0.1
*/
if (!defined('ABSPATH')) exit;

function memory_live_menu(){
    add_menu_page(ucwords(str_replace('-', ' ', 'memory-live')), ucwords(str_replace('-', ' ', 'memory-live')), 'manage_options', 'memory-live', 'memory_live_page');
}
add_action('admin_menu', 'memory_live_menu');

function memory_live_page(){
    echo '<div class="wrap"><h1>'.esc_html(ucwords(str_replace('-', ' ', 'memory-live'))).'</h1></div>';
}

function memory_live_rest(){
    register_rest_route('memory-live/v1', '/ping', [
        'methods' => 'GET',
        'callback' => function(){ return ['message'=>'pong']; },
        'permission_callback' => '__return_true'
    ]);
}
add_action('rest_api_init', 'memory_live_rest');

function memory_live_shortcode(){
    return '<div class="memory-live"></div>';
}
add_shortcode('memory-live', 'memory_live_shortcode');
