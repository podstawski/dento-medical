<?php

    include __DIR__ .'/fun.php';

    
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
                  'about'=>'xxxxxx'
    ];
    
    $url='http://szczecin.kuria.pl/wspolnoty/koscioly/';
    $html=url_get($url);
    $a=[];
    preg_match_all('~href="(http://szczecin.kuria.pl/wspolnoty/koscioly[^"]+)"~',$html,$a);
    foreach ($a[1] AS $url)
    {
        
        if ($url=='http://szczecin.kuria.pl/wspolnoty/koscioly/') continue;
        $html=url_get($url);
        
        
        $rec=array();
        foreach ($match_array AS $k=>$v) $rec[$k]=find_on_tag($html,$v);        
        
        $c=[];
        preg_match("~href=\"$url\"[^>]*>[ \r\n\t]*<span class=\"x1\" >([^<]+)</span>([^<]+)</a>~",$html,$c);
        
        $rec['city']=trim($c[1]);
        if (!$rec['city']) continue;
        
        $name2findmap=trim($c[1]).' '.trim($c[2]);
        
        $d=[];
        //preg_match('~point=new google.maps.LatLng\(([0-9\.,]+)\);markersmap.push\(createMarker\(mapmap,point,"'.$name2findmap.'~',$html,$d);
        
        preg_match_all('~point=new google.maps.LatLng\(([0-9\.,]+)\);markersmap.push\(createMarker\(mapmap,point,"([^"]+)"~',$html,$d);
        
        
        if (count($d)!=3) die($name2findmap.': '.print_r($d,1));
        foreach ($d[2] AS &$dd) $dd=trim($dd);
        
        $k=false;
        foreach($d[2] AS $i=>$n) {
            if (strstr($n,$name2findmap)) {
                $k=$i;
                break;
            }
        }
        
        if (!strlen($k)) foreach($d[2] AS $i=>$n) {
            if (strstr($n,$rec['city'])) {
                $k=$i;
                break;
            }
        }
        
        //if (!strlen($k)) die("$url | $name2findmap | [$k]".': '.print_r($d[2],1));
        if (!strlen($k)) continue;
        
        $rec['latlng']=$d[1][$k];
        
        $e=[];
        
        if(preg_match('~<img[^>]+src="([^"]+)"[^>]+class="ramka"~',$html,$e))
        {
            $rec['about']=$e[1];    
        }
        
        $rec['sun']=preg_replace('/([0-9]+[.:,][0-9][0-9])/','\\1,',$rec['sun']);
        $pos = strpos($html,'Godziny mszy świętych.');
        if ($pos) {
            $masses=substr($html,$pos);
            $masses=substr($masses,0,strpos($masses,'</table>'));
            
            $masses=str_replace('Nd.','',$masses);
            
            $f=[];
            
            if(preg_match_all('~class="tydzien">[ ]+([^ <]+)[ ]+</td><td>([^<]+)<~',$masses,$f))
            {
                foreach($f[2] AS &$ff) {
                    $ff=preg_replace('/ +/',' ',trim($ff));
                    $ff=preg_replace('/([0-9]+[.:,][0-9][0-9])/','\\1,',$ff);
                }
                if (count($f[2])==6 && count(array_unique($f[2]))==1) $rec['week']=$f[2][0];
                else {
                    foreach($f[1] AS $i=>$dt) $rec['week'].=$dt.' '.$f[2][$i].'; ';
                   
                }
            }
        } else {
            //echo "$url\n";
        }
        

        
        $b=[];
        preg_match('~href="(http://szczecin.kuria.pl/wspolnoty/wspolnoty-parafialne/[^"]+)"~',$html,$b);
        $adres=url_get($b[1]);
        $pos=strpos($adres,'<div class="dane_parafii"');
        $adres=substr($adres,$pos);
        $adres=substr($adres,0,strpos($adres,'</div>'));
        
        //echo $adres;
        //break;
        
        //echo "$url\n";
    }
    //"
    
    
    die();
    echo '"'.implode('","',array_keys($match_array)).'"'."\n";
    
    foreach (['b','c','d','g','h','i','j','k','l','m','n','o','p','r','s','t','u','w','z'] AS $litera)
    {
        for ($i=1;$i<10;$i++)
        {
            $url='http://www.diecezja-pelplin.pl/dekanaty-i-parafie?sid=104&task=list.alpha.'.$litera.'&site='.$i;
            $html=url_get($url);

            $pos=strpos($html,'Miejscowość:');
            if (!$pos) break;
            $pos=strpos($html,'spEntriesListContainer');
            if (!$pos) {
                echo "Problem spEntriesListContainer: $url\n";
                continue;
            }
            $html=substr($html,$pos);
            $a=[];

            preg_match_all('~href="(/dekanaty-i-parafie\?pid=104&amp;sid=[^"]+)"~',$html,$a);

            foreach ($a[1] AS $url)
            {
                $url='http://www.diecezja-pelplin.pl'.str_replace('&amp;','&',$url);
                $html=url_get($url);
                foreach ($match_array AS $k=>$v) $rec[$k]=find_on_tag($html,$v);
                $rec['url']=$url;
                
                $pos=strpos($rec['sun'],'święta „zniesione”:');
                if ($pos) $rec['sun']=substr($rec['sun'],0,$pos);
                $pos=strpos($rec['fest'],'dni powszednie:');
                if ($pos) $rec['fest']=substr($rec['fest'],0,$pos);
                
                foreach (['sun','fest','week'] AS $d)
                {
                    $rec[$d]=preg_replace('/([^0-9])([0-9])([0-9][0-9])([^0-9]*)/','\1\2:\3\4',$rec[$d]);
                    $rec[$d]=preg_replace('/([^0-9])([0-9][0-9])([0-9][0-9])([^0-9]*)/','\1\2:\3\4',$rec[$d]);
                
                }
                
                //"Marker":{"Lat":"54.0173537","Long":"17.2457661"
                $b=[];
                if (preg_match('~"Marker":{"Lat":"([^"]+)","Long":"([^"]+)"~',$html,$b))
                {
                    $rec['latlng']=$b[1].','.$b[2];
                }
                $adr=$rec['address'];
                $rec['address']=$rec['postal'].' '.$rec['city'];
                if ($adr) $rec['address'].=", $adr";
                
                echo '"'.implode('","',$rec).'"'."\n";
                //print_r($rec);
            }
        }

        
 
    
    }

  