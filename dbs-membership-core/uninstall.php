<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Remove options
delete_option('dbs_mc_archetypes');
delete_option('dbs_mc_rank_names');
delete_option('dbs_mc_enable_geo');

// Remove role
remove_role('dbs_member');
