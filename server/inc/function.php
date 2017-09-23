<?php
    /**
     * function.php
     * 常用方法
     * say
     * 2017-09-21 14:44:00
     */

    //*错误
    function error($code=999, $msg=false){
        if(!$msg) $msg = isset(code::$code[$code])?code::$code[$code]:'未知错误';
        throw new Exception($msg, $code);
    }

    //*debug
    function debug($content){
        if(APP_DEBUG){
            echo '['.date('Y-m-d H:i:s').']'.PHP_EOL;
            var_dump($content);
            if(APP_DEBUG==2){
                error_log(var_export($content, 1));
            }
        }
    }