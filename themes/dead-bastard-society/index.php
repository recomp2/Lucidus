<?php get_header(); ?>
<main>
    <h1>Welcome to Dead Bastard Society</h1>
    <?php
    if ( have_posts() ) {
        while ( have_posts() ) {
            the_post();
            the_content();
        }
    }
    ?>
</main>
<?php get_footer(); ?>
