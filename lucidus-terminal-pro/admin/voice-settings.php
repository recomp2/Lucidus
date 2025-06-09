<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function lucidus_voice_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    if ( isset( $_POST['save_voice'] ) && check_admin_referer( 'lucidus_voice_settings', 'lucidus_voice_settings_nonce' ) ) {
        update_option( 'lucidus_voice_model', sanitize_text_field( $_POST['voice_model'] ) );
        update_option( 'lucidus_voice_mute', isset( $_POST['mute'] ) ? 1 : 0 );
    }
    $model = esc_attr( get_option( 'lucidus_voice_model', 'tts-1' ) );
    $mute  = get_option( 'lucidus_voice_mute', 0 );
    ?>
    <div class="wrap">
        <h1>Voice Settings</h1>
        <form method="post">
            <?php wp_nonce_field( 'lucidus_voice_settings', 'lucidus_voice_settings_nonce' ); ?>
            <p>
                <label>Voice Model
                    <input type="text" name="voice_model" value="<?php echo $model; ?>">
                </label>
            </p>
            <p>
                <label><input type="checkbox" name="mute" value="1" <?php checked( $mute, 1 ); ?>>Mute</label>
            </p>
            <p><input type="submit" name="save_voice" class="button-primary" value="Save"></p>
        </form>
        <form method="post">
            <?php wp_nonce_field( 'lucidus_test_voice', 'lucidus_test_voice_nonce' ); ?>
            <p><input type="submit" name="test_voice" class="button" value="Test Voice"></p>
        </form>
        <?php
        if ( isset( $_POST['test_voice'] ) && check_admin_referer( 'lucidus_test_voice', 'lucidus_test_voice_nonce' ) ) {
            $api_key = get_option( 'lucidus_openai_key', '' );
            if ( $api_key ) {
                $response = lucidus_openai_tts( 'Testing voice', $model, $api_key );
                if ( ! is_wp_error( $response ) ) {
                    echo '<p>Voice request sent.</p>';
                } else {
                    echo '<p>Error.</p>';
                }
            } else {
                echo '<p>No API Key.</p>';
            }
        }
        ?>
    </div>
    <?php
}
