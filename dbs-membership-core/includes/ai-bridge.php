<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Notify Lucidus that a member profile changed.
 */
function dbs_mc_notify_lucidus($user_id) {
    do_action('dbs_mc_profile_saved', $user_id);
}

/**
 * Retrieve a member profile from JSON memory.
 */
function dbs_mc_get_member_memory($user_id) {
    $user = get_userdata($user_id);
    if (!$user) {
        return null;
    }
    $upload_dir = wp_upload_dir();
    $file = trailingslashit($upload_dir['basedir']) . 'dbs-library/memory-archive/profiles/' . $user->user_login . '.json';
    if (!file_exists($file)) {
        return null;
    }
    return json_decode(file_get_contents($file), true);
}
