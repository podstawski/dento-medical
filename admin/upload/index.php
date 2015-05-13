<?php
    include __DIR__.'/../base.php';
    include __DIR__.'/../head.php';
    
    function przecinek2strumien($data)
    {
        $last_data=$data;
        while(true)
        {
            $data=preg_replace('/,"([^"]+),([^"]+)",*/',',"\\1ZJEBANY_PRZECINEK\\2",',$data);
            if ($last_data==$data) break;
            $last_data=$data;
        }


        $data=str_replace('"','',$data);
        $data=str_replace(',','|',$data);
        $data=str_replace('ZJEBANY_PRZECINEK',',',$data);

        return $data;
    }
    
    
    $key=isset($_GET['key'])?$_GET['key']:'';
    if ($key) {
        if (substr($key,0,8)=='https://')
        {
            $key=str_replace('?','/',$key);
            $k='';
            foreach(explode('/',$key) AS $p) if (strlen($p)>strlen($k)) $k=$p;
            $key=$k;
        }
        
        $url='https://docs.google.com/spreadsheet/ccc?key='.$key.'&output=csv';
        $csv=file_get_contents($url);
        
        $data=explode("\n",$csv);
        $header=explode("|",przecinek2strumien($data[0]));

        for($i=1;$i<count($data);$i++)
        {
            $line=explode("|",przecinek2strumien($data[$i]));
        }
        
        
        mydie($csv);
    }
    
?>

<form method="get">
    <input type="text" value="<?php echo $key?>" name="key" placeholder="spreadsheet key"/><input type="submit" value="go!" />
</form>

<?php
    include __DIR__.'/../foot.php';
    