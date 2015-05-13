<html>
<head>
    <meta charset="utf-8">
    <title><?php echo $title?:'Admin panel';?></title>
  
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">

    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>

    <!-- fonts -->
    <link href='https://fonts.googleapis.com/css?family=Roboto+Condensed:400,700,300&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
    <!-- /fonts -->
    
    <!-- style -->
    <style>
        <?php include __DIR__.'/style.css'; ?>      
    </style>
  
  
</head>
<body>

<?php

    echo '<div class="menu"><ul>';
    
    if (!isset($menu)) $menu='';
    foreach (scandir(dirname(__FILE__)) AS $d)
    {
        
            if ($d[0]=='.') continue;
            if (!is_dir(__DIR__.'/'.$d)) continue;
	
	    $txt=$d;
            
	    $class=$d==$menu?'active':'';
            echo '<li class="'.$class.'"><a href="'.dirname($_SERVER['SCRIPT_NAME']).'/'.$d.'/">'.$txt.'</a></li>';
    }
    
    echo "</ul></div>";
    echo '<div class="body">';
   
