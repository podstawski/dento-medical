
var marker;
var map;

window.addEventListener("load", function(event) {

    $('.church-map').each(function() {

        var self=this;
        map = L.map(this);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        
        map.setView([$(self).attr('lat'), $(self).attr('lng')], 16);
        
        
        var marker = L.marker([$(self).attr('lat'), $(self).attr('lng')], {
            title: $(this).attr('title'),
            draggable:true,
            icon: L.icon({
                iconUrl: '../img/gmap_icon.png',
                iconSize: [23, 43],
                iconAnchor: [11, 43]
            })
        }).addTo(map);
        
        
        followMe(map);
        
        
        marker.on('dragend', function(e){
          var marker = e.target;
          var position = marker.getLatLng();          
          $('#lat').val(position.lat);
          $('#lng').val(position.lng);
        });
        
        map.on('click', function(e){
          var position = e.latlng;
          $('#lat').val(position.lat);
          $('#lng').val(position.lng);
          marker.setLatLng(e.latlng); 
        });
      
        
    });

});



$(function() {
  
  $('#iamhere').click(function(){
   
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function (pos) {
          $('#lat').val(pos.coords.latitude);
          $('#lng').val(pos.coords.longitude);
          
          marker.setLatLng({lat:$('#lat').val(), lng:$('#lng').val()});
          map.panTo( {lat:$('#lat').val(), lng:$('#lng').val()} );
      });
    }
  
  });

  
});