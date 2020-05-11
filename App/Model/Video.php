<?php
namespace App\Model;


class Video extends Base {

    public $tableName = "video";

    public function getVideoData($condition = [], $page = 1, $size = 10) {
        $this->db->paginate($this->tableName, $page);
        echo $this->db->getLastQuery();
    }

}