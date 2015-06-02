<?php

    include __DIR__ .'/fun.php';

    
    $match_array=['name'=>', p.w.:|p.w.:',
                  'postal'=>'xxxxxxxxx',
                  'city'=>'xxxxxxxxx',
                  'address'=>', Adres:|Adres:',
                  'www'=>'www:',
                  'email'=>'E-mail:',
                  'phone'=>', tel.|tel.',
                  'rector'=>'Proboszcz:',
                  'sun'=>'xxxxxxx',
                  'week'=>'xxxx',
                  'fest'=>'xxxxxxxxx',
                  'latlng'=>'<iframe src="',
                  'url'=>'xxxxxxxx',
    ];
    
    echo '"'.implode('","',array_keys($match_array)).'"'."\n";
    
    

    $url='http://www.diecezja.rzeszow.pl/struktura/parafie/parafie-alfabetycznie/';
    $html=url_get($url);

    $html=fromtxt2txt($html,'<div id="main-wrapper">','<aside id="sidebar"');
    
    $a=[];
    
    if (preg_match_all('~<a href="(http://www.diecezja.rzeszow.pl/[^"]+)"~',$html,$a))
    {
        if (isset($argv[1])) $a[1]=[$argv[1]];
        
        $rand=rand(1,count($a[1]));
        $lp=0;
        
        foreach ($a[1] AS $i=>$url)
        {

            $url=str_replace('&amp;','&',$url);
            $html=url_get($url);
            
            //echo "$url\n";continue;
            
            //$html=iconv('ISO-8859-2','UTF-8',$html);
            $html=fromtxt2txt($html,'<div id="main-wrapper">','<div class="main-box vce-related-box">');
            $html=preg_replace('~&#[0-9]+;~',' ',$html);
            $rec=array();
            
            //$html=preg_replace('~\s*<br/>\s*~',', ',$html);
            foreach ($match_array AS $k=>$v) $rec[$k]=find_on_tag($html,$v);
            
            $rec['name']=fromtxt2txt($rec['name'],'',', Adres:');
            $rec['address']=fromtxt2txt($rec['address'],'',', tel.');
            $rec['rector']=fromtxt2txt($rec['rector'],'',',');
            $rec['url']=$url;
            
            if (strstr($rec['email'],'www:')) $rec['email']='';
            if (strstr($rec['www'],'gmina')) $rec['www']='';
            if (substr($rec['www'],0,7)=='http://') $rec['www']=substr($rec['www'],7);
            
            $m=[];
            if (preg_match_all('~niedziele, ([^<]+)<~',$html,$m)) $rec['sun']=implode('; ',$m[1]);
            elseif (preg_match_all('~niedziele i święta, ([^<]+)<~',$html,$m)) $rec['sun']=implode('; ',$m[1]);
            if (preg_match_all('~dzień powszedni, ([^<]+)<~',$html,$m)) $rec['week']=implode('; ',$m[1]);
            
            $rec['sun']=str_replace('czas letni i zimowy','',$rec['sun']);
            $rec['week']=str_replace('czas letni i zimowy','',$rec['week']);
            
            $rec['sun']=str_replace('czas letni,','lato',$rec['sun']);
            $rec['sun']=str_replace('czas zimowy,','zima',$rec['sun']);
            
            $rec['week']=str_replace('czas letni,','lato',$rec['week']);
            $rec['week']=str_replace('czas zimowy,','zima',$rec['week']);
            
            $rec['sun']=preg_replace('~^,~','',$rec['sun']);
            $rec['week']=preg_replace('~^,~','',$rec['week']);


            if (!preg_match('~[0-9][0-9]-[0-9][0-9][0-9]~',$rec['address'])) {
                if (preg_match('~([^>]*[0-9][0-9]-[0-9][0-9][0-9][^<]*)~',$html,$m))
                {
                    $rec['address'].=', '.$m[1];
                }
            }
            
            $l=[];
            if (preg_match('~!2d([0-9.]+)!3d([0-9.]+)!~',$rec['latlng'],$l))
            {
                $rec['latlng']=$l[2].','.$l[1];
                
            }
            
            //if (++$lp==$rand) {print_r($rec); break;}
            

            echo '"'.implode('","',$rec).'"'."\n";
        }
    }

        

    
   