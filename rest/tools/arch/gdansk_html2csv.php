<?php

    include __DIR__.'/fun.php';

    
    $match_array=['name'=>'xxxxxxxxx',
                  'postal'=>'xxxxxxxxx',
                  'city'=>'xxxxxxxxx',
                  'address'=>'xxxxxxxxx',
                  'www'=>'xxxxxxx',
                  'email'=>'xxxxxxx',
                  'phone'=>'xxxxxxxxx',
                  'rector'=>'xxxxxxxxx',
                  'sun'=>'class="niedziela"> Nd. </td><td>',
                  'week'=>'xxxxxxxxx',
                  'fest'=>'xxxxxxxxx',
                  'latlng'=>'xxxxxxx',
                  'url'=>'xxxxxx',
    ];
    
    echo '"'.implode('","',array_keys($match_array)).'"'."\n";
    
    
    $url='http://diecezja.gdansk.pl/parafie';
    $html=url_get($url);
    
    
    
    
    $all=[];
    
    $re='<tr><td>[^<]+</td><td><a href="([^"]+)"><h6>([^<]+)</a></h6></td>';
    $re.='[^<]*<td><span style="font-size:85%;">[^<]*</span></td>';
    $re.='[^<]*<td width="20%"><span style="font-size:85%;">([^<]+)<br>([^<]+)<b>([^<]+)</b></span></td>';
    $re.='[^<]*<td width="10%"><span style="font-size:85%;">([^<]+)</span></td>';
    $re.='[^<]*<td><span style="font-size:85%;"><a href="mailto:([^?]+)[^"]*">[^<]*<img src="/images/loga/at.png">[^<]*</a></span></td>';
    $re.='[^<]*<td><span style="font-size:85%;"><a href="http://([^"]*)" target="_blank">[^<]*</a></span></td>';
    
    preg_match_all('~'.$re.'~',$html,$all);
    
    foreach ($all[1] AS $i=>$url)
    {
        $rec=[];
        foreach ($match_array AS $k=>$v) $rec[$k]=find_on_tag($html,$v);
        
        $rec['url']="http://diecezja.gdansk.pl$url";
        $rec['name']='Kościół '.$all[2][$i];
        $rec['address']=trim($all[4][$i]).' '.$all[5][$i].', '.$all[3][$i];
        $rec['phone']=$all[6][$i];
        $rec['email']=str_replace('AT','@',$all[7][$i]);
        if (strlen($rec['email'])==1) $rec['email']='';
        $rec['www']=$all[8][$i];
        
        echo '"'.implode('","',$rec).'"'."\n";
    }
