<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dbs_mc_initiate_nonce']) && wp_verify_nonce($_POST['dbs_mc_initiate_nonce'], 'dbs_mc_initiate')) {
    $user_id = get_current_user_id();
    if ($user_id) {
        $archetype = sanitize_text_field($_POST['dbs_archetype']);
        $town = sanitize_text_field($_POST['dbs_town']);
        $latin_name = dbs_mc_generate_latin_name($archetype);
        $geo_name = dbs_mc_generate_geo_name($town);

        update_user_meta($user_id, 'dbs_archetype', $archetype);
        update_user_meta($user_id, 'dbs_town', $town);
        update_user_meta($user_id, 'dbs_latin_name', $latin_name);
        update_user_meta($user_id, 'dbs_geo_name', $geo_name);
        dbs_mc_assign_rank($user_id);
        dbs_mc_write_profile($user_id);
        dbs_mc_notify_lucidus($user_id);
        wp_redirect(add_query_arg('dbs_confirm', 1, get_permalink()));
        exit;
    } else {
        echo '<p>You must be logged in.</p>';
    }
}
?>
<form method="post" class="dbs-initiation-form">
    <?php wp_nonce_field('dbs_mc_initiate', 'dbs_mc_initiate_nonce'); ?>
    <p>
        <label for="dbs_archetype">Archetype</label>
        <input type="text" name="dbs_archetype" id="dbs_archetype" required>
    </p>
    <p>
        <label for="dbs_town">Town</label>
        <input type="text" name="dbs_town" id="dbs_town" required>
    </p>
    <p><button type="submit">Join DBS</button></p>
</form>
<?php if (isset($_GET['dbs_confirm'])) { include DBS_MC_PLUGIN_DIR . 'frontend/confirmation.php'; } ?>
