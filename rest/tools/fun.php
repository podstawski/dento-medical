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
        $closing_tags=['<','"'];
        if (is_array($tags) && count($tags)>1) {
            $closing_tags[]=$tags[1];
            $tags=$tags[0];
        }
        
        $tags=strtolower($tags);
        $html=preg_replace("/[\r\n\t ]+/",' ',$html);
        
        foreach (explode('|',$tags) AS $tag) {
            
            
            if (strlen($tag))
            {
                $pos=strpos(strtolower($html),$tag);
                
                if (!$pos) continue;
                $name=trim(substr($html,$pos+strlen($tag)));
            }
            else
            {
                $name=$html;
            }
            
            if (!strlen($name)) continue;
            
            
            
            $t=false;
            while ($t || $name[0]=='<' || $name[0]==' ' || ord($name[0])==194 || ord($name[0])==160)
            {
                if ($name[0]=='<') $t=true;
                if ($name[0]=='>') $t=false;
                $name=substr($name,1);
            }
            
            
            
            $res='';
            while (strlen($name) && !in_array($name[0],$closing_tags) ) {
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
        if ($a && $pos=strpos($html,$a)) $html=substr($html,$pos);
        if ($b && $pos=strpos($html,$b)) $html=substr($html,0,$pos);
        return $html;
    }
    
    function hours($html)
    {
        $html=preg_replace('~^([0-9]+)([ ,;$]+)~','\1:00\2',$html);
        $html=preg_replace('~([ ,;]+)([0-9]+)$~','\1\2:00',$html);        
    
        for($i=0;$i<8;$i++) $html=preg_replace('~([ ,;]+)([0-9]+)([ ,;]+)~','\1\2:00\3',$html);
        return $html;
    }

    
    function addy($html)
    {
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
            return $b[3];
        }        
    }
    
    
    function msze($html,$kiedy)
    {
        $m=[];
        preg_match_all('~<td>'.$kiedy.'</td>\s*<td>([^<]+)</td>\s*<td>(.+?)</td>~si',$html,$m);
        
        foreach ($m[1] AS $i=>$h)
        {
            $m[2][$i]=trim(str_replace(['wszystkich','<a href="#" class="hourInfoLink">( i )</a>','<span class="hourInfo">','</span>'],'',$m[2][$i]));
            $m[2][$i]=preg_replace('~\s+~',' ',$m[2][$i]);
            
            if ($m[2][$i]) $m[1][$i].=' ('.$m[2][$i].')';
        }
        
        return implode('; ',$m[1]);
        
                
    }