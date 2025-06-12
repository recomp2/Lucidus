document.addEventListener('lucidus-modules-ready', ()=>{
    fetch(lucidus_chat.feed_url)
        .then(r=>r.json())
        .then(data=>{
            const div=document.getElementById('prophecy-expansion');
            if(div) div.textContent = data.map(p=>p.content).join('\n');
        });
});
