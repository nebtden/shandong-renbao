<?php

namespace common\components;

use Yii;
use yii\db\Query;

class AlxgBase
{
    protected $pk = 'id';
    protected $query;                  //query对象
    protected $currentTable = null;   //当前操作的表
    protected $cacheDuration = -1;
    /**
     * AlxgBase constructor.
     * @param null $table 当前操作的表
     * @param null $pk 当前操作表的主键
     */
    public function __construct($table = null, $pk = null)
    {
        if ($table && is_string($table)) $this->currentTable = '{{%' . $table . '}}';
        if ($pk !== null && is_string($pk)) $this->pk = $pk;
    }

    /**
     * 调用Query的方法
     * @param $name
     * @param $arguments
     * @return $this
     */
    public function __call($name, $arguments)
    {

        if (method_exists($this->query, $name)) {
            call_user_func_array(array($this->query, $name), $arguments);
        }
        return $this;
    }

    /**
     * 批量插入
     * @param $table
     * @param $columns
     * @param $rows
     * @return int
     */
    public function batchInsert($table, $columns, $rows)
    {
        if (!$table) $table = $this->currentTable;
        return Yii::$app->db->createCommand()->batchInsert($table, $columns, $rows)->execute();
    }

    /**
     * 插入
     * @param $data
     * @return $this|string|\yii\db\Command
     */
    public function myInsert($data)
    {
        $db = Yii::$app->db;
        $cmd = $db->createCommand();
        $res = $cmd->insert($this->currentTable, $data)->execute();
        if ($res) return $db->getLastInsertID();
        return $res;
    }


    /**
     * 更新
     * @param array $data
     * @param array $condition
     * @param array $bind
     * @return bool|int
     */
    public function myUpdate($data = [], $condition = [], $bind = [])
    {
        $pk = $this->pk;
        $pkVal = null;
        if (isset($data[$pk])) {
            $pkVal = $data[$pk];
            unset($data[$pk]);
        }
        if (empty($condition) && $pkVal !== null) {
            $condition[$pk] = $pkVal;
        }
        if (empty($condition)) return false;
        $db = Yii::$app->db;
        $cmd = $db->createCommand();
        $res = $cmd->update($this->currentTable, $data, $condition, $bind)->execute();
        return $res;
    }

    /**
     * 删除
     * @param array $condition
     * @param array $bind
     * @return int
     */
    public function myDel($condition = [], $bind = [])
    {
        $db = Yii::$app->db;
        $cmd = $db->createCommand();
        $res = $cmd->delete($this->currentTable, $condition, $bind)->execute();
        return $res;
    }

    //查询初始化
    public function table()
    {
        unset($this->query);
        $this->query = new Query();
        $this->query->from($this->currentTable);
        return $this;
    }

    /**
     * 使用缓存
     * @param int $duration -1不使用缓存，0，永久缓存，正整数，缓存时间
     */
    public function cache($duration = -1){
        $this->cacheDuration = $duration;
        return $this;
    }

    /**
     * 查询
     * @param array $field
     * @param null $options
     * @return $this
     */
    public function select($field = [], $options = null)
    {
        $this->query->select($field, $options);
        return $this;
    }

    public function one()
    {
        return $this->query->createCommand()->cache($this->cacheDuration)->queryOne();
    }

    public function all()
    {
        return $this->query->createCommand()->cache($this->cacheDuration)->queryAll();
    }

    public function column()
    {
        return $this->query->column();
    }

    public function sum($q)
    {
        return $this->query->sum($q);
    }

    public function count($q = '*')
    {
        return $this->query->count($q);
    }

    /**
     * @param $page
     * @param $pagesize
     * @return $this
     */
    public function page($page, $pagesize)
    {
        $page = intval($page);
        if (!is_int($page) || $page < 1) $page = 1;
        $offset = ($page - 1) * $pagesize;
        $this->query->limit($pagesize)->offset($offset);
        return $this;
    }

    public function getLastSql()
    {
        return $this->query->createCommand()->rawSql;
    }

    /**
     * @param string $field 要获取的字段
     * @param array $where 查询条件
     * @param string $order 排序字段
     * @return array|bool       返回数据列表
     */
    public function page_list($field = '*', $where = [], $order = 'id DESC' ,$one=false)
    {
        if (!$field) $field = '*';
        if (!$where) $where = [];
        if (!$order) $order = 'id DESC';

        $request = Yii::$app->request;
        $page = $request->get("pageNumber", 1);
        $pagesize = $request->get("pageSize", 10);
        if($one){
            $page = 1;
            $pagesize = 1;
        }
        $list = $this->table()->select($field)->where($where)->page($page, $pagesize)->orderBy($order)->all();

        $cot = $this->table()->where($where)->count();
        return ['IsOk' => 1, 'total' => $cot, 'rows' => $list];
    }
}