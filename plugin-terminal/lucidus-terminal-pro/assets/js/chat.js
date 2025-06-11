jQuery(document).ready(function($){
    function appendMessage(cls, text){
        $('#chat-window').append($('<div>', { 'class': cls, text: text }));
        $('#chat-window').scrollTop($('#chat-window')[0].scrollHeight);
    }

    function sendMessage(){
        var message = $('#chat-input').val();
        if(!message) return;
        $('#chat-input').val('');
        appendMessage('user', message);
        $.ajax({
            url: lucidus_chat.ajax_url,
            method: 'POST',
            beforeSend: function(xhr){
                xhr.setRequestHeader('X-WP-Nonce', lucidus_chat.nonce);
            },
            data: {message: message},
            success: function(res){
                if(res.reply){
                    appendMessage('bot', res.reply);
                }
                if(res.quote){
                    appendMessage('system', res.quote);
                }
            }
        });
    }

    $('#chat-send').on('click', sendMessage);
    $('#chat-input').on('keypress', function(e){
        if(e.which === 13){
            e.preventDefault();
            sendMessage();
        }
    });
});
