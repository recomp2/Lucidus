<?php
/**
 * Template Name: Lucidus Terminal
 */

get_header(); ?>

<div id="lucidus-terminal-wrapper">
  <h1 class="terminal-heading">🌀 Lucidus Terminal</h1>
  <div id="lucidus-chat-log" class="lucidus-log"></div>

  <textarea id="lucidus-input" placeholder="Speak, type, or whisper your prophecy..."></textarea>

  <div class="terminal-controls">
    <button id="lucidus-send-btn">Send</button>
    <button id="lucidus-mic-btn">🎙️ Mic</button>
    <button id="lucidus-toggle-mode">🧠 Text/Voice</button>
  </div>

  <audio id="lucidus-audio-playback" controls hidden></audio>
</div>

<?php
// Load terminal scripts
wp_enqueue_script('lucidus-chat', plugin_dir_url(__FILE__) . '../lucidus-terminal-pro/assets/js/lucidus-chat.js', array(), '1.0', true);
wp_enqueue_script('lucidus-sfx', plugin_dir_url(__FILE__) . '../lucidus-terminal-pro/assets/js/lucidus-sfx.js', array(), '1.0', true);
wp_enqueue_script('lucidus-stream-audio', plugin_dir_url(__FILE__) . '../lucidus-terminal-pro/assets/js/lucidus-stream-audio.js', array(), '1.0', true);

wp_enqueue_style('lucidus-style', plugin_dir_url(__FILE__) . '../lucidus-terminal-pro/assets/css/lucidus-chat.css');
?>

<style>
  #lucidus-terminal-wrapper {
    background: #111;
    color: #0f0;
    padding: 2rem;
    font-family: monospace;
  }
  textarea {
    width: 100%;
    height: 80px;
    background: #000;
    color: #0f0;
    margin-top: 1rem;
  }
  .terminal-controls {
    margin-top: 1rem;
  }
  .terminal-controls button {
    margin-right: 1rem;
    background: #333;
    color: #fff;
    border: none;
    padding: 0.5rem 1rem;
  }
</style>

<?php get_footer(); ?>
