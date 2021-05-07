<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use common\components\W;
use yii\db\Query;
class Base_model extends ActiveRecord
{
    public $tablePK = 'id';
    public $cacheTime=1;
    protected $formData = [];
    protected $cmdQuery;
    protected $boolCache = false;//是否启用缓存
    protected $cmdByCreate; //通过query生成的cmd对象
    public function __construct($cacheTime=1){
        $this->cacheTime=$cacheTime;
    }

    //创建数据对象（DAO）
    protected function Conn($sql){
        return Yii::$app->db->createCommand($sql);
    }

    //读取数据
    public function getData($field='*',$query='all',$where='',$order='',$limit='',$group='',$lock=false){
        if($where)$where=" where 1 and ".$where;
        if($order)$order=" order by ".$order;
        if($limit)$limit=" limit ".$limit;
        if($group)$group=" group by ".$group;
        $sql="select $field from ".$this->tableName()." $where $group $order $limit";
        if($lock)
        {
            $sql.=' lock in share mode';
        }

        $key = md5($sql);
        if(!$this->cacheTime){
            Yii::$app->cache->delete($key);
        }
        $rs = Yii::$app->cache->get($key);
        if(!$rs){
            $query = 'query'.ucfirst($query);
            $rs=$this->Conn($sql)->$query();
            Yii::$app->cache->set($key, $rs, $this->cacheTime);
        }
        //	var_dump($rs);exit;
        return $rs;

    }

    //更新数据
    public function upData($fieldVal=array(),$where=''){
        if($where)$where=" where 1 and ".$where;
        $field2Value='';
        foreach ($fieldVal as $k=>$v){
            $v=mysql_escape_string($v);
            if(strpos($v,$k)!==false){
                if(strpos($v,'+')!==false || strpos($v,'-')!==false || strpos($v,'*')!==false || strpos($v,'/')!==false)$field2Value.=",`".$k."` = ".$v;
                else $field2Value.=",`".$k."` = '".$v."'";
            }else $field2Value.=",`".$k."` = '".$v."'";
        }
        $field2Value=substr($field2Value, 1);
        $sql="update ".$this->tableName()." set $field2Value $where";

        //echo $sql;// die;
        $rs=$this->Conn($sql)->execute();
        return $rs;
    }

    //写入数据
    public function addData($fieldVal=array(), $upFields = array()){
        $fields = $values = $keyVals ='';
        $i = 0;
        foreach ($fieldVal as $k=>$v){
            if(is_array($v)){
                $valuesStr='';
                foreach ($v as $key => $val){
                    if($i==0){
                        $fields.=',`'.$key.'`';
                    }
                    $val=mysql_escape_string($val);
                    $valuesStr.=",'".$val."'";
                }
                $values .= ',('.substr($valuesStr, 1).')';
                ++$i;
            }else{
                $v=mysql_escape_string($v);
                $fields.=',`'.$k.'`';
                $values.=",'".$v."'";
            }
        }
        foreach ($upFields as $field){
            $keyVals.=',`'.$field.'` = VALUES(`'.$field.'`)';
        }
        $fields=substr($fields, 1);
        $values=($i==0?'('.substr($values, 1).')':substr($values, 1));
        $keyVals=substr($keyVals, 1);
        $sql="insert into ".$this->tableName()." ($fields) values $values";
        if($keyVals)$sql.=" ON DUPLICATE KEY UPDATE $keyVals";//表中须有Unique索引字段才能使用
        //echo $sql;die;
        try{
            $rs=$this->Conn($sql)->execute();
            if($rs)return W::I();//lastInertId
        }catch (\Exception $e){
            //echo $e;
        }
        return 0;
    }
    //删除数据
    public  function  deldata($where)
    {
        if($where)$where=" where 1 and ".$where;
        $sql='delete from '.$this->tableName().$where;
        $rs=$this->Conn($sql)->execute();
        return rs;
    }

