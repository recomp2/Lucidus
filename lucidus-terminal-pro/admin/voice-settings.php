<?php
if ( ! current_user_can( 'manage_options' ) ) {
    return;
}

if ( isset( $_POST['lucidus_voice_settings_nonce'] ) && wp_verify_nonce( $_POST['lucidus_voice_settings_nonce'], 'lucidus_voice_settings_save' ) ) {
    if ( isset( $_POST['lucidus_voice_model'] ) ) {
        update_option( 'lucidus_voice_model', sanitize_text_field( $_POST['lucidus_voice_model'] ) );
    }
    if ( isset( $_POST['lucidus_voice_speed'] ) ) {
        update_option( 'lucidus_voice_speed', floatval( $_POST['lucidus_voice_speed'] ) );
    }
    if ( isset( $_POST['lucidus_voice_pitch'] ) ) {
        update_option( 'lucidus_voice_pitch', floatval( $_POST['lucidus_voice_pitch'] ) );
    }
    if ( isset( $_POST['lucidus_voice_prompt'] ) ) {
        update_option( 'lucidus_voice_prompt', sanitize_text_field( $_POST['lucidus_voice_prompt'] ) );
    }
    echo '<div class="updated"><p>Settings saved.</p></div>';
}

$model  = get_option( 'lucidus_voice_model', 'nova' );
$speed  = get_option( 'lucidus_voice_speed', 1.0 );
$pitch  = get_option( 'lucidus_voice_pitch', 0 );
$prompt = get_option( 'lucidus_voice_prompt', 'Lucidus is alive.' );
?>
<div class="wrap">
<h1>Lucidus Voice Settings</h1>
<form method="post">
<?php wp_nonce_field( 'lucidus_voice_settings_save', 'lucidus_voice_settings_nonce' ); ?>
<table class="form-table">
<tr>
<th scope="row"><label for="lucidus_voice_model">Voice Model</label></th>
<td>
<select name="lucidus_voice_model" id="lucidus_voice_model">
<?php
$models = array( 'nova', 'echo', 'onyx', 'shimmer', 'fable' );
foreach ( $models as $m ) {
    echo '<option value="' . esc_attr( $m ) . '"' . selected( $model, $m, false ) . '>' . esc_html( ucfirst( $m ) ) . '</option>';
}
?>
</select>
</td>
</tr>
<tr>
<th scope="row"><label for="lucidus_voice_speed">Speed</label></th>
<td>
<input type="range" name="lucidus_voice_speed" id="lucidus_voice_speed" min="0.25" max="4.0" step="0.05" value="<?php echo esc_attr( $speed ); ?>" oninput="document.getElementById('lucidus_speed_value').innerText = this.value" />
<span id="lucidus_speed_value"><?php echo esc_html( $speed ); ?></span>
</td>
</tr>
<tr>
<th scope="row"><label for="lucidus_voice_pitch">Pitch</label></th>
<td>
<input type="range" name="lucidus_voice_pitch" id="lucidus_voice_pitch" min="-20" max="20" step="1" value="<?php echo esc_attr( $pitch ); ?>" oninput="document.getElementById('lucidus_pitch_value').innerText = this.value" />
<span id="lucidus_pitch_value"><?php echo esc_html( $pitch ); ?></span>
</td>
</tr>
<tr>
<th scope="row"><label for="lucidus_voice_prompt">Prompt</label></th>
<td>
<input type="text" class="regular-text" name="lucidus_voice_prompt" id="lucidus_voice_prompt" value="<?php echo esc_attr( $prompt ); ?>" placeholder="Test phrase" />
</td>
</tr>
</table>
<p class="submit">
<input type="submit" class="button-primary" value="Save Settings" />
<button type="button" class="button" onclick="playLucidusTest()">Test Voice</button>
</p>
</form>
</div>
<script type="text/javascript">
function playLucidusTest() {
    const audio = new Audio("<?php echo admin_url( 'admin-ajax.php?action=lucidus_test_voice' ); ?>");
    audio.play();
}
</script>
