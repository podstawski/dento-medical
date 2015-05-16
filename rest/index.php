<?php
    $_SERVER['backend_start']=microtime(true);
    include __DIR__.'/library/backend/include/all.php';
    
    
    allow_origin('webkameleon.com');
    autoload([__DIR__.'/classes',__DIR__.'/models',__DIR__.'/controllers']);
    $config=json_config(__DIR__.'/configs/application.json');
    $method=http_method();
    
    
    
    $bootstrap = new Bootstrap($config);
    register_shutdown_function(function () {
        @Bootstrap::$main->closeConn();   
    });
    
    $bootstrap->run(strtolower($method));
    
    
