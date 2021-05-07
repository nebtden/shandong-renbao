<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/23 0023
 * Time: 下午 4:47
 */
namespace common\models;

use Yii;
use common\components\AlxgBase;

class Car_coupon_explain extends AlxgBase
{
    protected $currentTable = '{{%car_coupon_explain}}';

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

    public function get_use_text(){
        $map['status'] = 1;
        $tmp = $this->table()->select("coupon_type,content,company")->where($map)->all();
        $list = [];
        foreach ($tmp as $val){
            $type = $val['coupon_type'];
            $list[$type][$val['company']] = explode('|||',$val['content']);
        }
        return $list;
    }
}