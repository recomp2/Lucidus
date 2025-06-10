<?php
/**
 * Dead Bastard Society Membership Core
 * Pending town reassignment admin page
 *
 * @package Dead Bastard Society
 */
if (!defined('ABSPATH')) { exit; }

add_action('admin_menu', function(){
    add_submenu_page('dbs-members', 'Geo Queue', 'Geo Queue', 'manage_options', 'dbs-geo-queue', 'dbs_geo_queue_page');
});

function dbs_geo_queue_page(){
    if (!current_user_can('manage_options')) return;

    $geos = dbs_load_geos();

    if (isset($_POST['reassign']) && check_admin_referer('dbs_geo_queue')) {
        $city  = sanitize_text_field($_POST['city']);
        $state = sanitize_text_field($_POST['state']);
        $user  = sanitize_user($_POST['user']);
        $key = strtolower("$city,$state");
        if(isset($geos[$key]) && $geos[$key]['status']==='pending') {
            $geos[$key]['pending_user'] = $user;
            $geos[$key]['timestamp'] = time();
            dbs_save_geos($geos);
            echo '<div class="updated"><p>Town reassigned.</p></div>';
        }
    }

    echo '<div class="wrap"><h1>Pending Town Claims</h1>';
    echo '<p>These towns are awaiting confirmation by a founder. Reassign to another user if needed.</p>';

    echo '<table class="wp-list-table widefat fixed striped"><thead><tr><th>City</th><th>State</th><th>Pending User</th><th>Timestamp</th><th>Reassign</th></tr></thead><tbody>';
    foreach ($geos as $g){
        if(isset($g['status']) && $g['status']==='pending'){
            $city = esc_html($g['city']);
            $state = esc_html($g['state']);
            $pending = esc_html($g['pending_user']);
            $time = date_i18n('Y-m-d H:i', intval($g['timestamp']));
            $users = get_users(['fields'=>['user_login']]);
            $select = '<select name="user">';
            foreach($users as $u){
                $sel = $u->user_login === $pending ? ' selected' : '';
                $select .= '<option value="'.esc_attr($u->user_login).'"'.$sel.'>'.esc_html($u->user_login).'</option>';
            }
            $select .= '</select>';
            echo '<tr><td>'.$city.'</td><td>'.$state.'</td><td>'.$pending.'</td><td>'.$time.'</td><td>';
            echo '<form method="post" style="display:inline">';
            wp_nonce_field('dbs_geo_queue');
            echo '<input type="hidden" name="city" value="'.esc_attr($g['city']).'">';
            echo '<input type="hidden" name="state" value="'.esc_attr($g['state']).'">';
            echo $select.' <button class="button" name="reassign" value="1">Reassign</button></form>';
            echo '</td></tr>';
        }
    }
    echo '</tbody></table></div>';
}
