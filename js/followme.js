
var iamhere;
function followMe(map) {
    if (navigator.geolocation ) {
        navigator.geolocation.getCurrentPosition(function (pos) {
    
            if (typeof(map)=='object') {
        
                iamhere = new google.maps.Marker({
                    position: new google.maps.LatLng(pos.coords.latitude,pos.coords.longitude),
                    map: map,
                    icon: '../img/iamhere.png',
                    title: 'Tu jestem'
                });
                
                google.maps.event.addListener(iamhere, 'click', function() {
                    this.getMap().setZoom(14);
                    this.getMap().setCenter(new google.maps.LatLng(this.getPosition().lat(),this.getPosition().lng()));
                    
                });
            } else {
                iamhere.setPosition( new google.maps.LatLng( pos.coords.latitude,pos.coords.longitude ) );
            }
            setTimeout(followMe,15000);
        });
        
    }  
}
