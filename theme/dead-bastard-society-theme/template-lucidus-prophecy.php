<?php
/* Template Name: Lucidus Prophecy */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
?>
<div id="lucidus-prophecy">
    <?php echo do_shortcode('[lucidus_terminal]'); ?>
    <?php echo do_shortcode('[lucidus_prophecy_feed]'); ?>
</div>
<?php
get_footer();
?>
