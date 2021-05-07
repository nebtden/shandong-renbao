<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/11 0011
 * Time: 上午 10:21
 */
namespace common\models;

use Yii;
use common\components\AlxgBase;

class Car_indexad extends AlxgBase{
    protected $currentTable = '{{%car_indexad}}';

    public function get_list($field = '*',$sort = '`sort` ASC'){
        return $this->table()->select($field)->where(['status' => 1])->orderBy($sort)->all();
    }

    public function edit_ad($data = []){
        $update = false;
        $now = time();
        if(isset($data['id'])) $update = true;
        $data['u_time'] = $now;
        if($update){
            return $this->myUpdate($data);
        }else{
            $data['c_time'] = $now;
            return $this->myInsert($data);
        }
    }
}