# Lucidus Memory Uploader

This WordPress plugin allows administrators to upload additional text files into Lucidus's memory. Uploaded files are stored in `wp-content/uploads/lucidus-memory` and can be retrieved via the helper function `Lucidus_Memory_Uploader::get_memory_files()`.

## Features
* Admin page under **Lucidus Memory** for uploading files
* Lists all stored files
* Memory files can be used by other Lucidus components

## Installation
1. Copy `lucidus-memory-uploader` to your `/wp-content/plugins/` directory.
2. Activate the plugin via the WordPress admin.
3. Visit **Lucidus Memory** in the admin menu to upload files.
