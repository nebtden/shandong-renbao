<?php
namespace common\models;
use Yii;

class Recieve extends Base_model
{
    public  static function tableName()
    {
        return '{{%reciever}}';
    }

    public static function  ablecode()
    {
        $model = new self();
        $res='';
        do{
            $number=rand(0,1000000000);
            $data = $model->getData('id', 'one', '`number`="' . $number . '"');
            $res=$number;
        }
        while($data);
        return $res;
    }


}