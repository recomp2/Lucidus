<?php get_header(); ?>
<main id="primary" class="site-main">
    <h1><?php the_title(); ?></h1>
    <div class="entry-content">
        <?php the_content(); ?>
        <form method="post">
            <?php wp_nonce_field('complete_scroll','complete_scroll_nonce'); ?>
            <input type="submit" name="complete_scroll" value="Complete Scroll">
        </form>
    </div>
</main>
<?php get_footer(); ?>
