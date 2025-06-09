jQuery(document).ready(function($){
    $('#lucidus-chat-form').on('submit', function(e){
        e.preventDefault();
        var message = $('#lucidus-chat-input').val();
        $('#lucidus-chat-input').val('');
        $('#lucidus-chat-log').append('<div class="user">'+message+'</div>');
        $.post(ajaxurl, {
            action: 'lucidus_chat',
            message: message,
            _ajax_nonce: lucidus_chat.nonce
        }, function(resp){
            if(resp.data){
                $('#lucidus-chat-log').append('<div class="bot">'+resp.data+'</div>');
            }
        });
    });
});
