<?php
namespace App\Lib\Upload;

class Image extends Base {
    public $fileType = "image";

    //限制图片文件上传的大小,单位是M
    public $maxSize = 122;

    //文件后缀的mediaType
    public $fileExtTypes = [
        'png',
        'jpeg',
        'jpg',
    ];
}