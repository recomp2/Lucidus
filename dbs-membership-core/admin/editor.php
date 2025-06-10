<?php
if (!current_user_can('manage_options')) {
    return;
}

$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
$user = get_userdata($user_id);
if (!$user) {
    echo '<div class="notice notice-error"><p>User not found.</p></div>';
    return;
}

if (isset($_POST['dbs_mc_editor_nonce']) && check_admin_referer('dbs_mc_save_profile', 'dbs_mc_editor_nonce')) {
    update_user_meta($user_id, 'dbs_latin_name', sanitize_text_field($_POST['dbs_latin_name']));
    update_user_meta($user_id, 'dbs_archetype', sanitize_text_field($_POST['dbs_archetype']));
    dbs_mc_assign_rank($user_id, intval($_POST['dbs_rank']));
    dbs_mc_set_behavior_tags($user_id, array_filter(array_map('trim', explode(',', $_POST['dbs_behavior_tags']))));
    update_user_meta($user_id, 'dbs_town', sanitize_text_field($_POST['dbs_town']));
    update_user_meta($user_id, 'dbs_county', sanitize_text_field($_POST['dbs_county']));
    update_user_meta($user_id, 'dbs_geo_name', sanitize_text_field($_POST['dbs_geo_name']));
    dbs_mc_write_profile($user_id);
    dbs_mc_notify_lucidus($user_id);
    echo '<div class="updated"><p>Profile saved.</p></div>';
}

$rank = get_user_meta($user_id, 'dbs_rank', true);
$latin = get_user_meta($user_id, 'dbs_latin_name', true);
$arch = get_user_meta($user_id, 'dbs_archetype', true);
$tags = implode(',', dbs_mc_get_behavior_tags($user_id));
$town = get_user_meta($user_id, 'dbs_town', true);
$county = get_user_meta($user_id, 'dbs_county', true);
$geo_name = get_user_meta($user_id, 'dbs_geo_name', true);
?>
<div class="wrap">
    <h1>Edit Member: <?php echo esc_html($user->user_login); ?></h1>
    <form method="post">
        <?php wp_nonce_field('dbs_mc_save_profile', 'dbs_mc_editor_nonce'); ?>
        <table class="form-table">
            <tr>
                <th><label for="dbs_latin_name">Latin Name</label></th>
                <td><input type="text" name="dbs_latin_name" id="dbs_latin_name" value="<?php echo esc_attr($latin); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="dbs_archetype">Archetype</label></th>
                <td>
                    <select name="dbs_archetype" id="dbs_archetype">
                        <?php foreach (dbs_mc_get_archetypes() as $a) : ?>
                            <option value="<?php echo esc_attr($a); ?>" <?php selected($arch, $a); ?>><?php echo esc_html(ucfirst($a)); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="dbs_rank">Rank</label></th>
                <td><input type="number" name="dbs_rank" id="dbs_rank" value="<?php echo esc_attr($rank); ?>"></td>
            </tr>
            <tr>
                <th><label for="dbs_behavior_tags">Behavior Tags</label></th>
                <td><input type="text" name="dbs_behavior_tags" id="dbs_behavior_tags" value="<?php echo esc_attr($tags); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="dbs_town">Town</label></th>
                <td><input type="text" name="dbs_town" id="dbs_town" value="<?php echo esc_attr($town); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="dbs_county">County</label></th>
                <td><input type="text" name="dbs_county" id="dbs_county" value="<?php echo esc_attr($county); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="dbs_geo_name">Geo Name</label></th>
                <td><input type="text" name="dbs_geo_name" id="dbs_geo_name" value="<?php echo esc_attr($geo_name); ?>" class="regular-text"></td>
            </tr>
        </table>
        <?php submit_button(); ?>
    </form>
</div>
