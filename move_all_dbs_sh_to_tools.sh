#!/bin/sh

echo "🚚 Moving all .sh setup and utility scripts to /Main/tools..."

TOOLS_DIR="/Main/tools"
mkdir -p "$TOOLS_DIR"

# Define list of files to move
for file in dbs_codex_setup_mainroot.sh dbs_codex_setup_mainroot_final.sh dbs_codex_setup_all_in_one.sh dbs_codex_setup_minimal_final.sh run_dbs_validation.sh move_dbs_sh_to_tools.sh dbs_codex_setup_script_for_codex_box.sh; do
  if [ -f "$file" ]; then
    mv "$file" "$TOOLS_DIR/"
    echo "✅ Moved $file to $TOOLS_DIR/"
  elif [ -f "/tmp/$file" ]; then
    mv "/tmp/$file" "$TOOLS_DIR/"
    echo "✅ Moved /tmp/$file to $TOOLS_DIR/"
  else
    echo "⚠️ $file not found in working or /tmp directory"
  fi
done

chmod +x "$TOOLS_DIR"/*.sh 2>/dev/null

echo ""
echo "✅ All .sh scripts are now in $TOOLS_DIR"
ls -l "$TOOLS_DIR"
