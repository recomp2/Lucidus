<?php
/* Template Name: DBS Dashboard */
get_header();
?>
<div id="dbs-dashboard">
    <h2><?php esc_html_e('Member Dashboard', 'dead-bastard-society-theme'); ?></h2>
    <?php echo do_shortcode('[dbs_dashboard]'); ?>
</div>
<?php
get_footer();
