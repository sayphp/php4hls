<?php
    /**
     * work.fucntion.php
     * 工作方法
     * say
     * 2017-09-22 14:47:00
     */

    function work(){
        $pid = posix_getpid();
        $task = new task(APP_TASK);
        $hls = new hls();
        $simple = new simple($hls->opt);
        $shm_key = $simple->key();
        var_dump($shm_key);
        do{
            //*检查当前共享内存是否存在
            $shm_id = shmop_open($shm_key, "a", 0644, 0);
            if ($shm_id) {
                $content = trim(shmop_read($shm_id, 0, shmop_size($shm_id)));
                $data = explode('|', $content);
                $key = array_search($pid, $data);
                if($key!==false){
                    $file = $task->get($key);
                    if($file){
                        var_dump($file);
                    }
                }else{
                    echo '挂了'.PHP_EOL;
                    exit();
                }
            }
            sleep(APP_INTERVAL);
        }while(APP_INTERVAL);
    }