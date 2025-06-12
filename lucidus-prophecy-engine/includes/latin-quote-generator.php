<?php
if (!defined('ABSPATH')) {
    exit;
}

function lpe_get_latin_quote() {
    $phrases = [
        'Nolite fumigare sine causa – Do not spark without reason.',
        'Carpe noctem – Seize the night.',
        'Vita brevis, ignis aeternus – Life is short, the fire eternal.'
    ];
    return $phrases[array_rand($phrases)];
}
?>
