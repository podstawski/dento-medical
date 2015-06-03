<?php

    include __DIR__ .'/fun.php';

    
    $match_array=['name'=>'',
                  'postal'=>'xxxxxxxxx',
                  'city'=>'xxxxxxxxx',
                  'address'=>'<p>',
                  'www'=>'href="http://',
                  'email'=>'href="mailto:',
                  'phone'=>', tel.|tel.',
                  'rector'=>'Proboszcz',
                  'sun'=>'Niedziele i święta',
                  'week'=>'xxxx',
                  'fest'=>'xxxxxxxxx',
                  'latlng'=>'<iframe src="',
                  'url'=>'xxxxxxxx',
    ];
    
    echo '"'.implode('","',array_keys($match_array)).'"'."\n";
    
    

    $url='http://www.diecezja.pl/parafie/lista-parafii.html';
    $html=url_get($url);

    //$html=fromtxt2txt($html,'Spis Parafii wg miejscowości','<aside id="sidebar"');
    
    $a=[];
    
    if (preg_match_all("~ELM_NAME:[^']*'([^']+)',[^']*ELM_URL:[^']*'(/parafie/lista-parafii/[^']+)'~",$html,$a))
    {
        
        $rand=rand(1,count($a[1]));
        $lp=0;
        
        foreach ($a[2] AS $i=>$url)
        {

            $url=str_replace('&amp;','&',$url);
            $url="http://www.diecezja.pl$url";
            $html=url_get($url);
            
            //echo "$url\n";continue;
            
            //$html=iconv('ISO-8859-2','UTF-8',$html);
            $html=fromtxt2txt($html,'<strong>ADRES</strong>','<div class="rPanel">');
            $html=str_replace('&oacute;','o',$html);
            $rec=array();
            
            $html=preg_replace('~<em>[^<]*</em>~','',$html);
            foreach ($match_array AS $k=>$v) $rec[$k]=find_on_tag($html,$v);
            
            $rec['name']=trim($a[1][$i]);
            $rec['url']=$url;
            
            if (!$rec['week']) {
                $rec['week']=find_on_tag($html,'Soboty');
                if ($rec['week']) $rec['week'].=' sobota';
            }
            
            $rec['sun']=hours($rec['sun']);
            $rec['week']=hours($rec['week']);
            
            $rec['phone']=str_replace('+48','',$rec['phone']);
            $rec['phone']=str_replace('/fax','',$rec['phone']);
            
            
            //if (++$lp==$rand) {print_r($rec); break;}
            

            echo '"'.implode('","',$rec).'"'."\n";
        }
    }

        

    
   