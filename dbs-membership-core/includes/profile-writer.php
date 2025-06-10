<?php
if (!defined('ABSPATH')) {
    exit;
}

function dbs_mc_write_profile($user_id) {
    $user = get_userdata($user_id);
    if (!$user) {
        return false;
    }

    $profile = [
        'user_id' => $user_id,
        'latin_name' => get_user_meta($user_id, 'dbs_latin_name', true),
        'archetype' => get_user_meta($user_id, 'dbs_archetype', true),
        'geo_name' => get_user_meta($user_id, 'dbs_geo_name', true),
        'rank' => intval(get_user_meta($user_id, 'dbs_rank', true)),
        'behavior_tags' => get_user_meta($user_id, 'dbs_behavior_tags', true)
    ];

    $upload_dir = wp_upload_dir();
    $dir = trailingslashit($upload_dir['basedir']) . 'dbs-library/memory-archive/profiles/';
    if (!file_exists($dir)) {
        wp_mkdir_p($dir);
    }
    $file = $dir . $user->user_login . '.json';
    file_put_contents($file, wp_json_encode($profile, JSON_PRETTY_PRINT));

    return $file;
}
