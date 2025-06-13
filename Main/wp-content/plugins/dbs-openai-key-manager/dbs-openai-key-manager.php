<?php
/*
Plugin Name: DBS OpenAI Key Manager
Description: Manage the OpenAI API key used by DBS services.
Version: 0.3
*/
if (!defined('ABSPATH')) exit;

function dbs_openai_key_manager_activate(){
    if(false === get_option('dbs_openai_key')){
        add_option('dbs_openai_key', '');
    }
}
register_activation_hook(__FILE__, 'dbs_openai_key_manager_activate');

function dbs_openai_key_manager_menu(){
    add_menu_page('OpenAI Key Manager', 'OpenAI Key', 'manage_options', 'dbs-openai-key-manager', 'dbs_openai_key_manager_page');
}
add_action('admin_menu', 'dbs_openai_key_manager_menu');

function dbs_openai_key_manager_page(){
    if(isset($_POST['openai_key'])){
        check_admin_referer('dbs_openai_key');
        update_option('dbs_openai_key', sanitize_text_field($_POST['openai_key']));
        if(function_exists('dbs_memory_logger')){
            dbs_memory_logger('OpenAI key updated');
        }
        echo '<div class="updated notice"><p>Key saved.</p></div>';
    }
    $key = esc_attr(get_option('dbs_openai_key', ''));
    echo '<div class="wrap"><h1>OpenAI Key Manager</h1>';
    $screen = get_current_screen();
    if($screen && method_exists($screen, 'add_help_tab')){
        $screen->add_help_tab([
            'id' => 'dbs_openai_help',
            'title' => __('Usage'),
            'content' => '<p>' . esc_html__('Store your OpenAI API key here for all DBS plugins.', 'dbs-openai-key-manager') . '</p>'
        ]);
    }
    echo '<form method="post">';
    wp_nonce_field('dbs_openai_key');
    echo '<input type="text" name="openai_key" value="'.$key.'" style="width:40em" />';
    echo '<p><button class="button button-primary">Save Key</button></p>';
    echo '</form></div>';
}

function dbs_openai_key_manager_rest(){
    register_rest_route('dbs-openai-key-manager/v1', '/key', [
        'methods' => 'GET',
        'callback' => function(){
            return ['key'=>get_option('dbs_openai_key','')];
        },
        'permission_callback' => '__return_true'
    ]);
    register_rest_route('dbs-openai-key-manager/v1', '/key', [
        'methods' => 'POST',
        'callback' => function($request){
            $key = sanitize_text_field($request->get_param('key'));
            update_option('dbs_openai_key', $key);
            if(function_exists('dbs_memory_logger')){ dbs_memory_logger('OpenAI key updated via API'); }
            return ['status'=>'saved'];
        },
        'permission_callback' => '__return_true'
    ]);
}
add_action('rest_api_init', 'dbs_openai_key_manager_rest');

function dbs_openai_key_manager_shortcode(){
    return '<div class="dbs-openai-key-manager"></div>';
}
add_shortcode('dbs-openai-key-manager', 'dbs_openai_key_manager_shortcode');
