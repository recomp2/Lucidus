#!/bin/bash

echo "📦 Starting Lucidus export zipping process..."

# Define paths
PLUGIN_DIR="/Main/wp-content/plugins"
THEME_DIR="/Main/wp-content/themes/dead-bastard-society"
DEST_DIR="/Main/zips"
mkdir -p "$DEST_DIR"

# Output files
PLUGIN_ZIP="$DEST_DIR/lucidus_plugins.zip"
THEME_ZIP="$DEST_DIR/lucidus_theme.zip"

# Zip plugins
echo "🔧 Zipping plugins..."
zip -r "$PLUGIN_ZIP" "$PLUGIN_DIR" > /dev/null 2>&1 && echo "✅ Plugins zipped to $PLUGIN_ZIP" || echo "❌ Plugin zip failed."

# Zip theme
echo "🎨 Zipping theme..."
zip -r "$THEME_ZIP" "$THEME_DIR" > /dev/null 2>&1 && echo "✅ Theme zipped to $THEME_ZIP" || echo "❌ Theme zip failed."

echo "🧠 Zipping complete. Retrieve files from: $DEST_DIR"
