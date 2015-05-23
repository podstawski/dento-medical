<?php

    //pudel.webkameleon.com/kiedymsza/rest/tools/gn.csv

    function url_get($url)
    {
        $cache=__DIR__.'/.cache/'.md5('PI:'.$url).'.html';

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
            
            //die($tag.'|'.$name);
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
    
    $match_array=['name'=>'wezwanie:',
                  'address'=>'adres:',
                  'www'=>'strona WWW:',
                  'email'=>'adres email:',
                  'phone'=>'telefon:',
                  'rector'=>'proboszcz',
                  'sun'=>'w niedziele:',
                  'week'=>'w dni powszednie:',
                  'fest'=>'święta zniesione:',
                  'url'=>'xxxxxx',
    ];
    
    echo '"'.implode('","',array_keys($match_array)).'"'."\n";
    
    
    $url='http://www.koszalin.opoka.org.pl/new/a.php?m=6&p=parafie';
    $html=url_get($url);
    $html=iconv('ISO-8859-2','UTF-8',$html);
    $html=substr($html,strpos($html,'Parafie w diecezji'));
    
    $p=[];
    preg_match_all('/<a href="p.php\?m=6&p=([0-9]+)">/',$html,$p);

    
    foreach ($p[1] AS $d)
    {
        $url="http://www.koszalin.opoka.org.pl/new/p.php?m=6&p=$d";
        $html=url_get($url);
        $html=iconv('ISO-8859-2','UTF-8',$html);
        $html=str_replace('&nbsp;',' ',$html);
        $html=substr($html,strpos($html,'Informacje o parafii'));
        
        $rec=[];
        foreach ($match_array AS $k=>$v) $rec[$k]=find_on_tag($html,$v);
        $rec['url']=$url;
        
        if ($rec['name']) $rec['name']='Kościół '.$rec['name'];
        $pos=strpos($rec['sun'],'w dni powszednie:');
        if ($pos) $rec['sun']=substr($rec['sun'],0,$pos);

        if (!$rec['name'] || !$rec['sun'] || !$rec['phone']) {
            //print_r($rec);
        }

        
        echo '"'.implode('","',$rec).'"'."\n";
    }
    
    die();
    
 