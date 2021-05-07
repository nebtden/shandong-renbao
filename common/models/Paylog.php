<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/15 0015
 * Time: 16:00
 */

namespace common\models;
use Yii;

class Paylog extends  Base_model {

    public static  function tableName(){
        return '{{%fans_applylog}}';
    }

    public  static function   paylogs($cid)
    {
        $model=new self();
        $res=array();
        if($cid)
        {
            $where=' cid in('.$cid.') and token="'.Yii::$app->session['token'].'"';
            $data=$model->getData('cid,paytime','all',$where);
            if($data)
            {
                foreach($data as $v)
                {
                    $res[$v['cid']]=$v;
                }
            }

        }

        return $res;
    }


}