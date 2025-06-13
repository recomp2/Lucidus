<?php
/*
Plugin Name: Lucidus Panel
Description: Control panel for Lucidus voice and terminal settings.
Version: 0.3
*/
if (!defined('ABSPATH')) exit;

function lucidus_panel_activate(){
    if(false === get_option('lucidus_voice_enabled')){
        add_option('lucidus_voice_enabled', '0');
    }
}
register_activation_hook(__FILE__, 'lucidus_panel_activate');

function lucidus_panel_menu(){
    add_menu_page('Lucidus Panel', 'Lucidus Panel', 'manage_options', 'lucidus-panel', 'lucidus_panel_page');
}
add_action('admin_menu', 'lucidus_panel_menu');

function lucidus_panel_page(){
    if(isset($_POST['lucidus_voice_enabled'])){
        check_admin_referer('lucidus_panel');
        update_option('lucidus_voice_enabled', $_POST['lucidus_voice_enabled'] ? '1' : '0');
        echo '<div class="updated notice"><p>Settings saved.</p></div>';
    }
    $enabled = get_option('lucidus_voice_enabled','0') === '1';
    echo '<div class="wrap"><h1>Lucidus Panel</h1>';
    $screen = get_current_screen();
    if($screen && method_exists($screen, 'add_help_tab')){
        $screen->add_help_tab([
            'id' => 'lucidus_panel_help',
            'title' => __('Usage'),
            'content' => '<p>' . esc_html__('Toggle voice output for Lucidus.', 'lucidus-panel') . '</p>'
        ]);
    }
    echo '<form method="post">';
    wp_nonce_field('lucidus_panel');
    echo '<label><input type="checkbox" name="lucidus_voice_enabled" value="1" '.($enabled?'checked':'').'> Enable Voice</label>';
    echo '<p><button class="button button-primary">Save</button></p>';
    echo '</form></div>';
}

function lucidus_panel_rest(){
    register_rest_route('lucidus-panel/v1', '/voice', [
        'methods' => 'GET',
        'callback' => function(){ return ['enabled'=>get_option('lucidus_voice_enabled','0')==='1']; },
        'permission_callback' => '__return_true'
    ]);
    register_rest_route('lucidus-panel/v1', '/voice', [
        'methods' => 'POST',
        'callback' => function($request){
            $val = $request->get_param('enabled') ? '1' : '0';
            update_option('lucidus_voice_enabled',$val);
            return ['status'=>'saved'];
        },
        'permission_callback' => '__return_true'
    ]);
}
add_action('rest_api_init', 'lucidus_panel_rest');

function lucidus_panel_shortcode(){
    $enabled = get_option('lucidus_voice_enabled','0')==='1';
    $state = $enabled ? 'enabled' : 'disabled';
    return '<div class="lucidus-panel">Voice is '.$state.'</div>';
}
add_shortcode('lucidus-panel', 'lucidus_panel_shortcode');
