<?php
if (!current_user_can('manage_options')) return;
$errors = array();
$required_dirs = array('/logs', '/dbs-library');
foreach($required_dirs as $d) {
    if (!is_writable(__DIR__ . '/..' . $d)) {
        $errors[] = "$d not writable";
    }
}
?>
<div class="wrap">
    <h1>Diagnostics</h1>
    <h2>PHP <?php echo phpversion(); ?>, WordPress <?php echo get_bloginfo('version'); ?></h2>
    <?php if($errors): ?>
        <div class="error"><p><?php echo implode('<br>', array_map('esc_html', $errors)); ?></p></div>
    <?php else: ?>
        <div class="updated"><p>All checks passed.</p></div>
    <?php endif; ?>
</div>
