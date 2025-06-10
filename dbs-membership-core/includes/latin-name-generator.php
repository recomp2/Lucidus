<?php
if (!defined('ABSPATH')) {
    exit;
}

function dbs_mc_generate_latin_name($archetype = '') {
    $prefixes = ['Chronicus', 'Nastyus', 'Randallus', 'Foglor', 'Smokus'];
    $suffixes = ['Blazion', 'Foglor', 'Dankson', 'Blaze', 'Doobius'];
    $prefix = $prefixes[array_rand($prefixes)];
    $suffix = $suffixes[array_rand($suffixes)];
    return "$prefix $suffix";
}
