<?php
if (!defined('ABSPATH')) exit;

function lucidus_core_feed_settings_page(){
    if(function_exists('dbs_update_user_memory')){
        dbs_update_user_memory('lucidus-core','feed-settings');
    }
    $opts = get_option('lucidus_feed_settings', [
        'enable_news' => 0,
        'enable_weather' => 0,
        'enable_astrology' => 0,
        'enable_external' => 0,
        'feed_urls' => '',
        'frequency' => 'daily',
        'target_tag' => 'global'
    ]);
    if(isset($_POST['lucidus_feed_settings_submit'])){
        check_admin_referer('lucidus_feed_settings');
        $opts['enable_news'] = isset($_POST['enable_news']) ? 1 : 0;
        $opts['enable_weather'] = isset($_POST['enable_weather']) ? 1 : 0;
        $opts['enable_astrology'] = isset($_POST['enable_astrology']) ? 1 : 0;
        $opts['enable_external'] = isset($_POST['enable_external']) ? 1 : 0;
        $opts['feed_urls'] = sanitize_textarea_field($_POST['feed_urls']);
        $opts['frequency'] = sanitize_text_field($_POST['frequency']);
        $opts['target_tag'] = sanitize_text_field($_POST['target_tag']);
        update_option('lucidus_feed_settings', $opts);
        echo '<div class="updated notice"><p>Settings saved.</p></div>';
    }
    ?>
    <div class="wrap">
        <h1>Lucidus Feed Settings</h1>
        <form method="post">
            <?php wp_nonce_field('lucidus_feed_settings'); ?>
            <table class="form-table">
                <tr><th scope="row">Enable News Feeds</th><td><input type="checkbox" name="enable_news" <?php checked($opts['enable_news'],1); ?>></td></tr>
                <tr><th scope="row">Enable Weather</th><td><input type="checkbox" name="enable_weather" <?php checked($opts['enable_weather'],1); ?>></td></tr>
                <tr><th scope="row">Enable Astrology</th><td><input type="checkbox" name="enable_astrology" <?php checked($opts['enable_astrology'],1); ?>></td></tr>
                <tr><th scope="row">Enable External APIs</th><td><input type="checkbox" name="enable_external" <?php checked($opts['enable_external'],1); ?>></td></tr>
                <tr><th scope="row">Feed URLs (one per line)</th><td><textarea name="feed_urls" rows="4" class="large-text"><?php echo esc_textarea($opts['feed_urls']); ?></textarea></td></tr>
                <tr><th scope="row">Frequency</th><td>
                    <select name="frequency">
                        <option value="hourly" <?php selected($opts['frequency'],'hourly'); ?>>Hourly</option>
                        <option value="daily" <?php selected($opts['frequency'],'daily'); ?>>Daily</option>
                        <option value="manual" <?php selected($opts['frequency'],'manual'); ?>>Manual</option>
                    </select>
                </td></tr>
                <tr><th scope="row">Target memory tag</th><td><input type="text" name="target_tag" value="<?php echo esc_attr($opts['target_tag']); ?>" class="regular-text"></td></tr>
            </table>
            <p><input type="submit" class="button button-primary" name="lucidus_feed_settings_submit" value="Save Settings"></p>
        </form>
    </div>
    <?php
}
