<?php
/*
Plugin Name: DBS Membership Core
Description: Core membership logic for Dead Bastard Society.
Version: 1.0
Author: Dr.G
License: MIT
*/

// Directory for profiles
function dbs_profile_dir() {
    $dir = WP_CONTENT_DIR . '/dbs-library/memory-archive/profiles';
    if (!file_exists($dir)) {
        wp_mkdir_p($dir);
    }
    return $dir;
}

// Shortcode: initiation form
function dbs_initiation_form() {
    if (isset($_POST['dbs_initiate'])) {
        $user = sanitize_text_field($_POST['dbs_user']);
        $consent = isset($_POST['dbs_consent']);
        if ($user && $consent) {
            $profile = ['user'=>$user,'created'=>current_time('mysql')];
            file_put_contents(dbs_profile_dir()."/{$user}.json", json_encode($profile));
            echo '<p>Initiation complete for '.esc_html($user).'</p>';
        } else {
            echo '<p>Missing username or consent.</p>';
        }
    }
    $html = '<form method="post">
        <input type="text" name="dbs_user" placeholder="Username" required />
        <label><input type="checkbox" name="dbs_consent" required /> I consent</label>
        <button type="submit" name="dbs_initiate">Initiate</button>
    </form>';
    return $html;
}
add_shortcode('dbs_initiation_form', 'dbs_initiation_form');

// Shortcode: member profile
function dbs_member_profile() {
    $user = isset($_GET['user']) ? sanitize_text_field($_GET['user']) : '';
    if ($user && file_exists(dbs_profile_dir()."/{$user}.json")) {
        $profile = json_decode(file_get_contents(dbs_profile_dir()."/{$user}.json"), true);
        return '<pre>'.esc_html(print_r($profile, true)).'</pre>';
    }
    return '<p>No profile found.</p>';
}
add_shortcode('member_profile','dbs_member_profile');

// Stub shortcodes for scroll_wall, quests, town_map
add_shortcode('scroll_wall', function(){ return '<div class="scroll-wall">Coming soon</div>'; });
add_shortcode('quests', function(){ return '<div class="quests">Coming soon</div>'; });
add_shortcode('town_map', function(){ return '<div class="town-map">Coming soon</div>'; });
?>
