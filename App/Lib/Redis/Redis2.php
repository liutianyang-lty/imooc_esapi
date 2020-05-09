<?php
namespace App\Lib\Redis;

use EasySwoole\Core\AbstractInterface\Singleton;
use EasySwoole\Config;
class Redis2 {
    use Singleton;

    public static $redis = "";

    private function __construct()
    {
        if (!extension_loaded('redis')) {
            throw new \Exception("redis.so文件不存在");
        }

        try {
            //获取redis配置
            //$redisConfig = Config::getInstance()->getConf('redis');

            //通过 Yaconf获取配置文件
            $redisConfig = \Yaconf::get('redis');
            //var_dump($redisConfig);
            self::$redis = new \Redis();
            $result = self::$redis->connect($redisConfig['host'], $redisConfig['port'], $redisConfig['time_out']);
        } catch (\Exception $e) {
            throw new \Exception("redis服务异常");
        }

        if ($result === false) {
            throw new \Exception("redis连接失败");
        }
    }

    /**
     * 魔术方法静态调用
     * @param $method_name
     * @param $param
     * @return mixed
     */
    public function __call($method_name, $param)
    {
        if (!self::$redis) {
            self::getInstance();
        }
        try {
            return call_user_func_array([self::$redis, $method_name], $param);
        } catch (\Exception $e) {
            print $e->getMessage();
            exit;
        }
    }

}