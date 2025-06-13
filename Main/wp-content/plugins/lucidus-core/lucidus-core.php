<?php
/*
Plugin Name: Lucidus Core
Description: Core prophecy logic for the DBS universe.
Version: 0.3
*/
if (!defined('ABSPATH')) exit;

function lucidus_core_activate(){
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
    if(!wp_next_scheduled('lucidus_feed_cron')){
        wp_schedule_event(time(), 'hourly', 'lucidus_feed_cron');
    }
    if(false === get_option('lucidus_internet_enabled')){
        add_option('lucidus_internet_enabled','0');
    }
    if(false === get_option('lucidus_internet_rank')){
        add_option('lucidus_internet_rank','Initiated Bastard');
    }
    if(false === get_option('lucidus_internet_feeds')){
        add_option('lucidus_internet_feeds','');
    }
}
function lucidus_core_deactivate(){
    wp_clear_scheduled_hook('lucidus_feed_cron');
}
register_activation_hook(__FILE__, 'lucidus_core_activate');
register_deactivation_hook(__FILE__, 'lucidus_core_deactivate');

// Utility to read prophecy log
function lucidus_core_get_prophecies(){
    $file = WP_CONTENT_DIR . '/dbs-library/memory-archive/system.json';
    if(!file_exists($file)) return [];
    $data = json_decode(file_get_contents($file), true);
    if(is_array($data) && isset($data['prophecies'])){
        return $data['prophecies'];
    }
    return [];
}

// Utility to save a new prophecy
function lucidus_core_save_prophecy($text){
    $file = WP_CONTENT_DIR . '/dbs-library/memory-archive/system.json';
    $data = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
    if(!isset($data['prophecies'])) $data['prophecies'] = [];
    $entry = ['time'=>current_time('mysql'),'prophecy'=>$text];
    $data['prophecies'][] = $entry;
    file_put_contents($file, json_encode($data));

    // also store per-user history
    if(is_user_logged_in()){
        $uid = get_current_user_id();
        $upath = WP_CONTENT_DIR . '/dbs-library/memory-archive/profiles/user_'.$uid.'.json';
        $u = file_exists($upath) ? json_decode(file_get_contents($upath), true) : [];
        if(!isset($u['prophecy_history'])){ $u['prophecy_history'] = []; }
        $u['prophecy_history'][] = $entry;
        file_put_contents($upath, json_encode($u));
    }

    if(function_exists('dbs_memory_logger')){
        dbs_memory_logger('Prophecy saved: '.$text);
    }
}

function lucidus_core_menu(){
    add_menu_page(ucwords(str_replace('-', ' ', 'lucidus-core')), ucwords(str_replace('-', ' ', 'lucidus-core')), 'manage_options', 'lucidus-core', 'lucidus_core_page');
}
add_action('admin_menu', 'lucidus_core_menu');

function lucidus_core_page(){
    if(function_exists('dbs_update_user_memory')){
        dbs_update_user_memory('lucidus-core','prophecy');
    }
    if(isset($_POST['lucidus_new_prophecy'])){
        check_admin_referer('lucidus_core_save');
        $text = sanitize_text_field($_POST['lucidus_new_prophecy']);
        lucidus_core_save_prophecy($text);
        echo '<div class="updated notice"><p>Prophecy saved.</p></div>';
    }
    echo '<div class="wrap"><h1>'.esc_html(ucwords(str_replace('-', ' ', 'lucidus-core'))).'</h1>';
    $screen = get_current_screen();
    if($screen && method_exists($screen, 'add_help_tab')){
        $screen->add_help_tab([
            'id' => 'lucidus_core_help',
            'title' => __('Usage'),
            'content' => '<p>' . esc_html__('Submit prophecies to store them in the memory archive.', 'lucidus-core') . '</p>'
        ]);
    }
    echo '<form method="post">';
    wp_nonce_field('lucidus_core_save');
    echo '<textarea name="lucidus_new_prophecy" rows="4" style="width:100%"></textarea>';
    echo '<p><button class="button button-primary">Save Prophecy</button></p>';
    echo '</form></div>';
    $entries = array_reverse(lucidus_core_get_prophecies());
    if($entries){
        echo '<h2>Latest Prophecies</h2><ul>';
        foreach($entries as $e){
            echo '<li>'.esc_html($e['time'].': '.$e['prophecy']).'</li>';
        }
        echo '</ul>';
    }
}

