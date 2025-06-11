# Extending Lucidus Logic

The Lucidus Terminal Pro plugin uses OpenAI's chat API to generate responses. You can extend its behavior in several ways.

## Add Commands
Hook into the REST endpoint to parse special commands:

```php
add_filter( 'lucidus_pre_chat', function( $message ) {
    if ( strpos( $message, '/mood' ) === 0 ) {
        // handle custom command
    }
    return $message;
});
```

`lucidus_handle_chat()` should apply this filter before sending data to the API.

## Custom Memory Stores
Replace the file-based memory system by filtering `lucidus_memory_file()` or overriding the load/save functions in another plugin.

## Voice Switching
Use user meta such as `dbs_voice` to determine which voice style to apply when rendering a reply. Adjust the JS front end to fetch this meta and select a voice.
