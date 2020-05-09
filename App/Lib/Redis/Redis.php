<?php
namespace App\Lib\Redis;

use EasySwoole\Core\AbstractInterface\Singleton;
use EasySwoole\Config;
class Redis {
    use Singleton;

    public $redis = "";

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
            $this->redis = new \Redis();
            $result = $this->redis->connect($redisConfig['host'], $redisConfig['port'], $redisConfig['time_out']);
        } catch (\Exception $e) {
            throw new \Exception("redis服务异常");
        }

        if ($result === false) {
            throw new \Exception("redis连接失败");
        }
    }

    public function get($key)
    {
        if (empty($key)) {
            return '';
        }

        return $this->redis->get($key);
    }
}