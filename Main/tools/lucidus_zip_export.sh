#!/bin/bash

# Lucidus Export Zipping Script
# Packages plugins and theme separately for download

PLUGIN_DIR="$(dirname "$0")/../wp-content/plugins"
THEME_DIR="$(dirname "$0")/../wp-content/themes/dead-bastard-society"
DEST_DIR="$(dirname "$0")/../zips"
mkdir -p "$DEST_DIR"

PLUGIN_ZIP="$DEST_DIR/lucidus_plugins.zip"
THEME_ZIP="$DEST_DIR/lucidus_theme.zip"

echo "📦 Starting Lucidus export zipping process..."

echo "🔧 Zipping plugins..."
zip -r "$PLUGIN_ZIP" "$PLUGIN_DIR" > /dev/null 2>&1 && echo "✅ Plugins zipped to $PLUGIN_ZIP" || echo "❌ Plugin zip failed."

echo "🎨 Zipping theme..."
zip -r "$THEME_ZIP" "$THEME_DIR" > /dev/null 2>&1 && echo "✅ Theme zipped to $THEME_ZIP" || echo "❌ Theme zip failed."

echo "🧠 Zipping complete. Retrieve files from: $DEST_DIR"
