<?php
/*
Plugin Name: Lucidus Terminal Pro
Description: Terminal interface with voice options.
Version: 0.2
*/
if (!defined('ABSPATH')) exit;

require_once plugin_dir_path(__FILE__).'admin/lucidus-terminal-pro-admin.php';
require_once plugin_dir_path(__FILE__).'admin/chat-settings.php';

function ltp_activate(){
    $defaults = [
        'ltp_voice' => '0',
        'ltp_memory' => '0',
        'ltp_chat_memory' => '1',
        'ltp_chat_model' => 'gpt-4o'
    ];
    foreach($defaults as $k=>$v){
        if(false === get_option($k)){
            add_option($k,$v);
        }
    }
    $dir = WP_CONTENT_DIR.'/dbs-library/memory-archive';
    if(!file_exists($dir)){
        wp_mkdir_p($dir);
    }
}
register_activation_hook(__FILE__, 'ltp_activate');

function ltp_register_menu() {
    add_menu_page('Lucidus Terminal', 'Lucidus Terminal', 'manage_options', 'lucidus-terminal', 'ltp_admin_page');
    add_submenu_page('lucidus-terminal','Chat Settings','Chat Settings','manage_options','lucidus-terminal-chat','ltp_chat_settings_page');
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
                'memory' => get_option('ltp_memory','0')==='1',
                'chat_model' => get_option('ltp_chat_model','gpt-4o')
            ];
        },
        'permission_callback' => '__return_true'
    ]);
    register_rest_route('lucidus/v1', '/settings', [
        'methods' => 'POST',
        'callback' => function($request){
            update_option('ltp_voice', $request->get_param('voice') ? '1':'0');
            update_option('ltp_memory', $request->get_param('memory') ? '1':'0');
            if($request->get_param('chat_model')){
                update_option('ltp_chat_model', sanitize_text_field($request->get_param('chat_model')));
            }
            return ['status'=>'saved'];
        },
        'permission_callback' => '__return_true'
    ]);

    register_rest_route('lucidus/v1', '/chat/stream', [
        'methods' => 'POST',
        'callback' => 'ltp_rest_chat_stream',
        'permission_callback' => '__return_true'
    ]);
    register_rest_route('lucidus/v1', '/chat/load-log', [
        'methods' => 'GET',
        'callback' => 'ltp_rest_chat_load_log',
        'permission_callback' => '__return_true'
    ]);
    register_rest_route('lucidus/v1', '/chat/public-feed', [
        'methods' => 'GET',
        'callback' => 'ltp_rest_chat_public_feed',
        'permission_callback' => '__return_true'
    ]);
}
add_action('rest_api_init', 'ltp_rest_init');

function ltp_shortcode() {
    return '<div id="lucidus-terminal"></div>';
}
add_shortcode('lucidus_terminal', 'ltp_shortcode');

function ltp_get_user_log_path($user_id){
    $dir = WP_CONTENT_DIR.'/dbs-library/memory-archive';
    return $dir.'/chatlog_'.$user_id.'.json';
}

function ltp_append_chat($user_id,$message){
    $path = ltp_get_user_log_path($user_id);
    $logs = file_exists($path) ? json_decode(file_get_contents($path), true) : [];
    $entry = ['time'=>current_time('mysql'),'message'=>$message];
    $logs[] = $entry;
    file_put_contents($path, json_encode($logs));

    $profile = WP_CONTENT_DIR.'/dbs-library/memory-archive/profiles/user_'.$user_id.'.json';
    $pdata = file_exists($profile) ? json_decode(file_get_contents($profile), true) : [];
    $pdata['last_message'] = $message;
    $words = array_slice(preg_split('/\s+/', $message),0,5);
    $topic = implode(' ', $words);
    $pdata['topics_discussed'][] = $topic;
    $pdata['chat_flags'][] = 'chat';
    file_put_contents($profile, json_encode($pdata));
}

function ltp_rest_chat_stream($request){
    $user = absint($request->get_param('user_id'));
    $message = sanitize_text_field($request->get_param('message'));
    if(!$user || $message===''){ return ['error'=>'missing']; }
    if(!function_exists('lucidus_has_internet_access') || !lucidus_has_internet_access($user,3)){
        return ['error'=>'restricted'];
    }
    ltp_append_chat($user,$message);
    return ['status'=>'logged'];
}

function ltp_rest_chat_load_log($request){
    $user = absint($request->get_param('user_id'));
    $path = ltp_get_user_log_path($user);
    if(!file_exists($path)) return [];
    return json_decode(file_get_contents($path), true);
}

function ltp_rest_chat_public_feed(){
    $dir = WP_CONTENT_DIR.'/dbs-library/memory-archive';
    $files = glob($dir.'/chatlog_*.json');
    $feed = [];
    foreach($files as $f){
        $data = json_decode(file_get_contents($f), true);
        if($data){
            $feed = array_merge($feed,$data);
        }
    }
    usort($feed,function($a,$b){ return strtotime($b['time'])-strtotime($a['time']); });
    return array_slice($feed,0,50);
}
