jQuery(document).ready(function($){
    $('#dbgpt-generate').on('click', function(e){
        e.preventDefault();
        var out = $('#dbgpt-output');
        out.text('Loading...');
        fetch(dbgptPrompt.restUrl)
            .then(function(res){ return res.json(); })
            .then(function(data){
                out.text(data.prompt || JSON.stringify(data));
            })
            .catch(function(err){ out.text('Error: '+err.message); });
    });
});
