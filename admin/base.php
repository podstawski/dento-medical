<?php

    include __DIR__.'/../rest/library/backend/include/all.php';

    autoload([__DIR__.'/../rest/classes',__DIR__.'/../rest/models',__DIR__.'/../rest/controllers']);
    $config=json_config(__DIR__.'/../rest/configs/application.json');


    
    $bootstrap = new Bootstrap($config);
