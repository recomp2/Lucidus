#!/bin/bash

echo "🌀 Starting Lucidus Setup Organizer..."

# Step 1: Define base structure
BASE_DIR=$(pwd)
THEME_DIR="$BASE_DIR/wp-content/themes/dead-bastard-society"
PLUGIN_DIR="$BASE_DIR/wp-content/plugins/lucidus-terminal-pro"
MEDIA_DIR="$THEME_DIR/lucidus-terminal"
MEMORY_DIR="$BASE_DIR/wp-content/dbs-library/memory-archive"

# Step 2: Create missing directories
mkdir -p "$THEME_DIR"
mkdir -p "$PLUGIN_DIR"
mkdir -p "$MEDIA_DIR"
mkdir -p "$MEMORY_DIR/injections" "$MEMORY_DIR/context"

# Step 3: Move known files into proper structure
find . -type f -iname "*.mp3" -exec mv -v {} "$MEDIA_DIR/" \;
find . -type f -iname "*.wav" -exec mv -v {} "$MEDIA_DIR/" \;
find . -type f -iname "*.flac" -exec mv -v {} "$MEDIA_DIR/" \;
find . -type f -iname "*.ogg" -exec mv -v {} "$MEDIA_DIR/" \;

# Step 4: Rename files safely
cd "$MEDIA_DIR"
for f in *; do
  safe_name=$(echo "$f" | tr '[:upper:]' '[:lower:]' | tr ' ' '-' | tr -cd '[:alnum:]-_.')
  [ "$f" != "$safe_name" ] && mv "$f" "$safe_name"
done

# Step 5: Organize plugin and theme PHP files
cd "$BASE_DIR"
find . -type f -name "*terminal-pro.php" -exec mv -v {} "$PLUGIN_DIR/" \;
find . -type f -name "*page-lucidus-terminal.php" -exec mv -v {} "$THEME_DIR/" \;

# Step 6: Version trace
echo "Lucidus Setup Completed on $(date)" >> "$MEMORY_DIR/context/deploy.log"

echo "✅ All files organized and ready for WordPress."
