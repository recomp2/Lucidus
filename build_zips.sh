#!/usr/bin/env bash
# Build zipped plugin packages for distribution
set -e

PLUGIN_DIRS=("dead-bastard-society" "lucidus-terminal-pro" "dbs-membership-core")

for dir in "${PLUGIN_DIRS[@]}"; do
  if [ -d "$dir" ]; then
    zip -r "${dir}.zip" "$dir" -x '*.DS_Store' > /dev/null
    echo "Created ${dir}.zip"
  else
    echo "Warning: ${dir} directory not found, skipping." >&2
  fi
done
