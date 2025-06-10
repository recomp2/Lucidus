<?php
$user_id = get_current_user_id();
$latin = get_user_meta($user_id, 'dbs_latin_name', true);
$rank = get_user_meta($user_id, 'dbs_rank', true);
$geo = get_user_meta($user_id, 'dbs_geo_name', true);
?>
<div class="dbs-confirmation">
    <h2>Welcome, <?php echo esc_html($latin); ?></h2>
    <p>Your rank: <?php echo esc_html($rank); ?></p>
    <p>Your town: <?php echo esc_html($geo); ?></p>
</div>
