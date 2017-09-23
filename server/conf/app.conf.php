<?php
    /**
     * app.conf.php
     * 应用配置文件
     * say
     * 2017-09-21 14:49:00
     */

    //*可采用默认配置
    define('APP_INTERVAL', 1);//时间间隔(ms)，为0时，执行完任务即结束脚本
    define('APP_TASK', 5);//任务数，smiple模式下为分裂最大的子进程数，swoole模式下为work_num

    //*重要配置
    define('APP_MODE', 'simple');//模式，写入模式类名称
    define('APP_FILE', 'file');//文件处理方式

    //*其他配置
    define('APP_DEBUG', 1);//0.不输出debug信息，1.输出debug信息，2.记录errorlog


