<?php
if (!current_user_can('manage_options')) {
    return;
}

$users = get_users();
?>
<div class="wrap">
    <h1>DBS Members</h1>
    <table class="widefat">
        <thead>
            <tr>
                <th>User</th><th>Latin Name</th><th>Archetype</th><th>Rank</th><th>Town</th><th>Tags</th><th>Profile</th><th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user) : ?>
                <?php $file = dbs_mc_write_profile($user->ID); ?>
                <tr>
                    <td><?php echo esc_html($user->user_login); ?></td>
                    <td><?php echo esc_html(get_user_meta($user->ID, 'dbs_latin_name', true)); ?></td>
                    <td><?php echo esc_html(get_user_meta($user->ID, 'dbs_archetype', true)); ?></td>
                    <td><?php echo esc_html(get_user_meta($user->ID, 'dbs_rank', true)); ?></td>
                    <td><?php echo esc_html(get_user_meta($user->ID, 'dbs_geo_name', true)); ?></td>
                    <td><?php echo esc_html(implode(', ', dbs_mc_get_behavior_tags($user->ID))); ?></td>
                    <td><a href="<?php echo esc_url(wp_normalize_path($file)); ?>" target="_blank">JSON</a></td>
                    <td><a href="<?php echo admin_url('admin.php?page=dbs-member-editor&user_id=' . $user->ID); ?>">Edit</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
