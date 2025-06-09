document.addEventListener('DOMContentLoaded', function() {
  const led = document.getElementById('lucidus-status-led');
  if (led) {
    // In a real plugin, status check logic would update this class
    led.style.background = 'green';
  }
});
