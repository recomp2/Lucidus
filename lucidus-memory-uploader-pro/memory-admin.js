jQuery(function($){
  $('#lucidus-memory-pro-check').on('click', function(){
    var file = $('#lucidus-memory-pro-query').val();
    $.post(LucidusMemory.ajax_url, {
      action: 'lucidus_memory_check',
      file: file,
      _ajax_nonce: LucidusMemory.nonce
    }, function(resp){
      if(resp.success){
        $('#lucidus-memory-pro-response').text(resp.data);
      }else{
        $('#lucidus-memory-pro-response').text(resp.data || 'Error');
      }
    });
  });
});
