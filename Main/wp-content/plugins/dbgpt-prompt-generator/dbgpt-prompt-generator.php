<?php
/*
Plugin Name: DBGPT Prompt Generator
Description: Generates creative writing prompts using the DBGPT API.
Version: 0.1
*/
if (!defined('ABSPATH')) exit;

function dbgpt_prompt_generator_activate(){
    $defaults = [
        'dbgpt_api_key' => '',
        'dbgpt_endpoint' => 'https://api.dbgpt.example.com/v1/prompt'
    ];
    foreach($defaults as $k=>$v){
        if(false === get_option($k)){
            add_option($k,$v);
        }
    }
}
register_activation_hook(__FILE__,'dbgpt_prompt_generator_activate');

function dbgpt_prompt_generator_menu(){
    add_menu_page('DBGPT Prompt','DBGPT Prompt','manage_options','dbgpt-prompt-generator','dbgpt_prompt_generator_page');
}
add_action('admin_menu','dbgpt_prompt_generator_menu');

function dbgpt_prompt_generator_page(){
    if(function_exists('dbs_update_user_memory')){
        dbs_update_user_memory('dbgpt-prompt-generator','settings');
    }
    if(isset($_POST['dbgpt_prompt_settings'])){
        check_admin_referer('dbgpt_prompt_settings');
        update_option('dbgpt_api_key', sanitize_text_field($_POST['dbgpt_api_key'] ?? ''));
        update_option('dbgpt_endpoint', esc_url_raw($_POST['dbgpt_endpoint'] ?? ''));
        echo '<div class="updated notice"><p>Settings saved.</p></div>';
    }
    $key = esc_attr(get_option('dbgpt_api_key',''));
    $endpoint = esc_attr(get_option('dbgpt_endpoint','https://api.dbgpt.example.com/v1/prompt'));
    echo '<div class="wrap"><h1>DBGPT Prompt Generator</h1><form method="post">';
    wp_nonce_field('dbgpt_prompt_settings');
    echo '<input type="hidden" name="dbgpt_prompt_settings" value="1" />';
    echo '<p><label>API Key<br><input type="text" name="dbgpt_api_key" value="'.$key.'" class="regular-text" /></label></p>';
    echo '<p><label>Endpoint URL<br><input type="text" name="dbgpt_endpoint" value="'.$endpoint.'" class="regular-text" /></label></p>';
    submit_button();
    echo '</form></div>';
}

function dbgpt_prompt_generator_scripts(){
    wp_enqueue_script('dbgpt-prompt-js', plugins_url('prompt.js', __FILE__), ['jquery'], '1.0', true);
    wp_localize_script('dbgpt-prompt-js','dbgptPrompt',{ 'restUrl'=>rest_url('dbgpt-prompt/v1/generate') });
    wp_enqueue_style('dbgpt-prompt-css', plugins_url('prompt.css', __FILE__), [], '1.0');
}
add_action('wp_enqueue_scripts','dbgpt_prompt_generator_scripts');

function dbgpt_prompt_generator_rest(){
    register_rest_route('dbgpt-prompt/v1','/generate',[
        'methods'=>'GET',
        'callback'=>'dbgpt_prompt_generator_fetch',
        'permission_callback'=>'__return_true'
    ]);
}
add_action('rest_api_init','dbgpt_prompt_generator_rest');

function dbgpt_prompt_generator_fetch(){
    $endpoint = get_option('dbgpt_endpoint');
    $key = get_option('dbgpt_api_key');
    $args = [
        'headers' => [
            'Authorization' => 'Bearer '.$key
        ]
    ];
    $response = wp_remote_get($endpoint, $args);
    if(is_wp_error($response)){
        return ['error'=>$response->get_error_message()];
    }
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body,true);
    return $data ?: ['prompt'=>$body];
}

function dbgpt_prompt_generator_shortcode(){
    return '<div id="dbgpt-prompt"><button id="dbgpt-generate" class="button">Generate Prompt</button><pre id="dbgpt-output"></pre></div>';
}
add_shortcode('dbgpt-prompt','dbgpt_prompt_generator_shortcode');
?>
