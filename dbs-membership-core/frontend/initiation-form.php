<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dbs_mc_initiate_nonce']) && wp_verify_nonce($_POST['dbs_mc_initiate_nonce'], 'dbs_mc_initiate')) {
    $user_id = get_current_user_id();
    if ($user_id) {
        $archetype = sanitize_text_field($_POST['dbs_archetype']);
        $town = sanitize_text_field($_POST['dbs_town']);
        $county = sanitize_text_field($_POST['dbs_county']);
        $latin_name = dbs_mc_generate_latin_name($archetype);
        $geo_name = dbs_mc_generate_geo_name($town);

        if (!dbs_mc_geo_claim_exists('default', $county)) {
            dbs_mc_store_geo_claim('default', $county, $geo_name);
        }

        update_user_meta($user_id, 'dbs_archetype', $archetype);
        update_user_meta($user_id, 'dbs_town', $town);
        update_user_meta($user_id, 'dbs_county', $county);
        update_user_meta($user_id, 'dbs_latin_name', $latin_name);
        update_user_meta($user_id, 'dbs_geo_name', $geo_name);
        dbs_mc_assign_rank($user_id);
        dbs_mc_set_behavior_tags($user_id, []);
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
        <select name="dbs_archetype" id="dbs_archetype" required>
            <?php foreach (dbs_mc_get_archetypes() as $arch) : ?>
                <option value="<?php echo esc_attr($arch); ?>"><?php echo esc_html(ucfirst($arch)); ?></option>
            <?php endforeach; ?>
        </select>
    </p>
    <p>
        <label for="dbs_town">Town</label>
        <input type="text" name="dbs_town" id="dbs_town" required>
    </p>
    <p>
        <label for="dbs_county">County</label>
        <input type="text" name="dbs_county" id="dbs_county" required>
    </p>
    <p><button type="submit">Join DBS</button></p>
</form>
<?php if (isset($_GET['dbs_confirm'])) { include DBS_MC_PLUGIN_DIR . 'frontend/confirmation.php'; } ?>
