<?php
    include __DIR__.'/../base.php';
    include __DIR__.'/../head.php';
    
    include __DIR__.'/../../rest/library/backend/include/migrate.php';
    
    backend_migrate(__DIR__.'/../../rest/configs/application.json',__DIR__.'/../../rest/migrate/classes');
    
    include __DIR__.'/../foot.php';
    