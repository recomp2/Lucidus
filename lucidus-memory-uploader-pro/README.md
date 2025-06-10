# Lucidus Memory Uploader PRO

The official **DBS** extension for managing Lucidus memory files. Uploaded memories live in `wp-content/uploads/lucidus-memory` and can be fetched via `Lucidus_Memory_Uploader::get_memory_files()`.

Upload actions are logged with timestamps to `wp-content/uploads/lucidus-context/upload-log.txt`. A shortcode and REST API expose memory file lists and log entries.

## Features
* Admin menu **Lucidus Memory PRO** for uploading files
* List stored files with size and last modified time
* Edit or delete memory files directly from the table
* Display total memory used
* Activity log of uploads/edits/deletions
* Small "Ask Lucidus" chat box checks if a file exists
* `[lucidus_memory_files]` shortcode to display files
* REST API endpoints:
  * `GET /wp-json/lucidus/v1/memory` – list uploaded files
  * `GET /wp-json/lucidus/v1/context` – list upload log entries

## Installation
This module is branded for the **Dead Bastard Society** universe. Zip the `lucidus-memory-uploader-pro` folder and upload it via WordPress.
1. Copy `lucidus-memory-uploader-pro` to your `/wp-content/plugins/` directory.
2. Activate via WordPress admin.
3. Visit **Lucidus Memory PRO** in the admin menu to manage memory files.
