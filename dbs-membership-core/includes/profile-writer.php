<?php
/**
 * Dead Bastard Society Membership Core
 * Profile JSON writer
 *
 * @package Dead Bastard Society
 */
if (!defined('ABSPATH')) { exit; }

function dbs_profile_path($username) {
    return DBS_MEMBERSHIP_DIR . 'memory-archive/profiles/' . strtolower($username) . '.json';
}

function dbs_write_profile($username, $data) {
    $path = dbs_profile_path($username);
    wp_mkdir_p(dirname($path));
    file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT));
    return $path;
}

function dbs_load_profile($username) {
    $path = dbs_profile_path($username);
    return file_exists($path) ? json_decode(file_get_contents($path), true) : [];
}
