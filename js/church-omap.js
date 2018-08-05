var map;

window.addEventListener("load", function(event) {
    $('.church-map').each(function() {
        var self=this;
        map = L.map(this);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        
        map.setView([$(self).attr('lat'), $(self).attr('lng')], 14);
        
        var marker = L.marker([$(self).attr('lat'), $(self).attr('lng')], {
            title: $(this).attr('title'),
            icon: L.icon({
                iconUrl: '../img/gmap_icon.png',
                iconSize: [23, 43],
                iconAnchor: [11, 43]
            })
        }).addTo(map).on('click',function(){
            var url='https://www.google.pl/maps/place/'+$(self).attr('lat')+','+$(self).attr('lng');
            window.open(url,'_blank');
        });
        
        
      
        
        followMe(map);
        
      
        var maplink=$('li a[href*="mapa"]');
        maplink.attr('href',maplink.attr('href')+'?m='+$(self).attr('lat')+','+$(self).attr('lng')+',14');
        
    });

});