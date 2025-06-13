#!/bin/sh

echo "🌀 DBS Minimal Setup Script – Lightweight Version"

# === PATH SETUP ===
WP_DIR="/Main/wp-content"
THEME_DIR="$WP_DIR/themes/dead-bastard-society"
PLUGIN_DIR="$WP_DIR/plugins"
LIBRARY_DIR="$WP_DIR/dbs-library"

echo "📁 Creating directories..."
mkdir -p "$THEME_DIR"
mkdir -p "$PLUGIN_DIR"
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

# === FILE EXTRACTION ONLY ===
echo "📦 Unzipping files..."
unzip -o dbs_complete_backup_fullfiles.zip -d "$LIBRARY_DIR"
unzip -o dbs_modules_bundle.zip -d "$PLUGIN_DIR"
unzip -o dbs_template_pages.zip -d "$THEME_DIR"

# === AUDIO COPY ONLY ===
mkdir -p "$THEME_DIR/lucidus-terminal" "$THEME_DIR/sfx"
cp ambience-warp.mp3 "$THEME_DIR/lucidus-terminal/"
cp scroll-unlock.wav "$THEME_DIR/sfx/"
cp prophecy-initiation.wav "$THEME_DIR/sfx/"

# === PERMISSIONS ONLY ===
chmod -R 755 "$WP_DIR"

echo ""
echo "✅ Minimal DBS environment setup complete."
