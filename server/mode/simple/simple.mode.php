<?php
    /**
     * simple.mode.php
     * 基础类文件
     * say
     * 2017-09-21 18:06:00
     */
    class simple implements modeInterface{

        protected $opt;

        public function __construct($opt){
            $this->opt = $opt;
            $this->check();
            $this->init();
        }

        public function check(){
            if(!extension_loaded('pcntl')) error(101);
            if(!extension_loaded('posix')) error(102);
            ffmpeg::check();
        }

        public function run(){
            for($i=0;$i<APP_TASK;$i++){
                $pid = pcntl_fork();
                switch($pid){
                    case -1://失败
                        echo '创建子进程失败'.PHP_EOL;
                        exit();
                        break;
                    case 0://子进程
                        work();//执行任务 去了
                        debug('子进程执行完成');
                        exit();
                        break;
                    default://主进程
                        $id_key = $this->key();
                        $sem_id = sem_get($id_key);
                        if (sem_acquire($sem_id)) {
                            debug('=============信号捕获==========');
                            //*打开共享内存
                            $shm_id = $this->shm();
                            $content = shmop_read($shm_id, 0, $this->size());
                            debug('pid:'.$pid);
                            $data = explode('|', $content);
                            if (!$data[0]) $data[0] = posix_getpid();
                            foreach ($data as $k => $v){
                                $v = trim($v);
                                if(!$v){
                                    $data[$k] = strval($pid);
                                    break;
                                }
                            }
                            shmop_write($shm_id, implode('|', $data), 0);
                            debug(shmop_read($shm_id, 0, $this->size()));
                            shmop_close($shm_id);
                            sem_release($sem_id);
                            debug('=============信号释放==========');
                        }else{
                            debug('=============信号未捕获==========');
                        }
                }
            }
            $task = new task();//启动任务
            $task->init(APP_TASK);
            $i = 0;
            do {
                echo '主进程循环' . PHP_EOL;
                //*1.分发任务
                $file = $task->find();
                if($file){
                    //*接收任务
                    debug('接收任务:'.$file);
                    //*分发任务
                    $id = $i%APP_TASK;
                    $id = $id?$id:5;
                    //*投递任务
                    $task->set($id);
                    $i++;
                }
                //*2.子进程维护
                sleep($this->opt['interval']);
            } while ($this->opt['interval']);
        }

        public function key(){
            return ftok(__FILE__, 'z');
        }

        //初始化
        public function init(){
            $id_key = $this->key();
            $sem_id = sem_get($id_key);
            if (sem_acquire($sem_id)) {
                $shm_key = $this->key();
                //*检查当前共享内存是否存在
                $shm_id = shmop_open($shm_key, "w", 0644, 0);
                if ($shm_id) {
                    $content = shmop_read($shm_id, 0, shmop_size($shm_id));
                    if ($content != '') {//*杀死正在未完成的前子进程
                        $data = explode('|', $content);
                        foreach ($data as $k => $v) {
                            if (!$v) {
                                posix_kill($v, 0);
                            }
                        }
                    }
                    shmop_delete($shm_id);
                    shmop_close($shm_id);
                }
                //*初始化覆盖数据
                $shm_id = shmop_open($shm_key, "n", 0644, $this->size());
                shmop_write($shm_id, $this->data(), 0);
                debug(shmop_read($shm_id, 0, $this->size()));
                //*关闭共享内存
                shmop_close($shm_id);
                //*关闭信号量
                sem_release($sem_id);
                //*返回共享内存id
                echo '完成初始化' . PHP_EOL;
            }
        }

        public function shm(){
            $shm_key = $this->key();
            $shm_id = shmop_open($shm_key, "w", 0644, $this->size());
//            debug(shmop_size($shm_id));
            if(!$shm_id) error(112);
            return $shm_id;
        }

        //程序大小
        public function size(){
            return 128;
        }

        public function data(){
            $data[0] = 0;
            for($i=1;$i<=APP_TASK;$i++){
                $data[$i] = 0;
            }
            return implode('|', $data);
        }
    }