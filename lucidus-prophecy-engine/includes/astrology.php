<?php
if (!defined('ABSPATH')) {
    exit;
}

function lpe_get_astrology($dob) {
    $date = strtotime($dob);
    if (!$date) {
        return 'Stars lost in smoke';
    }
    $month = (int)date('m', $date);
    $signs = [
        1 => 'Capricorn',
        2 => 'Aquarius',
        3 => 'Pisces',
        4 => 'Aries',
        5 => 'Taurus',
        6 => 'Gemini',
        7 => 'Cancer',
        8 => 'Leo',
        9 => 'Virgo',
        10 => 'Libra',
        11 => 'Scorpio',
        12 => 'Sagittarius'
    ];
    $sign = isset($signs[$month]) ? $signs[$month] : 'Unknown';
    return 'Your dominant sign is ' . $sign;
}
?>
