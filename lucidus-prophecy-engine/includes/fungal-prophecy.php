<?php
if (!defined('ABSPATH')) {
    exit;
}

function lpe_get_fungal_prophecy($strain, $dob) {
    if ($strain) {
        return 'The spirit of ' . $strain . ' guides your roots.';
    }
    $month = (int)date('m', strtotime($dob));
    $plants = [
        1 => 'Snowy Mycelium',
        2 => 'Early Sprout',
        3 => 'Spring Shroom',
        4 => 'Verdant Bud',
        5 => 'Blooming Cap',
        6 => 'Midsummer Stalk',
        7 => 'Deep Root',
        8 => 'Sun Seeker',
        9 => 'Autumn Spores',
        10 => 'Harvest Cap',
        11 => 'Frosty Stem',
        12 => 'Solstice Mold'
    ];
    return 'Your fungal guide is ' . ($plants[$month] ?? 'the Unknown Spore');
}
?>
