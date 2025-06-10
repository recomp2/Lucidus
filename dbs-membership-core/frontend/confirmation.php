<?php
$user_id = get_current_user_id();
$latin = get_user_meta($user_id, 'dbs_latin_name', true);
$rank = get_user_meta($user_id, 'dbs_rank', true);
$geo = get_user_meta($user_id, 'dbs_geo_name', true);
$phonetic = dbs_mc_generate_phonetic($latin);
$tags = dbs_mc_get_behavior_tags($user_id);
?>
<div class="dbs-confirmation">
    <h2>Welcome, <?php echo esc_html($latin); ?></h2>
    <p class="dbs-phonetic">(<?php echo esc_html($phonetic); ?>)</p>
    <p>Your rank: <?php echo esc_html($rank); ?></p>
    <p>Your town: <?php echo esc_html($geo); ?></p>
    <p>Tags: <?php echo esc_html(implode(', ', $tags)); ?></p>
    <p class="dbs-message">Lucidus remembers you.</p>
</div>
