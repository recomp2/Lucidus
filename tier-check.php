<?php
if (!defined('ABSPATH')) exit;
require_once __DIR__ . '/tier-access-control.php';

function tier_access_get_user_tier($user_id = null) {
    if (null === $user_id) {
        $user_id = get_current_user_id();
    }
    $tier = get_user_meta($user_id, 'dbs_member_tier', true);
    if (!$tier) {
        $settings = tier_access_get_settings();
        $tier = key($settings); // first tier as default
    }
    return $tier;
}

function tier_access_has_feature($feature, $user_id = null) {
    $settings = tier_access_get_settings();
    $tier = tier_access_get_user_tier($user_id);
    return !empty($settings[$tier][$feature]);
}
