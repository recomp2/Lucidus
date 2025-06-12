jQuery(function($){
  function loadProphecies(){
    $.get(lucidus_chat.feed_url, function(data){
      var box = $('#prophecy-feed');
      box.empty();
      data.forEach(function(p){
        box.append($('<div class="prophecy">').text(p.content));
      });
      box.scrollTop(box[0].scrollHeight);
    });
  }
  loadProphecies();
  setInterval(loadProphecies, 60000);
});
