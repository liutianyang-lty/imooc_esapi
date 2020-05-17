<?php
namespace App\Mode\Es;

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

        $client = ClientBuilder::create()->setHosts(["127.0.0.1:8301"])->build();
        //$result = $client->get($params);
        $result = $client->search($params);

        return $result;
    }
}