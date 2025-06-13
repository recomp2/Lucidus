<?php
/*
Plugin Name: mood engine
Description: Placeholder for mood-engine plugin.
Version: 0.1
*/
if (!defined('ABSPATH')) exit;

function mood_engine_menu(){
    add_menu_page(ucwords(str_replace('-', ' ', 'mood-engine')), ucwords(str_replace('-', ' ', 'mood-engine')), 'manage_options', 'mood-engine', 'mood_engine_page');
}
add_action('admin_menu', 'mood_engine_menu');

function mood_engine_page(){
    if(function_exists('dbs_update_user_memory')){
        dbs_update_user_memory('mood-engine','settings');
    }
    echo '<div class="wrap"><h1>'.esc_html(ucwords(str_replace('-', ' ', 'mood-engine'))).'</h1></div>';
}

function mood_engine_rest(){
    register_rest_route('mood-engine/v1', '/ping', [
        'methods' => 'GET',
        'callback' => function(){ return ['message'=>'pong']; },
        'permission_callback' => '__return_true'
    ]);
}
add_action('rest_api_init', 'mood_engine_rest');

function mood_engine_shortcode(){
    return '<div class="mood-engine"></div>';
}
add_shortcode('mood-engine', 'mood_engine_shortcode');
