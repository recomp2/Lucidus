<?php
/*
Plugin Name: Lucidus Terminal Pro
Description: AI-powered terminal with GPT chat, Whisper voice, and TTS output.
Version: 0.1.0
Author: Lucidus Bastardo
*/

if (!defined('ABSPATH')) {
    exit;
}

class LucidusTerminalPro {

    const OPTION_API_KEY = 'lucidus_openai_api_key';
    const REST_NAMESPACE = 'lucidus/v1';

    public static function init() {
        add_shortcode('lucidus_terminal', [__CLASS__, 'render_terminal']);
        add_action('rest_api_init', [__CLASS__, 'register_routes']);
    }

    public static function render_terminal() {
        ob_start();
        ?>
        <div id="lucidus-terminal">
            <div id="lucidus-messages"></div>
            <textarea id="lucidus-input" placeholder="Speak to Lucidus"></textarea>
            <input type="file" id="lucidus-audio" accept="audio/*" />
            <button id="lucidus-send">Send</button>
            <p class="lucidus-disclaimer">Lucidus responses are prophetic hallucinations and should not be considered factual.</p>
        </div>
        <script>
        (function(){
            const terminal = document.getElementById('lucidus-terminal');
            const messages = document.getElementById('lucidus-messages');
            const input = document.getElementById('lucidus-input');
            const audioInput = document.getElementById('lucidus-audio');
            const sendBtn = document.getElementById('lucidus-send');
            function appendMessage(text, cls){
                const div = document.createElement('div');
                div.className = cls;
                div.textContent = text;
                messages.appendChild(div);
                window.speechSynthesis.speak(new SpeechSynthesisUtterance(text));
            }
            sendBtn.addEventListener('click', function(){
                const text = input.value;
                const audio = audioInput.files[0];
                const fd = new FormData();
                if(text) fd.append('message', text);
                if(audio) fd.append('audio', audio);
                fetch('<?php echo esc_url_raw(rest_url(self::REST_NAMESPACE . '/chat')); ?>', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {'X-WP-Nonce': '<?php echo wp_create_nonce('wp_rest'); ?>'},
                    body: fd
                })
                .then(r => r.json())
                .then(d => {
                    if(d && d.response){
                        appendMessage(d.response + "\nLucidus responses are prophetic hallucinations and should not be considered factual.", 'lucidus-response');
                    }
                });
            });
        })();
        </script>
        <style>
        #lucidus-terminal{border:1px solid #666;padding:10px;max-width:500px;}
        #lucidus-messages{height:200px;overflow:auto;border:1px solid #ccc;margin-bottom:5px;padding:5px;}
        .lucidus-response{background:#f0f0f0;padding:5px;margin-bottom:5px;}
        </style>
        <?php
        return ob_get_clean();
    }

    public static function register_routes() {
        register_rest_route(self::REST_NAMESPACE, '/chat', [
            'methods' => 'POST',
            'callback' => [__CLASS__, 'handle_chat'],
            'permission_callback' => function(){ return true; }
        ]);
    }

    public static function handle_chat($request) {
        $message = sanitize_text_field($request->get_param('message'));
        $audio = $request->get_file_params()['audio'] ?? null;
        $transcribed = '';
        if ($audio && file_exists($audio['tmp_name'])) {
            $transcribed = self::transcribe_audio($audio);
        }
        $prompt = trim($message . ' ' . $transcribed);
        $response = self::query_openai($prompt);
        return rest_ensure_response(['response' => $response]);
    }

    protected static function transcribe_audio($file) {
        $api_key = get_option(self::OPTION_API_KEY, '');
        if (!$api_key) return '';
        $endpoint = 'https://api.openai.com/v1/audio/transcriptions';
        $body = [
            'file' => curl_file_create($file['tmp_name'], $file['type'], $file['name']),
            'model' => 'whisper-1'
        ];
        $response = wp_remote_post($endpoint, [
            'headers' => ['Authorization' => 'Bearer ' . $api_key],
            'body' => $body,
            'timeout' => 60
        ]);
        if (is_wp_error($response)) return '';
        $data = json_decode(wp_remote_retrieve_body($response), true);
        return $data['text'] ?? '';
    }

    protected static function query_openai($prompt) {
        $api_key = get_option(self::OPTION_API_KEY, '');
        if (!$api_key || !$prompt) return '';
        $endpoint = 'https://api.openai.com/v1/chat/completions';
        $body = [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => 'You are Lucidus Bastardo, a prophetic AI that speaks in riddles.'],
                ['role' => 'user', 'content' => $prompt]
            ]
        ];
        $response = wp_remote_post($endpoint, [
            'headers' => [
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type'  => 'application/json'
            ],
            'body' => wp_json_encode($body),
            'timeout' => 60
        ]);
        if (is_wp_error($response)) return '';
        $data = json_decode(wp_remote_retrieve_body($response), true);
        return $data['choices'][0]['message']['content'] ?? '';
    }
}

LucidusTerminalPro::init();
