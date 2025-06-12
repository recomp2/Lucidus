<?php
/* Template Name: Membership Map */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
?>
<div id="dbs-map-page">
    <?php echo do_shortcode('[dbs_members_map]'); ?>
</div>
<?php
get_footer();
?>
