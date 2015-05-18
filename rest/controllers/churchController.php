<?php

class churchController extends Controller {
    public function get_search()
    {
        $opt=$this->nav_array(Bootstrap::$main->getConfig('church.search.limit'));
    
        $distance=Bootstrap::$main->getConfig('church.search.distance');
        $data=[];
    
        $geo=explode(',',$this->data('geo'));
        
        if (!isset($geo[1]))
        {
            $geo=Tools::geoip();
            $geo=@[$geo['location']['latitude'],$geo['location']['longitude']];
        }
        

        if ($geo[0]+0 && isset($geo[1]) && $geo[1]+0)
        {
            $church=new churchModel();
        
        
            $when=Bootstrap::$main->now;
            if ($this->data('date_submit')) $when=strtotime($this->data('date_submit'));
            
            
            $month=date('m',$when);
            $dow=date('w',$when);
            $dow_requested=$dow;
            $year=date('Y',$when);
        
            $now=$this->time2int($this->data('now'))?:$this->time2int(date('H:i'));
            $time=$this->data('date_submit')?0:$now;

            
            //earch($lat,$lng,$distance,$time,$m,$dow,$limit,$offset)
            
            $data=$church->search($geo[0],$geo[1],$distance,$time,$month,$dow,$now,$opt['limit'],$opt['offset'])?:[];
            if (!count($data) && $dow!=$dow_requested)
                $data=$church->search($geo[0],$geo[1],$distance,$time,$month,$dow_requested,$now,$opt['limit'],$opt['offset'])?:[];
            if (!count($data) && $dow==$dow_requested)
            {
            
                $data=$church->search($geo[0],$geo[1],$distance,$this->time2int('5:30'),$month,($dow+1)%7,$this->time2int('5:30'),$opt['limit'],$opt['offset'])?:[];
            }
            
            
            $this->clear_data($data,true);
        }
    
        return array('status'=>true,'options'=>$opt,'data'=>$data);
    }
    
    
    protected function clear_data(&$data,$geo=false)
    {
        foreach ($data AS &$rec)
        {
            $rec['name_url']=Tools::str_to_url($rec['name']);
            if (isset($rec['time'])) $rec['time']=$this->int2time($rec['time']);
            if (isset($rec['distance'])) $rec['distance']=round($rec['distance']);
            unset($rec['password']);
            unset($rec['md5hash']);
            if ($geo) {
                unset($rec['lat']);
                unset($rec['lng']);
            }
        }
    }
    
    protected function int2time($i)
    {
        return date('H:i',$i);
    }
    
    protected function time2int($time)
    {
        $t=explode(':',$time);
        if (strlen($t[0])==1) $t[0]='0'.$t[0];
        if (strlen($t[1])==1) $t[1]='0'.$t[1];
        $time=implode(':',$t);
        return strtotime("1970-01-01 $time");
    }
    

}