<?php
/**
 * Dead Bastard Society Membership Core
 * Profile viewer shortcode
 *
 * @package Dead Bastard Society
 */
if (!defined('ABSPATH')) { exit; }

add_shortcode('dbs_profile', 'dbs_profile_shortcode');

function dbs_profile_shortcode($atts){
    $u = isset($atts['user']) ? sanitize_user($atts['user']) : '';
    if (!$u && is_user_logged_in()){
        $u = wp_get_current_user()->user_login;
    }
    if (!$u){
        return '<p>No profile specified.</p>';
    }
    $profile = dbs_load_profile($u);
    if (!$profile){
        return '<p>Profile not found.</p>';
    }
    $out  = '<div class="dbs-profile">';
    $out .= '<h2>'.esc_html($profile['latin_name']).'</h2>';
    $out .= '<p>Archetype: '.esc_html($profile['archetype']).'</p>';
    $out .= '<p>Rank: '.dbs_rank_label($profile['rank']).'</p>';
    $out .= '<p>Geo: '.esc_html($profile['geo']).'</p>';
    if (!empty($profile['tags'])){
        $out .= '<p>Tags: '.esc_html(implode(', ', (array)$profile['tags'])).'</p>';
    }
    $out .= '</div>';
    return $out;
}
