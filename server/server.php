<?php
    /**
     * server.php.
     * say
     * 2017-09-21 10:51:00
     */

    require 'inc/init.php';
    try{
        $server = new hls();
        $server->start();
    }catch(Exception $e){
        var_dump($e->getMessage());
    }
