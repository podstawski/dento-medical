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

function get_time_from_txt($txt)
{
    $time=[];
    if (!preg_match_all('/[0-9]+[.:][0-9][0-9]/',$txt,$time)) return false;
    if (is_array($time[0])) return $time[0][0];
    return str_replace('.',':',$time[0]);
}

function time2int($time)
{
    return strtotime("1970-01-01 $time");
}

function change_params(&$dows,&$months,&$params,$txt)
{
    $txt=strtolower($txt);
    if (strstr($txt,'nie wakacje') || strstr($txt,'wakacje nie') )
        $months=[1,2,3,4,5,6,9,10,11,12];
    elseif (strstr($txt,'wakac'))
        $months=[7,8];
    elseif (strstr($txt,'młodzie') || strstr($txt,'akadem'))
        $params['youth']=1;
    elseif (strstr($txt,'dziec'))
        $params['kids']=1;
    elseif (strstr($txt,'sob'))
        $dows=[6];
    elseif (strstr($txt,'zim'))
        $months=[1,2,3,11,12];
    elseif (strstr($txt,'lat') || strstr($txt,'letn'))
        $months=[4,5,6,7,8,9,10];

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
    
    $result=[];
    
    foreach ($masses AS $mass) {
        
        $daysofweek=$dows;
        $months=[1,2,3,4,5,6,7,8,9,10,11,12];
        $params=[];
        
        $parenthesis=false;
        preg_match('/\([^\)]+\)/',$mass,$parenthesis);        
        $mass=preg_replace('/\([^\)]+\)/','',$mass);
        
        $parenthesis = isset($parenthesis[0])? $parenthesis[0]:'';
        
        $time=get_time_from_txt($mass);
        if (!$time) continue;
        
        change_params($daysofweek,$months,$params,$mass);
        
        if ($parenthesis) {
            $p_time=get_time_from_txt($parenthesis);
            
            if ($p_time) {
                $p_daysofweek=$daysofweek;
                $p_months=$months;
                change_params($p_daysofweek,$p_months,$params,$parenthesis);
                $result[]=['time'=>time2int($p_time),'dows'=>$p_daysofweek,'m'=>$p_months,'params'=>$params];
                    
                if ($p_months!=$months)
                {
                    $result[]=['time'=>time2int($time),'dows'=>$daysofweek,'m'=>array_diff($months,$p_months),'params'=>$params];
                }
            } else {
                change_params($daysofweek,$months,$params,$parenthesis);    
                $result[]=['time'=>time2int($time),'dows'=>$daysofweek,'m'=>$months,'params'=>$params];
            }
        } else {
            $result[]=['time'=>time2int($time),'dows'=>$daysofweek,'m'=>$months,'params'=>$params];    
        }
        
    }
    
    return $result;
}