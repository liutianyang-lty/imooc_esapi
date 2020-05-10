<?php
namespace App\Lib\Upload;

class Base
{
    protected $request;
    public $type = ""; //文件类型
    public $size = "";
    public $clientMediaType = "";

    public function __construct($request)
    {
        $this->request = $request;
        $files = $this->request->getSwooleRequest()->files;
        $types = array_keys($files);
        $this->type = $types[0];
    }

    public function upload()
    {
        if ($this->type != $this->fileType) {
            return false;
        }

        $videos = $this->request->getUploadedFile($this->type);

        $this->size = $videos->getSize();
        echo $this->size;
        $this->checkSize();
        $fileName  = $videos->getClientFileName();

        $this->clientMediaType = $videos->getClientMediaType();
        $this->checkMediaType();

        $this->getFile($fileName);

    }

    public function getFile($fileName)
    {
        $pathinfo = pathinfo($fileName);
        print_r($pathinfo);
    }

    /**
     * @return bool
     */
    public function checkSize()
    {
        if (empty($this->size)) {
            return false;
        }

        //TODO
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function checkMediaType()
    {
        $clienMediaType = explode('/', $this->clientMediaType);
        $clienMediaType = $clienMediaType[1] ?? "";

        if (empty($clienMediaType)) {
            throw new \Exception("上传{$this->type}文件不合法");
        }

        if (!in_array($clienMediaType, $this->fileExtTypes)) {
            throw new \Exception("上传{$this->type}文件不合法");
        }

        return true;
    }
}