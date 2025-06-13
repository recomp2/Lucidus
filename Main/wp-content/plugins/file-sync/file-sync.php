<?php
/*
Plugin Name: file sync
Description: Placeholder for file-sync plugin.
Version: 0.1
*/
if (!defined('ABSPATH')) exit;

function file_sync_menu(){
    add_menu_page(ucwords(str_replace('-', ' ', 'file-sync')), ucwords(str_replace('-', ' ', 'file-sync')), 'manage_options', 'file-sync', 'file_sync_page');
}
add_action('admin_menu', 'file_sync_menu');

function file_sync_page(){
    if(function_exists('dbs_update_user_memory')){
        dbs_update_user_memory('file-sync','settings');
    }
    echo '<div class="wrap"><h1>'.esc_html(ucwords(str_replace('-', ' ', 'file-sync'))).'</h1></div>';
}

function file_sync_rest(){
    register_rest_route('file-sync/v1', '/ping', [
        'methods' => 'GET',
        'callback' => function(){ return ['message'=>'pong']; },
        'permission_callback' => '__return_true'
    ]);
}
add_action('rest_api_init', 'file_sync_rest');

function file_sync_shortcode(){
    return '<div class="file-sync"></div>';
}
add_shortcode('file-sync', 'file_sync_shortcode');
