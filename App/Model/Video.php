<?php
namespace App\Model;


class Video extends Base {

    public $tableName = "video";

    /**
     * 获取video表中的分页数据
     * @param array $condition
     * @param int $page
     * @param int $size
     * @return array
     * @throws \Exception
     */
    public function getVideoData($condition = [], $page = 1, $size = 10) {
        if (!empty($condition['cat_id'])) {
            $this->db->where('cat_id', $condition['cat_id']);
        }
        if (!empty($size)) {
            $this->db->pageLimit = $size;
        }

        $this->db->orderBy('id','desc');
        $res = $this->db->paginate($this->tableName, $page);

        $data = [
            'total_page' => $this->db->totalPages,
            'page_size' => $size,
            'count' => intval($this->db->totalCount),
            'lists' => $res
        ];
        return $data;
    }

}