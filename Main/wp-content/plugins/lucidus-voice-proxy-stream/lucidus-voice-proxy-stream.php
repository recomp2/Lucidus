<?php
/*
Plugin Name: lucidus voice proxy stream
Description: Placeholder for lucidus-voice-proxy-stream plugin.
Version: 0.1
*/
if (!defined('ABSPATH')) exit;

function lucidus_voice_proxy_stream_menu(){
    add_menu_page(ucwords(str_replace('-', ' ', 'lucidus-voice-proxy-stream')), ucwords(str_replace('-', ' ', 'lucidus-voice-proxy-stream')), 'manage_options', 'lucidus-voice-proxy-stream', 'lucidus_voice_proxy_stream_page');
}
add_action('admin_menu', 'lucidus_voice_proxy_stream_menu');

function lucidus_voice_proxy_stream_page(){
    echo '<div class="wrap"><h1>'.esc_html(ucwords(str_replace('-', ' ', 'lucidus-voice-proxy-stream'))).'</h1></div>';
}

function lucidus_voice_proxy_stream_rest(){
    register_rest_route('lucidus-voice-proxy-stream/v1', '/ping', [
        'methods' => 'GET',
        'callback' => function(){ return ['message'=>'pong']; },
        'permission_callback' => '__return_true'
    ]);
}
add_action('rest_api_init', 'lucidus_voice_proxy_stream_rest');

function lucidus_voice_proxy_stream_shortcode(){
    return '<div class="lucidus-voice-proxy-stream"></div>';
}
add_shortcode('lucidus-voice-proxy-stream', 'lucidus_voice_proxy_stream_shortcode');
