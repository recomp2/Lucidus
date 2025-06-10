<?php
/**
 * Dead Bastard Society Membership Core
 * Scroll management page
 *
 * @package Dead Bastard Society
 */
if (!defined('ABSPATH')) { exit; }

add_action('admin_menu', function(){
    add_submenu_page('dbs-members', 'Scrolls', 'Scrolls', 'manage_options', 'dbs-scrolls', 'dbs_scrolls_page');
});

function dbs_get_all_scrolls(){
    $base = DBS_LIBRARY_DIR . 'scrolls/';
    $scrolls = [];
    if (is_dir($base)){
        $states = glob($base.'*', GLOB_ONLYDIR);
        foreach ($states as $stateDir){
            foreach (glob($stateDir.'/*.json') as $file){
                $data = json_decode(file_get_contents($file), true);
                if ($data){
                    $data['file'] = $file;
                    $scrolls[] = $data;
                }
            }
        }
    }
    return $scrolls;
}

function dbs_scrolls_page(){
    if (!current_user_can('manage_options')) return;

    if (isset($_GET['dbs_action']) && isset($_GET['file'])) {
        $file = sanitize_text_field($_GET['file']);
        if ($_GET['dbs_action']==='delete' && file_exists($file)) {
            $data = json_decode(file_get_contents($file), true);
            unlink($file);
            dbs_remove_geo($data['city'], $data['state']);
            echo '<div class="updated"><p>Scroll deleted.</p></div>';
        }
    }

    if (isset($_POST['dbs_edit_scroll']) && check_admin_referer('dbs_edit_scroll')) {
        $file = sanitize_text_field($_POST['file']);
        $data = [
            'chapter'=>sanitize_text_field($_POST['chapter']),
            'city'=>sanitize_text_field($_POST['city']),
            'state'=>sanitize_text_field($_POST['state']),
            'founder'=>sanitize_text_field($_POST['founder']),
            'created'=>sanitize_text_field($_POST['created'])
        ];
        file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
        dbs_update_geo($data['city'],$data['state'],$data['chapter']);
        echo '<div class="updated"><p>Scroll saved.</p></div>';
    }

    $edit = null;
    if (isset($_GET['edit'])) {
        $path = sanitize_text_field($_GET['edit']);
        if (file_exists($path)) {
            $edit = json_decode(file_get_contents($path), true);
            $edit['file']=$path;
        }
    }

    echo '<div class="wrap"><h1>Scrolls</h1>';
    if ($edit){
        echo '<h2>Edit Scroll</h2><form method="post">';
        wp_nonce_field('dbs_edit_scroll');
        echo '<input type="hidden" name="file" value="'.esc_attr($edit['file']).'" />';
        echo '<p><label>Chapter <input type="text" name="chapter" value="'.esc_attr($edit['chapter']).'" /></label></p>';
        echo '<p><label>City <input type="text" name="city" value="'.esc_attr($edit['city']).'" /></label></p>';
        echo '<p><label>State <input type="text" name="state" value="'.esc_attr($edit['state']).'" /></label></p>';
        echo '<p><label>Founder <input type="text" name="founder" value="'.esc_attr($edit['founder']).'" /></label></p>';
        echo '<p><label>Created <input type="text" name="created" value="'.esc_attr($edit['created']).'" /></label></p>';
        submit_button('Save Scroll','primary','dbs_edit_scroll');
        echo '</form><hr />';
    }

    $scrolls = dbs_get_all_scrolls();
    echo '<table class="wp-list-table widefat fixed striped"><thead><tr><th>Chapter</th><th>City</th><th>State</th><th>Founder</th><th>Actions</th></tr></thead><tbody>';
    foreach ($scrolls as $s){
        $actions = '<a href="?page=dbs-scrolls&edit='.urlencode($s['file']).'">Edit</a> | ';
        $actions .= '<a href="?page=dbs-scrolls&dbs_action=delete&file='.urlencode($s['file']).'" onclick="return confirm(\'Delete?\')">Delete</a>';
        echo '<tr><td>'.esc_html($s['chapter']).'</td><td>'.esc_html($s['city']).'</td><td>'.esc_html($s['state']).'</td><td>'.esc_html($s['founder']).'</td><td>'.$actions.'</td></tr>';
    }
    echo '</tbody></table></div>';
}

