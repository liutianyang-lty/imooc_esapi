<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/5/10
 * Time: 下午12:25
 */

namespace EasySwoole\Core\Component\Rpc;


use EasySwoole\Core\Component\Invoker;
use EasySwoole\Core\Component\Openssl;
use EasySwoole\Core\Component\Rpc\Client\TaskObj;
use EasySwoole\Core\Component\Rpc\Common\Parser;
use EasySwoole\Core\Component\Rpc\Common\ServiceCaller;
use EasySwoole\Core\Component\Rpc\Client\ServiceResponse;
use EasySwoole\Core\Component\Rpc\Common\Status;
use EasySwoole\Core\Component\Rpc\Common\ServiceNode;
use EasySwoole\Core\Component\Trigger;


class CoroutineClient
{
    private $taskList = [];

    private $clientConnectTimeOut = 0.1;

    private $failNodes = [];

    function addCall(string $serviceName,string $serviceGroup,string $action,...$args)
    {
        $obj = new TaskObj();
        $obj->setServiceName($serviceName);
        $obj->setServiceAction($action);
        $obj->setServiceGroup($serviceGroup);
        $obj->setArgs($args);
        $this->taskList[] = $obj;
        return $obj;
    }

    function call($timeOut = 0.1)
    {
        $clients = [];
        $taskMap = [];
        $nodeMap = [];
        $this->failNodes = [];
        foreach ($this->taskList as $task){
            //获取节点
            $serviceNode = Server::getInstance()->getServiceOnlineNode($task->getServiceName());
            if($serviceNode instanceof ServiceNode){
                $client = $this->connect($serviceNode);
                if($client){
                    $client->send($this->buildData($serviceNode,$task));
                    $index = count($clients);
                    $clients[$index] = $client;
                    $taskMap[$index] = $task;
                    $nodeMap[$index] = $serviceNode;
                }else{
                    $response = new ServiceResponse($task->toArray()+['status'=>Status::CLIENT_CONNECT_FAIL]);
                    $this->callFunc($response,$task);
                    $this->failNodes[] = $serviceNode;
                }
            }else{
                $response = new ServiceResponse($task->toArray()+['status'=>Status::CLIENT_SERVER_NOT_FOUND]);
                $this->callFunc($response,$task);
            }
        }

        foreach ($clients as $index => $client) {
            $msg = $client->recv($timeOut);
            $node = $nodeMap[$index];
            if ($msg == '') {
                $this->failNodes[] = $node;
            }
            $response = $this->decodeData($node,$msg);
            $this->callFunc($response,$taskMap[$index]);
            $client->close();
            unset($clients[$index]);
        }
    }

    function getFailNodes():array
    {
        return $this->failNodes;
    }

    private function callFunc(ServiceResponse $obj,TaskObj $taskObj)
    {
        if($obj->getStatus() === Status::OK){
            $func = $taskObj->getSuccessCall();
        }else{
            $func = $taskObj->getFailCall();
        }
        if(is_callable($func)){
            try{
                Invoker::callUserFunc($func,$obj);
            }catch (\Throwable $exception){
                Trigger::throwable($exception);
            }
        }
    }

    private function connect(ServiceNode $node): ?\Swoole\Coroutine\Client
    {
        $client = new \Swoole\Coroutine\Client(SWOOLE_SOCK_TCP);
        $client->set([
            'open_length_check' => true,
            'package_length_type' => 'N',
            'package_length_offset' => 0,
            'package_body_offset' => 4,
            'package_max_length' => 1024 * 64
        ]);
        if ($client->connect($node->getAddress(), $node->getPort(),$this->clientConnectTimeOut)) {
            return $client;
        } else {
            $client->close();
            return null;
        }
    }

    private function buildData(ServiceNode $node,TaskObj $taskObj)
    {
        $data = (new ServiceCaller($taskObj->toArray()))->__toString();
        if(!empty($node->getEncryptToken())){
            $openssl = new Openssl($node->getEncryptToken());
            $data = $openssl->encrypt($data);
        }
        return Parser::pack($data);
    }

    private function decodeData(ServiceNode $node,?string $raw):ServiceResponse
    {
        $raw = Parser::unPack($raw);
        if(!empty($node->getEncryptToken())){
            $openssl = new Openssl($node->getEncryptToken());
            $raw = $openssl->decrypt($raw);
            if($raw === false){
                $json = [
                    'status'=>Status::PACKAGE_ENCRYPT_DECODED_ERROR
                ];
            }
        }
        //如果已经解密失败,则不再做包解析
        if(!isset($json)){
            $json = json_decode($raw,true);
        }
        //若包解析失败
        if(!is_array($json)){
            $json = [
                'status'=>Status::CLIENT_WAIT_RESPONSE_TIMEOUT
            ];
        }
        return new ServiceResponse( $json + ['responseNode'=>$node]);
    }
}