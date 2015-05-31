<?php

    include __DIR__ .'/fun.php';

    
    $match_array=['name'=>'xxxxx',
                  'postal'=>'class="sobi2Listing_field_postcode">',
                  'city'=>'class="sobi2Listing_field_city">',
                  'address'=>'class="sobi2Listing_field_street">',
                  'www'=>'class="sobi2Listing_field_website"><a href="http://',
                  'email'=>'<a href="mailto:',
                  'phone'=>'Telefon:',
                  'rector'=>'xxxxxxxxx',
                  'sun'=>'niedziela:|niedziele:',
                  'week'=>'dni powszednie:',
                  'fest'=>'xxxxxxxxx',
                  'latlng'=>'new GLatLng(',
                  'url'=>'xxxxxxxx',
    ];
    
    echo '"'.implode('","',array_keys($match_array)).'"'."\n";
    
    $rand=rand(1,70);$lp=0;
    
    for ($page=0;$page<=70;$page+=10)
    {
        $url='http://diecezjaelk.pl/parafie.html?catid=2&start='.$page;
        $html=url_get($url);
        
        $a=[];
        if (preg_match_all('~<p class="sobi2ItemTitle"><a href="(http://diecezjaelk.pl/parafie.html[^"]+)"[^>]*>([^<]+)<[^>]+>(.*?)<table class=\'sobi2Listing_plugins\'>~si',$html,$a))
        {
            foreach ($a[1] AS $i=>$url)
            {
                $rec=array();
                $url=str_replace('&amp;','&',$url);
                
                foreach ($match_array AS $k=>$v) $rec[$k]=find_on_tag($a[3][$i],$v);
                $pos=strpos($a[2][$i],'-');
                $rec['name']=trim(substr($a[2][$i],$pos+1));
                //$rec['city']=trim(substr($a[2][$i],0,$pos));
                $rec['url']=$url;
                //$rec['about']=$a[3][$i];

                $html=url_get($url);
                $html=substr($html,strpos($html,'<h1>'));
                
                foreach ($match_array AS $k=>$v) $rec[$k]=$rec[$k]?:find_on_tag($html,$v);
                $b=[];
                if (!$rec['sun']) if (preg_match_all('~id="sobi2Details_field_msza[0-9]+">([0-9:.]+)</span>~',$html,$b)){
                    $rec['sun']=implode(', ',$b[1]);
                }
                
                if ($pos=strpos($rec['latlng'],')')) $rec['latlng']=substr($rec['latlng'],0,$pos);
                $rec['latlng']=str_replace(' ','',$rec['latlng']);
                
                $rec['address']=$rec['postal'].' '.$rec['city'].', '.$rec['address'];
                //if (++$lp==$rand)  {print_r($rec); break 2;}              
                
                echo '"'.implode('","',$rec).'"'."\n";
            }
        }
        //break;
        
    }
    
   