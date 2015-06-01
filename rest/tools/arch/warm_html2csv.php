<?php

    include __DIR__ .'/fun.php';

    
    $match_array=['name'=>'xxxxxxxx',
                  'postal'=>'xxxxxxxxx',
                  'city'=>'xxxxxxxxx',
                  'address'=>'<img src="themes/default/images/icons2/house.png" alt=""/>',
                  'www'=>'<a href="http://',
                  'email'=>'<a href="mailto:',
                  'phone'=>'<img src="themes/default/images/icons2/mobile_phone.png" alt=""/>',
                  'rector'=>'Proboszcz:',
                  'sun'=>'xxxxxxx',
                  'week'=>'xxxx',
                  'fest'=>'xxxxxxxxx',
                  'latlng'=>'xxxxxxx',
                  'url'=>'xxxxxxxx',
    ];
    
    echo '"'.implode('","',array_keys($match_array)).'"'."\n";
    
    

    $url='http://archwarmia.pl/parafie/parafie/';
    $html=url_get($url);

    
    $a=[];
    
    if (preg_match_all('~<a href="(/parafie/parafie/[^"]+)">([^<]+)<~',$html,$a))
    {
        $rand=rand(1,count($a[1]));
        $lp=0;
        
        foreach ($a[1] AS $i=>$url)
        {

            $url=str_replace('&amp;','&',$url);
            //$url=str_replace('&nbsp;',' ',$url);
            $url='http://archwarmia.pl'.$url;

            $html=url_get($url);
            //$html=iconv('ISO-8859-2','UTF-8',$html);
            $html=substr($html,strpos($html,'<h4>Kontakt</h4>'));
            
            $rec=array();
            
            $html=preg_replace('~\s*<br/>\s*~',', ',$html);
            foreach ($match_array AS $k=>$v) $rec[$k]=find_on_tag($html,$v);
            $rec['url']=$url;            
            $name=explode(':',$a[2][$i]);
            
            if (!isset($name[1])) continue;
            $rec['name']=trim($name[1]);
            $rec['city']=$name[0];
            
            if (strstr($rec['phone'],'Msze')) $rec['phone']='';
            
            $html=substr($html,strpos($html,'<div class="massContainer">'));
            $html=substr($html,0,strpos($html,'<h4>Duszpasterze</h4>'));
            
            $msze=$html;
            $msze=substr($msze,0,strpos($msze,'<div class="massDayTitle">Dni powszednie</div>'));
            $m=[];
            if (preg_match_all('~<div>\s*([^<]+?)\s*</div>~s',$msze,$m)) $rec['sun']=implode('; ',$m[1]);

            $msze=$html;
            $msze=substr($msze,strpos($msze,'<div class="massDayTitle">Dni powszednie</div>'));
            $msze=substr($msze,0,strpos($msze,'<div class="massDayTitle">Święta poza niedzielą</div>'));
            
            if (preg_match_all('~<div>\s*([^<]+?)\s*</div>~s',$msze,$m)) $rec['week']=implode('; ',$m[1]);

            $msze=$html;
            $msze=substr($msze,strpos($msze,'<div class="massDayTitle">Święta poza niedzielą</div>'));
            
            if (preg_match_all('~<div>\s*([^<]+?)\s*</div>~s',$msze,$m)) $rec['fest']=implode('; ',$m[1]);
            
            
            //if (++$lp==$rand) {print_r($rec); break;}
            
        
        
            
            
            echo '"'.implode('","',$rec).'"'."\n";
        }
    }

        

    
   