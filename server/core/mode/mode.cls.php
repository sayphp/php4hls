<?php
    /**
     * mode.cls.php
     * 模式类
     * say
     * 2017-09-21 15:02:00
     */
    class mode{

        protected $mode;

        protected $opt;

        public function __construct($opt){
            $this->opt = $opt;
            if(PHP_SAPI!='cli') error(21);//*检查命令行
            if(!class_exists($opt['mode'])) error(11);
            $this->mode = new $opt['mode']($this->opt);
        }

        public function run(){
            $this->mode->run();
        }

    }