function lucidus_core_rest(){
    register_rest_route('lucidus-core/v1', '/prophecy', [
        'methods' => 'GET',
        'callback' => function(){
            $entries = lucidus_core_get_prophecies();
            return end($entries);
        },
        'permission_callback' => '__return_true'
    ]);
    register_rest_route('lucidus-core/v1', '/prophecy', [
        'methods' => 'POST',
        'callback' => function($request){
            $text = sanitize_text_field($request->get_param('text'));
            lucidus_core_save_prophecy($text);
            return ['status'=>'saved'];
        },
        'permission_callback' => '__return_true'
    ]);
}
add_action('rest_api_init', 'lucidus_core_rest');

function lucidus_core_shortcode(){
    $entries = lucidus_core_get_prophecies();
    $last = end($entries);
    $text = $last ? esc_html($last['prophecy']) : 'No prophecy yet.';
    return '<div class="lucidus-prophecy">'.$text.'</div>';
}
add_shortcode('lucidus-core', 'lucidus_core_shortcode');

// Feed Manager integration
require_once plugin_dir_path(__FILE__).'includes/feed-handler.php';
require_once plugin_dir_path(__FILE__).'includes/internet-access.php';
if(is_admin()){
    require_once plugin_dir_path(__FILE__).'admin/feed-settings.php';
    require_once plugin_dir_path(__FILE__).'admin/feed-diagnostics.php';
    require_once plugin_dir_path(__FILE__).'admin/memory-injection.php';
}

function lucidus_feed_cron_task(){
    $settings = get_option('lucidus_feed_settings');
    if(!$settings || $settings['frequency'] === 'manual') return;
    lucidus_feed_fetch_entries();
}
add_action('lucidus_feed_cron','lucidus_feed_cron_task');

function lucidus_core_feed_menu(){
    add_menu_page('Lucidus Feeds','Lucidus Feeds','manage_options','lucidus-feeds','lucidus_core_feed_settings_page');
    add_submenu_page('lucidus-feeds','Feed Settings','Feed Settings','manage_options','lucidus-feeds','lucidus_core_feed_settings_page');
    add_submenu_page('lucidus-feeds','Feed Logs','Feed Logs','manage_options','lucidus-feed-logs','lucidus_core_feed_diagnostics_page');
    add_submenu_page('lucidus-feeds','Memory Injection Settings','Memory Injection Settings','manage_options','lucidus-memory-injection','lucidus_core_memory_injection_page');
    add_menu_page('Lucidus Internet Access','Lucidus Internet','manage_options','lucidus-internet','lucidus_core_internet_access_page');
}
add_action('admin_menu','lucidus_core_feed_menu');

function lucidus_feed_summary_shortcode(){
    $entries = lucidus_feed_fetch_entries();
    $out = '<ul class="lucidus-feed-summary">';
    foreach(array_slice($entries,0,5) as $e){
        $out .= '<li>'.esc_html($e['title']).'</li>';
    }
    $out .= '</ul>';
    return $out;
}
add_shortcode('lucidus_feed_summary','lucidus_feed_summary_shortcode');

function lucidus_feed_status_shortcode(){
    $settings = lucidus_feed_get_settings();
    $status = [
        'News: '.($settings['enable_news']?'on':'off'),
        'Weather: '.($settings['enable_weather']?'on':'off'),
        'Astrology: '.($settings['enable_astrology']?'on':'off'),
        'External: '.($settings['enable_external']?'on':'off')
    ];
    return '<div class="lucidus-feed-status">'.esc_html(implode(' | ',$status)).'</div>';
}
add_shortcode('lucidus_feed_status','lucidus_feed_status_shortcode');
