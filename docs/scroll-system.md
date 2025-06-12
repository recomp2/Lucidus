# Scroll System

Scrolls are standard WordPress posts that represent lore entries in the DBS universe.

## Creating Scrolls
Use `wp_insert_post()` to create a new scroll. Example from `test-flow.php`:

```php
$post_id = wp_insert_post([
    'post_title'   => 'Test Scroll',
    'post_content' => 'This is a test scroll entry.',
    'post_status'  => 'publish',
    'post_author'  => $user_id,
]);
```

## Integration
- Posting a scroll can trigger mood adjustments for the active archetype.
- Scroll content may be injected into Lucidus memory for richer conversations.

## Extensions
Add custom post types or taxonomies to categorize scrolls (e.g., region or quest). Filter new posts with hooks like `save_post` to integrate with the mood engine.
