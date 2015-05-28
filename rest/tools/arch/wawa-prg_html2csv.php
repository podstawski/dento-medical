<?php

    include __DIR__ .'/fun.php';

    
    $match_array=['name'=>'</a></div></div><div>',
                  'postal'=>'xxxxxxxxx',
                  'city'=>'xxxxxxxxx',
                  'address'=>'<div class=\'WDktk\'>',
                  'www'=>"target='_blank'>",
                  'email'=>"href='mailto:?",
                  'phone'=>"<div class='icon-phone'><span class='num'>",
                  'rector'=>"<div caption='Proboszcz'>",
                  'sun'=>'uroczystości:|Niedziele i uroczystości:|Niedziele:',
                  'week'=>'Dni powszednie:',
                  'fest'=>'xxxxxxxxx',
                  'latlng'=>'xxxxxxx',
                  'url'=>'xxxxxxxx',
    ];
    
    echo '"'.implode('","',array_keys($match_array)).'"'."\n";
    
    

    $url='http://www.diecezja.waw.pl/struktura/parafie/';
    $html=url_get($url);
    $ids=[];
    
    $a=[];
    if (preg_match_all("~<a href='/struktura/parafie/([0-9]+)/'~",$html,$a))
    {
        foreach ($a[1] AS $id)
        {
            if (isset($ids[$id])) continue;
            $ids[$id]=true;
            
            $url='http://www.diecezja.waw.pl/struktura/parafie/'.$id.'/';
            $html=url_get($url);
            $html=substr($html,strpos($html,"<a href='/struktura/'>Struktura</a>"));
            $html=str_replace('</br>',', ',$html);
            $rec=array();
            foreach ($match_array AS $k=>$v) $rec[$k]=find_on_tag($html,$v);
            $rec['url']=$url;
            
            $name=explode(',',$rec['name']);
            $rec['name']=$name[0];
            //print_r($rec); break;
            
            //continue;
            

            
            
            echo '"'.implode('","',$rec).'"'."\n";
        }
    }
    //break;
        

    
   