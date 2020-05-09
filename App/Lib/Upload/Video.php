<?php
namespace App\Lib\Upload;

class Video extends Base {
    public $fileType = "video";

    public $maxSize = 122;

    //文件后缀的mediaType
    public $fileExtTypes = [
        'mp4',
        'x-flv',
    ];
}