
var map, markerarray;

function clear_markers()
{
    for (var i = 0; i < markerarray.length; i++) {
        markerarray[i].setMap(null);
    }
    markerarray=[];    
}



function initialize(lat,lng,zoom,here) {
    
    
    var myLatlng = new google.maps.LatLng(lat,lng);
    var mapOptions = {
        zoom: zoom,
        center: myLatlng
    }
    map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
    
    
    markerarray=[];

    for(var i=0;i<churches.length; i++)
    {
        lat=churches[i].lat;
        lng=churches[i].lng;
         
        var marker = new google.maps.Marker({
            position: new google.maps.LatLng(lat,lng),
            map: map,
            icon: '../img/'+(churches[i].mass_count>0?'gmap_icon.png':'gmap_icon_red.png'),
            title: churches[i].name,
            url: churches[i].url
        });
        google.maps.event.addListener(marker, 'click', function() {
            window.location.href = this.url;
        });
        

        
        markerarray.push(marker);
    }



        
    google.maps.event.addListener(map, 'idle', function(ev){
        var center=map.getCenter();
        var latlng=center.lat()+','+center.lng()+','+map.getZoom();    
        var lh=location.href;
        var pyt=lh.indexOf('?');
        var me=lh.indexOf('m=');
        if (me>0) lh=lh.substr(0,me);
        if (pyt>0) lh+='&';
        else lh+='?';
        lh+='m='+latlng;
        lh=lh.replace('&&','&');
        history.pushState('', 'Mapa', lh);
                
    });        


    
    
}



google.maps.event.addDomListener(window, 'load', function() {
    
        
    if (typeof(LATLNG)!='undefined') {
        var pos=LATLNG.split(',');
        initialize(parseFloat(pos[0]),parseFloat(pos[1]),parseInt(pos[2]),false);
        
    }
    
   
    
});