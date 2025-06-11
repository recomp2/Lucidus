# Dead Bastard Society Package

This repository contains a WordPress theme and two plugins.

## Contents
- `theme/dead-bastard-society-theme`
- `plugin-terminal/lucidus-terminal-pro`
- `plugin-members/dbs-members-plugin`

## Installation
1. Zip each directory individually.
2. Upload the zip via **Appearance → Themes** or **Plugins → Add New** in WordPress.
3. Activate the theme and plugins.
4. Configure API keys under **Lucidus Terminal → Settings**.
5. Use `[lucidus_terminal]` shortcode to embed the chat interface on any page.
6. Theme and plugins include basic translation support and a primary navigation menu.
7. Each user has a separate memory file in `wp-content/uploads/lucidus-terminal/memory-{USERID}.json`. You can clear your chat memory from the plugin settings page.

The terminal plugin registers a `scroll` post type. Recent scrolls are added to Lucidus' context and can be retrieved via the `/lucidus/v1/scrolls` REST endpoint.
Prophecies are generated automatically every 50 minutes. Display them using `[lucidus_prophecy_feed]` or the `/lucidus/v1/prophecies` endpoint.

## Development
All code is licensed under the MIT license.

## New Theme Pages
- **Lucidus Chat**: Provides the main chat terminal and prophecy feed.
- **Lucidus Profile**: Shows your chat history with Lucidus.
- **420 Scroll Feed**: Displays recent prophecies in a scrolling view.
- **Lucidus Chapters**: Placeholder for chapter management.
- **Lucidus Archive**: Lists archived chat and prophecy data.
- **Lucidus Settings**: Admin settings page embedded in a theme template.
- **Membership Map**: Uses `[dbs_members_map]` to render an interactive map of members with `town_lat` and `town_lng` metadata.
- **Module System**: Drop modules into `plugin-terminal/modules` to extend Lucidus features automatically.

The plugin now logs every chat exchange and prophecy to JSON files in `wp-content/uploads/dbs-library/memory-archive/` and exposes them via the `/lucidus/v1/archive` REST endpoint.

## Tier-Based Access Control
Lucidus features are restricted by user tier and archetype. Tiers can be configured under **Lucidus Terminal → Tier Settings**. When a user lacks permission, Lucidus responds with a sarcastic denial based on the user's archetype.

## Progressive Web App
This release registers a service worker through `pwa-wrapper.js` to enable offline fallback.

## Lore Manifest
Lucidus references a lore manifest stored in `lucidus-terminal-pro/config/lucidus-lore-manifest.json`.
Helper functions load this data so modules can access tone presets, archetype legends and denial messages. Developers can modify the JSON to tweak Lucidus personality without touching PHP code.
\n## System Prophecies\nLucidus monitors system health hourly and logs suggestions in `lucidus-insight-log.json`. View the last recommendations under **Lucidus Terminal → System Prophecies**.
