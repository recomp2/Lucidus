<?php
/**
 * Dead Bastard Society Membership Core
 * Profile Editor admin page
 *
 * @package Dead Bastard Society
 */
if (!defined('ABSPATH')) { exit; }

add_action('admin_menu', function() {
    add_submenu_page('dbs-members', 'Profile Editor', 'Profile Editor', 'manage_options', 'dbs-editor', 'dbs_profile_editor_page');
});

function dbs_profile_editor_page() {
    if (!current_user_can('manage_options')) return;
    $username = isset($_GET['user']) ? sanitize_user($_GET['user']) : '';
    $profile = $username ? dbs_load_profile($username) : [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && check_admin_referer('dbs_save_profile')) {
        $profile = [
            'latin_name' => sanitize_text_field($_POST['latin_name']),
            'archetype' => sanitize_text_field($_POST['archetype']),
            'tags'      => array_map('sanitize_text_field', (array) $_POST['tags']),
            'rank'      => (int) $_POST['rank'],
            'geo'       => sanitize_text_field($_POST['geo'])
        ];
        dbs_write_profile($username, $profile);
        echo '<div class="updated"><p>Profile saved.</p></div>';
    }

    echo '<div class="wrap"><h1>Profile Editor</h1>';
    if (!$username) {
        echo '<p>No user specified.</p></div>';
        return;
    }
    echo '<form method="post">';
    wp_nonce_field('dbs_save_profile');
    echo '<table class="form-table"><tr><th><label>Latin Name</label></th><td><input type="text" name="latin_name" value="'.esc_attr($profile['latin_name']).'" /></td></tr>';
    echo '<tr><th><label>Archetype</label></th><td><input type="text" name="archetype" value="'.esc_attr($profile['archetype']).'" /></td></tr>';
    echo '<tr><th><label>Tags (comma)</label></th><td><input type="text" name="tags" value="'.esc_attr(implode(',', (array) $profile['tags'])).'" /></td></tr>';
    echo '<tr><th><label>Rank</label></th><td><input type="number" name="rank" value="'.esc_attr($profile['rank']).'" /></td></tr>';
    echo '<tr><th><label>Geo</label></th><td><input type="text" name="geo" value="'.esc_attr($profile['geo']).'" /></td></tr>';
    echo '</table>'; submit_button(); echo '</form></div>';
}
