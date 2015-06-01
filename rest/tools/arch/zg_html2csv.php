<?php

    include __DIR__ .'/fun.php';

    
    $match_array=['name'=>'xxxxxxxx',
                  'postal'=>'xxxxxxxxx',
                  'city'=>'xxxxxxxxx',
                  'address'=>'Adres:|<h2>Dane teleadresowe</h2> <ul> <li>',
                  'www'=>'<a href="http://',
                  'email'=>'<a href="mailto:',
                  'phone'=>'tel.:|tel.|tel:',
                  'rector'=>'Proboszcz:',
                  'sun'=>'Msze św. w niedziele:',
                  'week'=>'Msza św. w tygodniu:',
                  'fest'=>'xxxxxxxxx',
                  'latlng'=>'xxxxxxx',
                  'url'=>'xxxxxxxx',
    ];
    
    echo '"'.implode('","',array_keys($match_array)).'"'."\n";
    
    

    $url='http://www.kuria.zg.pl/index.php/diecezja/dekanaty/parafie';
    $html=url_get($url);

    
    $a=[];
    
    if (preg_match_all('~<a href="(/index.php/diecezja/dekanaty/parafie/item[^"]+)">([^<]+)<~',$html,$a))
    {
        $rand=rand(1,count($a[1]));
        $lp=0;
        
        foreach ($a[1] AS $i=>$url)
        {

            $url=str_replace('&amp;','&',$url);
            //$url=str_replace('&nbsp;',' ',$url);
            $url='http://www.kuria.zg.pl'.$url;

            $html=url_get($url);
            //echo "$url\n";
            //$html=iconv('ISO-8859-2','UTF-8',$html);
            
            $html=fromtxt2txt($html,'<h3>Dane teleadresowe</h3>','<span>Dział:</span>');
            $html=str_replace('<br />',', ',$html);
            $rec=array();
            
            foreach ($match_array AS $k=>$v) $rec[$k]=find_on_tag($html,$v);
            $rec['url']=$url;            
            $name=explode('-',$a[2][$i]);
            
            if (!isset($name[1])) continue;
            $rec['name']=trim($name[1]);
            $rec['city']=trim($name[0]);
            
            if ($pos=strpos($rec['rector'],'(')) $rec['rector']=substr($rec['rector'],0,$pos);
           
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
                
                if ($rec['email']=='kuria@kuria.zg.pl') $rec['email']='';
            }
            
            if (!preg_match('~[0-9][0-9]-[0-9][0-9][0-9]~',$rec['address']))
            {
                $c=[];
                if (preg_match('~<ul>.*<li>([^<]*[0-9][0-9]-[0-9][0-9][0-9][^<]*)</li>.*</ul>~','',$c))
                {
                    $rec['address']=$c[1];
                }
            }
            
            //if (++$lp==$rand) {print_r($rec); break;}
            
        
        
            
            
            echo '"'.implode('","',$rec).'"'."\n";
        }
    }

        

    
   