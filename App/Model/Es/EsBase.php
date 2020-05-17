<?php
namespace App\Model\Es;

use EasySwoole\Core\Component\Di;
class EsBase {

    public $esClient = null;

    public function __construct()
    {
        $this->esClient = Di::getInstance()->get("ES");
    }

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


        $result = $this->esClient->search($params);

        return $result;
    }
}