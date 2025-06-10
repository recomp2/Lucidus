# Lucidus Terminal Pro

This plugin registers custom post types for the Dead Bastard Society universe and implements basic quest logic for scroll completion and rank progression.

## Features

- Custom post types: **badge**, **patch**, **scroll**
- Template overrides for each custom type
- Quest system that increases a user's rank when a scroll is completed
- Progress saved as JSON files in `/wp-content/dbs-library/`

## Usage

1. Upload `lucidus-terminal-pro` to your WordPress plugins directory.
2. Activate the plugin.
3. Create scroll posts and mark them complete via the provided form on the single scroll template.
4. Completed scrolls increase a user's rank and store progress in the DBS library folder.
