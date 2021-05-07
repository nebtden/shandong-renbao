<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/15 0015
 * Time: 16:01
 */

namespace common\models;
use Yii;

class Fans_cashType  extends  Base_model{

    public static function  tableName(){
        return "{{%fans_cashType}}";
    }

    public function   Cashinfo()
    {
        $arr=array();
        $data=$this->getData('*','all','token="'.Yii::$app->session['token'].'" and status=1');
        foreach($data as $v)
        {
            $arr[$v['openid']]=array($v['cash_type'],$v['idCard'],$v['account'],$v['realname'],$v['cash_name']);

        }
        return $arr;
    }

}