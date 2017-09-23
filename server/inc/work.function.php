<?php
    /**
     * work.fucntion.php
     * 工作方法
     * say
     * 2017-09-22 14:47:00
     */

    function work(){
        sleep(rand(15,15));
        echo 'wahahahaha'.posix_getpid().PHP_EOL;
    }