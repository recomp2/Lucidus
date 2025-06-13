<?php
/* Template Name: Member Mirror */
get_header();
if(function_exists('lucidus_memory_trigger')){ lucidus_memory_trigger(); }
?>
<main>
<h1>Member Mirror</h1>
<?php if(is_user_logged_in()): ?>
<?php $rank = get_user_meta(get_current_user_id(),'dbs_rank',true); ?>
<p>Your rank: <?php echo esc_html($rank?$rank:'Unranked'); ?></p>
<?php else: ?>
<p>Please <a href="<?php echo esc_url(wp_login_url()); ?>">log in</a> to see your profile.</p>
<?php endif; ?>
</main>
<?php get_footer(); ?>
