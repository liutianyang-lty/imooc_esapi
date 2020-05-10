<?php
namespace App\Lib\Upload;

use App\Lib\Utils;
class Base
{
    protected $request;
    public $type = ""; //文件类型
    public $size = ""; //文件大小
    public $clientMediaType = "";
    public $file;

    public function __construct($request)
    {
        $this->request = $request;
        //获取文件信息
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

        //获取文件大小并进行验证
        $this->size = $videos->getSize();
        $this->checkSize();

        $fileName  = $videos->getClientFileName();

        $this->clientMediaType = $videos->getClientMediaType();
        $this->checkMediaType();

        $file = $this->getFile($fileName);
        $flag = $videos->moveTo($file);

        if (!empty($flag)) {
            return $this->file;
        }

        return false;

    }

    public function getFile($fileName)
    {
        $pathinfo = pathinfo($fileName);
        $extension =  $pathinfo['extension'];

        //文件存放路径
        $dirname = "/" . $this->type . "/" . date("Y") . "/" . date("m");
        $dir = EASYSWOOLE_ROOT . "/webroot" .$dirname ; //EASYSWOOLE_ROOT为项目的路径即imooc_esapi的路径
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true); //如果目录不存在就创建
        }
        $basename = "/" . Utils::getFileKey($fileName) . "." . $extension;
        $this->file = $dirname . $basename;
        return $dir . $basename;
    }

    /**
     * 验证文件大小
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