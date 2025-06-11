<?php
/*
Plugin Name: DBS Membership Core
Description: Core membership logic for Dead Bastard Society.
Version: 1.1
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

// Load all profiles
function dbs_get_profiles() {
    $profiles = [];
    foreach (glob(dbs_profile_dir() . '/*.json') as $file) {
        $profiles[] = json_decode(file_get_contents($file), true);
    }
    return $profiles;
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
// Scroll wall displays a list of initiated members
function dbs_scroll_wall() {
    $profiles = dbs_get_profiles();
    if (empty($profiles)) {
        return '<p>No scrolls discovered yet.</p>';
    }
    $out = '<ul class="dbs-scroll-wall">';
    foreach ($profiles as $p) {
        $name = isset($p['user']) ? esc_html($p['user']) : 'Unknown';
        $out .= '<li>' . $name . '</li>';
    }
    $out .= '</ul>';
    return $out;
}
add_shortcode('scroll_wall', 'dbs_scroll_wall');

// Simple quest list by rank
function dbs_quests() {
    $quests = [
        'recruit' => ['Find the entrance', 'Speak the oath'],
        'member'  => ['Unlock the scrolls', 'Guard the gateway'],
        'elder'   => ['Guide new souls', 'Keep the memory']
    ];
    $out = '<div class="dbs-quests">';
    foreach ($quests as $rank => $steps) {
        $out .= '<h3>' . ucfirst($rank) . '</h3><ol>';
        foreach ($steps as $s) {
            $out .= '<li>' . esc_html($s) . '</li>';
        }
        $out .= '</ol>';
    }
    $out .= '</div>';
    return $out;
}
add_shortcode('quests', 'dbs_quests');

// Town map placeholder
function dbs_town_map() {
    $map_url = plugins_url('town-map.png', __FILE__);
    if (!file_exists(dirname(__FILE__) . '/town-map.png')) {
        $map_url = 'https://placehold.co/600x400?text=Town+Map';
    }
    return '<img class="dbs-town-map" src="' . esc_url($map_url) . '" alt="Town map" />';
}
add_shortcode('town_map', 'dbs_town_map');
?>
