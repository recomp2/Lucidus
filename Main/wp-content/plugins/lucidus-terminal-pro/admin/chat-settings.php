<?php
if (!defined('ABSPATH')) exit;

function ltp_chat_settings_page(){
    if(!current_user_can('manage_options')) return;
    if(function_exists('dbs_update_user_memory')){
        dbs_update_user_memory('lucidus-terminal-pro','chat-settings');
    }
    if(isset($_POST['ltp_chat_settings'])){
        check_admin_referer('ltp_chat_settings');
        update_option('ltp_chat_memory', isset($_POST['ltp_chat_memory'])? '1':'0');
        update_option('ltp_chat_model', sanitize_text_field($_POST['ltp_chat_model'] ?? 'gpt-4o'));
        echo '<div class="updated notice"><p>'.esc_html__('Chat settings saved.','ltp').'</p></div>';
    }

    $chat_memory = get_option('ltp_chat_memory','1')==='1';
    $chat_model = get_option('ltp_chat_model','gpt-4o');
    $current_user = wp_get_current_user();
    $req = new WP_REST_Request('GET','/');
    $req->set_param('user_id', $current_user->ID);
    $log = ltp_rest_chat_load_log($req);
    echo '<div class="wrap"><h1>'.esc_html__('Lucidus Chat Settings','ltp').'</h1>';
    echo '<form method="post">';
    wp_nonce_field('ltp_chat_settings');
    echo '<input type="hidden" name="ltp_chat_settings" value="1" />';
    echo '<p><label><input type="checkbox" name="ltp_chat_memory" value="1" '.($chat_memory?'checked':'').'/> '.esc_html__('Enable Chat Memory','ltp').'</label></p>';
    echo '<p>'.esc_html__('Chat Model','ltp').': <select name="ltp_chat_model">';
    foreach(['gpt-4o','whisper','prophecy'] as $m){
        echo '<option value="'.esc_attr($m).'" '.selected($chat_model,$m,false).'>'.esc_html(ucfirst($m)).'</option>';
    }
    echo '</select></p>';
    echo '<p><button class="button button-primary">'.esc_html__('Save Settings','ltp').'</button></p>';
    echo '</form>';

    if($log){
        echo '<h2>'.esc_html__('Recent Chats','ltp').'</h2><table class="widefat"><tbody>';
        $count = 0;
        foreach(array_reverse($log) as $entry){
            if($count++>=10) break;
            echo '<tr><td>'.esc_html($entry['time']).'</td><td>'.esc_html($entry['message']).'</td></tr>';
        }
        echo '</tbody></table>';
    }
    echo '</div>';
}
