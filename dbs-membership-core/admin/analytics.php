<?php
/**
 * Dead Bastard Society Membership Core
 * Analytics dashboard
 *
 * @package Dead Bastard Society
 */
if (!defined('ABSPATH')) { exit; }

add_action('admin_menu', function(){
    add_submenu_page('dbs-members', 'Analytics', 'Analytics', 'manage_options', 'dbs-analytics', 'dbs_analytics_page');
});

function dbs_analytics_page(){
    if (!current_user_can('manage_options')) return;
    $users = count_users();
    $total = $users['total_users'];
    $geofile = DBS_LIBRARY_DIR.'geos.json';
    $geos = file_exists($geofile) ? json_decode(file_get_contents($geofile), true) : [];
    $towns = count($geos);
    $scrollDir = DBS_LIBRARY_DIR.'scrolls';
    $scrolls = 0;
    if (is_dir($scrollDir)){
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($scrollDir)) as $file){
            if (pathinfo($file, PATHINFO_EXTENSION) === 'json') $scrolls++;
        }
    }
    echo '<div class="wrap"><h1>DBS Analytics</h1>';
    echo '<p>Total Members: '.intval($total).'</p>';
    echo '<p>Total Towns: '.intval($towns).'</p>';
    echo '<p>Founding Scrolls: '.intval($scrolls).'</p>';
    echo '</div>';
}
