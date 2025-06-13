<?php
if (!defined('ABSPATH')) exit;

function ltp_admin_page(){
    if(isset($_POST['ltp_voice'])){
        check_admin_referer('ltp_save');
        update_option('ltp_voice', $_POST['ltp_voice'] ? '1':'0');
        update_option('ltp_memory', $_POST['ltp_memory'] ? '1':'0');
        echo '<div class="updated notice"><p>Settings saved.</p></div>';
    }
    $voice = get_option('ltp_voice','0');
    $memory = get_option('ltp_memory','0');
    $screen = get_current_screen();
    if($screen && method_exists($screen, 'add_help_tab')){
        $screen->add_help_tab([
            'id'=>'ltp_help',
            'title'=>__('Terminal Help'),
            'content'=>'<p>'.esc_html__('Configure voice and memory options for the terminal interface.','ltp').'</p>'
        ]);
    }
    echo '<div class="wrap"><h1>Lucidus Terminal Settings</h1>';
    echo '<form method="post">';
    wp_nonce_field('ltp_save');
    echo '<label><input type="checkbox" name="ltp_voice" value="1" '.($voice==='1'?'checked':'').'> Enable Voice</label><br />';
    echo '<label><input type="checkbox" name="ltp_memory" value="1" '.($memory==='1'?'checked':'').'> Enable Memory Logging</label>';
    echo '<p><button class="button button-primary">Save</button></p>';
    echo '</form></div>';
}


