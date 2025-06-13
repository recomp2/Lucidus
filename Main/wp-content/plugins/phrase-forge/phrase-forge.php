<?php
/*
Plugin Name: phrase forge
Description: Create and recall trigger phrases.
Version: 0.2
*/
if (!defined('ABSPATH')) exit;

function phrase_forge_get_phrases(){
    $file = WP_CONTENT_DIR . '/dbs-library/memory-archive/scrolls/phrases.json';
    return file_exists($file) ? json_decode(file_get_contents($file), true) : [];
}

function phrase_forge_save_phrases($phrases){
    $file = WP_CONTENT_DIR . '/dbs-library/memory-archive/scrolls/phrases.json';
    file_put_contents($file, json_encode($phrases));
}

function phrase_forge_menu(){
    add_menu_page(ucwords(str_replace('-', ' ', 'phrase-forge')), ucwords(str_replace('-', ' ', 'phrase-forge')), 'manage_options', 'phrase-forge', 'phrase_forge_page');
}
add_action('admin_menu', 'phrase_forge_menu');

function phrase_forge_page(){
    if(function_exists('dbs_update_user_memory')){
        dbs_update_user_memory('phrase-forge','settings');
    }
    if(isset($_POST['pf_phrase'])){
        check_admin_referer('phrase_forge');
        $phrases = phrase_forge_get_phrases();
        $phrases[] = sanitize_text_field($_POST['pf_phrase']);
        phrase_forge_save_phrases($phrases);
        if(function_exists('dbs_memory_logger')){ dbs_memory_logger('Phrase added'); }
        echo '<div class="updated notice"><p>Phrase saved.</p></div>';
    }
    echo '<div class="wrap"><h1>'.esc_html(ucwords(str_replace('-', ' ', 'phrase-forge'))).'</h1>';
    echo '<form method="post">';
    wp_nonce_field('phrase_forge');
    echo '<input name="pf_phrase" type="text" placeholder="New phrase" />';
    echo '<button class="button">Save</button></form>';
    $phrases = phrase_forge_get_phrases();
    if($phrases){
        echo '<ul>';
        foreach($phrases as $p){ echo '<li>'.esc_html($p).'</li>'; }
        echo '</ul>';
    }
    echo '</div>';
}

function phrase_forge_rest(){
    register_rest_route('phrase-forge/v1', '/phrases', [
        'methods' => 'GET',
        'callback' => 'phrase_forge_get_phrases',
        'permission_callback' => '__return_true'
    ]);
    register_rest_route('phrase-forge/v1', '/phrases', [
        'methods' => 'POST',
        'callback' => function($request){
            $phrases = phrase_forge_get_phrases();
            $phrases[] = sanitize_text_field($request->get_param('phrase'));
            phrase_forge_save_phrases($phrases);
            return ['status'=>'saved'];
        },
        'permission_callback' => '__return_true'
    ]);
}
add_action('rest_api_init', 'phrase_forge_rest');

function phrase_forge_shortcode(){
    $phrases = phrase_forge_get_phrases();
    if(!$phrases) return '<p>No phrases.</p>';
    return '<ul class="phrase-forge"><li>'.implode('</li><li>', array_map('esc_html',$phrases)).'</li></ul>';
}
add_shortcode('phrase-forge', 'phrase_forge_shortcode');
