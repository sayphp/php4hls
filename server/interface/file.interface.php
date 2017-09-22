<?php
    /**
     * file.interface.php
     * 文件接口
     * say
     * 2017-09-21 15:48:00
     */
    interface fileInterface{

        public function download($file_url);//文件下载

        public function upload($file_url);//文件上传

    }