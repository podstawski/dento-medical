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