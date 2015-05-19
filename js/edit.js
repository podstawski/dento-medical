
$('.carousel').carousel();

var marker;
var map;

google.maps.event.addDomListener(window, 'load', function(){

    $('.church-map').each(function() {

        var myLatlng = new google.maps.LatLng($(this).attr('lat'),$(this).attr('lng'));
        
        var mapOptions = {
          zoom: 16,
          center: myLatlng
        }
        map = new google.maps.Map(this, mapOptions);
        
        marker = new google.maps.Marker({
            position: myLatlng,
            map: map,
            title: $(this).attr('title'),
            icon: '../img/gmap_icon.png'
        });
        
        google.maps.event.addListener(map, 'click', function (event) {
        
          $('#lat').val(event.latLng.lat());
          $('#lng').val(event.latLng.lng());
          
          marker.setPosition( new google.maps.LatLng( $('#lat').val(), $('#lng').val() ) );
          //map.panTo( new google.maps.LatLng( $('#lat').val(), $('#lng').val() ) );

        });
        
    });

});

$(function() {
  
  $('#iamhere').click(function(){
   
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function (pos) {
          $('#lat').val(pos.coords.latitude);
          $('#lng').val(pos.coords.longitude);
          
          marker.setPosition( new google.maps.LatLng( $('#lat').val(), $('#lng').val() ) );
          map.panTo( new google.maps.LatLng( $('#lat').val(), $('#lng').val() ) );
      });
    }
  
  });

  $('.table td.rm').click(function(){
    $(this).parent().fadeOut(1000, function(){
      $(this).remove();
    });
  });
  
  $('.table input.chkall').click(function(){
    var ch=$(this).prop('checked');
    $(this).parent().parent().find('input[type="checkbox"]').prop('checked',ch);
  });
  
  $('button.save').click(function(){
    var data=$('#churchForm').serialize();
    $.post(REST+'/church',data,function (resp) {
      console.log(resp);
    });
  });
  
  
});