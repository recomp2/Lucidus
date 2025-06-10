<?php
/**
 * Dead Bastard Society Membership Core
 * Latin name generation logic
 *
 * @package Dead Bastard Society
 */
if (!defined('ABSPATH')) { exit; }

function dbs_generate_latin_name($first, $last) {
    $prefixes = ['Magnus', 'Stonus', 'Luridus', 'Vapor', 'Glor'];
    $suffixes = ['ius', 'or', 'ax', 'on', 'ar'];
    $prefix = $prefixes[array_rand($prefixes)];
    $suffix = $suffixes[array_rand($suffixes)];
    return $prefix . substr($first, 0, 1) . $suffix;
}
