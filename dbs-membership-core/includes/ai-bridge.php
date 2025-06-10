<?php
/**
 * Dead Bastard Society Membership Core
 * AI bridge to update memory context
 *
 * @package Dead Bastard Society
 */
if (!defined('ABSPATH')) { exit; }

function dbs_update_ai_memory($username, $profile) {
    $summary = "User: $username\n";
    $summary .= 'Latin: ' . $profile['latin_name'] . "\n";
    $summary .= 'Rank: ' . $profile['rank'] . "\n";
    $summary .= 'Geo: ' . $profile['geo'] . "\n";
    $path = DBS_LIBRARY_DIR . 'ai/';
    wp_mkdir_p($path);
    file_put_contents($path . strtolower($username) . '.txt', $summary);
    do_action('dbs_ai_memory_update', $username, $profile);
}
