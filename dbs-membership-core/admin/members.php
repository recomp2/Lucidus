<?php
/**
 * Dead Bastard Society Membership Core
 * Admin member directory
 *
 * @package Dead Bastard Society
 */
if (!defined('ABSPATH')) { exit; }

add_action('admin_menu', function() {
    add_menu_page('DBS Members', 'DBS Members', 'manage_options', 'dbs-members', 'dbs_members_page');
});

function dbs_members_page() {
    if (!current_user_can('manage_options')) return;

    // Handle actions
    if (isset($_GET['dbs_action']) && isset($_GET['user'])) {
        $user = get_user_by('login', sanitize_user($_GET['user']));
        if ($user) {
            switch ($_GET['dbs_action']) {
                case 'delete':
                    require_once ABSPATH.'wp-admin/includes/user.php';
                    wp_delete_user($user->ID);
                    echo '<div class="updated"><p>User deleted.</p></div>';
                    break;
                case 'promote':
                    $rank = (int) get_user_meta($user->ID, 'dbs_rank', true);
                    update_user_meta($user->ID, 'dbs_rank', $rank + 1);
                    echo '<div class="updated"><p>User promoted.</p></div>';
                    break;
            }
        }
    }

    $search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
    $args = ['search' => '*'.esc_attr($search).'*', 'search_columns' => ['user_login', 'display_name']];
    $users = get_users($args);
    echo '<div class="wrap"><h1>DBS Members</h1>';
    echo '<form method="get"><input type="hidden" name="page" value="dbs-members" />';
    echo '<p class="search-box"><input type="search" name="s" value="'.esc_attr($search).'" />';
    submit_button('Search Members', 'secondary', false, false); echo '</p></form>';
    echo '<table class="wp-list-table widefat fixed striped"><thead><tr><th>Username</th><th>Latin Name</th><th>Rank</th><th>Geo</th><th>Behavior</th><th>Actions</th></tr></thead><tbody>';
    foreach ($users as $u) {
        $profile = dbs_load_profile($u->user_login);
        $latin = isset($profile['latin_name']) ? $profile['latin_name'] : '';
        $rank = dbs_rank_label((int) get_user_meta($u->ID, 'dbs_rank', true));
        $geo = isset($profile['geo']) ? $profile['geo'] : '';
        $behavior = isset($profile['tags']) ? implode(',', $profile['tags']) : '';
        $actions = '<a href="?page=dbs-members&dbs_action=promote&user='.$u->user_login.'">Promote</a> | ';
        $actions .= '<a href="?page=dbs-members&dbs_action=delete&user='.$u->user_login.'" onclick="return confirm(\'Delete?\');">Delete</a>';
        echo '<tr><td>'.esc_html($u->user_login).'</td><td>'.esc_html($latin).'</td><td>'.esc_html($rank).'</td><td>'.esc_html($geo).'</td><td>'.esc_html($behavior).'</td><td>'.$actions.'</td></tr>';
    }
    echo '</tbody></table></div>';
}
