<?php
if (!defined('ABSPATH')) exit;

function tier_access_get_config() {
    static $config = null;
    if ($config !== null) return $config;
    $file = __DIR__ . '/membership-tier-config.json';
    if (file_exists($file)) {
        $json = file_get_contents($file);
        $config = json_decode($json, true);
    }
    if (!is_array($config)) {
        $config = ['tiers'=>[]];
    }
    return $config;
}

function tier_access_get_settings() {
    $options = get_option('lucidus_tier_settings');
    if (!is_array($options)) {
        $options = tier_access_get_config()['tiers'];
    }
    return $options;
}
function tier_access_behavior_config() {
    static $behavior = null;
    if ($behavior !== null) return $behavior;
    $file = __DIR__ . '/lucidus-tier-behavior.json';
    if (file_exists($file)) {
        $json = file_get_contents($file);
        $behavior = json_decode($json, true);
    }
    if (!is_array($behavior)) {
        $behavior = ['rank_behavior_map'=>[], 'deny_response_templates'=>[]];
    }
    return $behavior;
}

function tier_access_get_user_rank($user_id = null) {
    if (null === $user_id) {
        $user_id = get_current_user_id();
    }
    $rank = get_user_meta($user_id, 'dbs_member_rank', true);
    return $rank ? $rank : 'member';
}

function tier_access_get_rank_behavior($rank) {
    $behavior = tier_access_behavior_config();
    return $behavior['rank_behavior_map'][$rank] ?? '';
}

function tier_access_get_user_archetype($user_id = null) {
    if (null === $user_id) {
        $user_id = get_current_user_id();
    }
    $arch = get_user_meta($user_id, 'dbs_archetype', true);
    return $arch ? $arch : 'dub';
}

function tier_access_get_archetype_variant($archetype) {
    $behavior = tier_access_behavior_config();
    return $behavior['archetype_response_variants'][$archetype] ?? '';
}

function tier_access_get_deny_response($user_id = null) {
    $behavior = tier_access_behavior_config();
    $msg = __('Access denied.', 'lucidus-terminal-pro');
    if (!empty($behavior['deny_response_templates'])) {
        $msg = $behavior['deny_response_templates'][array_rand($behavior['deny_response_templates'])];
    }
    $arch = tier_access_get_user_archetype($user_id);
    $variant = tier_access_get_archetype_variant($arch);
    if ($variant) {
        $msg = $variant . ': ' . $msg;
    }
    return $msg;
}

function tier_access_prompt_append($rank, $archetype, $mood = 'neutral') {
    $behavior = tier_access_behavior_config();
    if (empty($behavior['custom_prompt_append'])) {
        return '';
    }
    $template = $behavior['custom_prompt_append'];
    return strtr($template, [
        '{rank}' => $rank,
        '{archetype}' => $archetype,
        '{mood}' => $mood,
    ]);
}
