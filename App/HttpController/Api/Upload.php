<?php
namespace App\HttpController\Api;

use App\HttpController\Api\Base;
use App\Lib\ClassArr;
use App\Lib\Upload\Video;

/**
 * 文件上传逻辑 - 视频 图片
 * Class Upload
 * @package App\HttpController\Api
 */
class Upload extends Base
{
    public function index()
    {

    }

    public function file()
    {
        $request = $this->request();
        $files = $request->getSwooleRequest()->files;
        $types = array_keys($files);
        $type = $types[0];
        if (empty($type)) {
            return $this->writeJson(400, '上传文件不合法');
        }


        try {
            set_time_limit(0);
            //$obj = new Video($request); //视频文件上传
            //$obj = new \App\Lib\Upload\Image($request); //图片文件上传

            //使用PHP的反射机制
            $classObj = new ClassArr();
            $classStats = $classObj->uploadClassStat();
            $uploadObj = $classObj->initClass($type, $classStats, [$request, $type]);
            $file = $uploadObj->upload();
        } catch (\Exception $e) {
            return $this->writeJson(400, $e->getMessage(), []);
        }

        if (empty($file)) {
            return $this->writeJson(400, "上传失败", []);
        }

        $data = [
            'url' => $file
        ];
        return $this->writeJson(200, "OK", $data);

//        $request = $this->request();
//        $videos = $request->getUploadedFile('file');
//        $flag = $videos->moveTo("/www/wwwroot/imooc_esapi/webroot/1.jpg");
//
//        $data = [
//            'url' => "1.jpg",
//            'flag' => $flag,
//        ];
//        if ($flag) {
//            return $this->writeJson(200, 'OK', $data);
//        } else {
//            return $this->writeJson(400, 'OK', $data);
//        }
    }
}