jQuery(document).ready(function($){
    $('#lpe-prophecy-form').on('submit', function(e){
        e.preventDefault();
        var formData = $(this).serializeArray();
        formData.push({name: 'action', value: 'lpe_generate'});
        formData.push({name: 'nonce', value: lpeAjax.nonce});
        $.post(lpeAjax.url, formData, function(response){
            if(response.success){
                $('#lpe-prophecy-output').html(response.data.prophecy);
            } else {
                $('#lpe-prophecy-output').text('An error occurred.');
            }
        });
    });
});
