<?php

    include __DIR__ .'/fun.php';

    
    $match_array=['name'=>'<strong style="line-height: 1.3em;">|<p><strong>',
                  'address'=>'</strong></p>',
                  'www'=>'<p><a href="',
                  'email'=>'xxxxxxx',
                  'phone'=>'<p>tel.',
                  'rector'=>'proboszcz: <strong>',
                  'sun'=>'<p>w niedzielę:',
                  'week'=>'<p>w dni powszednie:',
                  'fest'=>'<p>w święta zniesione:',
                  'url'=>'xxxxxx',
    ];
    
    echo '"'.implode('","',array_keys($match_array)).'"'."\n";
    
    $url='http://www.diecezja.bydgoszcz.pl/index.php/spis-parafii';
    $html=url_get($url);
    $a=[];
    preg_match_all('~<td>[^<]*<a href="(/index.php/spis-parafii/[^"]+)">~',$html,$a);
    foreach ($a[1] AS $url)
    {
        
        $url="http://www.diecezja.bydgoszcz.pl$url";
        $html=url_get($url);
        
        $html=substr($html,strpos($html,'<table class="contentpaneopen">'));
        

        $rec=array();
        foreach ($match_array AS $k=>$v) $rec[$k]=find_on_tag($html,$v);        
        $rec['url']=$url;

        $b=[];
        if (preg_match('~var addy[0-9]+ = ([^\n]+)\n\s*addy[0-9]+ = addy[0-9]+ \+ ([^\n]+)\n~',$html,$b))
        {
            for($i=32;$i<128;$i++)
            {
                $b[1]=str_replace("&#$i;",chr($i),$b[1]);
                $b[2]=str_replace("&#$i;",chr($i),$b[2]);
            }
            
            $b[3]=$b[1].$b[2];
            $b[3]=str_replace([';',' ','+',"'"],'',$b[3]);
            $rec['email']=$b[3];
            

        }
        
        
        echo '"'.implode('","',$rec).'"'."\n";
        continue;
        
        
        $c=[];
        preg_match("~href=\"$url\"[^>]*>[ \r\n\t]*<span class=\"x1\" >([^<]+)</span>([^<]+)</a>~",$html,$c);
        
        $rec['city']=trim($c[1]);
        if (!$rec['city']) continue;
        
        $name2findmap=trim($c[1]).' '.trim($c[2]);
        
        if (strstr($name2findmap,'filia')) $rec['name']=$name2findmap;
        else $rec['name']=trim($c[2]);
        
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
        
        $rec['url']=$url;

        
        $b=[];
        preg_match('~href="(http://szczecin.kuria.pl/wspolnoty/wspolnoty-parafialne/[^"]+)"~',$html,$b);
        $adres=url_get($b[1]);
        
        
        $r=[];
        if(preg_match('~href="http://szczecin.kuria.pl/wspolnoty/duszpasterze[^>]+[^<]*<span class="x1" >[^<]+<br></span>([^<]+)</a>~',$adres,$r))
        {
            $rec['rector']=$r[1];
        }
        $pos=strpos($adres,'<div class="dane_parafii"');
        $adres=substr($adres,$pos);
        $adres=substr($adres,0,strpos($adres,'</div>'));
        
        
        $rec['email']=find_on_tag($adres,'mailto:');
        $rec['www']=find_on_tag($adres,'href="http://');
        
        $adres=substr($adres,0,strpos($adres,'Gmina:'));
        $a=explode('<br',$adres);
        
        foreach ($a AS $i=>&$aa)
        {
            if (substr($aa,0,1)=='/') $aa=substr($aa,1);
            if (substr($aa,0,1)=='>') $aa=substr($aa,1);
            $aa=preg_replace('/<[^>]*>/','',$aa);
            $aa=trim($aa);
            if (!$aa) continue;
            
            $phone=preg_replace('/[^0-9]/','',$aa);
            if (strlen($phone)>8){
                if (strlen($rec['phone'])) $rec['phone'].=', ';
                $aa=trim(preg_replace('/tel[.]*/i','',$aa));
                $rec['phone'].=$aa;
            } else {
                if (strlen($rec['address'])) $rec['address'].=', ';
                $rec['address'].=$aa;
            }
            
        }
        //print_r($a); break;
        
        
        echo '"'.implode('","',$rec).'"'."\n";
        //print_r($rec);break;
    }

    
    
  