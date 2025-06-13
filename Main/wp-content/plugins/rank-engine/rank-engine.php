<?php
/*
Plugin Name: Rank Engine
Description: Handles DBS member ranks.
Version: 0.1
*/
if (!defined('ABSPATH')) exit;

function rank_engine_get_ranks(){
    $file = WP_CONTENT_DIR . '/dbs-library/memory-archive/profiles/ranks.json';
    return file_exists($file) ? json_decode(file_get_contents($file), true) : [];
}

function rank_engine_save_ranks($ranks){
    $file = WP_CONTENT_DIR . '/dbs-library/memory-archive/profiles/ranks.json';
    file_put_contents($file, json_encode($ranks));
}

function rank_engine_get_user_rank($user_id){
    return get_user_meta($user_id, 'dbs_rank', true);
}

function rank_engine_set_user_rank($user_id, $rank){
    update_user_meta($user_id, 'dbs_rank', sanitize_text_field($rank));
}

function rank_engine_assign_default($user_id){
    $default = get_option('rank_engine_default', 'Initiate');
    rank_engine_set_user_rank($user_id, $default);
}
add_action('user_register','rank_engine_assign_default');

function rank_engine_menu(){
    add_menu_page('Rank Engine', 'Rank Engine', 'manage_options', 'rank-engine', 'rank_engine_page');
}
add_action('admin_menu', 'rank_engine_menu');

function rank_engine_page(){
    if(function_exists('dbs_update_user_memory')){
        dbs_update_user_memory('rank-engine','settings');
    }
    if(isset($_POST['re_add'])){
        check_admin_referer('rank_engine');
        $ranks = rank_engine_get_ranks();
        $ranks[] = sanitize_text_field($_POST['re_rank']);
        rank_engine_save_ranks($ranks);
        echo '<div class="updated notice"><p>Rank added.</p></div>';
    }
    if(isset($_POST['re_default'])){
        check_admin_referer('rank_engine_default');
        update_option('rank_engine_default', sanitize_text_field($_POST['re_default']));
        echo '<div class="updated notice"><p>Default rank updated.</p></div>';
    }
    echo '<div class="wrap"><h1>Rank Engine</h1><form method="post">';
    wp_nonce_field('rank_engine');
    echo '<input name="re_rank" type="text" placeholder="Rank" />';
    echo '<button class="button">Add</button></form>';
    $ranks = rank_engine_get_ranks();
    if($ranks){
        echo '<ul>';
        foreach($ranks as $r){ echo '<li>'.esc_html($r).'</li>'; }
        echo '</ul>';
        $default = get_option('rank_engine_default', $ranks[0]);
        echo '<h2>Default Rank</h2><form method="post">';
        wp_nonce_field('rank_engine_default');
        echo '<select name="re_default">';
        foreach($ranks as $r){
            echo '<option value="'.esc_attr($r).'" '.selected($default,$r,false).'>' . esc_html($r) . '</option>';
        }
        echo '</select> <button class="button">Save Default</button></form>';
    }
    echo '</div>';
}

function rank_engine_rest(){
    register_rest_route('rank-engine/v1','/ranks',[
        'methods'=>'GET',
        'callback'=>function(){ return rank_engine_get_ranks(); },
        'permission_callback'=>'__return_true'
    ]);
    register_rest_route('rank-engine/v1','/user/(?P<id>\d+)',[
        'methods'=>'GET',
        'callback'=>function($request){ return ['rank'=>rank_engine_get_user_rank($request['id'])]; },
        'permission_callback'=>'__return_true'
    ]);
    register_rest_route('rank-engine/v1','/user/(?P<id>\d+)',[
        'methods'=>'POST',
        'callback'=>function($request){
            rank_engine_set_user_rank($request['id'], $request->get_param('rank'));
            return ['status'=>'saved'];
        },
        'permission_callback'=>'__return_true'
    ]);
}
add_action('rest_api_init','rank_engine_rest');

function rank_engine_shortcode(){
    $ranks = rank_engine_get_ranks();
    return $ranks?'<div class="rank-engine">'.implode(', ', array_map('esc_html',$ranks)).'</div>':'<p>No ranks set.</p>';
}
add_shortcode('rank-engine','rank_engine_shortcode');
