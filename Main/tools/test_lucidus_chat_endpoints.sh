#!/bin/bash

# Lucidus Chat API Test Script
# Targets Raspberry Pi or any server running a Lucidus instance

API_ROOT="http://localhost/wp-json/lucidus/v1"
USER_ID=1234  # replace with actual user ID for testing
LOG_FILE="chat_api_test_results.log"

echo "🧪 Starting Lucidus Chat API tests..." > "$LOG_FILE"

# Test 1: Send a chat message
echo "➡️ Testing /chat/stream endpoint..." >> "$LOG_FILE"
curl -s -X POST "$API_ROOT/chat/stream" \
  -H "Content-Type: application/json" \
  -d '{"user_id": '"$USER_ID"', "message": "What do you see in the smoke, Lucidus?"}' >> "$LOG_FILE"
echo "\n---\n" >> "$LOG_FILE"

# Test 2: Load user chat log
echo "➡️ Testing /chat/load-log endpoint..." >> "$LOG_FILE"
curl -s "$API_ROOT/chat/load-log?user_id=$USER_ID" >> "$LOG_FILE"
echo "\n---\n" >> "$LOG_FILE"

# Test 3: Fetch public scroll/chat wall
echo "➡️ Testing /chat/public-feed endpoint..." >> "$LOG_FILE"
curl -s "$API_ROOT/chat/public-feed" >> "$LOG_FILE"
echo "\n---\n" >> "$LOG_FILE"

echo "✅ All chat endpoint tests complete. Check $LOG_FILE for results."
