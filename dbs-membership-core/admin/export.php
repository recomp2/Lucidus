<?php
/**
 * Dead Bastard Society Membership Core
 * Export members to CSV
 *
 * @package Dead Bastard Society
 */
if (!defined('ABSPATH')) { exit; }

add_action('admin_menu', function(){
    add_submenu_page('dbs-members', 'Export Members', 'Export', 'manage_options', 'dbs-export', 'dbs_export_page');
});

function dbs_export_page(){
    if (!current_user_can('manage_options')) return;

    if (isset($_GET['download_csv'])) {
        $users = get_users();
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="dbs-members.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['Username','Email','Latin Name','Rank','Geo','Tags']);
        foreach ($users as $u){
            $profile = dbs_load_profile($u->user_login);
            fputcsv($out,[
                $u->user_login,
                $u->user_email,
                isset($profile['latin_name'])?$profile['latin_name']:'',
                dbs_rank_label((int)get_user_meta($u->ID,'dbs_rank',true)),
                isset($profile['geo'])?$profile['geo']:'',
                isset($profile['tags'])?implode(',',(array)$profile['tags']):''
            ]);
        }
        fclose($out);
        exit;
    }

    echo '<div class="wrap"><h1>Export Members</h1>';
    echo '<p><a class="button button-primary" href="?page=dbs-export&download_csv=1">Download CSV</a></p>';
    echo '</div>';
}

