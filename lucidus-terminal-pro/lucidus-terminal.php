<?php
/*
Plugin Name: Lucidus Terminal Pro
Description: Admin interface and modular script loader for the Dead Bastard Society.
Version: 1.0
Author: DBS Systems
*/

if (!defined('ABSPATH')) { exit; }

define('LUCIDUS_TERMINAL_PRO_PATH', plugin_dir_path(__FILE__));
define('LUCIDUS_TERMINAL_PRO_URL', plugin_dir_url(__FILE__));

require_once LUCIDUS_TERMINAL_PRO_PATH . 'core/lucidus-terminal-core.php';
