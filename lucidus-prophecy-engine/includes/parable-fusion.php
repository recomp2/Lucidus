<?php
if (!defined('ABSPATH')) {
    exit;
}

function lpe_get_parable($archetype) {
    $parables = [
        'Dub'     => 'The seed on fertile soil will grow if tended.',
        'Randall' => 'The wise fool listens to the wind.',
        'Nasty P' => 'Chaos reveals the hidden path.'
    ];
    return isset($parables[$archetype]) ? $parables[$archetype] : 'Mystery parable.';
}
?>
