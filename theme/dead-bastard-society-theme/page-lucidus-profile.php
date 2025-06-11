<?php
/* Template Name: Lucidus Profile */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
if (!is_user_logged_in()) {
    echo '<p>' . esc_html__('Please log in to view your profile.', 'dead-bastard-society-theme') . '</p>';
} else {
    $user_id = get_current_user_id();
    $memory = lucidus_load_memory($user_id);
    ?>
    <div id="lucidus-profile">
        <h2><?php esc_html_e('Your Lucidus History', 'dead-bastard-society-theme'); ?></h2>
        <ul>
            <?php foreach ($memory as $entry): ?>
                <li><?php echo esc_html($entry['role'] . ': ' . $entry['content']); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php
}
get_footer();
?>
