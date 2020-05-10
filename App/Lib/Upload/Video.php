<?php
namespace App\Lib\Upload;

class Video extends Base {
    public $fileType = "video";

    //限制视频文件上传的大小
    public $maxSize = 122;

    //文件后缀的mediaType
    public $fileExtTypes = [
        'mp4',
        'x-flv',
    ];
}