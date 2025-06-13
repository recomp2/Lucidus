#!/bin/bash
# Lucidus Feed Cron Runner
# Simulates pulling and injecting feed data into memory system

API_ENDPOINT="http://localhost/wp-json/lucidus/v1/feed/pull"
MEMORY_INJECT_URL="http://localhost/wp-json/lucidus/v1/memory/inject"
USER_ID=1234  # Optional: targeted feed
LOG_FILE="/Main/wp-content/dbs-library/logs/feed-cron-log.txt"

mkdir -p "$(dirname "$LOG_FILE")"
{
  echo "🔄 Running scheduled Lucidus feed update: $(date)"
  FEED_CONTENT=$(curl -s "$API_ENDPOINT")
  if [ -z "$FEED_CONTENT" ]; then
    echo "❌ Feed pull failed or returned empty."
  else
    echo "✅ Feed content pulled."
    PAYLOAD=$(printf '%s' "$FEED_CONTENT" | jq -Rs .)
    curl -s -X POST "$MEMORY_INJECT_URL" \
      -H "Content-Type: application/json" \
      -d '{"tag": "external_feed", "content": '"$PAYLOAD"'}'
    echo "\n---\n"
  fi
  echo "🧠 Feed memory injection complete."
} >> "$LOG_FILE" 2>&1

