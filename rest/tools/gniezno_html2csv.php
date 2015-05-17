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

        $tags=strtolower($tags);
        $html=preg_replace("/[\r\n\t ]+/",' ',$html);
        foreach (explode('|',$tags) AS $tag) {
            $pos=strpos(strtolower($html),$tag);
            if (!$pos) continue;
            $name=substr($html,$pos+strlen($tag));
            
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
            $name=trim(substr($name,0,$end));
            $name=str_replace("\t",' ',$name);
            $name=str_replace('"','',$name);
            break;
        }
        return $name;
    }
    
    $match_array=['name'=>'<span class="bold1a"><strong>|<span class="bold1a"><strong>|<p style="text-align: justify;"><strong>|<p style="text-align: justify;">|<p><strong>|<strong><span class="bold1a">|<p align="left"><strong>',
                  'address'=>'<td width="100%"><b><font size="2"><b>|<td width="100%"><b><font size="2">',
                  'www'=>'www: <a href="',
                  'email'=>'e-mail: <a href="',
                  'phone'=>'<b>Tel.:</b>',
                  'rector'=>'Proboszcz|Administrator|proboszcz',
                  'sun'=>'iedziele:|niedziele i  święta:|niedziele i uroczystości:|niedziela:|niedziele i  święta:|niedziele i&nbsp; święta:',
                  'week'=>'dni powszednie:',
                  'fest'=>'święta zniesione:',
                  'url'=>'xxxxxx',
    ];
    
    echo '"'.implode('","',array_keys($match_array)).'"'."\n";
    
    foreach (['b','c','d','g','i','j','k','l','l1','m','n','o','p','r','s','s1','t','u','w','z','z1'] AS $litera)
    {
        $html=url_get('http://www.archidiecezja.pl/parafie/'.$litera.'.html');
        $pos=strpos($html,'<div id="show-list">');
        $html=substr($html,$pos);
        $pos=strpos($html,'</div>');
        $html=substr($html,0,$pos);
        $a=[];
        
        preg_match_all('/href="([^"]+)"/',$html,$a);
        foreach ($a[1] AS $url)
        {
            
            $html=url_get($url);
            $pos=strpos($html,'<div class="catalogue_desc">');
            if (!$pos) {
                echo "Problem catalogue_desc: $url\n";
                continue;
            }
            $html=substr($html,$pos);
            
            $rec=[];
            foreach ($match_array AS $k=>$v) $rec[$k]=find_on_tag($html,$v);
            $rec['url']=$url;
            
            if (!$rec['name']) continue;
            
            if (!$rec['sun']) echo "Brak mszy: $url\n";
            //echo '"'.implode('","',$rec).'"'."\n";
            
           

        }
    
    }

  