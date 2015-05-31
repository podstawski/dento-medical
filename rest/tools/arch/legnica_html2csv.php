<?php

    include __DIR__.'/fun.php';

    
    $match_array=['name'=>'xxxxxxxxx',
                  'address'=>'xxxxxxxxx',
                  'www'=>'WWW: <a href="',
                  'email'=>'href="mailto:',
                  'phone'=>'xxxxxxxxx',
                  'rector'=>'xxxxxxxxx',
                  'sun'=>'xxxxxxxxx',
                  'week'=>'xxxxxxxxx',

                  'url'=>'xxxxxx',
    ];
    
    echo '"'.implode('","',array_keys($match_array)).'"'."\n";
    
    
    $url='http://www.diecezja.legnica.pl/teksty/parafie.php';
    $html=url_get($url);
    $html=iconv('ISO-8859-2','UTF-8',$html);
    
    
    
    $all=[];
    
    $re='<a href="([^"]+)"[^>]*>\s*<h5 class=teksty><b>([^<]+)</a><br>';
    $re.='[^<]*Adres: </b>([^<]+)<br>';
    $re.='(.+?)</h5>';

    

    
    preg_match_all('~'.$re.'~si',$html,$all);
    
    $lp=0;
    $rand=rand(1,160);
    
    foreach ($all[1] AS $i=>$url)
    {
        $rec=[];
        foreach ($match_array AS $k=>$v) $rec[$k]=find_on_tag($html,$v);
        
        $rec['url']="http://www.diecezja.legnica.pl/teksty/$url";
        
        $rec['name']=trim(substr($all[2][$i],strpos($all[2][$i],',')+1));
        
        $rec['address']=str_replace(')','',$all[3][$i]);
        $rec['address']=str_replace(' (',', ',$rec['address']);
        $rec['www']=str_replace('http://','',$rec['www']);
        
        $html=url_get($rec['url']);
        $html=iconv('ISO-8859-2','UTF-8',$html);
        
        $rec['rector']=find_on_tag($html,'<h5 class=teksty><br><b>');
        $rec['sun']=find_on_tag($html,'w niedziele:');
        $rec['week']=find_on_tag($html,'w dni powszednie:');
        $rec['phone']=find_on_tag($html,'<br>Tel.');
        
        //if (++$lp==$rand) {print_r($rec); break;}
        //continue;
        
       
        
        echo '"'.implode('","',$rec).'"'."\n";
    }
