<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Generate a latin style name using archetype context.
 */
function dbs_mc_generate_latin_name($archetype = '') {
    $prefix_pool = [
        'dub'     => ['Dubonicus', 'Fogicus', 'Stoneus'],
        'randall' => ['Randallus', 'Chaosus', 'Magikus'],
        'nasty_p' => ['Nastyus', 'Flamus', 'Dankus'],
        'default' => ['Chronicus', 'Mysticus', 'Smokus']
    ];
    $suffix_pool = [
        'dub'     => ['Fogmar', 'Blaze', 'Munch'],
        'randall' => ['Grimoire', 'Arcana', 'Fyre'],
        'nasty_p' => ['Resinus', 'Inferno', 'Glazion'],
        'default' => ['Doobius', 'Foglor', 'Blazion']
    ];

    $arch = $archetype && isset($prefix_pool[$archetype]) ? $archetype : 'default';

    $prefix = $prefix_pool[$arch][array_rand($prefix_pool[$arch])];
    $suffix = $suffix_pool[$arch][array_rand($suffix_pool[$arch])];

    return "$prefix $suffix";
}

/**
 * Generate a basic phonetic mapping for a latin name.
 */
function dbs_mc_generate_phonetic($latin_name) {
    $latin_name = trim($latin_name);
    if ($latin_name === '') {
        return '';
    }
    $replacements = [
        'ae' => 'eye',
        'au' => 'ow',
        'ch' => 'k',
        'c'  => 'k',
        'g'  => 'g',
        'i'  => 'ee',
        'j'  => 'y',
        'v'  => 'w',
    ];
    $phonetic = strtolower($latin_name);
    foreach ($replacements as $search => $rep) {
        $phonetic = str_replace($search, $rep, $phonetic);
    }
    $phonetic = preg_replace('/[^a-z ]/', '', $phonetic);
    $words = array_map('ucfirst', explode(' ', $phonetic));
    return implode(' ', $words);
}
