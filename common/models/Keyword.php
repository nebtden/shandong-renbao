<?php
namespace common\models;
use Yii;

class Keyword extends Base_model
{
    public  static function tableName()
    {
        return '{{%keyword}}';
    }

    //关键字处理
    public static function dealKeyword($data){
        $model = new self();
        $where = "`token`='".Yii::$app->session['token']."' and `module` = '".$data['module']."' and `m_id` = '".$data['m_id']."'";
        $key = $model->getData('*','one',$where);
        $flag = false;
        if(!$key){
            $data['token'] = Yii::$app->session['token'];
            $data['createtime'] = time();
            $flag = $model ->addData($data);
        }else{
            $flag = $model ->upData($data,$where);
        }
        return $flag;
    }

}
