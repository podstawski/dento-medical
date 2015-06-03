<?php

    include __DIR__ .'/fun.php';

    
    $match_array=['name'=>', p.w.:|p.w.:',
                  'postal'=>'xxxxxxxxx',
                  'city'=>'xxxxxxxxx',
                  'address'=>'<img src="images/li.gif" border=0 height=5 width=5>',
                  'www'=>'<br> <a href="http://',
                  'email'=>'mailto:',
                  'phone'=>', tel.|tel.',
                  'rector'=>'Proboszcz:',
                  'sun'=>'niedziele i święta, godz.:|niedziele i święta:',
                  'week'=>'xxxx',
                  'fest'=>'xxxxxxxxx',
                  'latlng'=>'<iframe src="',
                  'url'=>'xxxxxxxx',
    ];
    
    echo '"'.implode('","',array_keys($match_array)).'"'."\n";
    
    

    $url='http://diecezja.zamojskolubaczowska.pl/parafie,alfa';
    $html=url_get($url);
    $html=iconv('iso-8859-2','UTF-8',$html);

    $html=fromtxt2txt($html,'<font color=RED>Alfabetyczny Spis parafii</font>','END SRODEK');
    
    $a=[];
    
    if (preg_match_all('~<a href="(parafia,[0-9]+)"[^>]*><b>([^<]+)</b>[^<]*<b>([^<]+)</b></a>~',$html,$a))
    {
        if (isset($argv[1])) $a[1]=[$argv[1]];
        
        $rand=rand(1,count($a[1]));
        $lp=0;
        
        foreach ($a[1] AS $i=>$url)
        {

            $url=str_replace('&amp;','&',$url);
            $url='http://diecezja.zamojskolubaczowska.pl/'.$url;
            $html=url_get($url);
            
            //echo "$url\n";continue;
            
            $html=iconv('iso-8859-2','UTF-8',$html);
            $html=fromtxt2txt($html,'<font color=#003399 face=CANDARA size=6><b>','END SRODEK');
            $html=str_replace('&nbsp;',' ',$html);
            $rec=array();
            
            //$html=preg_replace('~\s*<br/>\s*~',', ',$html);
            foreach ($match_array AS $k=>$v) $rec[$k]=find_on_tag($html,$v);
            $rec['name']=$a[3][$i];
            $rec['city']=$a[2][$i];
            $rec['phone']=trim($rec['phone']);
            if($rec['phone'] && $rec['phone'][0]=='0') $rec['phone']=substr($rec['phone'],1);
            
            $rec['url']=$url;
            
            
            //if (++$lp==$rand) {print_r($rec); break;}
            

            echo '"'.implode('","',$rec).'"'."\n";
        }
    }

        

    
   