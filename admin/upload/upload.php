<?php

function przecinek2strumien($data)
{
    static $counter;
    $counter++;
    $counter_debug=0;
    
    $data=','.$data.',';
    $last_data=$data;
    while(true)
    {
        
        $data=preg_replace('/,"([^"]*),([^"]*)",/',',"\\1ZJEBANY_PRZECINEK\\2",',$data);
        if ( $counter==$counter_debug) echo "<p>$last_data</p>";
        if ($last_data==$data) break;
        $last_data=$data;
    }

    $data=substr($data,1,strlen($data)-2);
    
    if ($counter==$counter_debug) mydie($data);

    $data=str_replace('"','',$data);
    $data=str_replace(',','|',$data);
    $data=str_replace('ZJEBANY_PRZECINEK',',',$data);

    return $data;
}

function find_latlng($postal,$address)
{

    $url='https://maps.google.com/maps/api/geocode/json?address='.urlencode($address).'&sensor=false&region=pl';
    $url.='&key='.Bootstrap::$main->getConfig('maps.server_key');

    $token='place:'.md5($url);
    $place=Tools::memcache($token);
    if (!$place)
    {
        echo '&nbsp; &nbsp;<a href="'.$url.'" target="_blank">'.$address.'</a><br/>';
        $place=json_decode(file_get_contents($url),true);
        if (isset($place['status']) && $place['status']=='OK') Tools::memcache($token,$place);
    }
    
    
    
    $result=['latlng'=>false];
    $result['addresses']=[];
    $result['address']=$address;
    $result['place']=$place;
    $result['url']=$url;
    
    if (!isset($place['results']) || !is_array($place['results']) || !count($place['results']) ) return $result;
    

    foreach ($place['results'] AS $res)
    {
        $a=[];
        $latlng=$res['geometry']['location']['lat'].','.$res['geometry']['location']['lng'];
        foreach( $res['address_components'] AS $compo)
        {
            $a[]=$compo['long_name'];
            if ($compo['types'][0]=='postal_code' && $compo['long_name']==$postal) {
                $result['latlng']=$latlng;
            }
        }
        $result['addresses'][$latlng]=implode('/',$a);
    }
    
    return $result;
}

function form($key,$id,$options)
{
    $result='<form method="GET">';
    $result.='<input type="hidden" name="id" value="'.$id.'"/>';
    $result.='<input type="hidden" name="key" value="'.$key.'"/>';
    $result.='<select name="latlng">';
    $result.='<option value="">choose</option>';
    foreach ($options AS $k=>$v) $result.='<option value="'.$k.'">'.$v.'</option>';
    $result.='</select>';
    $result.='<input type="text" placeholder="type yourself" name="latlng2"/>';
    $result.='<input type="submit" value="save"/>'; 
    $result.='</form>';
    return $result;
}

function analyze_mass($dows,$txt)
{
    $o=$txt;
    $txt=str_replace(',',';',$txt);
    $txt=str_replace(' i ',';',$txt);
    $txt=str_replace(' lub ',';',$txt);
    $txt=str_replace('godz.','',$txt);
    $txt=str_replace('godz','',$txt);
    
    
    $masses=explode(';',$txt);
    
    //$masses[]=$o;
    

    static $counter;
    //if (++$counter==40) die('starczy');
    
    $sun=$dows==[0];
    
    foreach ($masses AS $mass) {
        $time=[];
        if (!preg_match_all('/[0-9]+[.:][0-9][0-9]/',$mass,$time)) continue;
        
        if (is_array($time[0]) && count($time[0])==1) $time[0]=$time[0][0];
        
        $parenthesis=[];
        preg_match('/\([^\)]+\)/',$mass,$parenthesis);
        //mydie($parenthesis);
        
        if (isset($parenthesis[0])) echo "<b>$parenthesis[0]</b>: $mass<br/>";
        
        if (is_array($time[0]) && !isset($parenthesis[0])) mydie($time,"$mass <br/> $o");
        continue;
        
        if (is_array($time[0])) {
            mydie($time[0],$mass);
        } else {
            $mass=str_replace($time[0],'',$mass);
            $time=str_replace('.',':',$time[0]);
            $epoch_time=strtotime("1970-01-01 $time");
            mydie($mass.': '.$epoch_time.' = '.date('d-m-Y H:i',$epoch_time),$time);            
        }        

    }
    
    //echo '<pre>'.print_r($masses,2).'</pre>';
}