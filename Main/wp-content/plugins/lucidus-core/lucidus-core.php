<?php
/*
Plugin Name: Lucidus Core
Description: Core prophecy logic for the DBS universe.
Version: 0.3
*/
if (!defined('ABSPATH')) exit;

function lucidus_core_activate(){
    $file = WP_CONTENT_DIR . '/dbs-library/system.json';
    if(!file_exists($file)){
        wp_mkdir_p(dirname($file));
        file_put_contents($file, json_encode([]));
    }
}
register_activation_hook(__FILE__, 'lucidus_core_activate');

// Utility to read prophecy log
function lucidus_core_get_prophecies(){
    $file = WP_CONTENT_DIR . '/dbs-library/system.json';
    if(!file_exists($file)) return [];
    $data = json_decode(file_get_contents($file), true);
    return is_array($data) ? $data : [];
}

// Utility to save a new prophecy
function lucidus_core_save_prophecy($text){
    $entries = lucidus_core_get_prophecies();
    $entries[] = ['time'=>current_time('mysql'),'prophecy'=>$text];
    file_put_contents(WP_CONTENT_DIR . '/dbs-library/system.json', json_encode($entries));
    if(function_exists('dbs_memory_logger')){
        dbs_memory_logger('Prophecy saved: '.$text);
    }
}

function lucidus_core_menu(){
    add_menu_page(ucwords(str_replace('-', ' ', 'lucidus-core')), ucwords(str_replace('-', ' ', 'lucidus-core')), 'manage_options', 'lucidus-core', 'lucidus_core_page');
}
add_action('admin_menu', 'lucidus_core_menu');

function lucidus_core_page(){
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
