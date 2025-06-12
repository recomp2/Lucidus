(function(){
    window.LucidusModules = window.LucidusModules || {};
    document.addEventListener('DOMContentLoaded', function(){
        const event = new Event('lucidus-modules-ready');
        document.dispatchEvent(event);
    });
})();
