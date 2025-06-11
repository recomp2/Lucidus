<div id="lucidus-group-terminal">
    <div id="lucidus-group-log" style="height:200px;overflow:auto;background:#111;color:#0f0;padding:10px;"></div>
    <input type="text" id="lucidus-group-input" style="width:80%;" />
    <button id="lucidus-group-send">Send</button>
</div>
<script type="text/javascript">
jQuery(function($){
    function appendMessage(msg){
        $('#lucidus-group-log').append($('<div>').text(msg));
    }
    $('#lucidus-group-send').on('click', function(){
        var msg = $('#lucidus-group-input').val();
        $.post(ajaxurl,{action:'lucidus_memory_pull', msg:msg},function(r){
            appendMessage('You: '+msg); appendMessage(r);
        });
    });
});
</script>

