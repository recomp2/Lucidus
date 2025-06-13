<?php
/*
Plugin Name: Quest Engine
Description: Manages quests in the DBS universe.
Version: 0.1
*/
if (!defined('ABSPATH')) exit;

function quest_engine_get_quests(){
    $file = WP_CONTENT_DIR . '/dbs-library/memory-archive/quests/quests.json';
    return file_exists($file) ? json_decode(file_get_contents($file), true) : [];
}

function quest_engine_save_quests($quests){
    $file = WP_CONTENT_DIR . '/dbs-library/memory-archive/quests/quests.json';
    file_put_contents($file, json_encode($quests));
}

function quest_engine_menu(){
    add_menu_page('Quest Engine', 'Quest Engine', 'manage_options', 'quest-engine', 'quest_engine_page');
}
add_action('admin_menu', 'quest_engine_menu');

function quest_engine_page(){
    if(function_exists('dbs_update_user_memory')){
        dbs_update_user_memory('quest-engine','settings');
    }
    if(isset($_POST['qe_add'])){
        check_admin_referer('quest_engine');
        $quests = quest_engine_get_quests();
        $quests[] = ['title'=>sanitize_text_field($_POST['qe_title'])];
        quest_engine_save_quests($quests);
        echo '<div class="updated notice"><p>Quest added.</p></div>';
    }
    echo '<div class="wrap"><h1>Quest Engine</h1><form method="post">';
    wp_nonce_field('quest_engine');
    echo '<input name="qe_title" type="text" placeholder="Quest title" />';
    echo '<button class="button">Add</button></form>';
    $quests = quest_engine_get_quests();
    if($quests){
        echo '<ul>';
        foreach($quests as $q){
            echo '<li>'.esc_html($q['title']).'</li>';
        }
        echo '</ul>';
    }
    echo '</div>';
}

function quest_engine_rest(){
    register_rest_route('quest-engine/v1','/quests',[
        'methods'=>'GET',
        'callback'=>function(){ return quest_engine_get_quests(); },
        'permission_callback'=>'__return_true'
    ]);
}
add_action('rest_api_init','quest_engine_rest');

function quest_engine_shortcode(){
    $quests = quest_engine_get_quests();
    if(!$quests) return '<p>No quests.</p>';
    $out='<ul class="quest-engine">';
    foreach($quests as $q){ $out.='<li>'.esc_html($q['title']).'</li>'; }
    $out.='</ul>'; return $out;
}
add_shortcode('quest-engine','quest_engine_shortcode');
