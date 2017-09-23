<?php
    /**
     * task.cls.php
     * 任务类
     * say
     * 2017-09-21 19:45:00
     */
    class task{

        public $pathname = '/php4hls/task';

        public $num;

        public function __construct($num=5){
            $this->num = $num;//任务数
        }

        //发布任务
        public function sub($file){
            $pathname = $this->pathname.'0';
            $id_key = ftok($pathname, 'm');
            $sem_id = sem_get($id_key);
            if(sem_acquire($sem_id)){
                $shm_id = shmop_open($id_key, "c", 0644, 1024*10);
                $content = shmop_read($shm_id, 0, shmop_size($shm_id));
                if($content==''){
                    $data = [];
                }else{
                    $data = explode('|', $content);
                }
                $data[] = $file;
                $content = implode('|', $data);
                shmop_write($shm_id, $content, 0);
                shmop_close($shm_id);
                sem_release($sem_id);
            }
        }

        //发现任务
        public function find(){
            $pathname = $this->pathname.'0';
            $id_key = ftok($pathname, 'm');
            $sem_id = sem_get($id_key);
            if(sem_acquire($sem_id)){
                $shm_id = shmop_open($id_key, "c", 0644, 1024*10);
                $content = shmop_read($shm_id, 0, shmop_size($shm_id));
                if($content==''){
                    $data = [];
                }else{
                    $data = explode('|', $content);
                }
                shmop_close($shm_id);
                sem_release($sem_id);
                if(isset($data[0])) return $data[0];
                return false;
            }
        }

        //分配任务
        public function set($id=1){
            $pathname = $this->pathname.'0';
            $id_key = ftok($pathname, 'm');
            $sem_id = sem_get($id_key);
            if(sem_acquire($sem_id)){
                $shm_id = shmop_open($id_key, "c", 0644, 1024*10);
                $content = shmop_read($shm_id, 0, shmop_size($shm_id));
                if($content==''){
                    $data = [];
                }else{
                    $data = explode('|', $content);
                }
                shmop_close($shm_id);
                sem_release($sem_id);
                if(isset($data[0])){
                    $pathname = $this->pathname.$id;
                    $id_key = ftok($pathname, 'm');
                    $sem_id = sem_get($id_key);
                    if(sem_acquire($sem_id)) {
                        $shm_id = shmop_open($id_key, "c", 0644, 1024 * 10);
                        unset($data[0]);
                        shmop_write($shm_id, implode('|', $data), 0);
                        shmop_close($shm_id);
                        sem_release($sem_id);
                    }
                    debug('任务分配完成');
                    return true;
                }else{
                    debug('没有任务');
                    return false;
                }
            }
        }

        //接收任务
        public function get($id=0){
            $pathname = $this->pathname.$id;
            $id_key = ftok($pathname, 'm');
            $sem_id = sem_get($id_key);
            if(sem_acquire($sem_id)) {
                $shm_id = shmop_open($id_key, "c", 0644, 1024 * 10);
                $content = shmop_read($shm_id, 0, shmop_size($shm_id));
                if($content==''){
                    $data = [];
                }else{
                    $data = explode('|', $content);
                }
                shmop_close($shm_id);
                sem_release($sem_id);
                return isset($data[0])?$data[0]:false;
            }
            return false;
        }

        //确认完成任务
        public function ack($id=0){
            $pathname = $this->pathname.$id;
            $id_key = ftok($pathname, 'm');
            $sem_id = sem_get($id_key);
            if(sem_acquire($sem_id)) {
                $shm_id = shmop_open($id_key, "c", 0644, 1024 * 10);
                $content = shmop_read($shm_id, 0, shmop_size($shm_id));
                if($content==''){
                    $data = [];
                }else{
                    $data = explode('|', $content);
                    unset($data[0]);
                }
                shmop_write($shm_id, implode('|', $data), 0);
                shmop_close($shm_id);
                sem_release($sem_id);
                return true;
            }
            return false;
        }

        //初始化
        public function init(){
            $ids = $this->keys();
            foreach($ids as $id){
                $sem_id = sem_get($id);
                if(sem_acquire($sem_id)){
                    $shm_id = shmop_open($id, "c", 0644, 1024*10);
                    $content = shmop_read($shm_id, 0, shmop_size($shm_id));
                    if($content==''){
                        $data = [];
                    }else{
                        $data = explode('|', $content);
                    }
                    shmop_write($shm_id, implode('|', $data), 0);
                    shmop_close($shm_id);
                    sem_release($sem_id);
                }
            }
        }

        //keys
        public function keys(){
            $ids = [];
            for($i=0;$i<=$this->num;$i++){
                $ids[$i] = ftok($this->pathname.$i, 'm');
            }
            return $ids;
        }
    }