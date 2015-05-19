<?php

        $part = substr($_SERVER['REQUEST_URI'], 1+strlen(dirname($_SERVER['SCRIPT_NAME'])));
        if ($pos = strpos($part, '?')) $part = substr($part, 0, $pos);
        $part=preg_replace('~/+~','/',$part);
        $parts = explode('/', $part);

	if ($parts[0] && file_exists(__DIR__.'/'.$parts[0].'/index.php'))
	{
		die(include(__DIR__.'/'.$parts[0].'/index.php'));
	}

	$admin_path=$_SERVER['REQUEST_URI'];
	if ($pos = strpos($admin_path, '?')) $admin_path = substr($admin_path, 0, $pos);
	if ($admin_path[strlen($admin_path)-1]!='/') $admin_path.='/';
	
	include __DIR__.'/base.php';
	
	
	$title='Admin';
	$menu='';
	include __DIR__.'/head.php';
	
	
	include __DIR__.'/foot.php';



