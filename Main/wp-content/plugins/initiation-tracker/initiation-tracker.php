<?php
/*
Plugin Name: initiation tracker
Description: Placeholder for initiation-tracker plugin.
Version: 0.1
*/
if (!defined('ABSPATH')) exit;

function initiation_tracker_menu(){
    add_menu_page(ucwords(str_replace('-', ' ', 'initiation-tracker')), ucwords(str_replace('-', ' ', 'initiation-tracker')), 'manage_options', 'initiation-tracker', 'initiation_tracker_page');
}
add_action('admin_menu', 'initiation_tracker_menu');

function initiation_tracker_page(){
    if(function_exists('dbs_update_user_memory')){
        dbs_update_user_memory('initiation-tracker','settings');
    }
    echo '<div class="wrap"><h1>'.esc_html(ucwords(str_replace('-', ' ', 'initiation-tracker'))).'</h1></div>';
}

function initiation_tracker_rest(){
    register_rest_route('initiation-tracker/v1', '/ping', [
        'methods' => 'GET',
        'callback' => function(){ return ['message'=>'pong']; },
        'permission_callback' => '__return_true'
    ]);
}
add_action('rest_api_init', 'initiation_tracker_rest');

function initiation_tracker_shortcode(){
    return '<div class="initiation-tracker"></div>';
}
add_shortcode('initiation-tracker', 'initiation_tracker_shortcode');
