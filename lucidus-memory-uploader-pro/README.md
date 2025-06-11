# Lucidus Memory Uploader PRO

The official **DBS** extension for managing Lucidus memory files. Uploaded memories live in `wp-content/uploads/lucidus-memory` and can be fetched via `Lucidus_Memory_Uploader::get_memory_files()`.

Upload actions are logged with timestamps to `wp-content/uploads/lucidus-context/upload-log.txt`. Shortcodes and REST API endpoints expose both the memory file list and the log entries.

Memory paths can be expanded via `memory-paths.json` in the plugin folder. Add one path per line in the **Memory Paths** submenu to scan external drives or cloud mounts. Paths should be absolute server locations.

## Features
* Admin menu **Lucidus Memory PRO** for uploading files
* Accepts only `.txt` or `.json` up to **5MB**
* List stored files with size and last modified time
* Edit or delete memory files directly from the table
* Display total memory used
* Activity log of uploads/edits/deletions (per user)
* Memory dashboard with per-folder totals and recent history
* Small "Ask Lucidus" chat box checks if a file exists via AJAX
* Search box filters files instantly in the table
* Custom admin CSS styles the memory table
* Supports multiple memory locations via **Memory Paths** manager
* Test Inject button to preview a file in Lucidus memory
* Auto-renames files if the same name already exists
* Memory index and file logs stored in `wp-content/uploads/lucidus-context`
* Additional REST endpoints for index and file logs
* `[lucidus_memory_files]` shortcode to display files
* `[lucidus_memory_log]` shortcode to show the context log
* REST API endpoints:
  * `GET /wp-json/lucidus/v1/memory` – list uploaded files
  * `GET /wp-json/lucidus/v1/context` – list upload log entries
  * `GET /wp-json/lucidus/v1/prophecy-status` – memory summary JSON
  * `GET /wp-json/lucidus/v1/index` – metadata index
  * `GET /wp-json/lucidus/v1/filelog` – file activity log
 * `POST /wp-json/lucidus/v1/initiate` – save initiation profile

You can enable or disable logging and choose whether to scan the uploads directory via the plugin Settings page.

## Installation
This module is branded for the **Dead Bastard Society** universe. Zip the `lucidus-memory-uploader-pro` folder and upload it via WordPress.
1. Copy `lucidus-memory-uploader-pro` to your `/wp-content/plugins/` directory.
2. Activate via WordPress admin.
3. Visit **Lucidus Memory PRO** in the admin menu to manage memory files.
