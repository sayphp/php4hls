<?php
    /**
     * function.php
     * 常用方法
     * say
     * 2017-09-21 14:44:00
     */
    function error($code=999, $msg=false){
        if(!$msg) $msg = isset(code::$code[$code])?code::$code[$code]:'未知错误';
        throw new Exception($msg, $code);
    }