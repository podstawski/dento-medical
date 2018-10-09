
var map, markerarray;

function clear_markers()
{
    for (var i = 0; i < markerarray.length; i++) {
        map.removeLayer(markerarray[i]);
    }
    markerarray=[];    
}



var idle = function(ev) {
    var center=map.getCenter();
    var latlng=center.lat+','+center.lng+','+map.getZoom();
    var lh=location.href;
    var pyt=lh.indexOf('?');
    var me=lh.indexOf('m=');
    if (me>0) lh=lh.substr(0,me);
    if (pyt>0) lh+='&';
    else lh+='?';
    lh+='m='+latlng;
    lh=lh.replace('&&','&');
    history.pushState('', 'Mapa', lh);
}

function initialize(lat,lng,zoom,here) {
    
    
    map = L.map('map-canvas');
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
		attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
	}).addTo(map);
    

    map.setView([lat, lng], zoom);
    
    markerarray=[];

    for(var i=0;i<churches.length; i++)
    {
        lat=churches[i].lat;
        lng=churches[i].lng;
         
        
        var marker = L.marker([lat, lng], {
            title: churches[i].name,
            url: churches[i].url,
            icon: L.icon({
                iconUrl: '../img/gmap_icon.png',
                iconSize: [23, 43],
                iconAnchor: [11, 43]
            })
        }).addTo(map).on('click',function(){
            window.location.href = this.options.url;
        });
        
        
        markerarray.push(marker);
  
         
    }

    map.on('moveend', idle);
        
}




window.addEventListener("load", function(event) {
    
        
    if (typeof(LATLNG)!='undefined') {
        var pos=LATLNG.split(',');
        initialize(parseFloat(pos[0]),parseFloat(pos[1]),parseInt(pos[2]),false);
        
    }
    
   
    
});