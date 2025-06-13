<?php
/*
Plugin Name: Lucidus Panel
Description: Control panel for Lucidus voice and terminal settings.
Version: 0.4
*/
if (!defined('ABSPATH')) exit;

function lucidus_panel_activate(){
    $defaults = [
        'lucidus_voice_enabled' => '0',
        'lucidus_microphone_enabled' => '0',
        'lucidus_tts_enabled' => '0',
        'lucidus_personality_mode' => 'dub',
        'lucidus_prompt_builder' => '',
        'lucidus_chat_memory' => '1'
    ];
    foreach($defaults as $key => $val){
        if(false === get_option($key)){
            add_option($key, $val);
        }
    }
}
register_activation_hook(__FILE__, 'lucidus_panel_activate');

function lucidus_panel_menu(){
    add_menu_page('Lucidus Panel', 'Lucidus Panel', 'manage_options', 'lucidus-panel', 'lucidus_panel_page');
    add_submenu_page('lucidus-panel','Diagnostics','Diagnostics','manage_options','lucidus-panel-diagnostics','lucidus_panel_diagnostics_page');
    add_submenu_page('lucidus-panel','Internet Access','Internet Access','manage_options','lucidus-panel-internet','lucidus_panel_internet_page');
}
add_action('admin_menu', 'lucidus_panel_menu');

function lucidus_panel_page(){
    if(function_exists('dbs_update_user_memory')){
        dbs_update_user_memory('lucidus-panel','settings');
    }
    if(isset($_POST['lucidus_panel_settings'])){
        check_admin_referer('lucidus_panel');
        update_option('lucidus_voice_enabled', isset($_POST['lucidus_voice_enabled']) ? '1' : '0');
        update_option('lucidus_microphone_enabled', isset($_POST['lucidus_microphone_enabled']) ? '1' : '0');
        update_option('lucidus_tts_enabled', isset($_POST['lucidus_tts_enabled']) ? '1' : '0');
        update_option('lucidus_personality_mode', sanitize_text_field($_POST['lucidus_personality_mode'] ?? 'dub'));
        update_option('lucidus_prompt_builder', sanitize_textarea_field($_POST['lucidus_prompt_builder'] ?? ''));
        update_option('lucidus_chat_memory', isset($_POST['lucidus_chat_memory']) ? '1' : '0');
        echo '<div class="updated notice"><p>Settings saved.</p></div>';
    }
    $enabled = get_option('lucidus_voice_enabled','0') === '1';
    $microphone = get_option('lucidus_microphone_enabled','0')==='1';
    $tts = get_option('lucidus_tts_enabled','0')==='1';
    $personality = get_option('lucidus_personality_mode','dub');
    $prompt = get_option('lucidus_prompt_builder','');
    $chatmem = get_option('lucidus_chat_memory','1')==='1';
    echo '<div class="wrap"><h1>Lucidus Panel</h1>';
    $screen = get_current_screen();
    if($screen && method_exists($screen, 'add_help_tab')){
        $screen->add_help_tab([
            'id' => 'lucidus_panel_help',
            'title' => __('Usage'),
            'content' => '<p>' . esc_html__('Toggle voice output for Lucidus.', 'lucidus-panel') . '</p>'
        ]);
    }
    echo '<form method="post">';
    wp_nonce_field('lucidus_panel');
    echo '<input type="hidden" name="lucidus_panel_settings" value="1" />';
    echo '<p><label><input type="checkbox" name="lucidus_voice_enabled" value="1" '.($enabled?'checked':'').'/> '.esc_html__('Enable Voice','lucidus-panel').'</label></p>';
    echo '<p><label><input type="checkbox" name="lucidus_microphone_enabled" value="1" '.($microphone?'checked':'').'/> '.esc_html__('Enable Microphone','lucidus-panel').'</label></p>';
    echo '<p><label><input type="checkbox" name="lucidus_tts_enabled" value="1" '.($tts?'checked':'').'/> '.esc_html__('Enable TTS','lucidus-panel').'</label></p>';
    echo '<p>'.esc_html__('Personality:','lucidus-panel').' <select name="lucidus_personality_mode">';
    foreach(['dub','randall','nasty'] as $p){
        echo '<option value="'.esc_attr($p).'"'.selected($p,$personality,false).'>'.esc_html(ucfirst($p)).'</option>';
    }
    echo '</select></p>';
    echo '<p><label>'.esc_html__('Prompt Builder','lucidus-panel').'<br><textarea name="lucidus_prompt_builder" rows="3" cols="50">'.esc_textarea($prompt).'</textarea></label></p>';
    echo '<p><label><input type="checkbox" name="lucidus_chat_memory" value="1" '.($chatmem?'checked':'').'/> '.esc_html__('Enable Chat Memory','lucidus-panel').'</label></p>';
    echo '<p><button class="button button-primary">'.esc_html__('Save','lucidus-panel').'</button></p>';
    echo '</form></div>';
}

