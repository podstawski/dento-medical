
var iamhere;
function followMe(map) {
    if (navigator.geolocation ) {
        navigator.geolocation.getCurrentPosition(function (pos) {
    
            if (typeof(map)=='object') {
        
                iamhere = marker = L.marker([pos.coords.latitude, pos.coords.longitude], {
                    title: 'Tu jestem',
                    icon: L.icon({
                        iconUrl: '../img/iamhere.png',
                        iconSize: [12, 12]
                    })
                }).addTo(map).on('click',function(){
                    map.setZoom(14);
                    map.setView({lat:pos.coords.latitude, lng:pos.coords.longitude});
                });
                
                
            } else {
                iamhere.setLatLng({lat:pos.coords.latitude, lng:pos.coords.longitude});
            }
            setTimeout(followMe,15000);
        });
        
    }  
}
