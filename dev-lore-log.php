<?php
if (!defined('ABSPATH')) exit;

function devLoreLog($message) {
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('[Lore] ' . $message);
    }
}
