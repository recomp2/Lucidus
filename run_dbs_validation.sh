#!/bin/sh

echo "🧪 DBS Admin Tool Runner"

TOOLS_DIR="/Main/tools"

echo "➡ Running shortcode + REST API validator..."
sh "$TOOLS_DIR/validate-shortcodes-rest.sh"

echo ""
echo "➡ Running admin menu UI checker..."
sh "$TOOLS_DIR/check-admin-ui.sh"

echo ""
echo "✅ Scans complete. Review output above for missing logic or placeholders."
