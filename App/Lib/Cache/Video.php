<?php
namespace App\Lib\Cache;

use App\Model\Video as VideoModel;
use EasySwoole\Core\Component\Cache\Cache;
use EasySwoole\Core\Component\Di;
/**
 * 生成Api的缓存
 * Class Video
 * @package App\Lib\Cache
 */
class Video {
    public function setIndexVideo()
    {
        $catIds = array_keys(\Yaconf::get("category.cats"));
        array_unshift($catIds, 0);

        //获取前n条视频数据
        $modelObj = new VideoModel();

        //获取视频数据
        foreach ($catIds as $catId) {
            $condition = [];
            if (!empty($catId)) {
                $condition['cat_id'] = $catId;
            }
            try {
                $data = $modelObj->getVideoCacheData($condition);
            } catch (\Exception $e) {
                //为了严谨这里出错可以报警： 短信 邮件
                $data = [];
            }

            if (empty($data)) {
                continue;
            }

            foreach ($data as &$list) {
                $list['create_time'] = date("Ymd H:i:s", $list['create_time']);
                $list['video_duration'] = gmstrftime("%H:%M:%S", $list['video_duration']); //将时长秒数转换为00:00:00的形式
            }

            //第一套方案：直接读取mysql进行返回
            //第二套方案：将json数据写入文件实现静态化
            //$flag = file_put_contents(EASYSWOOLE_ROOT."/webroot/video/json/".$catId.".json", json_encode($data));

            //第三套方案：将数据存入内存
            //$flag = Cache::getInstance()->set("index_video_data_cat_id_".$catId, $data);

            //第四套方案：将数据存入redis
           $flag =  Di::getInstance()->get('REDIS')->set("index_video_data_cat_id_".$catId, $data);
//            if (empty($flag)) {
//                //报警 邮件 短信
//                echo "cat_id:" . $catId . " put data error".PHP_EOL;
//            } else {
//                echo "cat_id:" . $catId . " put data success".PHP_EOL;
//            }
        }
    }
}