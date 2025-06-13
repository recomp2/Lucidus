<?php
/*
Plugin Name: DBS Memory Logger
Description: Records memory actions with viewable log and settings page.
Version: 0.3
*/
if (!defined('ABSPATH')) exit;

require_once plugin_dir_path(__FILE__).'admin/dbs-memory-logger-admin.php';

function dbs_memory_logger_activate(){
    $file = WP_CONTENT_DIR . '/dbs-library/memory-archive/system.json';
    if(!file_exists($file)){
        wp_mkdir_p(dirname($file));
        $default = [
            'prophecies'=>[],
            'scrolls'=>[],
            'badges'=>[],
            'quests'=>[],
            'behavior_tags'=>[],
            'log'=>[]
        ];
        file_put_contents($file, json_encode($default));
    }
    if(false === get_option('dbs_memory_logger_enabled')){
        add_option('dbs_memory_logger_enabled','1');
    }
    foreach([
        'dbs_memory_logger_voice' => '0',
        'dbs_memory_logger_microphone' => '0',
        'dbs_memory_logger_tts' => '0',
        'dbs_memory_logger_personality' => 'dub',
        'dbs_memory_logger_prompt' => ''
    ] as $opt => $val){
        if(false === get_option($opt)){
            add_option($opt, $val);
        }
    }
}
register_activation_hook(__FILE__, 'dbs_memory_logger_activate');

function dbs_memory_logger_log_file(){
    return WP_CONTENT_DIR . '/dbs-library/memory-archive/system.json';
}

function dbs_memory_logger($message) {
    $file = dbs_memory_logger_log_file();
    $dir = dirname($file);
    if (!is_dir($dir)) {
        wp_mkdir_p($dir);
    }
    if (file_exists($file) && filesize($file) > 1024*1024) {
        rename($file, $file . '.' . time() . '.bak');
    }
    $data = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
    if(!isset($data['log'])) $data['log'] = [];
    $data['log'][] = ['time' => current_time('mysql'), 'message' => $message];
    file_put_contents($file, json_encode($data));
}

function dbs_update_user_memory($plugin_slug, $section){
    if(!is_user_logged_in()) return;
    $uid = get_current_user_id();
    $path = WP_CONTENT_DIR.'/dbs-library/memory-archive/profiles/user_'.$uid.'.json';
    $data = file_exists($path) ? json_decode(file_get_contents($path), true) : [];
    $data['last_admin_section'] = $plugin_slug.'_'.$section;
    $data['last_change_time'] = current_time('mysql');
    if(!empty($_POST)){
        $data['last_change_saved'] = true;
    }
    file_put_contents($path, json_encode($data));
    dbs_memory_logger('Admin '.$plugin_slug.' '.$section.' accessed by '.$uid);
}


function dbs_memory_rest() {
    register_rest_route('dbs/v1', '/log', [
        'methods' => 'POST',
        'callback' => function($request){
            $msg = sanitize_text_field($request->get_param('message'));
            dbs_memory_logger($msg);
            return ['status' => 'logged'];
        },
        'permission_callback' => '__return_true'
    ]);
    register_rest_route('dbs/v1', '/log', [
        'methods' => 'GET',
        'callback' => function(){
            $file = WP_CONTENT_DIR . '/dbs-library/memory-archive/system.json';
            if(!file_exists($file)) return [];
            $data = json_decode(file_get_contents($file), true);
            return isset($data['log']) ? $data['log'] : [];
        },
        'permission_callback' => '__return_true'
    ]);
    register_rest_route('dbs/v1', '/settings', [
        'methods' => 'GET',
        'callback' => function(){
            $options = [
                'enabled' => get_option('dbs_memory_logger_enabled','1'),
                'voice' => get_option('dbs_memory_logger_voice','0'),
                'microphone' => get_option('dbs_memory_logger_microphone','0'),
                'tts' => get_option('dbs_memory_logger_tts','0'),
                'personality' => get_option('dbs_memory_logger_personality','dub'),
                'prompt' => get_option('dbs_memory_logger_prompt','')
            ];
            return $options;
        },
        'permission_callback' => '__return_true'
    ]);
    register_rest_route('dbs/v1', '/settings', [
        'methods' => 'POST',
        'callback' => function($request){
            $data = $request->get_json_params();
            update_option('dbs_memory_logger_enabled', !empty($data['enabled']) ? '1' : '0');
            update_option('dbs_memory_logger_voice', !empty($data['voice']) ? '1' : '0');
            update_option('dbs_memory_logger_microphone', !empty($data['microphone']) ? '1' : '0');
            update_option('dbs_memory_logger_tts', !empty($data['tts']) ? '1' : '0');
            update_option('dbs_memory_logger_personality', sanitize_text_field($data['personality'] ?? 'dub'));
            update_option('dbs_memory_logger_prompt', sanitize_textarea_field($data['prompt'] ?? ''));
            return ['status'=>'saved'];
        },
        'permission_callback' => '__return_true'
    ]);
}
add_action('rest_api_init', 'dbs_memory_rest');
