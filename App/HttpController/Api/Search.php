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

        $esObj = new EsVideo();
        $result = $esObj->searchByName($keyword, $this->params['from'], $this->params['size']);
        return $this->writeJson(Status::CODE_OK, "OK",$result);
    }
}