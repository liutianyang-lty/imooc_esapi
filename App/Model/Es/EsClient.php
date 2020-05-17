<?php
namespace App\Model\Es;

use EasySwoole\Core\AbstractInterface\Singleton;
use Elasticsearch\ClientBuilder;
use EasySwoole\Core\Component\Logger;
class EsClient
{
    //单例模式
    use Singleton;

    public static $esClinet = null;

    //私有化构造函数
    private function __construct()
    {
        $config = \Yaconf::get("es");
        try {
            //es实例
            self::$esClinet = ClientBuilder::create()->setHosts([$config['host'] . ":" . $config['port']])->build();
        } catch (\Exception $e) {
            //记录日志
            Logger::getInstance()->log($e->getMessage());
        }

        if (empty(self::$esClinet)) {
            throw new \Exception("es连接异常");
        }

    }

    //私有化克隆方法
    private function __clone(){ }


    /**
     *
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return self::$esClinet->$name(...$arguments);
    }
}
