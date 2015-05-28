<?php

    include __DIR__ .'/fun.php';

    
    $match_array=['name'=>'',
                  'postal'=>'xxxxxxxxx',
                  'city'=>'xxxxxxxxx',
                  'address'=>'itemprop="headline" >Kontakt</div>',
                  'www'=>'xxxxxx',
                  'email'=>'<a href="mailto:',
                  'phone'=>'<td class="col-1">tel. +48|<td class="col-1">tel.',
                  'rector'=>'xxxxxxxxx',
                  'sun'=>'<td class="col-1"><strong>Niedziele i święta:',
                  'week'=>'<td class="col-1"><strong>Dni powszednie:',
                  'fest'=>'xxxxxxxxx',
                  'latlng'=>'xxxxxxx',
                  'url'=>'xxxxxxxx',
    ];
    
    echo '"'.implode('","',array_keys($match_array)).'"'."\n";
    
    
    for ($page=1;$page<=21;$page++)
    {
        $url=$page==1?'http://archidiecezja.warszawa.pl/parafie/':'http://archidiecezja.warszawa.pl/parafie/strona/'.$page.'/';
        $html=url_get($url);
        
        $a=[];
        if (preg_match_all('~<article .*?</article>~si',$html,$a))
        {
            foreach ($a[0] AS $art)
            {
                $rec=array();
                foreach ($match_array AS $k=>$v) $rec[$k]=find_on_tag($art,$v);
                
                $w=[];
                if (preg_match('~<td class="col-2"><a href="http://([^"]*)"~',$art,$w))
                {
                    $rec['www']=$w[1];   
                }
                if (preg_match('~<a href=\'(http://archidiecezja.warszawa.pl/parafie/[^\']+)\'~',$art,$w))
                {
                    $rec['url']=$w[1];   
                }
                
                $name=explode(' ',$rec['name']);
                if (substr($name[0],-1)=='.') $name[0]=substr($name[0],0,strlen($name[0])-1);
                if (strlen($name[0])>3 && strstr($rec['address'],$name[0])) unset($name[0]);
                $rec['name']=implode(' ',$name);
                
                $latlng=url_get($rec['url']);
                
                if (preg_match("/av_google_map\['0'\]\['marker'\]\['0'\]\['lat'\] =([0-9. ]+);/",$latlng,$w))
                {
                    $rec['latlng']=trim($w[1]).',';

                }
                if (preg_match("/av_google_map\['0'\]\['marker'\]\['0'\]\['long'\] =([0-9. ]+);/",$latlng,$w))
                {
                    $rec['latlng'].=trim($w[1]);

                }
                
                if (strstr($rec['email'],'<')) $rec['email']='';
                if (strstr($rec['www'],'<')) $rec['www']='';
                
                
                
                echo '"'.implode('","',$rec).'"'."\n";
            }
        }
        //break;
        
    }
    
   