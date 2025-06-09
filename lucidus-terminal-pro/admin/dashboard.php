<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function lucidus_dashboard_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    $openai_key   = get_option( 'lucidus_openai_key', '' );
    $voice_model  = get_option( 'lucidus_voice_model', 'tts-1' );
    $memory_files = count( lucidus_list_memory_files() );
    ?>
    <div class="wrap">
        <h1>Lucidus Dashboard</h1>
        <p><strong>OpenAI Key Set:</strong> <?php echo $openai_key ? 'Yes' : 'No'; ?></p>
        <p><strong>Voice Model:</strong> <?php echo esc_html( $voice_model ); ?></p>
        <p><strong>Memory Files:</strong> <?php echo intval( $memory_files ); ?></p>
    </div>
    <?php
}
