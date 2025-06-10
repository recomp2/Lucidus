<?php
/**
 * Dead Bastard Society Membership Core
 * Confirmation screen after initiation
 *
 * @package Dead Bastard Society
 */
if (!defined('ABSPATH')) { exit; }

add_shortcode('dbs_confirmation', 'dbs_confirmation_page');

function dbs_confirmation_page() {
    $username = isset($_GET['user']) ? sanitize_user($_GET['user']) : '';
    if (!$username) return '<p>Invalid.</p>';
    $profile = dbs_load_profile($username);
    if (!$profile) return '<p>No profile.</p>';
    $message = '<h2>Welcome '.$profile['latin_name'].'</h2>';
    $message .= '<p>Archetype: '.esc_html($profile['archetype']).'</p>';
    $message .= '<p>Rank: '.dbs_rank_label($profile['rank']).'</p>';
    $message .= '<p>Your Chapter Has Begun in '.esc_html($profile['geo']).'</p>';
    if (isset($_GET['new_town']) && $_GET['new_town']) {
        $message .= '<p>You are the founder of this town chapter!</p>';
    }
    return $message;
}
