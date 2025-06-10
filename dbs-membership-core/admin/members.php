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
            <tr><th>User</th><th>Latin Name</th><th>Rank</th></tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user) : ?>
                <tr>
                    <td><?php echo esc_html($user->user_login); ?></td>
                    <td><?php echo esc_html(get_user_meta($user->ID, 'dbs_latin_name', true)); ?></td>
                    <td><?php echo esc_html(get_user_meta($user->ID, 'dbs_rank', true)); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
