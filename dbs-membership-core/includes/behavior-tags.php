<?php
/**
 * Dead Bastard Society Membership Core
 * Behavior tag logic
 *
 * @package Dead Bastard Society
 */
if (!defined('ABSPATH')) { exit; }

function dbs_assign_initial_tags() {
    $tags = ['chaotic', 'misty', 'horny'];
    return [$tags[array_rand($tags)]];
}
