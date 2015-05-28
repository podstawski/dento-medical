<?php

    include __DIR__ .'/fun.php';

    
    $match_array=['name'=>'<h1 style="margin-top:20px;">',
                  'postal'=>'xxxxxxxxx',
                  'city'=>'xxxxxxxxx',
                  'address'=>'xxxxxxxxx',
                  'www'=>"target='_blank'>",
                  'email'=>'<a href="mailto:',
                  'phone'=>"<div class='icon-phone'><span class='num'>",
                  'rector'=>"Księża pracujący w parafii</div>",
                  'sun'=>'Niedziele i święta:',
                  'week'=>'Dni powszednie:',
                  'fest'=>'xxxxxxxxx',
                  'latlng'=>'xxxxxxx',
                  'url'=>'xxxxxxxx',
    ];
    
    echo '"'.implode('","',array_keys($match_array)).'"'."\n";
    
    
    for ($json=1;$json<25;$json++)
    {
        $data=json_decode(file_get_contents(__DIR__.'/.cache/'.$json.'.json'),true);
    
        foreach ($data['Miejscowosci'] AS $m)
        {
            foreach ($m['Parafie'] AS $p)
            {
                $url=$p['Url'];
                $html=url_get($url);
                $html=substr($html,strpos($html,'<div id="page-content">'));
                foreach ($match_array AS $k=>$v) $rec[$k]=find_on_tag($html,$v);
                $rec['url']=$url;
                
                $rec['latlng']=$p['SzerokoscGeograficzna'].','.$p['DlugoscGeograficzna'];
                
                $a=[];
                if (preg_match('~<div id="dane-kontaktowe">\s*<div><p><strong>[^<]*</strong><br />([^<]*)<br />([^<]*)~',$html,$a))
                {
                   $rec['address']=$a[2].', '.$a[1];
                }
                //print_r($rec); break 3;
                echo '"'.implode('","',$rec).'"'."\n";
            }
        }
    }


  
        

    
   