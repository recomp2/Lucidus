<?php
if (!defined('ABSPATH')) {
    exit;
}

function dbs_mc_set_behavior_tags($user_id, $tags = []) {
    update_user_meta($user_id, 'dbs_behavior_tags', array_map('sanitize_text_field', $tags));
}

function dbs_mc_get_behavior_tags($user_id) {
    $tags = get_user_meta($user_id, 'dbs_behavior_tags', true);
    return is_array($tags) ? $tags : [];
}