function lucidus_panel_diagnostics_page(){
    if(!current_user_can('manage_options')){
        return;
    }
    if(function_exists('dbs_update_user_memory')){
        dbs_update_user_memory('lucidus-panel','diagnostics');
    }
    $file = WP_CONTENT_DIR . '/dbs-library/memory-archive/system.json';
    $entries = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
    echo '<div class="wrap"><h1>'.esc_html__('Diagnostics','lucidus-panel').'</h1>';
    if(empty($entries)){
        echo '<p>'.esc_html__('No memory activity logged.','lucidus-panel').'</p>';
    }else{
        echo '<ul class="lucidus-diagnostics-log">';
        foreach(array_slice(array_reverse($entries),0,10) as $e){
            $msg = esc_html($e['time'].': '.$e['message']);
            echo '<li>'.$msg.'</li>';
        }
        echo '</ul>';
    }
    echo '</div>';
}

function lucidus_panel_internet_page(){
    if(!function_exists('lucidus_core_internet_access_page')){
        echo '<div class="wrap"><p>Core module missing.</p></div>';
        return;
    }
    lucidus_core_internet_access_page();
}

function lucidus_panel_rest(){
    register_rest_route('lucidus-panel/v1', '/settings', [
        'methods' => 'GET',
        'callback' => function(){
            return [
                'voice'=>get_option('lucidus_voice_enabled','0')==='1',
                'microphone'=>get_option('lucidus_microphone_enabled','0')==='1',
                'tts'=>get_option('lucidus_tts_enabled','0')==='1',
                'personality'=>get_option('lucidus_personality_mode','dub'),
                'prompt'=>get_option('lucidus_prompt_builder',''),
                'chat_memory'=>get_option('lucidus_chat_memory','1')==='1'
            ];
        },
        'permission_callback' => '__return_true'
    ]);
    register_rest_route('lucidus-panel/v1', '/settings', [
        'methods' => 'POST',
        'callback' => function($request){
            $data = $request->get_json_params();
            update_option('lucidus_voice_enabled', !empty($data['voice'])?'1':'0');
            update_option('lucidus_microphone_enabled', !empty($data['microphone'])?'1':'0');
            update_option('lucidus_tts_enabled', !empty($data['tts'])?'1':'0');
            update_option('lucidus_personality_mode', sanitize_text_field($data['personality'] ?? 'dub'));
            update_option('lucidus_prompt_builder', sanitize_textarea_field($data['prompt'] ?? ''));
            update_option('lucidus_chat_memory', !empty($data['chat_memory'])?'1':'0');
            return ['status'=>'saved'];
        },
        'permission_callback' => '__return_true'
    ]);
}
add_action('rest_api_init', 'lucidus_panel_rest');

function lucidus_panel_shortcode(){
    $enabled = get_option('lucidus_voice_enabled','0')==='1';
    $state = $enabled ? 'enabled' : 'disabled';
    return '<div class="lucidus-panel">Voice is '.$state.'</div>';
}
add_shortcode('lucidus-panel', 'lucidus_panel_shortcode');

