<?php
if (!current_user_can('manage_options')) return;
$gospel_path = __DIR__ . '/../canon/default-gospel.json';
$gospel = file_exists($gospel_path) ? json_decode(file_get_contents($gospel_path), true) : array();
if (isset($_POST['line'])) {
    $gospel[] = array('text' => sanitize_text_field($_POST['line']), 'time' => time());
    file_put_contents($gospel_path, json_encode($gospel, JSON_PRETTY_PRINT));
}
?>
<div class="wrap">
    <h1>Gospel</h1>
    <form method="post">
        <input type="text" name="line" class="regular-text" />
        <?php submit_button('Add Line'); ?>
    </form>
    <ul>
    <?php foreach($gospel as $entry): ?>
        <li><?php echo esc_html($entry['text']); ?></li>
    <?php endforeach; ?>
    </ul>
</div>
