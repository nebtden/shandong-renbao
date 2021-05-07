<?php
namespace common\models;
use Yii;

class Ad_location extends Base_model
{
    public  static function tableName()
    {
        return '{{%ad_location}}';
    }

    public static  function Categorys(){
        $self=new self();
        $data=$self->getData('*','all','status=1');
        foreach($data as $k=>$v){
            $data[$v['id']]=$v['name'];
        }
        return $data;
    }

}
