<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}
// Cleanup options and user meta.
delete_option('dbs_mc_archetypes');
// Additional cleanup can be added here.
