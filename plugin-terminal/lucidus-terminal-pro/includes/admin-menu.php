<?php
if (!defined('ABSPATH')) exit;

function lucidus_terminal_menu() {
    add_menu_page(__('Lucidus Terminal', 'lucidus-terminal-pro'), __('Lucidus Terminal', 'lucidus-terminal-pro'), 'manage_options', 'lucidus-terminal', 'lucidus_terminal_page');
    add_submenu_page('lucidus-terminal', __('Settings', 'lucidus-terminal-pro'), __('Settings', 'lucidus-terminal-pro'), 'manage_options', 'lucidus-terminal-settings', 'lucidus_terminal_settings_page');
}
add_action('admin_menu', 'lucidus_terminal_menu');

function lucidus_terminal_assets() {
    wp_enqueue_script('lucidus-chat', plugin_dir_url(dirname(__DIR__)) . 'assets/js/chat.js', ['jquery'], '1.0', true);
    wp_enqueue_script('lucidus-prophecy-feed', plugin_dir_url(dirname(__DIR__)) . 'assets/js/prophecy-feed.js', ['jquery'], '1.0', true);
    wp_enqueue_script('lucidus-module-integration', plugin_dir_url(dirname(__DIR__)) . 'modules/module-integration.js', ['jquery'], '1.0', true);
    wp_enqueue_style('lucidus-chat', plugin_dir_url(dirname(__DIR__)) . 'assets/css/chat.css');
    wp_localize_script('lucidus-chat', 'lucidus_chat', [
        'ajax_url' => rest_url('lucidus/v1/chat'),
        'feed_url' => rest_url('lucidus/v1/prophecies'),
        'nonce'    => wp_create_nonce('wp_rest')
    ]);
}

function lucidus_terminal_page() {
    if (!current_user_can('manage_options')) {
        return;
    }
    lucidus_terminal_assets();
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Lucidus Terminal', 'lucidus-terminal-pro'); ?></h1>
        <div id="lucidus-terminal">
            <div id="chat-window"></div>
            <input type="text" id="chat-input" placeholder="<?php esc_attr_e('Speak to Lucidus', 'lucidus-terminal-pro'); ?>" />
            <button id="chat-send">Send</button>
        </div>
    </div>
    <?php
}

function lucidus_terminal_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    if (isset($_POST['openai_key']) && check_admin_referer('lucidus_save_settings')) {
        update_option('lucidus_openai_key', sanitize_text_field(wp_unslash($_POST['openai_key'])));
        echo '<div class="updated"><p>' . esc_html__('Settings saved.', 'lucidus-terminal-pro') . '</p></div>';
    }

    if (isset($_POST['clear_memory']) && check_admin_referer('lucidus_clear_memory')) {
        lucidus_clear_memory();
        echo '<div class="updated"><p>' . esc_html__('Memory cleared.', 'lucidus-terminal-pro') . '</p></div>';
    }

    $key = esc_attr(get_option('lucidus_openai_key'));
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Lucidus Terminal Settings', 'lucidus-terminal-pro'); ?></h1>
        <form method="post">
            <?php wp_nonce_field('lucidus_save_settings'); ?>
            <label><?php esc_html_e('OpenAI API Key', 'lucidus-terminal-pro'); ?></label>
            <input type="text" name="openai_key" value="<?php echo $key; ?>" size="50" />
            <p class="submit"><button class="button-primary"><?php esc_html_e('Save', 'lucidus-terminal-pro'); ?></button></p>
        </form>
        <form method="post" style="margin-top:20px;">
            <?php wp_nonce_field('lucidus_clear_memory'); ?>
            <input type="hidden" name="clear_memory" value="1" />
            <?php submit_button(__('Clear Memory', 'lucidus-terminal-pro')); ?>
        </form>
    </div>
    <?php
}

function lucidus_terminal_shortcode() {
    lucidus_terminal_assets();
    ob_start();
    ?>
    <div id="lucidus-terminal">
        <div id="chat-window"></div>
        <input type="text" id="chat-input" placeholder="Speak to Lucidus" />
        <button id="chat-send">Send</button>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('lucidus_terminal', 'lucidus_terminal_shortcode');
?>
