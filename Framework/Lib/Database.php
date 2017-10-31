<?php
namespace Framework\Lib;
class Database extends DbMysqli{
    /**
     * 数据库获取分页方式
     * @param $pagesize
     * @return array|bool|object
     */
    public function paginate($pagesize = 15){
        $page  = abs(intval($_GET['page']));
        $start = $page == 0 ? 0 : ( $page - 1 ) * $pagesize;
        $limit = $start.",".$pagesize;
        return $this->limit($limit)->fetch_all();
    }
}