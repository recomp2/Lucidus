<?php
if(!defined('ABSPATH')) exit;

function lucidus_get_user_rank($user_id = null){
    if(!$user_id) $user_id = get_current_user_id();
    return get_user_meta($user_id, 'dbs_rank', true);
}

function lucidus_internet_user_level($user_id = null){
    $rank = strtolower(lucidus_get_user_rank($user_id));
    switch($rank){
        case 'elder bastard':
        case 'elder':
            return 4;
        case 'chronic commander':
            return 3;
        case 'initiated bastard':
        case 'initiated':
            return 2;
        default:
            return 1; // newbie
    }
}

function lucidus_has_internet_access($user_id = null, $required = 2){
    if(get_option('lucidus_internet_enabled','0') !== '1') return false;
    return lucidus_internet_user_level($user_id) >= $required;
}

function lucidus_core_internet_access_page(){
    if(function_exists('dbs_update_user_memory')){
        dbs_update_user_memory('lucidus-core','internet-access');
    }
    $enabled = get_option('lucidus_internet_enabled','0')==='1';
    $feed_urls = get_option('lucidus_internet_feeds','');
    $required = get_option('lucidus_internet_rank','Initiated Bastard');
    if(isset($_POST['lucidus_test_connect'])){
        check_admin_referer('lucidus_internet');
        $resp = wp_remote_get('https://httpbin.org/get');
        $status = is_wp_error($resp) ? __('Failed','lucidus-core') : __('Success','lucidus-core');
        if(function_exists('dbs_memory_logger')){ dbs_memory_logger('Internet test run'); }
    }
    if(isset($_POST['lucidus_internet_save'])){
        check_admin_referer('lucidus_internet');
        $enabled = isset($_POST['lucidus_internet_enabled']);
        update_option('lucidus_internet_enabled', $enabled?'1':'0');
        $feed_urls = sanitize_textarea_field($_POST['lucidus_internet_feeds']);
        update_option('lucidus_internet_feeds', $feed_urls);
        $required = sanitize_text_field($_POST['lucidus_internet_rank']);
        update_option('lucidus_internet_rank',$required);
        if(function_exists('dbs_memory_logger')){
            dbs_memory_logger('Internet access settings updated');
        }
    }
    $status = $enabled ? __('Enabled','lucidus-core') : __('Disabled','lucidus-core');
    echo '<div class="wrap"><h1>Lucidus Internet Access</h1>';
    echo '<form method="post">';
    wp_nonce_field('lucidus_internet');
    echo '<p><label><input type="checkbox" name="lucidus_internet_enabled" value="1" '.($enabled?'checked':'').'/> '.esc_html__('Enable Internet Access','lucidus-core').'</label></p>';
    echo '<p><label>'.esc_html__('Feed URLs (one per line)','lucidus-core').'<br><textarea name="lucidus_internet_feeds" rows="4" class="large-text">'.esc_textarea($feed_urls).'</textarea></label></p>';
    $ranks = ['Newbie Bastard','Initiated Bastard','Chronic Commander','Elder Bastard'];
    echo '<p>'.esc_html__('Minimum Rank for GPT Access:','lucidus-core').' <select name="lucidus_internet_rank">';
    foreach($ranks as $r){ echo '<option value="'.esc_attr($r).'" '.selected($required,$r,false).'>'.esc_html($r).'</option>'; }
    echo '</select></p>';
    echo '<p><button class="button button-primary" name="lucidus_internet_save">'.esc_html__('Save Settings','lucidus-core').'</button> ';
    echo '<button class="button" name="lucidus_test_connect">'.esc_html__('Test Connection','lucidus-core').'</button></p>';
    echo '<p>'.esc_html__('Connection Status: ','lucidus-core').$status.'</p>';
    echo '</form></div>';
}


