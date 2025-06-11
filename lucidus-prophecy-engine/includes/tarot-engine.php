<?php
if (!defined('ABSPATH')) {
    exit;
}

function lpe_draw_tarot($archetype) {
    $cards = [
        'Dub' => ['The Smiling Sage', 'The Chill Cup'],
        'Randall' => ['The Prankster', 'The Wandering Fool'],
        'Nasty P' => ['The Inverted Bong', 'The Wild Ember']
    ];
    $set = isset($cards[$archetype]) ? $cards[$archetype] : array_merge(...array_values($cards));
    return $set[array_rand($set)];
}
?>
