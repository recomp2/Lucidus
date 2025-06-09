<?php
if (!current_user_can('manage_options')) return;
$profile_dir = __DIR__ . '/../memory-archive/profiles';
$files = glob($profile_dir . '/*.{json,txt}', GLOB_BRACE);
$selected = isset($_GET['file']) ? basename($_GET['file']) : '';
$content = '';
if ($selected && file_exists("$profile_dir/$selected")) {
    $content = file_get_contents("$profile_dir/$selected");
}
?>
<div class="wrap">
    <h1>Memory Archive</h1>
    <form method="get">
        <select name="file">
            <option value="">Select file...</option>
            <?php foreach($files as $f): $n = basename($f); ?>
                <option value="<?php echo esc_attr($n); ?>" <?php selected($selected, $n); ?>><?php echo esc_html($n); ?></option>
            <?php endforeach; ?>
        </select>
        <?php submit_button('Open', 'secondary', '', false); ?>
    </form>
    <?php if($content): ?>
        <h2><?php echo esc_html($selected); ?></h2>
        <form method="post">
            <textarea name="content" rows="15" cols="80" class="large-text code"><?php echo esc_textarea($content); ?></textarea>
            <input type="hidden" name="file" value="<?php echo esc_attr($selected); ?>" />
            <?php submit_button('Save'); ?>
        </form>
    <?php endif; ?>
</div>
<?php
if (isset($_POST['file']) && file_exists("$profile_dir/" . basename($_POST['file']))) {
    file_put_contents("$profile_dir/" . basename($_POST['file']), stripslashes($_POST['content']));
    echo '<div class="updated"><p>File saved.</p></div>';
}
?>
