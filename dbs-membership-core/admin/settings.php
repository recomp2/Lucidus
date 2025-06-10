<?php
/**
 * Dead Bastard Society Membership Core
 * Settings page
 *
 * @package Dead Bastard Society
 */
if (!defined('ABSPATH')) { exit; }

add_action('admin_menu', function() {
    add_submenu_page('dbs-members', 'DBS Settings', 'Settings', 'manage_options', 'dbs-settings', 'dbs_settings_page');
});

function dbs_settings_page() {
    if (!current_user_can('manage_options')) return;
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && check_admin_referer('dbs_save_settings')) {
        update_option('dbs_archetypes', wp_unslash($_POST['archetypes']));
        update_option('dbs_latin_rules', sanitize_text_field($_POST['latin_rules']));
        update_option('dbs_geo_behavior', sanitize_text_field($_POST['geo_behavior']));
        echo '<div class="updated"><p>Settings saved.</p></div>';
    }
    $arch = get_option('dbs_archetypes', "");
    $latin = get_option('dbs_latin_rules', "");
    $geo = get_option('dbs_geo_behavior', "");
    echo '<div class="wrap"><h1>DBS Settings</h1><form method="post">';
    wp_nonce_field('dbs_save_settings');
    echo '<table class="form-table">';
    echo '<tr><th><label>Archetype Descriptions</label></th><td><textarea name="archetypes" rows="5" cols="50">'.esc_textarea($arch).'</textarea></td></tr>';
    echo '<tr><th><label>Latin Naming Rules</label></th><td><input type="text" name="latin_rules" value="'.esc_attr($latin).'" /></td></tr>';
    echo '<tr><th><label>Geo Tag Behavior</label></th><td><input type="text" name="geo_behavior" value="'.esc_attr($geo).'" /></td></tr>';
    echo '</table>'; submit_button(); echo '</form></div>';
}
