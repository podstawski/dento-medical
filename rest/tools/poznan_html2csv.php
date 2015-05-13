<?php

    function url_get($url)
    {
        $cache=__DIR__.'/.cache/'.md5('PO:'.$url).'.html';

        if (file_exists($cache)) return file_get_contents($cache);
        $html=file_get_contents($url);
        $html=iconv('ISO-8859-2','UTF-8',$html);
        file_put_contents($cache,$html);
        return $html;
    }
    
    function find_on_tag($html,$tags)
    {
        $name='';
        foreach (explode('|',$tags) AS $tag) {
            $pos=strpos($html,$tag);
            if (!$pos) continue;
            $name=substr($html,$pos+strlen($tag));
            $end=strpos($name,'<');
            $endtag=strpos($name,'>');
            if ($endtag && $endtag<$end)
            {
                $name=substr($name,$endtag+1);
                $end=strpos($name,'<');
            }
            $name=trim(substr($name,0,$end));
            $name=str_replace("\t",' ',$name);
            $name=str_replace('"','',$name);
            break;
        }
        return $name;
    }

    $html=file_get_contents('poznan.html');
    
    $mathes='';
    
    preg_match_all('~task,view/contact_id,([0-9]+)/~',$html,$mathes);
    
    $match_array=['name'=>'<h2>',
                  'address'=>'<td width="100%"><b><font size="2"><b>|<td width="100%"><b><font size="2">',
                  'www'=>'www: <a href="',
                  'email'=>'e-mail: <a href="',
                  'phone'=>'<b>Tel.:</b>',
                  'rector'=>'<b>Proboszcz:</b>',
                  'sun'=>'<b>Msze św. w niedziele:</b>|<b>Msze św. w niedziele: </b>|<b>Msze św. niedzielne:</b>',
                  'week'=>'<b>Msze św. w dni powszednie:</b>',
                  'fest'=>'<b>Msze św. w święta i w dni pracy:</b>|<b>Msze św. w święta w dni pracy:</b>|<b>Msze św. w święta w dni pracy: </b>',
                  'url'=>'xxxxxx',
    ];
    
    
    if (isset($argv[1])) $mathes[1]=[$argv[1]];
    
    echo '"'.implode('","',array_keys($match_array)).'"'."\n";
    
    foreach ($mathes[1] AS $id) {
        $url='http://www.archpoznan.pl/index2.php?option=com_contact&task=view&contact_id='.$id;
        $html=url_get($url);
        
        $rec=[];
        foreach ($match_array AS $k=>$v) $rec[$k]=find_on_tag($html,$v);
        $rec['url']=$url;
        
        echo '"'.implode('","',$rec).'"'."\n";
    }