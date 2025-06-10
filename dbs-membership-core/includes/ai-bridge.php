<?php
if (!defined('ABSPATH')) {
    exit;
}

function dbs_mc_notify_lucidus($user_id) {
    // Placeholder: hook into Lucidus API or trigger action.
    do_action('dbs_mc_profile_saved', $user_id);
}
