<?php
namespace App\Lib\Upload;

class Base
{
    protected $request;
    public $type = ""; //文件类型
    public $size = "";
    public function __construct($request)
    {
        $this->request = $request;
        $files = $this->request->getSwooleRequest()->files;
        $types = array_keys($files);
        $this->type = $types[0];
        print_r($this->type);
    }

    public function upload()
    {
        if ($this->type != $this->fileType) {
            return false;
        }

        $videos = $this->request->getUploadedFile($this->type);

        $this->size = $videos->getSize();
        $this->checkSize();
        $fileName  = $videos->getClientFileName();
        echo $fileName;
    }

    public function checkSize()
    {
        if (empty($this->size)) {
            return false;
        }
    }
}