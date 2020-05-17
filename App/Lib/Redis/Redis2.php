<?php
namespace App\Lib\Redis;

use EasySwoole\Core\AbstractInterface\Singleton;
use EasySwoole\Config;
class Redis2 {
    use Singleton;

    public static $redis = "";

    //私有化构造函数
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
     * @param $method_name // 调用的redis方法名
     * @param $param // 调用redis方法时传递的参数
     * @return mixed
     */
//    public function __call($method_name, $param)
//    {
//        if (!self::$redis) {
//            self::getInstance();
//        }
//        try {
//            return call_user_func_array([self::$redis, $method_name], $param);
//        } catch (\Exception $e) {
//            print $e->getMessage();
//            exit;
//        }
//    }

    //私有化克隆方法
    public function __clone()
    {
        // TODO: Implement __clone() method.
    }

    /**
     * 魔术方法调用redis中的方法
     * @param $method_name
     * @param $arguments
     * @return mixed
     */
    public function __call($method_name, $arguments)
    {
        return self::$redis->$method_name(...$arguments);
    }

}