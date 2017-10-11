<?php
    /**
     * hls.cls.php
     * 视频转码切片服务类
     * say
     * 2017-09-21 14:52:00
     */
    class hls{

        public $opt = [
            'mode' => APP_MODE,//模式
            'work_num' => 1,//由于单一调度已经能够满足需求，这里硬编码为1
            'task_num' => APP_TASK,
            'debug' => APP_DEBUG,
            'interval' => APP_INTERVAL,
        ];

        protected $work_num;//工人数

        protected $task_num;//任务数

        protected $debug;//debug

        protected $interval;//时间间隔

        //*启动
        public function start(){
            //模式初始化
            $mode = new mode($this->opt);
            $mode->run();//运行
        }

        //*守护进程
        public function daemon(){

        }

        //*工人
        public function work(){

        }

        //命令
        public static function cmd($cmd){
            exec($cmd, $data, $code);
            if($code) error(22, "shell命令错误:".$cmd);
            return $data;
        }
    }