


google.maps.event.addDomListener(window, 'load', function(){

    $('.church-map').each(function() {

        var myLatlng = new google.maps.LatLng($(this).attr('lat'),$(this).attr('lng'));
        
        var mapOptions = {
          zoom: 14,
          center: myLatlng
        }
        var map = new google.maps.Map(this, mapOptions);
        
        var marker = new google.maps.Marker({
            position: myLatlng,
            map: map,
            title: $(this).attr('title'),
            icon: '../img/gmap_icon.png'
        });
        
        followMe(map);
        
        
        google.maps.event.addListener(map, 'click', function (event) {
        
          var url='https://www.google.pl/maps/place/'+myLatlng.lat()+','+myLatlng.lng();
          window.open(url,'_blank');

        });
        
        google.maps.event.addListener(marker, 'click', function (event) {
        
          var url='https://www.google.pl/maps/place/'+myLatlng.lat()+','+myLatlng.lng();
          window.open(url,'_blank');

        });
        
        
        var maplink=$('li a[href*="mapa"]');
        maplink.attr('href',maplink.attr('href')+'?m='+myLatlng.lat()+','+myLatlng.lng()+',14');
        
    });

});

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


