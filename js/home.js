
var proceed_prority=0;

$(function(){
    $('#kiedyMszaForm .date').pickadate({
        onSet:function() {
            proceed_prority=2;
            ga('send', 'pageview', '/?date');
            grid_start('datepicker',2);
        },
        format: 'dddd, dd mmm yyyy',
        selectYears: false,
    });

    $('#kiedyMszaForm .time').pickatime({
        onSet:function() {
            proceed_prority=2;
            ga('send', 'pageview', '/?time');
            grid_start('timepicker',2);
        },        
        format: 'HH:i',
        formatSubmit: 'HH:i',
        min: [6,0],
        max: [21,0],
        interval: 60
    });
    
    setTimeout(function() {
        if (proceed_prority==0)
            grid_start('timer',0);
    },400);
    
    
    
});



