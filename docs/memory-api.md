# Memory API

Lucidus stores conversation history in a JSON file within the uploads directory.

## Endpoints
- `POST /wp-json/lucidus/v1/chat` – send a message and receive a reply.
- `GET /wp-json/lucidus/v1/memory` – retrieve stored conversation data (requires `manage_options`).
- `DELETE /wp-json/lucidus/v1/memory` – clear stored memory (requires `manage_options`).

## Functions
- `lucidus_memory_file()` – returns the path to the memory file.
- `lucidus_load_memory()` – returns the memory array.
- `lucidus_save_memory( $memory )` – writes the array to disk.
- `lucidus_clear_memory()` – resets the file to an empty array.

## Customization
You can filter or modify memory before saving by hooking into your own actions or filters surrounding `lucidus_save_memory()`.
