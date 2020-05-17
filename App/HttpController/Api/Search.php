<?php
namespace App\HttpController\Api;

use App\Model\Es\EsVideo;
use EasySwoole\Core\Http\Message\Status;
/**
 * Class Search
 * @package App\HttpController\Api
 */
class Search extends Base
{


    public function index()
    {
        $keyword = $this->params['keyword'];
        if (empty($keyword)) {
            return $this->writeJson(Status::CODE_OK, "OK", $this->getPagingDatas(0, []));
        }

        try {
            $esObj = new EsVideo();
            $result = $esObj->searchByName($keyword, $this->params['from'], $this->params['size']);
        } catch (\Exception $e) {
            // TODO
        }

        if (empty($result)) {
            return $this->writeJson(Status::CODE_OK, "OK", $this->getPagingDatas(0,[]));
        }

        $hits = $result['hits']['hits'];
        $total = $result['hits']['total'];

        if (empty($total)) {
            return $this->writeJson(Status::CODE_OK, "OK", $this->getPagingDatas(0,[]));
        }

        $resData = [];
        foreach ($hits as $hit) {
            $source = $hit['_source'];
            $resData[] = [
                'id' => $hit['_id'],
                'name' => $source['name'],
                'image' => $source['image'],
                'url' => $source['url'],
                'type' => $source['type'],
                'uploader' => $source['uploader'],
                'status' => $source['status'],
                'video_id' => $source['video_id'],
                'create_time' => '',
                'video_duration' => '',
                'keywords' => [$keyword],
            ];
        }
        return $this->writeJson(Status::CODE_OK, "OK", $this->getPagingDatas($total,$resData));
    }
}