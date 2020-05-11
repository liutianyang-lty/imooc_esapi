<?php
namespace App\HttpController;
use EasySwoole\Core\Http\AbstractInterface\Controller;
use App\Lib\AliyunSdk\AliVod;
class Index extends Controller
{
    function index()
    {
        // TODO: Implement index() method.
        $this->response()->write('hello world');
    }

    public function testali()
    {
        $obj = new AliVod();
        $title = "singwa-test-upload";
        $videoName = "1.mp4";
        $result = $obj->createUploadVideo($title, $videoName);
        $uploadAdress = json_decode(base64_decode($result->UploadAdress, true));
        $uploadAuth = json_decode(base64_decode($result->UploadAuth));

        $obj->initOssClient($uploadAuth, $uploadAdress);
        $videoFile = "/www/wwwroot/imooc_esapi/webroot/video/2020/05/68ee80b1287bcd0b.mp4";
        $result = $obj->uploadLocalFile($uploadAdress, $videoFile);
        print_r($result);
    }
}

