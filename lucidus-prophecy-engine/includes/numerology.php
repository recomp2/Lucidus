<?php
if (!defined('ABSPATH')) {
    exit;
}

function lpe_get_numerology($dob, $name) {
    $life = array_sum(str_split(preg_replace('/[^0-9]/', '', $dob)));
    while ($life > 9) {
        $life = array_sum(str_split($life));
    }

    $letters = strtolower(preg_replace('/[^a-z]/i', '', $name));
    $destiny = 0;
    for ($i = 0; $i < strlen($letters); $i++) {
        $destiny += ord($letters[$i]) - 96;
    }
    while ($destiny > 9) {
        $destiny = array_sum(str_split($destiny));
    }

    return [
        'life_path' => $life,
        'destiny'   => $destiny,
    ];
}
?>
