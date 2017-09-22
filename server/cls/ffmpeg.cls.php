<?php
    /**
     * ffmpeg.cls.php
     * ffmpeg视频工具类
     * say
     * 2017-09-21 15:14:00
     */
    class ffmpeg{

        //是否安装
        public static function check(){
            $cmd = 'ffmpeg -version';
            $data = hls::cmd($cmd);
            return true;
        }


    }