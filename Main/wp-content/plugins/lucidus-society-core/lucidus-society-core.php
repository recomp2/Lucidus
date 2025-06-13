<?php
/*
Plugin Name: lucidus society core
Description: Placeholder for lucidus-society-core plugin.
Version: 0.1
*/
if (!defined('ABSPATH')) exit;

function lucidus_society_core_menu(){
    add_menu_page(ucwords(str_replace('-', ' ', 'lucidus-society-core')), ucwords(str_replace('-', ' ', 'lucidus-society-core')), 'manage_options', 'lucidus-society-core', 'lucidus_society_core_page');
}
add_action('admin_menu', 'lucidus_society_core_menu');

function lucidus_society_core_page(){
    echo '<div class="wrap"><h1>'.esc_html(ucwords(str_replace('-', ' ', 'lucidus-society-core'))).'</h1></div>';
}

function lucidus_society_core_rest(){
    register_rest_route('lucidus-society-core/v1', '/ping', [
        'methods' => 'GET',
        'callback' => function(){ return ['message'=>'pong']; },
        'permission_callback' => '__return_true'
    ]);
}
add_action('rest_api_init', 'lucidus_society_core_rest');

function lucidus_society_core_shortcode(){
    return '<div class="lucidus-society-core"></div>';
}
add_shortcode('lucidus-society-core', 'lucidus_society_core_shortcode');
