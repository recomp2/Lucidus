(function(){
  'use strict';

  // Loads the style codex
  async function loadCodex(){
    const response = await fetch('/wp-content/dbs-library/codex/lucidus-terminal-style-codex.json');
    return response.json();
  }

  // Apply a theme mode by name
  function applyTheme(codex, mode){
    const palette = codex.primary_palette[mode];
    if(!palette) return;
    document.documentElement.style.setProperty('--lucidus-bg', palette.background);
    document.documentElement.style.setProperty('--lucidus-text', palette.text);
    document.documentElement.style.setProperty('--lucidus-accent', palette.accent);
    document.documentElement.style.setProperty('--lucidus-highlight', palette.highlight);
  }

  // Interpret commands
  function handleCommand(cmd, codex){
    cmd = cmd.toLowerCase();
    if(cmd.includes('dark')){
      applyTheme(codex, 'dark_mode');
    } else if(cmd.includes('chaos')){
      applyTheme(codex, 'chaos_mode');
    } else if(cmd.includes('light')){
      applyTheme(codex, 'light_mode');
    }
  }

  // Bind input field or voice command
  function init(){
    loadCodex().then(codex => {
      const input = document.querySelector('#lucidus-command');
      if(!input) return;
      input.addEventListener('change', function(){
        handleCommand(this.value, codex);
        this.value = '';
      });
    });
  }

  document.addEventListener('DOMContentLoaded', init);
})();
