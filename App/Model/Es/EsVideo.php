<?php
namespace App\Model\Es;

use EasySwoole\Core\Component\Di;
class EsVideo {
    public $index = "imooc_video";
    public $type = "video";

    /**
     * searchByName
     * @param $name
     * @param string $type
     * @return array
     */
    public function searchByName($name, $type = "match")
    {
        $name =trim($name);
        if (empty($name)) {
            return [];
        }

        $params = [
            "index" => $this->index,
            "type" => $this->type,
            //"id" => 1,
            "body" => [
                "query" => [
                    $type => [
                        "name" => $name,
                    ]
                ]
            ]
        ];

        $client = Di::getInstance()->get("ES");
        $result = $client->search($params);

        return $result;
    }
}