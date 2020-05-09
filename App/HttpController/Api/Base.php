<?php
namespace App\HttpController\Api;
use EasySwoole\Core\Http\AbstractInterface\Controller;

/**
 * Api模块下的基础类库
 * Class Base
 * @package App\HttpController\Api
 */
class Base extends Controller
{
    public function index()
    {

    }

    /**
     * 类似于拦截器
     * 可用于权限相关的判断
     * @param $action
     * @return bool|null
     */
    public function onRequest($action): ?bool
    {
        return true;
    }

    /**
     * 处理请求不合法的情况：找不到类
     * @param \Throwable $throwable
     * @param $actionName
     */
    public function onException(\Throwable $throwable, $actionName): void
    {
        $this->writeJson(400, '请求不合法', []);
    }
}
