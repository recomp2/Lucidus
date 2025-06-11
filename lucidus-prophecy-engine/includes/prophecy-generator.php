<?php
if (!defined('ABSPATH')) {
    exit;
}

function lpe_generate_prophecy($data) {
    $username   = sanitize_text_field($data['username'] ?? '');
    $dob        = sanitize_text_field($data['dob'] ?? '');
    $town       = sanitize_text_field($data['town'] ?? '');
    $archetype  = sanitize_text_field($data['archetype'] ?? 'Dub');
    $strain     = sanitize_text_field($data['strain'] ?? '');
    $question   = sanitize_textarea_field($data['question'] ?? '');

    $astrology  = lpe_get_astrology($dob);
    $numerology = lpe_get_numerology($dob, $username);
    $card       = lpe_draw_tarot($archetype);
    $fungal     = lpe_get_fungal_prophecy($strain, $dob);
    $parable    = lpe_get_parable($archetype);
    $latin      = lpe_get_latin_quote();

    $prophecy  = "<strong>Astrology:</strong> {$astrology}<br>";
    $prophecy .= "<strong>Numerology:</strong> Life Path {$numerology['life_path']}, Destiny {$numerology['destiny']}<br>";
    $prophecy .= "<strong>Tarot:</strong> {$card}<br>";
    $prophecy .= "<strong>Fungal Wisdom:</strong> {$fungal}<br>";
    $prophecy .= "<strong>Parable:</strong> {$parable}<br>";
    if ($question) {
        $prophecy .= "<strong>Question:</strong> {$question}<br>";
    }
    $prophecy .= "<em>{$latin}</em>";

    return $prophecy;
}
