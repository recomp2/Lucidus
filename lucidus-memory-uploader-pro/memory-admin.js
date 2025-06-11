jQuery(function($){
  $('#lucidus-memory-pro-check').on('click', function(){
    var file = $('#lucidus-memory-pro-query').val();
    var $btn = $(this);
    $btn.prop('disabled', true).text('Checking...');
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
      $btn.prop('disabled', false).text('Ask Lucidus');
    });
  });

  $('#lucidus-memory-search').on('keyup', function(){
    var term = $(this).val().toLowerCase();
    $('#lucidus-memory-table tbody tr').each(function(){
      var text = $(this).find('td:first').text().toLowerCase();
      $(this).toggle(text.indexOf(term) !== -1);
    });
  });

  $(document).on('click', '.lucidus-inject', function(e){
    e.preventDefault();
    var file = $(this).data('file');
    var $link = $(this);
    $link.text('Injecting...');
    $.post(LucidusMemory.ajax_url, {
      action: 'lucidus_memory_inject',
      file: file,
      _ajax_nonce: LucidusMemory.nonce
    }, function(resp){
      alert(resp.data);
      $link.text('Test Inject');
    });
  });
});
