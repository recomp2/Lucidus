<?php
/*
Plugin Name: Lucidus Tier Access Loader
Description: Initializes the Lucidus tier access module.
*/
if ( ! defined( 'ABSPATH' ) ) { exit; }

require_once __DIR__ . '/tier-access-control.php';
require_once __DIR__ . '/tier-check.php';
require_once __DIR__ . '/lucidus-tier-handler.php';
require_once __DIR__ . '/admin-tier-settings.php';
require_once __DIR__ . '/blocked-access-handler.php';
require_once __DIR__ . '/access-log.php';
?>
