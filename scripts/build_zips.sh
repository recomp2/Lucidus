#!/bin/bash
# Build zip packages for DBS plugins
set -euo pipefail

out_dir="dist"
mkdir -p "$out_dir"

for plugin in dead-bastard-society lucidus-terminal-pro dbs-membership-core; do
    if [ -d "$plugin" ]; then
        zip -r "$out_dir/${plugin}.zip" "$plugin" > /dev/null
        echo "Created $out_dir/${plugin}.zip"
    else
        echo "Directory $plugin does not exist. Skipping." >&2
    fi
done
