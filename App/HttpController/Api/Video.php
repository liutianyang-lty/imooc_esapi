<?php
namespace App\HttpController\Api;

use App\Model\Video as VideoModel;
use EasySwoole\Core\Http\Message\Status;
use EasySwoole\Core\Utility\Validate\Rules;
use EasySwoole\Core\Utility\Validate\Rule;
use EasySwoole\Core\Component\Logger;
/**
 *
 * Class Upload
 * @package App\HttpController\Api
 */
class Video extends Base
{
    public $logType = "video:";

    public function add()
    {
        $params = $this->request()->getRequestParam();

        //日志记录
        Logger::getInstance()->log($this->logType . "add:" . json_encode($params));

        //数据校验
        $ruleObj = new Rules();
        $ruleObj->add('name', "视频名称错误")->withRule(Rule::REQUIRED)->withRule(Rule::MAX_LEN, 20);
        $ruleObj->add('url', "视频地址错误")->withRule(Rule::REQUIRED);
        $ruleObj->add('image', "图片地址错误")->withRule(Rule::REQUIRED);
        $ruleObj->add('content', "视频描述错误")->withRule(Rule::REQUIRED);
        $ruleObj->add('cat_id', "栏目ID错误")->withRule(Rule::REQUIRED);
        $validate = $this->validateParams($ruleObj);
        if ($validate->hasError()) {
            print_r($validate->getErrorList());
            return $this->writeJson(Status::CODE_BAD_REQUEST, $validate->getErrorList()->first()->getMessage());
        }
        $data = [
            'name' => $params['name'],
            'url' => $params['url'],
            'image' => $params['image'],
            'content' => $params['content'],
            'cat_id' => intval($params['cat_id']),
            'create_time' => time(),
            'uploader' => 'singwa',
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