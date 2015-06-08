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
    $d2=[];
    $txt=strtolower($txt);
    if (strstr($txt,'nie wakacje') || strstr($txt,'wakacje nie') )
        $months=[1,2,3,4,5,6,9,10,11,12];
    elseif (strstr($txt,'wakac'))
        $months=[7,8];
        
    if (strstr($txt,'młodzie') || strstr($txt,'akadem'))
        $params['youth']=1;
    if (strstr($txt,'dziec'))
        $params['kids']=1;
    if (strstr($txt,'sob'))
        $d2[]=6;
    elseif (strstr($txt,'sobota'))
        $d2[]=6;
    if (strstr($txt,'poniedz'))
        $d2[]=1;
    if (strstr($txt,'wtore'))
        $d2[]=2;
    if (strstr($txt,'roda'))
        $d2[]=3;
    if (strstr($txt,'czwart'))
        $d2[]=4;
    if (strstr($txt,'tek'))
        $d2[]=5;    
    if (strstr($txt,'zim'))
        $months=[1,2,3,11,12];
    if (strstr($txt,'lat') || strstr($txt,'letn'))
        $months=[4,5,6,7,8,9,10];
    if (strstr($txt,'pn.'))
        $d2[]=1;
    if (strstr($txt,'wt.'))
        $d2[]=2;
    if (strstr($txt,'Śr.') || strstr($txt,'śr.'))
        $d2[]=3;
    if (strstr($txt,'cz.') || strstr($txt,'czw.'))
        $d2[]=4;
    if (strstr($txt,'pt.'))
        $d2[]=5;
    if (strstr($txt,'so.'))
        $d2[]=6;

        
    if (strstr($txt,'suma'))
        $params['description']='Suma';

    if (strstr($txt,'nabo'))
        $params['description']='Nabożeństwo';
        
    if (count($d2)) $dows=$d2;
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
    $last_time=0;
    
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
        
        if ($sun && $time<$last_time) $daysofweek=[0];
        
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
        
        $last_time=$time;
    }
    
    return $result;
}

function add_masses($church,$masses,$id=null)
{
    if ($id) $church->get($id);
    $church->remove_masses();
    $mass_cache=[];
    $mass=new massModel();

    foreach ($masses AS $m)
    {
        $rec=['church'=>$church->id,'time'=>$m['time']];
        foreach ($m['params'] AS $param=>$v) $rec[$param]=$v;
        
        foreach ($m['dows'] AS $dow)
        {
            foreach($m['m'] AS $moy) {
                $rec['dow']=$dow;
                $rec['moy']=$moy;
                
                
                $mass_token=$m['time'].'-'.$dow.'-'.$moy;
                if (isset($mass_cache[$mass_token])) continue;
                $mass_cache[$mass_token]=true;
                
                $mass->load($rec,true);
                $mass->save();
            }
        }
    }
    
}

function deduplicate_masses($masses) {
    $m=explode(';',$masses);
    $dup=[];
    
    foreach ($m AS $i=>$mass)
    {
        $m[$i]=$mass=trim($mass);
        if (isset($dup[$mass])) unset($m[$i]);
        $dup[$mass]=1;
        
    }
    return implode('; ',$m);
}