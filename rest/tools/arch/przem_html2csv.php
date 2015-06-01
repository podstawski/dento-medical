<?php

    include __DIR__ .'/fun.php';

    
    $match_array=['name'=>'xxxxxxxx',
                  'postal'=>'xxxxxxxxx',
                  'city'=>'xxxxxxxxx',
                  'address'=>'xxxxxxx',
                  'www'=>'xxxxxxx',
                  'email'=>'xxxxxxx',
                  'phone'=>'xxxxxx',
                  'rector'=>'xxxxxx',
                  'sun'=>'xxxxxxx',
                  'week'=>'xxxxxxx',
                  'fest'=>'xxxxxxxxx',
                  'latlng'=>'xxxxxxx',
                  'url'=>'xxxxxxxx',
    ];
    
    echo '"'.implode('","',array_keys($match_array)).'"'."\n";
    
    

    $url='http://przemyska.pl/godziny-mszy-sw/';
    $html=url_get($url);
    $html=fromtxt2txt($html,'<h1 class="post-title">Godziny Mszy św.</h1>','</article>');
    
    $a=[];
    $msze=[];
    if (preg_match_all('~<td class="column-1">([^<]*)</td><td class="column-2">([^<]*)</td><td class="column-3">([^<]*)</td>~',$html,$a))
    {
        foreach ($a[1] AS $i=>$gdzie) $msze[$a[1][$i].':'.$a[2][$i]]=$a[3][$i];
    }
    $url='http://przemyska.pl/parafie/';
    $html=url_get($url);
    $html=fromtxt2txt($html,'<h1 class="post-title">Parafie</h1>','</article>');

    
    
    $a=[];
    
    if (preg_match_all('~<td class="column-1">([^<]*)</td><td class="column-2">([^<]*)</td><td class="column-3">([^<]*)</td><td class="column-4">([^<]*)</td><td class="column-5">([^<]*)</td><td class="column-6">([^<]*)</td>~',$html,$a))
    {
        $rand=rand(1,count($a[1]));
        $lp=0;
        $rand=3;
        
        foreach ($a[2] AS $i=>$name)
        {

            $rec=array();
            foreach ($match_array AS $k=>$v) $rec[$k]=find_on_tag($html,$v);
            $rec['name']=$name;
            
            $token=$a[1][$i].':'.$a[2][$i];
            if (isset($msze[$token])) $rec['sun']=$msze[$token];
            
            $rec['phone']=$a[6][$i];
            $rec['city']=$a[5][$i];
            $rec['postal']=$a[4][$i];
            $rec['address']=$rec['postal'].' '.$rec['city'].', '.$a[3][$i];
            
            
            //if (++$lp==$rand) {print_r($rec); break;}
            
        
        
            
            
            echo '"'.implode('","',$rec).'"'."\n";
        }
    }

        

    
   