# Performance Audit

This document reviews potential performance concerns in the DBS theme and plugins.

## Heavy Templates

The current templates are lightweight. The login, register and dashboard templates contain minimal markup and rely on shortcodes. No heavy loops or expensive queries are executed.

## Unoptimized Queries

- `dbs_create_page()` in `plugin-members/includes/pages.php` uses `get_page_by_title()`, which is fine on activation, but should not be called repeatedly on front-end requests.
- Memory handling performs file I/O for every chat request. Consider caching memory in transients or an external store if usage grows.

## Lazy Loading Recommendations

- **OpenAI Requests**: Calls to the OpenAI API are executed by the REST endpoint. Ensure the JavaScript chat interface is only enqueued when the `[lucidus_terminal]` shortcode or admin page is displayed. The current code already does this, which helps performance.
- **Maps or Voice Engines**: If future features integrate maps or voice synthesis, load those assets conditionally, only on pages that require them.
- **GPT Models**: Large prompt history can slow requests. Implement pagination or memory trimming to keep conversation history concise.

Overall, the system is lightweight, but monitoring API response times and file I/O will help maintain performance as features expand.
