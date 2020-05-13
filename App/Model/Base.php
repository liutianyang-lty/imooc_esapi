<?php
namespace App\Model;

use EasySwoole\Core\Component\Di;
class Base {

    public $db = "";

    public function __construct()
    {
        if (empty($this->tableName)) {
             throw new \Exception("table error");
        }

        $db = Di::getInstance()->get("MYSQL"); //通过依赖注入得到mysql实例

        if ($db instanceof \Mysqlidb) {
            $this->db = $db;
        } else {
            throw new \Exception("db error");
        }
    }

    /**
     * 添加的通用方法
     * @param $data
     * @return bool
     * @throws \Exception
     */
    public function add($data)
    {
        if (empty($data) || !is_array($data)) {
            return false;
        }

        return $this->db->insert($this->tableName, $data);
    }

    public function getById($id)
    {
        $id = intval($id);
        if (empty($id)) {
            return [];
        }
        $this->db->where('id', $id);
        $result = $this->db->getOne($this->tableName);
        return $result ?? [];
    }
}