<?php
namespace App\HttpController\Api;

use App\Model\Video as VideoModel;
use EasySwoole\Core\Http\Message\Status;
/**
 *
 * Class Upload
 * @package App\HttpController\Api
 */
class Video extends Base
{
    public function add()
    {
        $params = $this->request()->getRequestParam();

        $data = [
            'name' => $params['name'],
            'url' => $params['url'],
            'image' => $params['image'],
            'content' => $params['content'],
            'cat_id' => $params['cat_id'],
            'create_time' => time(),
            'status' => \Yaconf::get("status.normal"),
        ];

        //写入数据库
        try {
            $modelObj = new VideoModel();
            $videoId = $modelObj->add($data);
        } catch (\Exception $e) {
            return $this->writeJson(Status::CODE_BAD_REQUEST, $e->getMessage());
        }

        if (!empty($videoId)) {
            return $this->writeJson(Status::CODE_OK, 'OK', ['id' => $videoId]);
        } else {
            return $this->writeJson(Status::CODE_BAD_REQUEST, "提交视频有误", ['id' =>0]);
        }

    }
}