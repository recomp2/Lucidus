<?php get_header(); ?>
<div class="content-area">
<?php if (have_posts()): while (have_posts()): the_post(); ?>
    <article <?php post_class(); ?>>
        <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
        <?php the_excerpt(); ?>
    </article>
<?php endwhile; else: ?>
    <p>No content found.</p>
<?php endif; ?>
</div>
<?php get_footer(); ?>

