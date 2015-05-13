<?php

class Bootstrap extends Bootstrapbase {
    public static $main;

    public function __construct($config)
    {
        parent::__construct($config);
        self::$main=parent::$main;
    }
}