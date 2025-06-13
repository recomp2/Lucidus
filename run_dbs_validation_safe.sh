#!/bin/sh

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
TOOLS_DIR="$SCRIPT_DIR/Main/tools"
LEGACY_DIR="/Main/tools"
REQUIRED_SCRIPTS="run_dbs_validation.sh validate-shortcodes-rest.sh check-admin-ui.sh"

echo "🔁 Ensuring all required scripts are in $TOOLS_DIR..."
mkdir -p "$TOOLS_DIR"

for script in $REQUIRED_SCRIPTS; do
  if [ -f "$TOOLS_DIR/$script" ]; then
    echo "✔️ $script already in place."
  elif [ -f "$script" ]; then
    cp "$script" "$TOOLS_DIR/"
    echo "✅ Copied $script to $TOOLS_DIR/"
  elif [ -f "/tmp/$script" ]; then
    mv "/tmp/$script" "$TOOLS_DIR/"
    echo "✅ Moved /tmp/$script to $TOOLS_DIR/"
  elif [ -f "$LEGACY_DIR/$script" ]; then
    mv "$LEGACY_DIR/$script" "$TOOLS_DIR/"
    echo "✅ Moved $LEGACY_DIR/$script to $TOOLS_DIR/"
  else
    echo "⚠️ $script not found anywhere. Validation may fail."
  fi
done

echo "🔐 Setting executable permissions..."
chmod +x "$TOOLS_DIR"/*.sh 2>/dev/null

echo "🧪 Running: run_dbs_validation.sh..."
if [ -f "$TOOLS_DIR/run_dbs_validation.sh" ]; then
  sh "$TOOLS_DIR/run_dbs_validation.sh"
else
  echo "❌ Cannot run validation: run_dbs_validation.sh not found."
fi

echo "✅ Script completed."

