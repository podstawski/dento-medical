<?php

    include __DIR__ .'/fun.php';

    
    $match_array=['name'=>'parafia pod wezwaniem</div>',
                  'postal'=>'xxxxxxxxx',
                  'city'=>'Miasto</div>',
                  'address'=>'ulica</div>',
                  'www'=>'Adres serwisu WWW</div>',
                  'email'=>'xxxxxxxx',
                  'phone'=>'telefon</div>',
                  'rector'=>'Nazwisko i imię</div>',
                  'sun'=>'niedziela i święta</div>',
                  'week'=>'dni powszednie</div>',
                  'fest'=>'święta zniesione',
                  'latlng'=>'xxxxxxxxx',
                  'url'=>'xxxxxxxx',
    ];
    
    echo '"'.implode('","',array_keys($match_array)).'"'."\n";
    
    

    $url='http://www.diecezja.tarnow.pl/index.php/schematyzm';
    $html=url_get($url);

    //$html=fromtxt2txt($html,'Spis Parafii wg miejscowości','<aside id="sidebar"');
    
    $a=[];
    
    if (preg_match_all('~<a href="(/index.php/schematyzm/[^"]+)"~',$html,$a))
    {
        if (isset($argv[1])) $a[1]=[$argv[1]];
        
        $rand=rand(1,count($a[1]));
        $lp=0;
        
        foreach ($a[1] AS $i=>$url)
        {

            $url=str_replace('&amp;','&',$url);
            $url="http://www.diecezja.tarnow.pl$url";
            $html=url_get($url);
            
            //echo "$url\n";continue;
            
            //$html=iconv('ISO-8859-2','UTF-8',$html);
            $html=fromtxt2txt($html,'<!-- end content-links -->','<ul class="pager pagenav">');
            $html=str_replace('&oacute;','ó',$html);
            $html=str_replace('&nbsp;',' ',$html);
            $rec=array();

            foreach ($match_array AS $k=>$v) $rec[$k]=find_on_tag($html,$v);
          
            $rec['url']=$url;
            
            $rec['email']=addy($html);
            
            if (strstr($rec['address'],'telefon')) $rec['address']='';
            if (strstr($rec['www'],'Msze')) $rec['www']='';
            
            if ($rec['address']) $rec['address'].=', ';
            $rec['address'].=$rec['city'];
            
            $rec['sun']=fromtxt2txt($rec['sun'],'','święta zniesione');
            
            //if (++$lp==$rand) {print_r($rec); break;}
            

            echo '"'.implode('","',$rec).'"'."\n";
        }
    }

        

    
   