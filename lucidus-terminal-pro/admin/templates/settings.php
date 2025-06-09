<div class="wrap">
    <h1>Lucidus Terminal Settings</h1>
    <?php if ( isset( $_GET['updated'] ) ) : ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e( 'Settings updated.', 'lucidus' ); ?></p>
        </div>
    <?php endif; ?>
    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
        <?php wp_nonce_field( 'lucidus_save_settings' ); ?>
        <input type="hidden" name="action" value="lucidus_save_settings">
        <table class="form-table">
            <tr>
                <th scope="row"><label for="lucidus_openai_key">OpenAI API Key</label></th>
                <td><input name="lucidus_openai_key" id="lucidus_openai_key" type="text" value="<?php echo esc_attr( get_option( 'lucidus_openai_key' ) ); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="lucidus_elevenlabs_key">ElevenLabs API Key</label></th>
                <td><input name="lucidus_elevenlabs_key" id="lucidus_elevenlabs_key" type="text" value="<?php echo esc_attr( get_option( 'lucidus_elevenlabs_key' ) ); ?>" class="regular-text"></td>
            </tr>
        </table>
        <?php submit_button(); ?>
    </form>
</div>
