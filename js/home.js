
function grid_start()
{
    if (kiedymsza_grid_ajax.length==0) {
        kiedymsza_grid('kiedyMszaForm','kiedymsza_results_template','kiedymsza_results',15,'rest/church/search',true);
    } else {
        kiedymsza_grid_reload();
    }
}




$(function(){
    $('.date').pickadate({
    
    });

    setTimeout(grid_start,400);
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (pos) {
            $('#geo').val(pos.coords.latitude+','+pos.coords.longitude);
            $('#navigator_missing').fadeOut();
            grid_start();
        });
    }    
});



