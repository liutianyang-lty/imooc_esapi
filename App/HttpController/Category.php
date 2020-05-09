<?php
namespace App\HttpController;
use EasySwoole\Core\Http\AbstractInterface\Controller;
class Category extends Controller
{
    public function index()
    {
        $data = [
            'id' => 1,
            'name' => 'singwa老师荣获国家级计算机大赛一等奖'
        ];

        return $this->writeJson(200, 'OK', $data);
        // TODO: Implement index() method.
        $this->response()->write('hello world');
    }
}

