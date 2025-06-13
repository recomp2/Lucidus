#!/bin/bash

# Lucidus Feed Cron Runner
# Simulates pulling and injecting feed data into memory system

API_ENDPOINT="http://localhost/wp-json/lucidus/v1/feed/pull"
MEMORY_INJECT_URL="http://localhost/wp-json/lucidus/v1/memory/inject"
USER_ID=1234  # Optional: can be used for targeted feed if needed
LOG_FILE="/Main/wp-content/dbs-library/logs/feed-cron-log.txt"

echo "🔄 Running scheduled Lucidus feed update: $(date)" >> $LOG_FILE

# Step 1: Pull feed content from feed-handler or external logic
FEED_CONTENT=$(curl -s "$API_ENDPOINT")

if [ -z "$FEED_CONTENT" ]; then
  echo "❌ Feed pull failed or returned empty." >> $LOG_FILE
else
  echo "✅ Feed content pulled." >> $LOG_FILE
  # Step 2: Inject feed content into Lucidus system memory
  curl -s -X POST "$MEMORY_INJECT_URL" \
    -H "Content-Type: application/json" \
    -d '{"tag": "external_feed", "content": "'"$FEED_CONTENT"'"}' >> $LOG_FILE
  echo "\n---\n" >> $LOG_FILE
fi

echo "🧠 Feed memory injection complete."
