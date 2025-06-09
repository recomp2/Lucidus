<div class="wrap">
    <h1>Lucidus Terminal Status</h1>
    <table class="widefat" style="max-width:600px">
        <tbody>
            <tr>
                <th>Plugin Version</th>
                <td><?php echo esc_html( LUCIDUS_PRO_VERSION ); ?></td>
            </tr>
            <tr>
                <th>WordPress Version</th>
                <td><?php echo esc_html( get_bloginfo( 'version' ) ); ?></td>
            </tr>
            <tr>
                <th>PHP Version</th>
                <td><?php echo esc_html( phpversion() ); ?></td>
            </tr>
            <tr>
                <th>OpenAI Key Set</th>
                <td><?php echo get_option( 'lucidus_openai_key' ) ? 'Yes' : 'No'; ?></td>
            </tr>
            <tr>
                <th>ElevenLabs Key Set</th>
                <td><?php echo get_option( 'lucidus_elevenlabs_key' ) ? 'Yes' : 'No'; ?></td>
            </tr>
        </tbody>
    </table>
</div>
