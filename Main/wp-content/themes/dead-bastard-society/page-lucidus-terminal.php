<?php
/* Template Name: Lucidus Terminal */
get_header();
if(function_exists('lucidus_memory_trigger')){ lucidus_memory_trigger(); }
?>
<main class="lucidus-container">
  <h1>Lucidus Terminal</h1>
  <?php echo do_shortcode('[lucidus_terminal]'); ?>
</main>
<?php get_footer(); ?>
