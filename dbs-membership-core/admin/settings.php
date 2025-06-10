<?php
if (!current_user_can('manage_options')) {
    return;
}

if (isset($_POST['dbs_mc_settings_nonce']) && check_admin_referer('dbs_mc_save_settings', 'dbs_mc_settings_nonce')) {
    update_option('dbs_mc_archetypes', sanitize_text_field($_POST['dbs_mc_archetypes']));
    update_option('dbs_mc_rank_names', sanitize_text_field($_POST['dbs_mc_rank_names']));
    echo '<div class="updated"><p>Settings saved.</p></div>';
}
$archetypes = get_option('dbs_mc_archetypes', 'dub, randall, nasty_p');
$rank_names = get_option('dbs_mc_rank_names', 'Initiate, Member, Elder');
?>
<div class="wrap">
    <h1>DBS Membership Settings</h1>
    <form method="post">
        <?php wp_nonce_field('dbs_mc_save_settings', 'dbs_mc_settings_nonce'); ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="dbs_mc_archetypes">Archetypes</label></th>
                <td><input name="dbs_mc_archetypes" type="text" id="dbs_mc_archetypes" value="<?php echo esc_attr($archetypes); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="dbs_mc_rank_names">Rank Names</label></th>
                <td><input name="dbs_mc_rank_names" type="text" id="dbs_mc_rank_names" value="<?php echo esc_attr($rank_names); ?>" class="regular-text"></td>
            </tr>
        </table>
        <?php submit_button(); ?>
    </form>
</div>
