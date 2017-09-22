<?php
    /**
     * server.conf.php
     * 服务框架配置类
     * say
     * 2017-09-21 14:50:00
     */

    defined('ROOT') or die('请于初始化文件(/server/inc/init.php)配置根目录地址(ROOT)');
    define('INC', ROOT.'inc'.DIRECTORY_SEPARATOR);//公共目录
    define('CLS', ROOT.'cls'.DIRECTORY_SEPARATOR);//框架目录
    define('CONF', ROOT.'conf'.DIRECTORY_SEPARATOR);//配置目录
    define('CORE', ROOT.'core'.DIRECTORY_SEPARATOR);//核心组件目录
    define('MODE', ROOT.'mode'.DIRECTORY_SEPARATOR);//模式组件目录
    define('PACK', ROOT.'pack'.DIRECTORY_SEPARATOR);//其他组件目录
    define('TARG', ROOT.'target'.DIRECTORY_SEPARATOR);//目标文件目录
    define('DATA', ROOT.'data'.DIRECTORY_SEPARATOR);//生成文件目录
    define('INTF', ROOT.'interface'.DIRECTORY_SEPARATOR);//接口目录
