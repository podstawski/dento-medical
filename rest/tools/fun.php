<?php

    function url_get($url)
    {
        $cache=__DIR__.'/.cache/'.md5('PO:'.$url).'.html';

        if (file_exists($cache) && filesize($cache)>1) return file_get_contents($cache);
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
            
            $res='';
            while (strlen($name) && $name[0]!='<' && $name[0]!='"') {
                $res.=$name[0];
                $name=substr($name,1);
            }
            /*
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
 
            */
            return $res;
            break;
        }
        return $name;
    }
    
    function fromtxt2txt($html,$a,$b)
    {
        if ($pos=strpos($html,$a)) $html=substr($html,$pos);
        if ($pos=strpos($html,$b)) $html=substr($html,0,$pos);
        return $html;
    }
