<?php
namespace App\HttpController\Api;
use App\HttpController\Api\Base;
use \EasySwoole\Core\Component\Di;
use App\Lib\Redis\Redis;
use App\Model\Video as VideoModel;
use EasySwoole\Core\Http\Message\Status;
use EasySwoole\Core\Component\Cache\Cache;
use App\Lib\Cache\Video as VideoCache;
class Index extends Base
{
    public function index()
    {

    }

    /**
     * 第一套方案  直接读取 静态化 json数据
     * 获取视频首页分页数据
     * @return bool
     */
    public function lists0()
    {
        $condition = [];
        if (!empty($this->params['cat_id'])) {
            $condition['cat_id'] = intval($this->params['cat_id']);
        }

        try {
            $videoModel = new VideoModel();
            $data = $videoModel->getVideoData($condition, $this->params['page'], $this->params['size']);
        } catch (\Exception $e) {
            return $this->writeJson(Status::CODE_BAD_REQUEST, "服务异常");
        }

        if (!empty($data['lists'])) {
            foreach ($data['lists'] as &$list) {
                $list['create_time'] = date("Ymd H:i:s", $list['create_time']);
                $list['video_duration'] = gmstrftime("%H:%M:%S", $list['video_duration']); //将时长秒数转换为00:00:00的形式
            }
        }

        return $this->writeJson(Status::CODE_OK, 'OK', $data);

    }

    /**
     * 第二套方案  原始  ---- 读取mysql
     * 获取视频首页分页数据
     * @return bool
     */
    public function lists()
    {
        $condition = [];
        if (!empty($this->params['cat_id'])) {
            $condition['cat_id'] = intval($this->params['cat_id']);
        }
        $catId = !empty($this->params['cat_id']) ? intval($this->params['cat_id']) : 0;
        try {
            $videoData = (new VideoCache())->getCache($catId);
        }catch (\Exception $e){
            return $this->writeJson(Status::CODE_BAD_REQUEST, "请求失败");
        }


        //PHP进行分页
        $count = count($videoData);
        return $this->writeJson(Status::CODE_OK, 'OK', $this->getPagingDatas($count, $videoData));

    }

    public function video()
    {
        $data = [
            'id' => 1,
            'name' => 'singwa老师荣获国家级计算机大赛一等奖',
            'params' => $this->request()->getRequestParam(),
        ];

        return $this->writeJson(202, 'OK', $data);
    }

    //easyswoole操作数据库mysql
    public function getVideo()
    {
        $db = Di::getInstance()->get('MYSQL');
        $result = $db->where("id", 1)->getOne('video');
        return $this->writeJson(200, 'OK', $result);
    }

    //easyswoole操作redis
    public function getRedis()
    {
//        $redis = new \Redis();
//        $redis->connect("0.0.0.0", 6379, 5);
//        $redis->set('singwa456', 90);

        //$result = Redis::getInstance()->get('singwa');
        $result = Di::getInstance()->get('REDIS')->get('singwa');
        return $this->writeJson(200, 'OK', $result);
    }

    public function yaconf()
    {
        $redis = \Yaconf::get('redis');
        return $this->writeJson(200, 'OK', $redis);
    }

    public function push()
    {
        $param = $this->request()->getRequestParam();
        Di::getInstance()->get('REDIS')->rPush('imooc_list_test', $param['f']);
    }
}