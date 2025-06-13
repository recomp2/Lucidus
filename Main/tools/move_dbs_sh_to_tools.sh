#!/bin/sh

echo "🔄 Ensuring all .sh utility scripts are placed in /Main/tools..."

TOOLS_DIR="/Main/tools"

mkdir -p "$TOOLS_DIR"

# Move recognized .sh scripts into tools folder if found in root or temp
for file in dbs_codex_setup_mainroot.sh dbs_codex_setup_mainroot_final.sh run_dbs_validation.sh; do
  if [ -f "$file" ]; then
    mv "$file" "$TOOLS_DIR/"
    echo "✅ Moved $file to $TOOLS_DIR/"
  elif [ -f "/tmp/$file" ]; then
    mv "/tmp/$file" "$TOOLS_DIR/"
    echo "✅ Moved /tmp/$file to $TOOLS_DIR/"
  else
    echo "⚠️ $file not found in current or temp directory"
  fi
done

# Ensure all tools are executable
chmod +x "$TOOLS_DIR"/*.sh

echo ""
echo "✅ All DBS scripts checked and placed in $TOOLS_DIR"
ls -l "$TOOLS_DIR"
