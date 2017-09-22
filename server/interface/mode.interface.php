<?php
    /**
     * mode.interface.php
     * 模式接口
     * say
     * 2017-09-21 15:48:00
     */
    interface modeInterface{

        public function __construct($opt);//构造函数

        public function check();//模式检查

        public function run();//运行方法

    }