<?php
    /**
     * task.cls.php
     * 任务类
     * say
     * 2017-09-21 19:45:00
     */
    class task{

        public $pathname = __FILE__;

        public $num;

        public function __construct($num=5){
            $this->num = $num;//任务数
        }

        //发布任务
        public function sub($file){
            $id_key = ftok($this->pathname, chr(65));
            $sem_id = sem_get($id_key);
            if(sem_acquire($sem_id)){
                $shm_id = shmop_open($id_key, "c", 0644, 1024*10);
                $content = trim(shmop_read($shm_id, 0, shmop_size($shm_id)));
                if($content==''){
                    $data = [];
                }else{
                    $data = explode('|', $content);
                }
                $data[] = $file;
                $str = implode('|', $data);
                $str = $this->str($str);
                shmop_write($shm_id, $str, 0);
                shmop_close($shm_id);
                sem_release($sem_id);
            }
        }

        //发现任务
        public function find(){
            $id_key = ftok($this->pathname, chr(65));
            $sem_id = sem_get($id_key);
            if(sem_acquire($sem_id)){
                $shm_id = shmop_open($id_key, "c", 0644, 1024);
                $content = trim(shmop_read($shm_id, 0, shmop_size($shm_id)));
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
            $id_key = ftok($this->pathname, chr(65));
            $sem_id = sem_get($id_key);
            if(sem_acquire($sem_id)){
                $shm_id = shmop_open($id_key, "w", 0644, 1024 * 10);
                $content = trim(shmop_read($shm_id, 0, shmop_size($shm_id)));
                debug('总任务的内容'.$content);
                if($content==''){
                    $data = [];
                }else{
                    $data = explode('|', $content);
                }
                $t = isset($data[0])?$data[0]:false;
                unset($data[0]);
                $tmp = $data;
//                debug($data);
                $tmp = array_values($tmp);
                debug('处理后内容'.implode('|',$tmp));
                $str = implode('|', $tmp);
                $str = $this->str($str);
                debug('???'.$str);
                $rs = shmop_write($shm_id, $str, 0);
                debug('==='.shmop_read($shm_id, 0, shmop_size($shm_id)));
                debug($rs);
                shmop_close($shm_id);
                sem_release($sem_id);
                if($t){
                    $id_key = ftok($this->pathname, chr(65+$id));
                    $sem_id = sem_get($id_key);
                    if(sem_acquire($sem_id)) {
                        $shm_id = shmop_open($id_key, 'w', 0644, 1024 * 10);
                        $content = trim(shmop_read($shm_id, 0, shmop_size($shm_id)));
                        debug('分支'.$id.'的内容'.$content);
                        if($content==''){
                            $d = [];
                        }else{
                            $d = explode('|', $content);
                        }

                        $d[] = $t;
                        $str = implode('|', $d);
                        $str = $this->str($str);
                        $rs = shmop_write($shm_id, $str, 0);
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
            $id_key = ftok($this->pathname, chr(65+$id));
            $sem_id = sem_get($id_key);
            if(sem_acquire($sem_id)) {
                $shm_id = shmop_open($id_key, "c", 0644, 1024 * 10);

                $content = trim(shmop_read($shm_id, 0, shmop_size($shm_id)));
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
            $id_key = ftok($this->pathname, chr(65+$id));
            $sem_id = sem_get($id_key);
            if(sem_acquire($sem_id)) {
                $shm_id = shmop_open($id_key, "c", 0644, 1024 * 10);
                $content = trim(shmop_read($shm_id, 0, shmop_size($shm_id)));
                if($content==''){
                    $data = [];
                }else{
                    $data = explode('|', $content);
                    unset($data[0]);
                }
                $str = implode('|', $data);
                $str = $this->str($str);
                shmop_write($shm_id, $str, 0);
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
                    $shm_id = shmop_open($id, 'c', 0644, 1024 * 10);
                    $content = trim(shmop_read($shm_id, 0, shmop_size($shm_id)));
                    if($content==''){
                        $data = [];
                    }else{
                        $data = explode('|', $content);
                    }
                    $str = implode('|', $data);
                    $str = $this->str($str);
                    shmop_write($shm_id, $str, 0);
                    shmop_close($shm_id);
                    sem_release($sem_id);
                }
            }
        }

        //清除
        public function clean(){
            $ids = $this->keys();
            foreach($ids as $id){
                $shm_id = shmop_open($id, 'c', 0644, 1024 * 10);
                var_dump($id);
                shmop_delete($shm_id);
                shmop_close($shm_id);
            }
        }

        //keys
        public function keys(){
            $ids = [];
            $k = 65;
            for($i=0;$i<=$this->num;$i++){
                $ids[$i] = ftok($this->pathname, chr(65+$i));
            }
            return $ids;
        }

        //format
        public function str($str){
           return str_pad($str, 1024*10, ' ', STR_PAD_RIGHT);
        }
    }