<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/1/9
 * Time: 下午1:04
 */

namespace EasySwoole;

use \EasySwoole\Core\AbstractInterface\EventInterface;
use EasySwoole\Core\Swoole\Process\ProcessManager;
use \EasySwoole\Core\Swoole\ServerManager;
use \EasySwoole\Core\Swoole\EventRegister;
use \EasySwoole\Core\Http\Request;
use \EasySwoole\Core\Http\Response;
use \EasySwoole\Core\Component\Di;
use App\Lib\Redis\Redis;
use App\Lib\Redis\Redis2;
use EasySwoole\Core\Utility\File;
use App\Lib\Process\ConsumerTest;

Class EasySwooleEvent implements EventInterface {

    public static function frameInitialize(): void
    {
        // TODO: Implement frameInitialize() method.
        date_default_timezone_set('Asia/Shanghai');
        //载入项目 Config 目录中所有的配置文件
        self::loadConf(EASYSWOOLE_ROOT . '/Config');
    }

    public static function loadConf($ConfPath)
    {
        $Conf = Config::getInstance();
        $files = File::scanDir($ConfPath);
        //var_dump($files);
        foreach ($files as $file) {
            $data = require_once $file;
            $Conf->setConf(strtolower(basename($file, '.php')),(array)$data);
        }
    }

    public static function mainServerCreate(ServerManager $server,EventRegister $register): void
    {
        // TODO: Implement mainServerCreate() method.
        //mysql 相关
        Di::getInstance()->set('MYSQL', \MysqliDb::class, Array(
            'host' => \Yaconf::get("database.host"),
            'username' => \Yaconf::get("database.username"),
            'password' => \Yaconf::get("database.password"),
            'db' => \Yaconf::get("database.db"),
            'port' => \Yaconf::get("database.port"),
            'charset' => \Yaconf::get("database.charset")
        ));

        //redis相关
        Di::getInstance()->set('REDIS', Redis::getInstance());

        //注册消费者进程
//        $allNum = 3;
//        for ($i = 0; $i < $allNum; $i++) {
//            ProcessManager::getInstance()->addProcess("consumer_{$i}", ConsumerTest::class);
//        }
    }

    public static function onRequest(Request $request,Response $response): void
    {
        // TODO: Implement onRequest() method.
    }

    public static function afterAction(Request $request,Response $response): void
    {
        // TODO: Implement afterAction() method.
    }
}