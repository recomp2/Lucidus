<?php
if (!defined('ABSPATH')) {
    exit;
}

function dbs_mc_get_default_rank() {
    return 1; // Initiate rank
}

function dbs_mc_assign_rank($user_id, $rank = null) {
    if (!$rank) {
        $rank = dbs_mc_get_default_rank();
    }
    update_user_meta($user_id, 'dbs_rank', intval($rank));
}
