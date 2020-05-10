<?php
namespace App\HttpController\Api;

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

        return $this->writeJson(200, 'OK', $params);
    }
}