<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title><?php echo $title?:'Admin panel';?></title>
  
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">

    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>

    <!-- fonts -->
    <link href='//fonts.googleapis.com/css?family=Ubuntu' rel='stylesheet' type='text/css'>
    <!-- /fonts -->
    
    <!-- style -->
    <style>
        <?php include __DIR__.'/style.css'; ?>      
    </style>
  
  
</head>
<body>

<?php

    $church=new churchModel();
    $image=new imageModel();
    
    $images0=$image->count(['active'=>null]);
    $pending=0;
    $pendingpath=Tools::saveRoot('church-pending');
    foreach (scandir($pendingpath) AS $f)
    {
        if ($f[0]=='.') continue;
	$pending++;
    }
    
    echo '<div class="menu"><ul>';
    
    echo '<li><a href="'.dirname($_SERVER['SCRIPT_NAME']).'">'.$church->count().'<span class="hidden-xs"> churches</span></a></li>';
    
    if (!isset($menu)) $menu='';
    foreach (scandir(dirname(__FILE__)) AS $d)
    {
        
            if ($d[0]=='.') continue;
            if (!is_dir(__DIR__.'/'.$d)) continue;
	
	    $txt=$d;
	    if ($d=='images') $txt.=" $images0";
	    if ($d=='pending') $txt.=" $pending";
            
	    $class=$d==$menu?'active':'';
	    if (strstr($txt,'logro') || strstr($txt,'upload') || strstr($txt,'migrate') || strstr($txt,'import')) $class.=' hidden-xs';
            echo '<li class="'.$class.'"><a href="'.dirname($_SERVER['SCRIPT_NAME']).'/'.$d.'/">'.$txt.'</a></li>';
    }
    
    echo "</ul></div>";
    echo '<div class="body">';
   
