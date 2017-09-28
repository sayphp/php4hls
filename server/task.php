<?php
    /**
     * task.php.
     * say
     * 2017-09-21 15:03:00
     */
    require 'inc/init.php';
    $task = new task(APP_TASK);
    $file = '/var/www/php4hls/test/123.avi';
    $task->sub($file);
//    $task->clean();