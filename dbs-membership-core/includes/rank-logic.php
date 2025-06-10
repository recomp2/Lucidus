<?php
/**
 * Dead Bastard Society Membership Core
 * Rank calculation logic
 *
 * @package Dead Bastard Society
 */
if (!defined('ABSPATH')) { exit; }

function dbs_initial_rank() {
    return 0;
}

function dbs_rank_label($rank) {
    $labels = [0 => 'Initiate', 1 => 'Acolyte', 2 => 'Bastard'];
    return isset($labels[$rank]) ? $labels[$rank] : 'Unknown';
}
