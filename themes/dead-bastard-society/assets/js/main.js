jQuery(document).ready(function($){
    $('.fade-in').css('opacity',0).each(function(i){
        $(this).delay(200*i).animate({opacity:1},1000);
    });
});

