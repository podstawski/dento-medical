var iamhere;
var heatmap,heatmapVisible=false;

function followMe() {
    if (navigator.geolocation ) {
        navigator.geolocation.getCurrentPosition(function (pos) {
            iamhere.setPosition( new google.maps.LatLng( pos.coords.latitude,pos.coords.longitude ) );
            setTimeout(followMe,15000);
        });
        
    }  
}


function initialize(lat,lng,zoom,here) {
    var myLatlng = new google.maps.LatLng(lat,lng);
    var mapOptions = {
        zoom: zoom,
        center: myLatlng
    }
    var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
    
    
    var myPosition=here?myLatlng:new google.maps.LatLng(0,0);

    iamhere = new google.maps.Marker({
        position: myPosition,
        map: map,
        icon: '../img/iamhere.png',
        title: 'Tu jestem'
    });
    
    
    $.get('heatmap',function(data) {
        var latlngData=[];
        for(i=0;i<data.length;i++) latlngData[latlngData.length] = new google.maps.LatLng(data[i].lat, data[i].lng);
        var pointArray = new google.maps.MVCArray(latlngData);
        heatmap = new google.maps.visualization.HeatmapLayer({
            data: pointArray
        });
    });

    
    setTimeout(followMe,15000);
    
    var markerarray=[];
        
    google.maps.event.addListener(map, 'idle', function(ev){
        var bounds = map.getBounds();
        var ne = bounds.getNorthEast();
        var sw = bounds.getSouthWest();
        var center=map.getCenter();
        var latlng=center.lat()+','+center.lng()+','+map.getZoom();
        
        var lh=location.href;
        var pyt=lh.indexOf('?');
        if (pyt>0) lh=lh.substr(0,pyt);
        history.pushState('', 'Mapa', lh+'?m='+latlng);

        
        var url='index.php?lat1='+ne.lat()+'&lng1='+ne.lng()+'&lat2='+sw.lat()+'&lng2='+sw.lng();
        

        


        $.get(url,function(churches) {
            
            for (var i = 0; i < markerarray.length; i++) {
              markerarray[i].setMap(null);
            }
            markerarray=[];

            if (churches.length==0 && !heatmapVisible) {
                heatmap.setMap(map);
                heatmapVisible=true;
            }
  
            if (churches.length>0 && heatmapVisible) {
                heatmap.setMap(null);
                heatmapVisible=false;
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
    
    
    if (navigator.geolocation && typeof(LATLNG)=='undefined') {
        navigator.geolocation.getCurrentPosition(function (pos) {
            
            initialize(pos.coords.latitude,pos.coords.longitude,13,true);
            
        });
        
    }
    
    if (typeof(LATLNG)!='undefined') {
        var pos=LATLNG.split(',');
        initialize(parseFloat(pos[0]),parseFloat(pos[1]),parseInt(pos[2]),false);
        
    }
    
    
    
});