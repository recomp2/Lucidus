<?php
/*
Plugin Name: diagnostics
Description: Diagnostics utilities for DBS.
Version: 0.2
*/
if (!defined('ABSPATH')) exit;

function diagnostics_activate(){
    $defaults = [
        'diagnostics_ping_url' => 'http://example.com',
        'diagnostics_debug' => '0',
        'diagnostics_log_level' => 'info'
    ];
    foreach($defaults as $k=>$v){
        if(false === get_option($k)){
            add_option($k,$v);
        }
    }
}
register_activation_hook(__FILE__,'diagnostics_activate');

function diagnostics_menu(){
    add_menu_page(ucwords(str_replace('-', ' ', 'diagnostics')), ucwords(str_replace('-', ' ', 'diagnostics')), 'manage_options', 'diagnostics', 'diagnostics_page');
}
add_action('admin_menu', 'diagnostics_menu');

function diagnostics_page(){
    if(function_exists('dbs_update_user_memory')){
        dbs_update_user_memory('diagnostics','settings');
    }
    if(isset($_POST['diagnostics_settings'])){
        check_admin_referer('diagnostics_settings');
        update_option('diagnostics_ping_url', sanitize_text_field($_POST['diagnostics_ping_url'] ?? ''));
        update_option('diagnostics_debug', isset($_POST['diagnostics_debug']) ? '1' : '0');
        update_option('diagnostics_log_level', sanitize_text_field($_POST['diagnostics_log_level'] ?? 'info'));
        if(function_exists('dbs_memory_logger')){
            dbs_memory_logger('Diagnostics settings updated');
        }
        echo '<div class="updated notice"><p>Settings saved.</p></div>';
    }
    $ping = esc_attr(get_option('diagnostics_ping_url','http://example.com')); 
    $debug = get_option('diagnostics_debug','0');
    $level = get_option('diagnostics_log_level','info');
    $levels = ['info','warning','error'];
    echo '<div class="wrap"><h1>'.esc_html(ucwords(str_replace('-', ' ', 'diagnostics'))).'</h1>';
    echo '<form method="post">';
    wp_nonce_field('diagnostics_settings');
    echo '<input type="hidden" name="diagnostics_settings" value="1" />';
    echo '<p><label>'.esc_html__('Ping URL','diagnostics').'<br><input type="text" name="diagnostics_ping_url" value="'.$ping.'" class="regular-text" /></label></p>';
    echo '<p><label><input type="checkbox" name="diagnostics_debug" value="1" '.checked('1',$debug,false).' /> '.esc_html__('Enable Debug Logging','diagnostics').'</label></p>';
    echo '<p><label>'.esc_html__('Log Level','diagnostics').'<select name="diagnostics_log_level">';
    foreach($levels as $l){
        echo '<option value="'.esc_attr($l).'" '.selected($level,$l,false).'>'.esc_html(ucfirst($l)).'</option>';
    }
    echo '</select></label></p>';
    submit_button();
    echo '</form></div>';
}

function diagnostics_rest(){
    register_rest_route('diagnostics/v1', '/ping', [
        'methods' => 'GET',
        'callback' => function(){ return ['message'=>'pong']; },
        'permission_callback' => '__return_true'
    ]);
    register_rest_route('diagnostics/v1', '/settings', [
        'methods' => 'GET',
        'callback' => function(){
            return [
                'ping_url'=>get_option('diagnostics_ping_url','http://example.com'),
                'debug'=>get_option('diagnostics_debug','0')==='1',
                'log_level'=>get_option('diagnostics_log_level','info')
            ];
        },
        'permission_callback' => '__return_true'
    ]);
    register_rest_route('diagnostics/v1', '/settings', [
        'methods' => 'POST',
        'callback' => function($request){
            $data = $request->get_json_params();
            update_option('diagnostics_ping_url', sanitize_text_field($data['ping_url'] ?? ''));
            update_option('diagnostics_debug', !empty($data['debug']) ? '1' : '0');
            update_option('diagnostics_log_level', sanitize_text_field($data['log_level'] ?? 'info'));
            if(function_exists('dbs_memory_logger')){ dbs_memory_logger('Diagnostics settings updated via API'); }
            return ['status'=>'saved'];
        },
        'permission_callback' => '__return_true'
    ]);
}
add_action('rest_api_init', 'diagnostics_rest');

function diagnostics_shortcode(){
    return '<div class="diagnostics"></div>';
}
add_shortcode('diagnostics', 'diagnostics_shortcode');