    //sql
    public function getsql($type,$fieldVal=array(),$where='')
    {
        $act=array('select','update','insert');
        switch($act[$type])
        {
            case 'select':
                if($where) $where=' where 1 and '.$where;
                $fields=implode(',',$fieldVal);
                if(!$fieldVal)
                {
                    $sql='select '.$fields.' from '.$this->tableName().$where;
                }
                else
                {
                    $sql='select  *  from '.$this->tableName().$where;
                }

                break;
            case 'update':
                if($where)$where=" where 1 and ".$where;
                $field2Value='';
                foreach ($fieldVal as $k=>$v){
                    $v=mysql_real_escape_string($v);
                    $field2Value.=",`".$k."` = '".$v."'";
                }
                $field2Value=substr($field2Value, 1);
                $sql="update ".$this->tableName()." set $field2Value $where";
                break;
            case 'insert':
                $fields=$values='';
                foreach ($fieldVal as $k=>$v){
                    $v=mysql_real_escape_string($v);
                    $fields.=',`'.$k.'`';
                    $values.=",'".$v."'";
                }
                $fields=substr($fields, 1);
                $values=substr($values, 1);
                $sql="insert into ".$this->tableName()." ($fields) values ($values)";
                break;

        }
        return $sql;
    }
    //返回表单数据，已自动完成
    public function getFormData(){
        return $this->formData;
    }
    //提取表单的数据
    public function loadData($data){
        $fields = $this->tableFields;
        foreach ($data as $key=>$val){
            if(in_array($key,$fields)){
                $this->formData[$key] = trim($val);
                $this->$key = trim($val);
            }
        }
        return $this;
    }

    //加载表单数据，并自动生成不在表单中的数据
    public function create($data){
        //加载表单数据
        $this->loadData($data);
        //自动生成
        $auto = $this->_auto;
        if(!$auto) return $this;
        $pk = $this->tablePK;
        //判断是否设置了pk值，如果有设置，则为update,如果没有，则为新增
        $_type = 0;//新增模式，1为修改模式
        if(isset($this->$pk)) $_type = 1;

        foreach ($auto as $val){
            $this->parseAuto($val,$_type);
        }
        return $this;
    }

    public function parseAuto($rule,$_type){
        list($field,$occasion,$method,$mixed) = $rule;
        if($occasion != $_type && $occasion != 2) return true;//不必要自动完成的情况
        if(isset($this->$field)) return true;//如果已经设置了值则跳过
        $value = null;
        if($method == 1) $value = $mixed;
        if($method == 2) $value = eval('return '.$mixed.';');
        $this->$field = $value;
        $this->formData[$field] = $value;
        return true;
    }

    /**
     * @param $data     要更新的数据
     * @param $where    更新条件
     * @param $params   绑定参数
     */
    public function myUpdate($data=[],$where=[],$params=[]){
        if(empty($data)) $data = $this->getFormData();
        if(empty($data)) return false;
        if(empty($where)){
            if(isset($data[$this->tablePK]) && $data[$this->tablePK]){
                $where[$this->tablePK] = $data[$this->tablePK];
                unset($data[$this->tablePK]);
            }
        }
        if(isset($data[$this->tablePK])){
            unset($data[$this->tablePK]);
        }
        $tablename = static::tableName();
        $cmd = Yii::$app->db->createCommand();
        $res = $cmd->update($tablename,$data,$where,$params)->execute();
        return $res;
    }

    //数据插入
    public function myInsert($data = []){
        if(empty($data)) $data = $this->getFormData();
        if(empty($data)) return false;
        $tablename = static::tableName();
        $cmd = Yii::$app->db->createCommand();
        if(!$data) return false;
        $res = $cmd->insert($tablename,$data)->execute();
        if($res) return Yii::$app->db->getLastInsertID();
        return $res;
    }

    /**
     * @param array $where  删除条件
     */
    public function myDel($where = []){
        $tablename = static::tableName();
        $cmd = Yii::$app->db->createCommand();
        $res = $cmd->delete($tablename,$where)->execute();
        return $res;
    }

