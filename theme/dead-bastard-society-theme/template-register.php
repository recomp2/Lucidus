<?php
/* Template Name: DBS Register */
get_header();
?>
<div id="dbs-register">
    <h2><?php esc_html_e('Member Registration', 'dead-bastard-society-theme'); ?></h2>
    <?php echo do_shortcode('[dbs_register]'); ?>
</div>
<?php
get_footer();
