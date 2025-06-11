=== Lucidus Terminal Pro ===
Contributors: dbsdevs
Tags: chat, gpt, terminal
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 1.1
License: MIT

Provides an AI chat terminal in WordPress with per-user memory and scroll integration.
Every 50 minutes Lucidus generates a prophecy using weather, news and astrological data.
View recent prophecies with the [lucidus_prophecy_feed] shortcode.
Use [lucidus_terminal] shortcode or the admin page under Lucidus Terminal.
Memory data is stored in the uploads directory as `memory-{USERID}.json`.
Recent `scroll` posts are pulled into the conversation automatically.

= 1.5 =
* Logs chats and prophecies to archive JSON files and exposes `/lucidus/v1/archive`.
* Added helper template and theme pages for integrated chat experience.

= 1.6 =
* Final security audit and optimized styles. Activation now flushes rewrite rules.

= 1.7 =
* Introduced dynamic module loader from `plugins/lucidus-terminal-pro/modules`.
* Added example prophecy expansion module and admin Modules page.
