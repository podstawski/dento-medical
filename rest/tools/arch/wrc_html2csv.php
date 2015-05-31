<?php

    include __DIR__ .'/fun.php';

    
    $match_array=['name'=>'<p class="up">',
                  'postal'=>'xxxxxxxxx',
                  'city'=>'xxxxxxxxx',
                  'address'=>'xxxxxx',
                  'www'=>'<a href="http://',
                  'email'=>'<a href="mailto:',
                  'phone'=>'<br>tel:',
                  'rector'=>'Proboszcz</b>:</td><td width="20">&nbsp;',
                  'sun'=>'niedziele: ',
                  'week'=>'dni powszednie:',
                  'fest'=>'xxxxxxxxx',
                  'latlng'=>'<span class="small">GPS:',
                  'url'=>'xxxxxxxx',
    ];
    
    echo '"'.implode('","',array_keys($match_array)).'"'."\n";
    
    

    $url='http://www.archidiecezja.wroc.pl/index.php?option=com_content&view=article&id=40&Itemid=72';
    $html=url_get($url);

    
    $a=[];
    $rand=rand(1,100);
    if (preg_match_all('~<a href="(/parafia.php[^"]+)"~',$html,$a))
    {
        foreach ($a[1] AS $url)
        {

            $url=str_replace('&amp;','&',$url);
            //$url=str_replace('&nbsp;',' ',$url);
            $url='http://www.archidiecezja.wroc.pl'.$url;

            $html=url_get($url);
            $html=iconv('ISO-8859-2','UTF-8',$html);
            $html=substr($html,strpos($html,'<table width="100%"'));
            
            $rec=array();
            foreach ($match_array AS $k=>$v) $rec[$k]=find_on_tag($html,$v);
            $rec['url']=$url;            
            
            if (strstr($rec['week'],'roboszcz')) $rec['week']='';
            
            $rec['latlng']=str_replace(['N',' '],'',$rec['latlng']);
            $rec['latlng']=str_replace('E',',',$rec['latlng']);
            
            $html=substr($html,strpos($html,$rec['name'])+strlen($rec['name']));
            $html=substr($html,0,strpos($html,'tel:'));
            while ($pos=strpos($html,'<p align="left">')) $html=substr($html,$pos+strlen('<p align="left">'));
            $html=str_replace('&nbsp;',' ',$html);
            $html=preg_replace('/<br>$/','',$html);
            $html=explode('<br>',$html);
            if (strstr($html[2],$html[0])) unset($html[0]);
            
            $rec['address']=implode(', ',$html);
            
            
            $pos=strpos($rec['rector'],'- ust.');
            if($pos) $rec['rector']=substr($rec['rector'],0,$pos);
            
            $rec['email']=str_replace('(a)','@',$rec['email']);
            
            
            
            //if (++$lp==$rand) {print_r($rec); break;}
            
        
        
            
            
            echo '"'.implode('","',$rec).'"'."\n";
        }
    }
    //break;
        

    
   