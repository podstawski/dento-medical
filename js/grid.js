

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



function kiedymsza_grid_load(txt)
{
    if (txt==null) {
        txt=$('#'+kiedymsza_grid_form).serialize();
    }
    
    var d = new Date();
    var url=kiedymsza_grid_ajax+'?now='+d.getHours()+':'+d.getMinutes()+'&limit='+kiedymsza_grid_limit+'&offset='+kiedymsza_grid_offset+'&when='+kiedymsza_grid_when+'&'+txt;
    
    $.get(url,function (r) {
        var html=$('#'+kiedymsza_grid_template).html();
        
        data=r.data;
        kiedymsza_grid_when=r.options.when;
        for(i=0;i<data.length;i++)
        {
            html2=smekta(html,data[i]);
            $(html2).appendTo('#'+kiedymsza_grid_results).fadeIn();
            kiedymsza_grid_offset++;
            
        }
        var div='<div id="kiedymsza_grid_scroll_to"></div>';
        
        if (data.length>0 && kiedymsza_grid_lazyload) $('#'+kiedymsza_grid_results).append(div);
        
        var navi='<ul>';
        var pages=Math.ceil(r.total/r.limit);
        var current_page=1+Math.ceil(r.offset/r.limit);

        
        var from=current_page-4;
        var to=current_page+5;
        while (from<=0)
        {
            from++;to++;
        }
        while (to>pages)
        {
            to--;
            if (from>1) from--;
        }
        
        
        if (from!=1) {
            navi+='<li class="first">';
            navi+='<a href="javascript:kiedymsza_grid_jump('+0+')">'+1+'</a></li>';
            navi+='<li class="break">...</li>';
        }
        
        for (i=from; i<=to; i++)
        {
            var c='';
            if (i==current_page) c='active'
            if (i==1) {
                if (c.length>0) c+=' ';
                c+='first';
            }
            if (i==pages) {
                if (c.length>0) c+=' ';
                c+='last';
            }
            navi+='<li class="'+c+'">';
            navi+='<a href="javascript:kiedymsza_grid_jump('+(i-1)*r.limit+')">'+i+'</a></li>';
        }

        if (to!=pages) {
            navi+='<li class="break">...</li>';
            navi+='<li class="last">';
            navi+='<a href="javascript:kiedymsza_grid_jump('+(pages-1)*r.limit+')">'+pages+'</a></li>';
            
        }
        
        navi+='</ul>';
        if (pages==1) {
            navi='';
        }
        $('.kiedymsza_grid_navi').html(navi);
        
        
    });   
}

function kiedymsza_grid_jump(offset)
{
    kiedymsza_grid_offset=offset;
    $('#'+kiedymsza_grid_results).html('');
    kiedymsza_grid_load(null);
}

function kiedymsza_grid_reload()
{
    //console.log('reload');
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
        wH = $(window).height(),
        wS = $(window).scrollTop();
  
    if (wS > (hT+hH-wH)){
        $('#kiedymsza_grid_scroll_to').remove();
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
    
    $('#'+kiedymsza_grid_results).html('');
    
    if (lazyload) {
        $('#'+kiedymsza_grid_form+' select,#'+kiedymsza_grid_form+' input').change(function() {
            kiedymsza_grid_offset=0;
            $('#'+kiedymsza_grid_results).html('');
            kiedymsza_grid_load(null);
        });
        
        $(window).scroll(kiedymsza_grid_scroll);
        $('#preview-content').scroll(kiedymsza_grid_scroll); //kameleon mode       
    }
    
    

    kiedymsza_grid_load(req);


}

function grid_start()
{
    if (kiedymsza_grid_ajax.length==0) {
        kiedymsza_grid('kiedyMszaForm','kiedymsza_results_template','kiedymsza_results',15,'rest/church/search',true);
    } else {
        kiedymsza_grid_reload();
    }
}

