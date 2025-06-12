<?php
/* Template Name: Lucidus Settings */
get_header();
if (!current_user_can('manage_options')) {
    echo '<p>' . esc_html__('You do not have permission to view this page.', 'dead-bastard-society-theme') . '</p>';
} else {
    lucidus_terminal_settings_page();
}
get_footer();
