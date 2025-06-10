<?php
/*
Plugin Name: Lucidus Memory Uploader PRO
Description: Allows administrators to upload files into Lucidus's memory directory for later use.
Version: 2.0.0
Author: Dr.G
License: MIT
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Lucidus_Memory_Uploader {
    private static $memory_dir;
    private static $context_dir;

    public static function init() {
        self::$memory_dir = wp_upload_dir()['basedir'] . '/lucidus-memory';
        self::$context_dir = wp_upload_dir()['basedir'] . '/lucidus-context';
        add_action('admin_menu', [__CLASS__, 'add_menu']);
        add_action('admin_post_lucidus_memory_upload', [__CLASS__, 'handle_upload']);
        add_action('admin_post_lucidus_memory_delete', [__CLASS__, 'handle_delete']);
        add_action('admin_post_lucidus_memory_save', [__CLASS__, 'handle_save']);
        add_action('wp_ajax_lucidus_memory_check', [__CLASS__, 'ajax_check_memory']);
        register_activation_hook(__FILE__, [__CLASS__, 'activate']);
        add_shortcode('lucidus_memory_files', [__CLASS__, 'shortcode_list_files']);
        add_action('rest_api_init', [__CLASS__, 'register_routes']);
    }

    public static function activate() {
        wp_mkdir_p(self::$memory_dir);
        wp_mkdir_p(self::$context_dir);
    }

    public static function add_menu() {
        add_menu_page('Lucidus Memory', 'Lucidus Memory', 'manage_options', 'lucidus-memory-pro', [__CLASS__, 'memory_page']);
    }

    public static function memory_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        $files = glob(self::$memory_dir . '/*');

        if (isset($_GET['edit']) && $files) {
            $edit = basename($_GET['edit']);
            $path = self::$memory_dir . '/' . $edit;
            if (file_exists($path)) {
                $content = file_get_contents($path);
                echo '<div class="wrap"><h1>Edit Memory File</h1>';
                echo '<form method="post" action="'.admin_url('admin-post.php').'">';
                echo '<input type="hidden" name="action" value="lucidus_memory_save" />';
                echo '<input type="hidden" name="file" value="'.esc_attr($edit).'" />';
                wp_nonce_field('lucidus_memory_save');
                echo '<textarea name="content" rows="20" style="width:100%">'.esc_textarea($content).'</textarea>';
                submit_button('Save Changes');
                echo '</form></div>';
                return;
            }
        }

        $total = 0;
        echo '<div class="wrap"><h1>Lucidus Memory Files</h1>';
        echo '<form method="post" enctype="multipart/form-data" action="'.admin_url('admin-post.php').'">';
        echo '<input type="hidden" name="action" value="lucidus_memory_upload" />';
        wp_nonce_field('lucidus_memory_upload');
        echo '<input type="file" name="memory_file" required />';
        submit_button('Upload');
        echo '</form>';

        if ($files) {
            echo '<h2>Existing Files</h2><table class="widefat"><thead><tr><th>Name</th><th>Size</th><th>Modified</th><th>Actions</th></tr></thead><tbody>';
            foreach ($files as $file) {
                $size = size_format(filesize($file));
                $time = date_i18n(get_option('date_format').' '.get_option('time_format'), filemtime($file));
                $total += filesize($file);
                $name = basename($file);
                $edit_link = admin_url('admin.php?page=lucidus-memory-pro&edit=' . urlencode($name));
                $delete_url = wp_nonce_url(admin_url('admin-post.php?action=lucidus_memory_delete&file=' . urlencode($name)), 'lucidus_memory_delete');
                echo '<tr><td>'.esc_html($name).'</td><td>'.$size.'</td><td>'.$time.'</td><td><a href="'.$edit_link.'">Edit</a> | <a href="'.$delete_url.'">Delete</a></td></tr>';
            }
            echo '</tbody></table>';
            echo '<p><strong>Total Memory Used: '.size_format($total).'</strong></p>';
        } else {
            echo '<p>No memory files.</p>';
        }

        echo '<h2>Check Memory Awareness</h2>';
        echo '<input type="text" id="lucidus-memory-pro-query" placeholder="Enter filename" />';
        echo '<button class="button" id="lucidus-memory-pro-check">Ask Lucidus</button>';
        echo '<pre id="lucidus-memory-pro-response"></pre>';
        echo '</div>';
        self::enqueue_script();
    }

    public static function handle_upload() {
        if (!current_user_can('manage_options') || !check_admin_referer('lucidus_memory_upload')) {
            wp_die('Permission denied');
        }
        if (empty($_FILES['memory_file']['name'])) {
            wp_redirect(admin_url('admin.php?page=lucidus-memory-pro')); exit;
        }
        $file = $_FILES['memory_file'];
        wp_mkdir_p(self::$memory_dir);
        $destination = self::$memory_dir . '/' . sanitize_file_name($file['name']);
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            wp_mkdir_p(self::$context_dir);
            $log_file = self::$context_dir . '/upload-log.txt';
            $entry = date_i18n('Y-m-d H:i:s') . " \t uploaded \t" . basename($destination) . "\n";
            file_put_contents($log_file, $entry, FILE_APPEND);
            wp_redirect(admin_url('admin.php?page=lucidus-memory-pro&uploaded=1')); exit;
        }
        wp_die('Upload failed');
    }

    public static function handle_delete() {
        if (!current_user_can('manage_options') || !check_admin_referer('lucidus_memory_delete')) {
            wp_die('Permission denied');
        }
        if (empty($_GET['file'])) {
            wp_redirect(admin_url('admin.php?page=lucidus-memory-pro')); exit;
        }
        $file = basename($_GET['file']);
        $path = self::$memory_dir . '/' . $file;
        if (file_exists($path)) {
            unlink($path);
            wp_mkdir_p(self::$context_dir);
            $log_file = self::$context_dir . '/upload-log.txt';
            $entry = date_i18n('Y-m-d H:i:s') . " \t deleted \t" . $file . "\n";
            file_put_contents($log_file, $entry, FILE_APPEND);
        }
        wp_redirect(admin_url('admin.php?page=lucidus-memory-pro&deleted=1')); exit;
    }

    public static function handle_save() {
        if (!current_user_can('manage_options') || !check_admin_referer('lucidus_memory_save')) {
            wp_die('Permission denied');
        }
        if (empty($_POST['file'])) {
            wp_redirect(admin_url('admin.php?page=lucidus-memory-pro')); exit;
        }
        $file = basename($_POST['file']);
        $path = self::$memory_dir . '/' . $file;
        $content = isset($_POST['content']) ? wp_unslash($_POST['content']) : '';
        file_put_contents($path, $content);
        wp_mkdir_p(self::$context_dir);
        $log_file = self::$context_dir . '/upload-log.txt';
        $entry = date_i18n('Y-m-d H:i:s') . " \t edited \t" . $file . "\n";
        file_put_contents($log_file, $entry, FILE_APPEND);
        wp_redirect(admin_url('admin.php?page=lucidus-memory-pro&saved=1')); exit;
    }

    public static function ajax_check_memory() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission denied');
        }
        $filename = isset($_POST['file']) ? basename($_POST['file']) : '';
        $path = self::$memory_dir . '/' . $filename;
        if (file_exists($path)) {
            wp_send_json_success('Lucidus acknowledges ' . $filename);
        }
        wp_send_json_error('No such memory found');
    }

    public static function get_memory_files() {
        $files = glob(self::$memory_dir . '/*');
        return array_map('file_get_contents', $files ?: []);
    }

    public static function shortcode_list_files() {
        $files = glob(self::$memory_dir . '/*');
        if (!$files) {
            return '<p>No memory files.</p>';
        }
        $out = '<ul>';
        foreach ($files as $file) {
            $out .= '<li>' . esc_html(basename($file)) . '</li>';
        }
        $out .= '</ul>';
        return $out;
    }

    public static function register_routes() {
        register_rest_route('lucidus/v1', '/memory', [
            'methods'  => 'GET',
            'callback' => [__CLASS__, 'api_get_memory_files'],
            'permission_callback' => '__return_true'
        ]);

        register_rest_route('lucidus/v1', '/context', [
            'methods'  => 'GET',
            'callback' => [__CLASS__, 'api_get_context_log'],
            'permission_callback' => '__return_true'
        ]);
    }

    public static function api_get_memory_files() {
        $files = glob(self::$memory_dir . '/*');
        return array_map('basename', $files ?: []);
    }

    public static function api_get_context_log() {
        $log_file = self::$context_dir . '/upload-log.txt';
        if (!file_exists($log_file)) {
            return [];
        }
        $lines = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        return $lines ?: [];
    }

    private static function enqueue_script() {
        wp_enqueue_script('lucidus-memory-pro-admin', plugins_url('memory-admin.js', __FILE__), ['jquery'], '1.0', true);
        wp_localize_script('lucidus-memory-pro-admin', 'LucidusMemory', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('lucidus_memory_check'),
        ]);
    }
}

Lucidus_Memory_Uploader::init();

?>
