<?php
    /**
     * do.php.
     * say
     * 2017-10-09 10:46:00
     */
    define('TARGET', '/var/www/php4hls/test/video/');//目录
    define('PATH', '/var/www/php4hls/test/huantuo/');//目录
    //阿里云OSS
    define('OSS_ID','');//ossid
    define('OSS_KEY', '');//osskey
    define('OSS_DOMAIN', 'aliyuncs.com');//域
    define('OSS_URL', 'http://video.sanhao.com/');//oss访问地址
    //视频规范
    define('V_W', 1706);//宽
    define('V_H', 960);//高
    //*1.环境检查`
    cmd('ffmpeg -version &>/dev/null ; echo $?');
    //*2.检查文件，获取待处理任务
    $lists = glob(TARGET."*.avi");
    //*3.处理视频
    foreach($lists as $file){
        //*3.1 标准化视频格式、尺寸
        $info = pathinfo($file);
//        var_dump($info);
        $f = PATH.$info['filename'];
        dir_init($f);
//        cmd('ffmpeg -i '.$file.' -s '.V_W.'*'.V_H.' '.$f.'/source.mp4');
        //*3.2 视频打包切片
        cmd('ffmpeg -i '.$file.' -vcodec copy -acodec aac '.$f.'/source.ts');
        //*3.3 m3u8文件生成
        cmd('/say/m3u8-segmenter/segmenter -i '.$f.'/source.ts -d 10 -p huantuo/'.$info['filename'].'/v -m '.$f.'/index.m3u8 -u '.OSS_URL);
        //删除source文件
//        @unlink($f.'/source.ts');
        //*3.4 上传OSS(包括ts、m3u8)
        up($f);
        echo '视频处理完成'.$file.PHP_EOL;
    }


    //*基础方法封装
    //命令执行
    function cmd($cmd){
//        var_dump($cmd);
        $rs = exec($cmd, $data, $status);
//        var_dump($status);
        if($status) exit('['.$rs.']:'.$cmd);
        return true;
    }
    //上传
    function up($f){
        $lists = glob($f.'/*');
//        var_dump($lists);
        $tmp = explode('/', $f);
        $p = end($tmp);
        foreach($lists as $file){
            $info = pathinfo($file);
            $size = filesize($file);
            $par = [
                'object' => 'huantuo/'.$p.'/'.$info['basename'],
                'type' => $info['extension'],
                'length' => $size,
            ];
            $url = auth($par);
            $rs = upload($url, fopen($file,'r'), $size, $info['extension']);
            if(!$rs) var_dump($rs);
//            echo '上传流文件'.$file.PHP_EOL;
        }
    }
    //授权
    function auth($par){
        $par['time'] = time()+3600*4;
        $url = 'http://';
        $domain = 'sanhao-video.oss-cn-beijing.'.OSS_DOMAIN;
        //获取访问域名
        $url .= $domain.'/';
        //资源对象
        $url .= urlencode($par['object']).'?';
        //密匙ID
        $url .= 'OSSAccessKeyId='.OSS_ID.'&';
        //有效时间
        $url .= 'Expires='.$par['time'].'&';
        //签名
        $url .= 'Signature='.sign($par);
        return $url;
    }
    //*签名
    function sign($par){
        $sign_str = "PUT\n";
        //Content-md5
        $sign_str .= "\n";
        //Content-TYPE
        $sign_str .= $par['type']."\n";
        //请求时间
        $sign_str .= $par['time']."\n";
        //资源
        $sign_str .= '/sanhao-video/'.$par['object'];

        return urlencode(base64_encode(hash_hmac('sha1',$sign_str,OSS_KEY,true)));
    }
    //上传文件方法
    function upload($url,$file,$file_size,$file_type){
        $header_arr = array(
            //'Content-Length: '.$file_size,
            'Content-Type: '.$file_type,
        );
        $curl = curl_init();
        $options = array(
            CURLOPT_URL => $url,//请求地址
            CURLOPT_HEADER => false,//不返回头信息
            CURLOPT_RETURNTRANSFER => true,//作为流返回
            CURLOPT_FOLLOWLOCATION => true,//有Location,则递归执行
            CURLINFO_HEADER_OUT => true,//头部信息输出
            CURLOPT_CUSTOMREQUEST => 'PUT',//使用delete请求
            CURLOPT_HTTPHEADER => $header_arr,//设置http头字段数组
            CURLOPT_PUT => 1,
            CURLOPT_INFILE => $file,//文件流//fopen($file,'r'),非文件流
            CURLOPT_INFILESIZE => $file_size,
            //CURLOPT_VERBOSE => true,
            //CURLOPT_POSTFIELDS => $file,
        );
        curl_setopt_array($curl, $options);//批量设置
        //*执行
        $data = curl_exec($curl);
        $rs = curl_getinfo($curl);//有用的返回信息
        curl_close($curl);
        if($rs['http_code']==200) return true;
        return $data;
    }
    //文件初始化
    function dir_init($f){
        if(!file_exists($f)) mkdir($f);
    }