#!/bin/sh

echo "🌀 DBS Full Setup Script - Finalized Codex Version"

# === PATH SETUP ===
WP_DIR="/Main/wp-content"
THEME_DIR="$WP_DIR/themes/dead-bastard-society"
PLUGIN_DIR="$WP_DIR/plugins"
LIBRARY_DIR="$WP_DIR/dbs-library"
TOOLS_DIR="/Main/tools"

echo "📁 Creating directory structure..."
mkdir -p "$THEME_DIR" "$PLUGIN_DIR" "$LIBRARY_DIR" "$TOOLS_DIR"
mkdir -p "$LIBRARY_DIR/memory-archive/profiles"
mkdir -p "$LIBRARY_DIR/memory-archive/geo"
mkdir -p "$LIBRARY_DIR/memory-archive/scrolls"
mkdir -p "$LIBRARY_DIR/memory-archive/quests"
mkdir -p "$LIBRARY_DIR/patches"
mkdir -p "$LIBRARY_DIR/characters"
mkdir -p "$LIBRARY_DIR/interactive"
mkdir -p "$LIBRARY_DIR/canon"
mkdir -p "$LIBRARY_DIR/comics"
mkdir -p "$LIBRARY_DIR/logs"

# === FILE EXTRACTION ===
echo "📦 Extracting available ZIPs..."
[ -f dbs_complete_backup_fullfiles.zip ] && unzip -o dbs_complete_backup_fullfiles.zip -d "$LIBRARY_DIR"
[ -f dbs_modules_bundle.zip ] && unzip -o dbs_modules_bundle.zip -d "$PLUGIN_DIR"
[ -f dbs_template_pages.zip ] && unzip -o dbs_template_pages.zip -d "$THEME_DIR"
[ -f dbs_all_scripts_bundle.zip ] && unzip -o dbs_all_scripts_bundle.zip -d /tmp/dbs-scripts && mv /tmp/dbs-scripts/*.sh "$TOOLS_DIR"

# === AUDIO FILES ===
echo "🎵 Copying audio if available..."
mkdir -p "$THEME_DIR/lucidus-terminal" "$THEME_DIR/sfx"
[ -f ambience-warp.mp3 ] && cp ambience-warp.mp3 "$THEME_DIR/lucidus-terminal/"
[ -f scroll-unlock.wav ] && cp scroll-unlock.wav "$THEME_DIR/sfx/"
[ -f prophecy-initiation.wav ] && cp prophecy-initiation.wav "$THEME_DIR/sfx/"

# === PERMISSIONS ===
chmod -R 755 "$WP_DIR"
chmod +x "$TOOLS_DIR"/*.sh 2>/dev/null

# === VALIDATION TOOL RUN ===
echo "🧪 Running validation scan..."
if [ -f "$TOOLS_DIR/run_dbs_validation.sh" ]; then
  sh "$TOOLS_DIR/run_dbs_validation.sh"
else
  echo "⚠️ Validation script not found in $TOOLS_DIR"
fi

# === COMPLETION LOG ===
echo ""
echo "✅ DBS Full Setup Complete"
echo "➡ Theme: $THEME_DIR"
echo "➡ Plugins: $PLUGIN_DIR"
echo "➡ Library: $LIBRARY_DIR"
echo "➡ Tools: $TOOLS_DIR"
