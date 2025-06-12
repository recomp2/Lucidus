<?php
/* Template Name: DBS Login */
get_header();
?>
<div id="dbs-login">
    <h2><?php esc_html_e('Member Login', 'dead-bastard-society-theme'); ?></h2>
    <?php echo do_shortcode('[dbs_login]'); ?>
</div>
<?php
get_footer();
