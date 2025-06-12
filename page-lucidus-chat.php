<?php
/* Template Name: Lucidus Chat */
get_header();
?>
<div id="lucidus-chat-page">
    <?php echo do_shortcode('[lucidus_terminal]'); ?>
    <h2><?php esc_html_e('Latest Prophecies', 'dead-bastard-society-theme'); ?></h2>
    <?php echo do_shortcode('[lucidus_prophecy_feed]'); ?>
</div>
<?php
get_footer();
