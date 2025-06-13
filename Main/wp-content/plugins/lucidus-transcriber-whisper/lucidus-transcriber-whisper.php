<?php
/*
Plugin Name: lucidus transcriber whisper
Description: Placeholder for lucidus-transcriber-whisper plugin.
Version: 0.1
*/
if (!defined('ABSPATH')) exit;

function lucidus_transcriber_whisper_menu(){
    add_menu_page(ucwords(str_replace('-', ' ', 'lucidus-transcriber-whisper')), ucwords(str_replace('-', ' ', 'lucidus-transcriber-whisper')), 'manage_options', 'lucidus-transcriber-whisper', 'lucidus_transcriber_whisper_page');
}
add_action('admin_menu', 'lucidus_transcriber_whisper_menu');

function lucidus_transcriber_whisper_page(){
    if(function_exists('dbs_update_user_memory')){
        dbs_update_user_memory('lucidus-transcriber-whisper','settings');
    }
    echo '<div class="wrap"><h1>'.esc_html(ucwords(str_replace('-', ' ', 'lucidus-transcriber-whisper'))).'</h1></div>';
}

function lucidus_transcriber_whisper_rest(){
    register_rest_route('lucidus-transcriber-whisper/v1', '/ping', [
        'methods' => 'GET',
        'callback' => function(){ return ['message'=>'pong']; },
        'permission_callback' => '__return_true'
    ]);
}
add_action('rest_api_init', 'lucidus_transcriber_whisper_rest');

function lucidus_transcriber_whisper_shortcode(){
    return '<div class="lucidus-transcriber-whisper"></div>';
}
add_shortcode('lucidus-transcriber-whisper', 'lucidus_transcriber_whisper_shortcode');
