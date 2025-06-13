#!/bin/sh

echo "🧪 DBS Admin Tool Runner"

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
TOOLS_DIR="$SCRIPT_DIR"

echo "➡ Running shortcode + REST API validator..."
sh "$TOOLS_DIR/validate-shortcodes-rest.sh"

echo ""
echo "➡ Running admin menu UI checker..."
sh "$TOOLS_DIR/check-admin-ui.sh"

echo ""
echo "✅ Scans complete. Review output above for missing logic or placeholders."
