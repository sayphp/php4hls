<?php
    /**
     * test.php.
     * say
     * 2017-09-23 18:05:00
     */
    $file = __FILE__;
    $x = 65;
    for($i=0;$i<16;$i++){
        $c = chr($x+$i);
        var_dump(ftok($file, $c));
    }