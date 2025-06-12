<?php
/* Template Name: 420 Scroll Feed */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
?>
<div id="scroll-feed-page">
    <h2><?php esc_html_e('Latest Prophecy Scrolls', 'dead-bastard-society-theme'); ?></h2>
    <?php echo do_shortcode('[lucidus_prophecy_feed]'); ?>
</div>
<?php
get_footer();
?>