    /**
     * @param string $field         要检索的数据字段
     * @param array $where          查询条件
     * @param array $wparams        要绑定到查询条件上的数据
     * @return $this
     */
    public function select($field="*",$where=[],$wparams = []){
        $this->cmdQuery = new Query();
        $this->cmdQuery->select($field)->from(static::tableName())->where($where,$wparams);
        return $this;
    }

    public function andWhere($where = [],$params=[]){
        if(empty($where)) return $this;
        $this->cmdQuery->andWhere($where,$params);
        return $this;
    }

    public function orWhere($where = [],$params=[]){
        if(empty($where)) return $this;
        $this->cmdQuery->orWhere($where,$params);
        return $this;
    }

    public function having($where = [],$params=[]){
        if(empty($where)) return $this;
        $this->cmdQuery->having($where,$params);
        return $this;
    }
    public function andHaving($where = [],$params=[]){
        if(empty($where)) return $this;
        $this->cmdQuery->andHaving($where,$params);
        return $this;
    }
    public function orHaving($where = [],$params=[]){
        if(empty($where)) return $this;
        $this->cmdQuery->orHaving($where,$params);
        return $this;
    }

    public function join($type,$table,$on = '',$params = []){
        $this->cmdQuery->join($type,$table,$on,$params);
        return $this;
    }

    /**
     * @param int $page     第page页
     * @param int $pagesize 每页记录条数
     */
    public function page($page = 1,$pagesize = 20){
        if(!$page) $page = 1;
        $page = (int)$page;
        $limit = $pagesize;
        $offset = ($page - 1) * $limit;
        $this->limitAndoffset($limit,$offset);
        return $this;
    }

    /**
     * @param null $limit
     * @param null $offset
     * @return $this
     */
    public function limitAndoffset($limit = null,$offset = null){
        if($limit !== null && is_numeric($limit)){
            $this->cmdQuery->limit($limit);
        }
        if($offset !== null && is_numeric($offset)){
            $this->cmdQuery->offset($offset);
        }
        return $this;
    }

    /**
     * @param string $order 排序设置
     * @return $this
     */
    public function orderBy($order = 'id DESC'){
        if($order) $this->cmdQuery->orderBy($order);
        return $this;
    }

    /**
     * group
     * @param string|array $group   分组字段
     */
    public function group($group = ''){
        if((is_string($group) || is_array($group)) && $group) $this->cmdQuery->groupBy($group);
        return $this;
    }

    public function indexBy($func = null){
        if($func) $this->cmdQuery->indexBy($func);
        return $this;
    }

    //返回所有数据
    public function all(){
        if($this->boolCache){
            return $this->cacheSql('all');
        }
        return $this->cmdQuery->all();
    }

    //返回一条数据
    public function one(){
        if($this->boolCache){
            return $this->cacheSql('one');
        }
        return $this->cmdQuery->one();
    }

    //获得当前要执行的sql语句
    public function getLastSql(){
        $cmd = $this->cmdByCreate = $this->cmdQuery->createCommand();
        $sql = $cmd->sql;
        $params = $cmd->params;
        $find = array_keys($params);
        $replace = array_values($params);
        $lastsql = str_replace($find,$replace,$sql);
        return $lastsql;
    }

    //设置缓存
    public function cache($bool,$expires = 0){
        if($bool && $expires) $this->boolCache = $expires;
        return $this;
    }
    //缓存处理
    protected function cacheSql($ext = 'all'){
        $key = md5($ext.$this->getLastSql());
        $cache = Yii::$app->cache;
        $data = $cache->get($key);
        $method = $ext;
        if($data === false){
            $data = $this->cmdQuery->$method();
            if(empty($data)){
                $this->boolCache =false;
                return [];
            }
            $cache->set($key,$data,$this->boolCache);
        }
        $this->boolCache =false;
        return $data;
    }
    //或数据总条数
    public function count($q = '*')
    {
        return $this->cmdQuery->count($q);
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
        $list = $this->select($field,$where)->page($page, $pagesize)->orderBy($order)->all();
        $cot = $this->select('id',$where)->count();
        return ['IsOk' => 1, 'total' => $cot, 'rows' => $list];
    }
}