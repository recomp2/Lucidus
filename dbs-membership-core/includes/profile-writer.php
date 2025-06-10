<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Write member profile to JSON memory archive.
 */
function dbs_mc_write_profile($user_id) {
    $user = get_userdata($user_id);
    if (!$user) {
        return false;
    }

    $latin_name = get_user_meta($user_id, 'dbs_latin_name', true);
    $behavior   = dbs_mc_get_behavior_tags($user_id);
    $archetype  = get_user_meta($user_id, 'dbs_archetype', true);
    $scroll_tags = get_user_meta($user_id, 'dbs_scroll_tags', true);
    if (!is_array($scroll_tags)) {
        $scroll_tags = [];
    }

    $profile = [
        'user_id'   => $user_id,
        'latin_name'=> $latin_name,
        'phonetic'  => dbs_mc_generate_phonetic($latin_name),
        'archetype' => $archetype,
        'vibe'      => implode(', ', $behavior),
        'geo_name'  => get_user_meta($user_id, 'dbs_geo_name', true),
        'rank'      => intval(get_user_meta($user_id, 'dbs_rank', true)),
        'scroll_tags' => $scroll_tags
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
