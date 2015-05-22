function kiedymsza_grid_log(txt)
{
    //console.log(txt);
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



function kiedymsza_grid_load(txt)
{
    if (txt==null) {
        txt=$('#'+kiedymsza_grid_form).serialize();
    }
    kiedymsza_grid_log('loading offset '+kiedymsza_grid_offset+', when '+kiedymsza_grid_when+', limit '+kiedymsza_grid_limit);
    
    var d = new Date();
    var url=kiedymsza_grid_ajax+'?now='+d.getHours()+':'+d.getMinutes()+'&limit='+kiedymsza_grid_limit+'&offset='+kiedymsza_grid_offset+'&when='+kiedymsza_grid_when+'&'+txt;
    
    $.get(url,function (r) {
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
            $('#'+kiedymsza_grid_results).append('<div id="kiedymsza_grid_scroll_to"></div>');
            $(window).scroll(kiedymsza_grid_scroll); 
            kiedymsza_grid_log('waiting to scroll');
        }        
        
        kiedymsza_grid_log('data loaded ('+data.length+'), offset->'+kiedymsza_grid_offset+', when->'+kiedymsza_grid_when);
        
    });   
}


function kiedymsza_grid_reload()
{
    $('#'+kiedymsza_grid_results).html('');
    kiedymsza_grid_offset=0;
    kiedymsza_grid_when=0;
    kiedymsza_grid_load();
}

function kiedymsza_grid_scroll()
{
    var scroll_to = $('#kiedymsza_grid_scroll_to');

    if (typeof(scroll_to.get(0))=='undefined') return; 
    
    var hT = scroll_to.offset().top,
        hH = scroll_to.outerHeight(),
        wH = kiedymsza_grid_winheight,
        wS = $(window).scrollTop();
        
    
    var h3=hT+hH-wH;
      
    if (wS > h3){
        $('#kiedymsza_grid_scroll_to').remove();
        kiedymsza_grid_log('scroller reached');
        kiedymsza_grid_load(null);
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
    
    

    kiedymsza_grid_load(req);


}

function grid_start(why)
{
    if (kiedymsza_grid_ajax.length==0) {
        kiedymsza_grid_log(why+': grid init');
        kiedymsza_grid('kiedyMszaForm','kiedymsza_results_template','kiedymsza_results',15,'rest/church/search',true);
    } else {
        kiedymsza_grid_log(why+': grid reload');
        kiedymsza_grid_reload();
    }
    
    
}

