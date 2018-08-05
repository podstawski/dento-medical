




$(function(){
  var $carousel = $('.carousel').carousel({
    interval: false
  });
  $('#churchCarousel .item .remove').click(function(){
    if (confirm('Czy na pewno usunąć zdjęcie?')) {
      var div=$(this);
      $.get(REST+'/image/remove/'+div.attr('rel'),function (rm) {
        if (typeof(rm.status)!='undefined' && rm.status) {
          div.parent().fadeOut(500,function(){
            var ActiveElement = $carousel.find('.item.active');
            ActiveElement.remove();
            var NextElement = $carousel.find('.item').first();
            NextElement.addClass('active');
          });
        }
      });
    }
  });


});


