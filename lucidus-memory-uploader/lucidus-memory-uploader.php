<?php
/*
Plugin Name: Lucidus Memory Uploader
Description: Allows administrators to upload files into Lucidus's memory directory for later use.
Version: 1.0.0
Author: Dr.G
License: MIT
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Lucidus_Memory_Uploader {
    private static $memory_dir;

    public static function init() {
        self::$memory_dir = wp_upload_dir()['basedir'] . '/lucidus-memory';
        add_action('admin_menu', [__CLASS__, 'add_menu']);
        add_action('admin_post_lucidus_memory_upload', [__CLASS__, 'handle_upload']);
        register_activation_hook(__FILE__, [__CLASS__, 'activate']);
    }

    public static function activate() {
        wp_mkdir_p(self::$memory_dir);
    }

    public static function add_menu() {
        add_menu_page('Lucidus Memory', 'Lucidus Memory', 'manage_options', 'lucidus-memory', [__CLASS__, 'memory_page']);
    }

    public static function memory_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        $files = glob(self::$memory_dir . '/*');
        echo '<div class="wrap"><h1>Lucidus Memory Files</h1>';
        echo '<form method="post" enctype="multipart/form-data" action="'.admin_url('admin-post.php').'">';
        echo '<input type="hidden" name="action" value="lucidus_memory_upload" />';
        wp_nonce_field('lucidus_memory_upload');
        echo '<input type="file" name="memory_file" required />';
        submit_button('Upload');
        echo '</form>';

        if ($files) {
            echo '<h2>Existing Files</h2><ul>';
            foreach ($files as $file) {
                echo '<li>' . esc_html(basename($file)) . '</li>';
            }
            echo '</ul>';
        }
        echo '</div>';
    }

    public static function handle_upload() {
        if (!current_user_can('manage_options') || !check_admin_referer('lucidus_memory_upload')) {
            wp_die('Permission denied');
        }
        if (empty($_FILES['memory_file']['name'])) {
            wp_redirect(admin_url('admin.php?page=lucidus-memory')); exit;
        }
        $file = $_FILES['memory_file'];
        wp_mkdir_p(self::$memory_dir);
        $destination = self::$memory_dir . '/' . sanitize_file_name($file['name']);
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            wp_redirect(admin_url('admin.php?page=lucidus-memory&uploaded=1')); exit;
        }
        wp_die('Upload failed');
    }

    public static function get_memory_files() {
        $files = glob(self::$memory_dir . '/*');
        return array_map('file_get_contents', $files ?: []);
    }
}

Lucidus_Memory_Uploader::init();

?>
