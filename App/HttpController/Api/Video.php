<?php
namespace App\HttpController\Api;

use App\Model\Video as VideoModel;
use EasySwoole\Core\Http\Message\Status;
use EasySwoole\Core\Utility\Validate\Rules;
use EasySwoole\Core\Utility\Validate\Rule;
use EasySwoole\Core\Component\Logger;
use EasySwoole\Core\Swoole\Task\TaskManager;
use EasySwoole\Core\Component\Di;
/**
 * 小视频- 增、删、改、查接口
 * Class Upload
 * @package App\HttpController\Api
 */
class Video extends Base
{
    public $logType = "video:";

    /**
     * 视频基本信息接口
     * @return bool|void
     */
    public function index()
    {
        $id = intval($this->params['id']);
        if (empty($id)) {
            return $this->writeJson(Status::CODE_BAD_REQUEST, "id不能为空");
        }

        //获取视频的基本信息
        try {
            $video = (new VideoModel())->getById($id);
        } catch (\Exception $e) {
            //使用日志记录错误信息
            Logger::getInstance()->log($e->getMessage());
            return $this->writeJson(Status::CODE_BAD_REQUEST, "请求不合法");
        }

        if (!$video || $video['status'] != \Yaconf::get("status.normal")) {
            return $this->writeJson(Status::CODE_BAD_REQUEST,"该视频不存在");
        }
        $video['video_duration'] = gmstrftime("%H:%M:%S", $video['video_duration']);

        //播放数统计逻辑
        // 投放task异步任务
        TaskManager::async(function () use($id) {
            // 逻辑
            // 将播放数存入redis有序集合中
            try {
                Di::getInstance()->get('REDIS')->zincrby(\Yaconf::get("redis.video_play_key"), 1, $id);

                //日排行 周排行 月排行 季度排行:
                //日排行记录
                Di::getInstance()->get('REDIS')->zincrby(\Yaconf::get("redis.video_play_key").":".date("Ymd"), 1, $id);

            } catch (\Exception $e) {
                //日志记录错误信息
                Logger::getInstance()->log("zincrby error:" . $e->getMessage());
            }

        });

        return $this->writeJson(Status::CODE_OK, "OK", $video);
    }

    /**
     * 视频播放数排行接口 今日排行 月排行 季度排行
     */
    public function rank()
    {
        $result = Di::getInstance()->get("REDIS")->zrevrange(\Yaconf::get("redis.video_play_key"), 0, -1, true);

        //获取日排行
        $dayRank = Di::getInstance()->get("REDIS")->zrevrange(\Yaconf::get("redis.video_play_key").":".date("Ymd"), 0, 9, true);
        $result['dayRank'] = $dayRank;
        return $this->writeJson(Status::CODE_OK, "OK", $result);
    }

    /**
     * 将视频的点赞数记录到redis中
     * @return bool
     */
    public function love()
    {
        $videoId = intval($this->params['videoId']);
        if (empty($videoId)) {
            return $this->writeJson(Status::CODE_BAD_REQUEST, "请求不合法");
        }

        //获取视频的基本信息
        try {
            $video = (new VideoModel())->getById($videoId);
        } catch (\Exception $e) {
            //使用日志记录错误信息
            Logger::getInstance()->log($e->getMessage());
            return $this->writeJson(Status::CODE_BAD_REQUEST, "请求不合法");
        }

        if (!$video || $video['status'] != \Yaconf::get("status.normal")) {
            return $this->writeJson(Status::CODE_BAD_REQUEST,"该视频不存在");
        }

        //点赞数量递增
        // 投放task异步任务
        TaskManager::async(function () use($videoId) {
            // 逻辑
            // 将播放数存入redis有序集合中
            try {
                $res = Di::getInstance()->get("REDIS")->zincrby(\Yaconf::get("redis.video_love"), 1, $videoId);
            } catch (\Exception $e) {
                //日志记录错误信息
                Logger::getInstance()->log("zincrby error:" . $e->getMessage());
            }
        });
    }

    /**
     * 添加视频接口
     * @return bool
     */
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