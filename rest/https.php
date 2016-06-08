<?php

if (isset($_SERVER['SERVER_SOFTWARE']) && strstr(strtolower($_SERVER['SERVER_SOFTWARE']),'engine'))
        if (!strstr($_SERVER['HTTP_HOST'],'beta') && !strstr($_SERVER['HTTP_HOST'],'piotr')) {
                if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS']!='on') {
                        header("HTTP/1.1 301 Moved Permanently");
                        Header('Location: https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
                        die();
                }
        }

