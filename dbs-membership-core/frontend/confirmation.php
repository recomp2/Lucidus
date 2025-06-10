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

    $city  = isset($_GET['city']) ? sanitize_text_field($_GET['city']) : '';
    $state = isset($_GET['state']) ? sanitize_text_field($_GET['state']) : '';
    $is_new = isset($_GET['new_town']) && $_GET['new_town'];

    if ($is_new && isset($_POST['chapter_name']) && check_admin_referer('dbs_name_town')) {
        $chapter = sanitize_text_field($_POST['chapter_name']);
        dbs_register_geo($city, $state, $chapter);
        dbs_write_scroll($state, $city, $chapter, $username);
        $profile['geo'] = $chapter;
        dbs_write_profile($username, $profile);
        dbs_update_ai_memory($username, $profile);
        $is_new = false;
        $found_scroll = true;
    }

    $message = '<h2>Welcome '.$profile['latin_name'].'</h2>';
    $message .= '<p>Archetype: '.esc_html($profile['archetype']).'</p>';
    $message .= '<p>Rank: '.dbs_rank_label($profile['rank']).'</p>';
    $message .= '<p>Your Chapter Has Begun in '.esc_html($profile['geo']).'</p>';

    if ($is_new) {
        $options = dbs_generate_geo_options($city);
        $message .= '<form method="post">';
        wp_nonce_field('dbs_name_town');
        foreach ($options as $opt) {
            $esc = esc_html($opt);
            $message .= "<p><label><input type='radio' name='chapter_name' value='$esc' required> $esc</label></p>";
        }
        $message .= '<p><button type="submit">Claim Name</button></p></form>';
    } elseif (isset($found_scroll)) {
        $message .= '<p>Your Founding Scroll Has Been Forged</p>';
    }

    return $message;
}
