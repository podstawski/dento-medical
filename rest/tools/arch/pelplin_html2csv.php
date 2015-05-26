<?php

    //pudel.webkameleon.com/kiedymsza/rest/tools/gn.csv

    function url_get($url)
    {
        $cache=__DIR__.'/.cache/'.md5('PO:'.$url).'.html';

        if (file_exists($cache)) return file_get_contents($cache);
        $html=file_get_contents($url);
        //$html=iconv('ISO-8859-2','UTF-8',$html);
        file_put_contents($cache,$html);
        return $html;
    }
    
    function find_on_tag($html,$tags)
    {
        $name='';

        $tags=strtolower($tags);
        $html=preg_replace("/[\r\n\t ]+/",' ',$html);
        foreach (explode('|',$tags) AS $tag) {
            if (strlen($tag))
            {
                $pos=strpos(strtolower($html),$tag);
                if (!$pos) continue;
                $name=substr($html,$pos+strlen($tag));
            }
            else
            {
                $name=$html;
            }
            
            if (!strlen($name)) continue;
            
            $t=false;
            while ($t || $name[0]=='<' || $name[0]==' ')
            {
                if ($name[0]=='<') $t=true;
                if ($name[0]=='>') $t=false;
                $name=substr($name,1);
            }            
            
            
            $end=strpos($name,'<');
            $endtag=strpos($name,'>');
            if ($endtag && $endtag<$end)
            {
                $name=substr($name,$endtag+1);
                $end=strpos($name,'<');
            }
            if ($end) $name=trim(substr($name,0,$end));
            $name=str_replace("\t",' ',$name);
            $name=str_replace('"','',$name);
            break;
        }
        return $name;
    }
    
    $match_array=['name'=>'<h1 class="SPTitle">',
                  'postal'=>'<strong>Kod pocztowy: </strong>',
                  'city'=>'<strong>Miejscowość: </strong>',
                  'address'=>'<strong>Ul. : </strong>',
                  'www'=>'xxxxxxx',
                  'email'=>'xxxxxxx',
                  'phone'=>'<strong>Telefon: </strong>',
                  'rector'=>'<strong>Proboszcz: </strong>',
                  'sun'=>'niedziele i święta:',
                  'week'=>'dni powszednie:',
                  'fest'=>'święta „zniesione”:',
                  'latlng'=>'xxxxxxx',
                  'url'=>'xxxxxx',
    ];
    
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

  