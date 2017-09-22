<?php
    /**
     * init.php
     * 初始化
     * say
     * 2017-09-21 14:43:00
     */

    define('ROOT', dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR);//根目录
    require ROOT.'conf/server.conf.php';
    require INC.'function.php';
    $dirs = [
        CONF."*.conf.php",
        INC."*.function.php",
        CLS."*.cls.php",
        INTF."*.interface.php",
        MODE."*/*.php",
        CORE."*/*.php",
        PACK."*/*.php",
    ];
    $files = glob("{".implode(',', $dirs)."}", GLOB_BRACE);
    foreach($files as $file){
        if($file==CONF.'server.conf.php') continue;
        require $file;
    }