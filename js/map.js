function initialize(lat,lng) {
    var myLatlng = new google.maps.LatLng(lat,lng);
    var mapOptions = {
        zoom: 14,
        center: myLatlng
    }
    var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
    
    var iamhere = new google.maps.Marker({
        position: myLatlng,
        map: map,
        icon: '../img/iamhere.png',
        title: 'Tu jestem'
    });
    
    
    var markerarray=[];
        
    google.maps.event.addListener(map, 'idle', function(ev){
        var bounds = map.getBounds();
        var ne = bounds.getNorthEast();
        var sw = bounds.getSouthWest();                
        
        var url='index.php?lat1='+ne.lat()+'&lng1='+ne.lng()+'&lat2='+sw.lat()+'&lng2='+sw.lng();
        

        
        if (map.getZoom()>=12) {
            $('#footer').fadeOut(500); 
        } 

        $.get(url,function(churches) {
            
            for (var i = 0; i < markerarray.length; i++) {
              markerarray[i].setMap(null);
            }
            markerarray=[];

            if (churches.length==0 && map.getZoom()<12) {
                $('#footer').fadeIn(500);
            }
  
            for(var i=0;i<churches.length; i++)
            {
                lat=churches[i].lat;
                lng=churches[i].lng;
                 
                var marker = new google.maps.Marker({
                    position: new google.maps.LatLng(lat,lng),
                    map: map,
                    icon: '../img/gmap_icon.png',
                    title: churches[i].name,
                    url: churches[i].url
                });
                google.maps.event.addListener(marker, 'click', function() {
                    window.location.href = this.url;
                });
                

                
                markerarray.push(marker);
            }
            
        });
        
    });        




}



google.maps.event.addDomListener(window, 'load', function() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (pos) {
            
            initialize(pos.coords.latitude,pos.coords.longitude);
            
        });
        
    }
    
});