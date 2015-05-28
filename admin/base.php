<?php

    include __DIR__.'/../rest/library/backend/include/all.php';

    autoload([__DIR__.'/../rest/classes',__DIR__.'/../rest/models',__DIR__.'/../rest/controllers']);
    $config=json_config(__DIR__.'/../rest/configs/application.json');

    if (isset($_SERVER['SERVER_ADDR']) && $_SERVER['SERVER_ADDR']==$_SERVER['REMOTE_ADDR'])
    {
        $config['db.dsn']=$config['db.dsn2'];
        $config['db.user']=$config['db.user2'];
        $config['db.pass']=$config['db.pass2'];

    }

    
    $bootstrap = new Bootstrap($config);
