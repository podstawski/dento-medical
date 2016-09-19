function kiedymsza_grid_log(txt)
{
    //$.get('dbg/index.php?ts='+Date.now()+'&tx='+encodeURIComponent(txt))
    //console.log(txt);
}

function kiedymsza_grid_footerlog(txt)
{
    //$('.footer-menu a').html(txt);
}

function smekta(pattern,vars) {
    
    for (key in vars)
    {
        if (vars[key]==null)  vars[key]='';
        
        re=new RegExp('\\[if:'+key+'\\](.|[\r\n])+\\[endif:'+key+'\\]',"g");
        if (vars[key].length==0 || vars[key]==null || vars[key]=='0') pattern=pattern.replace(re,'');
        
        re=new RegExp('\\['+key+'\\]',"g");
        pattern=pattern.replace(re,vars[key]);
        
        
        pattern=pattern.replace('[if:'+key+']','');
        pattern=pattern.replace('[endif:'+key+']','');
        
    }
    
    return pattern;

}

var kiedymsza_grid_limit;
var kiedymsza_grid_offset=0;
var kiedymsza_grid_form;
var kiedymsza_grid_template;
var kiedymsza_grid_results;
var kiedymsza_grid_ajax='';
var kiedymsza_grid_lazyload=false;
var kiedymsza_grid_when=0;
var kiedymsza_grid_winheight=0;
var kiedymsza_grid_pri=0;



function kiedymsza_grid_load(pri)
{
    if (pri==null) pri=kiedymsza_grid_pri;
    
    kiedymsza_grid_pri=pri;
     
    txt=$('#'+kiedymsza_grid_form).serialize();
    dbg='loading offset='+kiedymsza_grid_offset+', when='+kiedymsza_grid_when+', limit='+kiedymsza_grid_limit+', pri='+pri;
    kiedymsza_grid_log(dbg);
    kiedymsza_grid_footerlog(dbg);
    
    
    
    var d = new Date();
    var url=kiedymsza_grid_ajax+'?now='+d.getHours()+':'+d.getMinutes()+'&limit='+kiedymsza_grid_limit+'&offset='+kiedymsza_grid_offset+'&when='+kiedymsza_grid_when+'&'+txt+'&pri='+pri;
    
    $.get(url,function (r) {
        //kiedymsza_grid_log(r);
        
        if (kiedymsza_grid_pri>r.options.pri) {
            kiedymsza_grid_log('data with smaller priority '+kiedymsza_grid_pri+'>'+r.options.pri);
            return;
        }        
        
        
        $('.kiedymsza_grid_scroll_to_wait').remove();
        
        //kiedymsza_grid_log('pri set to '+kiedymsza_grid_pri);
        //kiedymsza_grid_pri=r.options.pri;
        
        var html=$('#'+kiedymsza_grid_template).html();
        
        data=r.data;
        if (typeof(r.options.when)!='undefined') kiedymsza_grid_when=r.options.when;
        for(i=0;i<data.length;i++)
        {
            html2=smekta(html,data[i]);
            $(html2).appendTo('#'+kiedymsza_grid_results).fadeIn(200*(i+1));
            kiedymsza_grid_offset++;
            
        }
        

        if (kiedymsza_grid_lazyload && kiedymsza_grid_limit==data.length) {
            $('#'+kiedymsza_grid_results).append('<div class="kiedymsza_grid_scroll_to"></div>');
            $(window).scroll(kiedymsza_grid_scroll); 
            kiedymsza_grid_log('waiting to scroll');
        }        
        
        kiedymsza_grid_log('data loaded ('+data.length+'), offset->'+kiedymsza_grid_offset+', when->'+kiedymsza_grid_when);
        
    });   
}


function kiedymsza_grid_reload(pri)
{
    kiedymsza_grid_log('start new table - '+pri);
    $('#'+kiedymsza_grid_results).html('');
    kiedymsza_grid_offset=0;
    kiedymsza_grid_when=0;
    kiedymsza_grid_load(pri);
}

function kiedymsza_grid_scroll()
{
    var scroll_to = $('.kiedymsza_grid_scroll_to');

    if (typeof(scroll_to.get(0))=='undefined') return; 
    
    var hT = scroll_to.offset().top,
        hH = scroll_to.outerHeight(),
        wH = kiedymsza_grid_winheight,
        wS = $(window).scrollTop();
        
    
    var h3=hT+hH-wH;
    kiedymsza_grid_footerlog(wS+' > '+h3+' : '+(wS > h3));
      
    if (1.3*wS > h3){
        kiedymsza_grid_log('scroller reached');
        $('.kiedymsza_grid_scroll_to').addClass('kiedymsza_grid_scroll_to_wait').removeClass('kiedymsza_grid_scroll_to');
        kiedymsza_grid_load();
    }
}



function kiedymsza_grid(form,template,results,limit,ajax,lazyload,req)
{
    kiedymsza_grid_limit = limit;
    kiedymsza_grid_ajax = ajax;
    kiedymsza_grid_form = form;
    kiedymsza_grid_template = template;
    kiedymsza_grid_results = results;
    kiedymsza_grid_lazyload = lazyload;
    kiedymsza_grid_winheight=$(window).height();
    
    
    $('#'+kiedymsza_grid_results).html('');
    
    if (lazyload) {
        
        $(window).scroll(kiedymsza_grid_scroll);      
    }
    
    

    kiedymsza_grid_load();


}

function grid_start(why,pri)
{
    if (kiedymsza_grid_ajax.length==0) {
        kiedymsza_grid_log(why+': grid init('+pri+')');
        kiedymsza_grid_pri=pri;
        kiedymsza_grid('kiedyMszaForm','kiedymsza_results_template','kiedymsza_results',15,'rest/church/search',true);
    } else {
        kiedymsza_grid_log(why+': grid reload');
        kiedymsza_grid_reload(pri);
    }
    
    
}

