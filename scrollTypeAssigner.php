<?php
if (!defined('ABSPATH')) exit;
require_once plugin_dir_path(__FILE__) . '../config/lore-loader/lucidus-lore-loader.php';

function scrollTypeAssigner($context = []) {
    if (!empty($context['denied'])) {
        return 'denial_scroll';
    }
    if (!empty($context['quest'])) {
        return 'trial_scroll';
    }
    if (!empty($context['chaos'])) {
        return 'doom_vision';
    }
    return 'minor_prophecy';
}
